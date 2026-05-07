<?php

namespace App\Exports;

use App\Models\PhysicalAccomplishment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PhysicalAccomplishmentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(private readonly array $filters) {}

    public function collection()
    {
        return PhysicalAccomplishment::with(['project.program', 'encoder'])
            ->where('verified_status', 'verified')
            ->when($this->filters['program'] ?? null, fn ($q, $v) =>
                $q->whereHas('project.program', fn ($q2) => $q2->where('code', $v))
            )
            ->when($this->filters['project_id'] ?? null, fn ($q, $v) =>
                $q->where('project_id', $v)
            )
            ->when($this->filters['year'] ?? null, fn ($q, $v) =>
                $q->where('year', $v)
            )
            ->when($this->filters['quarter_from'] ?? null, fn ($q, $v) =>
                $q->where('quarter', '>=', $v)
            )
            ->when($this->filters['quarter_to'] ?? null, fn ($q, $v) =>
                $q->where('quarter', '<=', $v)
            )
            ->when($this->filters['month_from'] ?? null, fn ($q, $v) =>
                $q->where('month', '>=', $v)
            )
            ->when($this->filters['month_to'] ?? null, fn ($q, $v) =>
                $q->where('month', '<=', $v)
            )
            ->orderBy('year')->orderBy('quarter')->orderBy('month')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Program', 'Project', 'Year', 'Quarter', 'Month',
            'Indicator Name', 'Target Value', 'Accomplished Value',
            'Accomplishment Rate (%)', 'Encoded By',
        ];
    }

    public function map($row): array
    {
        return [
            $row->project->program->code ?? '—',
            $row->project->title,
            $row->year,
            'Q' . $row->quarter,
            $row->month,
            $row->indicator_name,
            number_format((float) $row->target_value, 2),
            number_format((float) $row->accomplished_value, 2),
            $row->accomplishment_rate !== null ? number_format((float) $row->accomplishment_rate, 2) . '%' : '—',
            $row->encoder->name ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '003087'],
            ], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Physical Accomplishments';
    }
}
