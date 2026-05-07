<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Program;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $program = Program::where('code', $request->program)->first();

        return view('projects.index', compact('program'));
    }

    public function create(): View
    {
        $programs = Program::where('is_active', true)->orderBy('code')->get();

        return view('projects.create', compact('programs'));
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load(['program', 'documents.uploader']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $programs = Program::where('is_active', true)->orderBy('code')->get();

        return view('projects.edit', compact('project', 'programs'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
