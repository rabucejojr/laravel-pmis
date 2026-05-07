<?php

namespace App\Jobs;

use App\Models\ProjectDocument;
use App\Services\PdfExtractorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExtractDocumentText implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public ProjectDocument $document) {}

    public function handle(PdfExtractorService $extractor): void
    {
        if ($this->document->mime_type !== 'application/pdf') {
            return;
        }

        $absolutePath = Storage::disk('local')->path($this->document->file_path);

        $text = $extractor->extract($absolutePath);

        if ($text !== '') {
            $this->document->update(['extracted_text' => $text]);
        }
    }
}
