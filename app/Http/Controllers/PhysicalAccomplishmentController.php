<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhysicalAccomplishmentRequest;
use App\Http\Requests\UpdatePhysicalAccomplishmentRequest;
use App\Imports\PhysicalAccomplishmentImport;
use App\Jobs\RunVerificationChecks;
use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PhysicalAccomplishmentController extends Controller
{
    public function index(Project $project): View
    {
        return view('physical-accomplishments.index', compact('project'));
    }

    public function create(Project $project): View
    {
        return view('physical-accomplishments.create', compact('project'));
    }

    public function store(StorePhysicalAccomplishmentRequest $request, Project $project): RedirectResponse
    {
        try {
            $entry = $project->physicalAccomplishments()->create(
                $request->validated() + ['encoded_by' => $request->user()->id]
            );
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()
                ->withErrors(['indicator_name' => 'An entry for this period and indicator already exists.']);
        }

        RunVerificationChecks::dispatch($entry);

        return redirect()->route('projects.physical-accomplishments.index', $project)
            ->with('success', 'Physical accomplishment saved and queued for verification.');
    }

    public function edit(Project $project, PhysicalAccomplishment $physicalAccomplishment): View
    {
        return view('physical-accomplishments.edit', compact('project', 'physicalAccomplishment'));
    }

    public function update(UpdatePhysicalAccomplishmentRequest $request, Project $project, PhysicalAccomplishment $physicalAccomplishment): RedirectResponse
    {
        try {
            $physicalAccomplishment->update($request->validated());
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()
                ->withErrors(['indicator_name' => 'An entry for this period and indicator already exists.']);
        }

        // Reset to pending so the verifier re-reviews the updated entry.
        $physicalAccomplishment->update(['verified_status' => 'pending', 'verification_notes' => null]);

        RunVerificationChecks::dispatch($physicalAccomplishment->fresh());

        return redirect()->route('projects.physical-accomplishments.index', $project)
            ->with('success', 'Physical accomplishment updated and re-queued for verification.');
    }

    public function destroy(Project $project, PhysicalAccomplishment $physicalAccomplishment): RedirectResponse
    {
        $this->authorize('delete', $physicalAccomplishment);

        $physicalAccomplishment->delete();

        return redirect()->route('projects.physical-accomplishments.index', $project)
            ->with('success', 'Physical accomplishment deleted.');
    }

    public function import(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        Excel::import(new PhysicalAccomplishmentImport($project), $request->file('file'));

        return redirect()->route('projects.physical-accomplishments.index', $project)
            ->with('success', 'Import complete. Entries are queued for verification.');
    }
}
