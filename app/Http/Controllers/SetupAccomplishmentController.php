<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSetupAccomplishmentRequest;
use App\Http\Requests\UpdateSetupAccomplishmentRequest;
use App\Jobs\RunVerificationChecks;
use App\Models\Project;
use App\Models\SetupAccomplishment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SetupAccomplishmentController extends Controller
{
    public function index(Project $project): View
    {
        $entries = $project->setupAccomplishments()
            ->latest('year')
            ->paginate(15);

        return view('setup-accomplishments.index', compact('project', 'entries'));
    }

    public function create(Project $project): View
    {
        return view('setup-accomplishments.create', compact('project'));
    }

    public function store(StoreSetupAccomplishmentRequest $request, Project $project): RedirectResponse
    {
        try {
            $entry = $project->setupAccomplishments()->create(
                $request->validated() + ['encoded_by' => $request->user()->id]
            );
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()
                ->withErrors(['year' => "A SETUP record for {$request->year} already exists for this project."]);
        }

        RunVerificationChecks::dispatch($entry);

        return redirect()->route('projects.setup-accomplishments.index', $project)
            ->with('success', 'SETUP KPI record saved and queued for verification.');
    }

    public function edit(Project $project, SetupAccomplishment $setupAccomplishment): View
    {
        return view('setup-accomplishments.edit', compact('project', 'setupAccomplishment'));
    }

    public function update(UpdateSetupAccomplishmentRequest $request, Project $project, SetupAccomplishment $setupAccomplishment): RedirectResponse
    {
        try {
            $setupAccomplishment->update($request->validated());
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()
                ->withErrors(['year' => "A SETUP record for {$request->year} already exists for this project."]);
        }

        $setupAccomplishment->update(['verified_status' => 'pending', 'verification_notes' => null]);

        RunVerificationChecks::dispatch($setupAccomplishment->fresh());

        return redirect()->route('projects.setup-accomplishments.index', $project)
            ->with('success', 'SETUP KPI record updated and re-queued for verification.');
    }

    public function destroy(Project $project, SetupAccomplishment $setupAccomplishment): RedirectResponse
    {
        $this->authorize('delete', $setupAccomplishment);

        $setupAccomplishment->delete();

        return redirect()->route('projects.setup-accomplishments.index', $project)
            ->with('success', 'SETUP KPI record deleted.');
    }
}
