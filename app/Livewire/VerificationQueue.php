<?php

namespace App\Livewire;

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\SetupAccomplishment;
use App\Models\VerificationLog;
use App\Notifications\EntryStatusChanged;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class VerificationQueue extends Component
{
    use WithPagination;

    public string $filterType   = '';   // 'financial' | 'physical' | 'setup' | ''
    public string $filterStatus = 'pending';  // 'pending' | 'flagged' | ''
    public string $filterYear   = '';
    public string $sortField    = 'created_at';
    public string $sortDir      = 'asc';

    public string $notesInput   = '';
    public ?int   $activeId     = null;
    public string $activeType   = '';  // 'financial' | 'physical' | 'setup'
    public string $activeAction = '';  // 'verified' | 'flagged' | 'rejected'

    public string $bulkAction = '';   // 'verified' | 'rejected'
    public string $bulkNotes  = '';

    protected $queryString = [
        'filterType'   => ['as' => 'type',   'except' => ''],
        'filterStatus' => ['as' => 'status', 'except' => 'pending'],
        'filterYear'   => ['as' => 'year',   'except' => ''],
        'sortField'    => ['as' => 'sort',   'except' => 'created_at'],
        'sortDir'      => ['as' => 'dir',    'except' => 'asc'],
    ];

    public function updatingFilterType(): void   { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterYear(): void   { $this->resetPage(); }

    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'asc';
        }
        $this->resetPage();
    }

    public function openConfirm(int $id, string $type, string $action): void
    {
        $this->activeId     = $id;
        $this->activeType   = $type;
        $this->activeAction = $action;
        $this->notesInput   = '';
    }

    public function cancelConfirm(): void
    {
        $this->activeId     = null;
        $this->activeType   = '';
        $this->activeAction = '';
        $this->notesInput   = '';
    }

    public function submitAction(): void
    {
        if (! auth()->user()->hasRole(['admin', 'verifier'])) {
            session()->flash('error', 'Unauthorized.');
            $this->cancelConfirm();
            return;
        }

        $this->validate(['notesInput' => 'nullable|string|max:1000']);

        $entry = $this->resolveEntry($this->activeId, $this->activeType);

        if (! $entry) {
            session()->flash('error', 'Entry not found.');
            $this->cancelConfirm();
            return;
        }

        if (! in_array($entry->verified_status, ['pending', 'flagged'])) {
            session()->flash('error', 'This entry has already been actioned.');
            $this->cancelConfirm();
            return;
        }

        if ($entry->verified_status === $this->activeAction) {
            session()->flash('error', 'Entry is already ' . $this->activeAction . '.');
            $this->cancelConfirm();
            return;
        }

        $entry->update([
            'verified_status'    => $this->activeAction,
            'verification_notes' => $this->notesInput ?: null,
        ]);

        $log = VerificationLog::create([
            'loggable_type' => get_class($entry),
            'loggable_id'   => $entry->id,
            'verifier_id'   => auth()->id(),
            'action'        => $this->activeAction,
            'notes'         => $this->notesInput ?: null,
        ]);

        $entry->loadMissing('encoder');
        if ($entry->encoder) {
            $entry->encoder->notify(new EntryStatusChanged($entry, $log->load('verifier')));
        }

        session()->flash('success', 'Entry marked as ' . $this->activeAction . '.');
        $this->cancelConfirm();
        $this->resetPage();
    }

    public function openBulkConfirm(string $action): void
    {
        $this->bulkAction = $action;
        $this->bulkNotes  = '';
    }

    public function cancelBulkConfirm(): void
    {
        $this->bulkAction = '';
        $this->bulkNotes  = '';
    }

    public function submitBulkAction(): void
    {
        if (! auth()->user()->hasRole(['admin', 'verifier'])) {
            session()->flash('error', 'Unauthorized.');
            $this->cancelBulkConfirm();
            return;
        }

        $this->validate(['bulkNotes' => 'nullable|string|max:1000']);

        $statuses = $this->filterStatus !== '' ? [$this->filterStatus] : ['pending', 'flagged'];
        $types    = $this->filterType !== '' ? [$this->filterType] : ['financial', 'physical', 'setup'];
        $count    = 0;

        $models = [
            'financial' => FinancialTarget::class,
            'physical'  => PhysicalAccomplishment::class,
            'setup'     => SetupAccomplishment::class,
        ];

        foreach ($types as $type) {
            $model = $models[$type] ?? null;
            if (! $model) continue;

            $query = $model::with(['project', 'encoder'])
                ->whereIn('verified_status', $statuses)
                ->when($this->filterYear, fn ($q) => $q->where('year', $this->filterYear));

            foreach ($query->get() as $entry) {
                $entry->update([
                    'verified_status'    => $this->bulkAction,
                    'verification_notes' => $this->bulkNotes ?: null,
                ]);

                $log = VerificationLog::create([
                    'loggable_type' => get_class($entry),
                    'loggable_id'   => $entry->id,
                    'verifier_id'   => auth()->id(),
                    'action'        => $this->bulkAction,
                    'notes'         => $this->bulkNotes ?: null,
                ]);

                if ($entry->encoder) {
                    $entry->encoder->notify(new EntryStatusChanged($entry, $log->load('verifier')));
                }

                $count++;
            }
        }

        session()->flash('success', "{$count} " . ($count === 1 ? 'entry' : 'entries') . " marked as {$this->bulkAction}.");
        $this->cancelBulkConfirm();
        $this->resetPage();
    }

    private function resolveEntry(int $id, string $type): FinancialTarget|PhysicalAccomplishment|SetupAccomplishment|null
    {
        return match($type) {
            'financial' => FinancialTarget::with(['project', 'encoder'])->find($id),
            'physical'  => PhysicalAccomplishment::with(['project', 'encoder'])->find($id),
            'setup'     => SetupAccomplishment::with(['project', 'encoder'])->find($id),
            default     => null,
        };
    }

    public function availableYears(): Collection
    {
        $ftYears = FinancialTarget::whereIn('verified_status', ['pending', 'flagged'])
            ->distinct()->orderBy('year')->pluck('year');
        $paYears = PhysicalAccomplishment::whereIn('verified_status', ['pending', 'flagged'])
            ->distinct()->orderBy('year')->pluck('year');
        $saYears = SetupAccomplishment::whereIn('verified_status', ['pending', 'flagged'])
            ->distinct()->orderBy('year')->pluck('year');

        return $ftYears->merge($paYears)->merge($saYears)->unique()->sort()->values();
    }

    public function render()
    {
        $statuses = $this->filterStatus !== '' ? [$this->filterStatus] : ['pending', 'flagged'];

        // --- Financial Targets ---
        $ftQuery = FinancialTarget::with(['project.program', 'encoder'])
            ->whereIn('verified_status', $statuses)
            ->when($this->filterYear, fn ($q) => $q->where('year', $this->filterYear));

        // --- Physical Accomplishments ---
        $paQuery = PhysicalAccomplishment::with(['project.program', 'encoder'])
            ->whereIn('verified_status', $statuses)
            ->when($this->filterYear, fn ($q) => $q->where('year', $this->filterYear));

        $entries = collect();

        if ($this->filterType === '' || $this->filterType === 'financial') {
            foreach ($ftQuery->get() as $ft) {
                $entries->push([
                    'type'               => 'financial',
                    'id'                 => $ft->id,
                    'project'            => $ft->project->title,
                    'program'            => $ft->project->program->code ?? '—',
                    'period'             => "Q{$ft->quarter} {$ft->year} / Mo.{$ft->month}",
                    'label'              => $ft->line_item,
                    'extra'              => '₱ ' . number_format($ft->target_amount, 2),
                    'encoder'            => $ft->encoder->name ?? '—',
                    'status'             => $ft->verified_status,
                    'notes'              => $ft->verification_notes,
                    'created_at'         => $ft->created_at,
                    'year'               => $ft->year,
                    'quarter'            => $ft->quarter,
                ]);
            }
        }

        if ($this->filterType === '' || $this->filterType === 'physical') {
            foreach ($paQuery->get() as $pa) {
                $entries->push([
                    'type'               => 'physical',
                    'id'                 => $pa->id,
                    'project'            => $pa->project->title,
                    'program'            => $pa->project->program->code ?? '—',
                    'period'             => "Q{$pa->quarter} {$pa->year} / Mo.{$pa->month}",
                    'label'              => $pa->indicator_name,
                    'extra'              => number_format($pa->accomplished_value, 2) . ' / ' . number_format($pa->target_value, 2),
                    'encoder'            => $pa->encoder->name ?? '—',
                    'status'             => $pa->verified_status,
                    'notes'              => $pa->verification_notes,
                    'created_at'         => $pa->created_at,
                    'year'               => $pa->year,
                    'quarter'            => $pa->quarter,
                ]);
            }
        }

        if ($this->filterType === '' || $this->filterType === 'setup') {
            $saQuery = SetupAccomplishment::with(['project.program', 'encoder'])
                ->whereIn('verified_status', $statuses)
                ->when($this->filterYear, fn ($q) => $q->where('year', $this->filterYear));

            foreach ($saQuery->get() as $sa) {
                $entries->push([
                    'type'       => 'setup',
                    'id'         => $sa->id,
                    'project'    => $sa->project->title,
                    'program'    => 'SETUP',
                    'period'     => (string) $sa->year,
                    'label'      => 'SETUP KPI Record',
                    'extra'      => number_format($sa->actual_num_projects) . ' projects · ₱' . number_format($sa->actual_ifund_amount, 0) . ' iFUND',
                    'encoder'    => $sa->encoder->name ?? '—',
                    'status'     => $sa->verified_status,
                    'notes'      => $sa->verification_notes,
                    'created_at' => $sa->created_at,
                    'year'       => $sa->year,
                    'quarter'    => 0,
                ]);
            }
        }

        // Sort
        $entries = $this->sortDir === 'asc'
            ? $entries->sortBy($this->sortField)
            : $entries->sortByDesc($this->sortField);

        // Manual pagination
        $page     = $this->getPage();
        $perPage  = 15;
        $total    = $entries->count();
        $items    = $entries->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.verification-queue', [
            'entries'        => $paginator,
            'availableYears' => $this->availableYears(),
            'totalPending'   => FinancialTarget::where('verified_status', 'pending')->count()
                              + PhysicalAccomplishment::where('verified_status', 'pending')->count()
                              + SetupAccomplishment::where('verified_status', 'pending')->count(),
            'totalFlagged'   => FinancialTarget::where('verified_status', 'flagged')->count()
                              + PhysicalAccomplishment::where('verified_status', 'flagged')->count()
                              + SetupAccomplishment::where('verified_status', 'flagged')->count(),
        ]);
    }
}
