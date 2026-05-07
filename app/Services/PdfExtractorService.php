<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class PdfExtractorService
{
    public function __construct(private Parser $parser) {}

    public function extract(string $absolutePath): string
    {
        if (! file_exists($absolutePath)) {
            return '';
        }

        try {
            $pdf  = $this->parser->parseFile($absolutePath);
            $text = $pdf->getText();
            return $text ?? '';
        } catch (\Throwable) {
            return '';
        }
    }
}
