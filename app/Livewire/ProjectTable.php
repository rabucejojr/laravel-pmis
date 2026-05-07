<?php

namespace App\Livewire;

use App\Models\Program;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectTable extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $programCode = '';
    public string $status      = '';
    public string $sortField   = 'created_at';
    public string $sortDir     = 'desc';

    protected $queryString = [
        'search'      => ['except' => ''],
        'programCode' => ['except' => '', 'as' => 'program'],
        'status'      => ['except' => ''],
    ];

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingProgram(): void { $this->resetPage(); }
    public function updatingStatus(): void  { $this->resetPage(); }

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

    public function render()
    {
        $programs = Program::where('is_active', true)->orderBy('code')->get();

        $projects = Project::with('program')
            ->when($this->search, fn ($q) =>
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                      ->orWhere('implementing_agency', 'like', "%{$this->search}%")
                      ->orWhere('location', 'like', "%{$this->search}%");
                })
            )
            ->when($this->programCode, fn ($q) =>
                $q->whereHas('program', fn ($q) =>
                    $q->where('code', $this->programCode)
                )
            )
            ->when($this->status, fn ($q) =>
                $q->where('status', $this->status)
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(15);

        return view('livewire.project-table', compact('projects', 'programs'));
    }
}
