<?php

namespace App\Livewire;

use App\Models\FinancialTarget;
use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class FinancialTargetTable extends Component
{
    use WithPagination;

    public int    $projectId;
    public string $filterYear    = '';
    public string $filterQuarter = '';
    public string $filterMonth   = '';
    public string $filterStatus  = '';
    public string $sortField     = 'year';
    public string $sortDir       = 'desc';

    protected $queryString = [
        'filterYear'    => ['except' => ''],
        'filterQuarter' => ['except' => ''],
        'filterMonth'   => ['except' => ''],
        'filterStatus'  => ['except' => ''],
        'sortField'     => ['except' => 'year'],
        'sortDir'       => ['except' => 'desc'],
    ];

    public function updatingFilterYear():    void { $this->resetPage(); }
    public function updatingFilterQuarter(): void { $this->resetPage(); }
    public function updatingFilterMonth():   void { $this->resetPage(); }
    public function updatingFilterStatus():  void { $this->resetPage(); }

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

    public function render(): View
    {
        $user = auth()->user();

        $targets = FinancialTarget::where('project_id', $this->projectId)
            ->when(! $user->hasRole(['admin', 'encoder', 'verifier']), fn ($q) =>
                $q->where('verified_status', 'verified')
            )
            ->when($this->filterYear,    fn ($q) => $q->where('year',            $this->filterYear))
            ->when($this->filterQuarter, fn ($q) => $q->where('quarter',         $this->filterQuarter))
            ->when($this->filterMonth,   fn ($q) => $q->where('month',           $this->filterMonth))
            ->when($this->filterStatus,  fn ($q) => $q->where('verified_status', $this->filterStatus))
            ->with('encoder')
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(15);

        $years = FinancialTarget::where('project_id', $this->projectId)
            ->distinct()->orderByDesc('year')->pluck('year');

        return view('livewire.financial-target-table', compact('targets', 'years'));
    }
}
