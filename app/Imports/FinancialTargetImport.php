<?php

namespace App\Imports;

use App\Jobs\RunVerificationChecks;
use App\Models\FinancialTarget;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FinancialTargetImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function __construct(private Project $project) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            try {
                $entry = FinancialTarget::create([
                    'project_id'       => $this->project->id,
                    'encoded_by'       => Auth::id(),
                    'year'             => (int) $row['year'],
                    'quarter'          => (int) $row['quarter'],
                    'month'            => (int) $row['month'],
                    'line_item'        => trim($row['line_item']),
                    'target_amount'    => (float) $row['target_amount'],
                    'obligated_amount' => (float) ($row['obligated_amount'] ?? 0),
                    'disbursed_amount' => (float) ($row['disbursed_amount'] ?? 0),
                ]);

                RunVerificationChecks::dispatch($entry);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                // Skip duplicate rows silently; DB unique index guards integrity.
            }
        }
    }

    public function rules(): array
    {
        return [
            'year'          => ['required', 'integer', 'min:2000', 'max:2099'],
            'quarter'       => ['required', 'integer', 'between:1,4'],
            'month'         => ['required', 'integer', 'between:1,12'],
            'line_item'     => ['required', 'string'],
            'target_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
