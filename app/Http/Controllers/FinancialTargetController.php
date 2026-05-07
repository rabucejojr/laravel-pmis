<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinancialTargetRequest;
use App\Http\Requests\UpdateFinancialTargetRequest;
use App\Imports\FinancialTargetImport;
use App\Jobs\RunVerificationChecks;
use App\Models\FinancialTarget;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class FinancialTargetController extends Controller
{
    public function index(Project $project): View
    {
        return view('financial-targets.index', compact('project'));
    }

    public function create(Project $project): View
    {
        return view('financial-targets.create', compact('project'));
    }

    public function store(StoreFinancialTargetRequest $request, Project $project): RedirectResponse
    {
        try {
            $entry = $project->financialTargets()->create(
                $request->validated() + ['encoded_by' => $request->user()->id]
            );
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()
                ->withErrors(['line_item' => 'A financial target for this period and line item already exists.']);
        }

        RunVerificationChecks::dispatch($entry);

        return redirect()->route('projects.financial-targets.index', $project)
            ->with('success', 'Financial target saved and queued for verification.');
    }

    public function edit(Project $project, FinancialTarget $financialTarget): View
    {
        return view('financial-targets.edit', compact('project', 'financialTarget'));
    }

    public function update(UpdateFinancialTargetRequest $request, Project $project, FinancialTarget $financialTarget): RedirectResponse
    {
        try {
            $financialTarget->update($request->validated());
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->withInput()
                ->withErrors(['line_item' => 'A financial target for this period and line item already exists.']);
        }

        // Reset to pending so the verifier re-reviews the updated entry.
        $financialTarget->update(['verified_status' => 'pending', 'verification_notes' => null]);

        RunVerificationChecks::dispatch($financialTarget->fresh());

        return redirect()->route('projects.financial-targets.index', $project)
            ->with('success', 'Financial target updated and re-queued for verification.');
    }

    public function destroy(Project $project, FinancialTarget $financialTarget): RedirectResponse
    {
        $this->authorize('delete', $financialTarget);

        $financialTarget->delete();

        return redirect()->route('projects.financial-targets.index', $project)
            ->with('success', 'Financial target deleted.');
    }

    public function import(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        Excel::import(new FinancialTargetImport($project), $request->file('file'));

        return redirect()->route('projects.financial-targets.index', $project)
            ->with('success', 'Import complete. Entries are queued for verification.');
    }
}
