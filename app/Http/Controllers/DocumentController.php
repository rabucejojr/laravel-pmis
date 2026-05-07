<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Jobs\ExtractDocumentText;
use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(StoreDocumentRequest $request, Project $project): RedirectResponse
    {
        $file = $request->file('file');
        $type = $request->document_type;

        $path = $file->store(
            "projects/{$project->id}/{$type}",
            'local'
        );

        $document = $project->documents()->create([
            'uploaded_by'   => $request->user()->id,
            'document_type' => $type,
            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
        ]);

        ExtractDocumentText::dispatch($document);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Document uploaded successfully.');
    }

    public function download(ProjectDocument $document)
    {
        abort_unless(
            Storage::disk('local')->exists($document->file_path),
            404,
            'File not found.'
        );

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function destroy(ProjectDocument $document): RedirectResponse
    {
        $project = $document->project;

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Document deleted.');
    }
}
