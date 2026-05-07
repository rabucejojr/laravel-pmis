<?php

namespace App\Imports;

use App\Jobs\RunVerificationChecks;
use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PhysicalAccomplishmentImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function __construct(private Project $project) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            try {
                $entry = PhysicalAccomplishment::create([
                    'project_id'         => $this->project->id,
                    'encoded_by'         => Auth::id(),
                    'year'               => (int) $row['year'],
                    'quarter'            => (int) $row['quarter'],
                    'month'              => (int) $row['month'],
                    'indicator_name'     => trim($row['indicator_name']),
                    'target_value'       => (float) $row['target_value'],
                    'accomplished_value' => (float) ($row['accomplished_value'] ?? 0),
                    // accomplishment_rate is a MySQL generated column — never set here
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
            'year'           => ['required', 'integer', 'min:2000', 'max:2099'],
            'quarter'        => ['required', 'integer', 'between:1,4'],
            'month'          => ['required', 'integer', 'between:1,12'],
            'indicator_name' => ['required', 'string'],
            'target_value'   => ['required', 'numeric', 'min:0'],
        ];
    }
}
