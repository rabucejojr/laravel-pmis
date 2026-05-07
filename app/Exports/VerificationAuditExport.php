<?php

namespace App\Exports;

use App\Models\VerificationLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VerificationAuditExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(private readonly array $filters) {}

    public function collection()
    {
        return VerificationLog::with(['verifier', 'loggable.project.program'])
            ->when($this->filters['verifier_id'] ?? null, fn ($q, $v) =>
                $q->where('verifier_id', $v)
            )
            ->when($this->filters['action'] ?? null, fn ($q, $v) =>
                $q->where('action', $v)
            )
            ->when($this->filters['date_from'] ?? null, fn ($q, $v) =>
                $q->whereDate('created_at', '>=', $v)
            )
            ->when($this->filters['date_to'] ?? null, fn ($q, $v) =>
                $q->whereDate('created_at', '<=', $v)
            )
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date', 'Verifier', 'Action', 'Entry Type',
            'Program', 'Project', 'Entry Label', 'Period', 'Notes',
        ];
    }

    public function map($row): array
    {
        $loggable = $row->loggable;
        $type     = $loggable ? class_basename($loggable) : '—';

        $label  = '—';
        $period = '—';
        $project = '—';
        $program = '—';

        if ($loggable) {
            $label   = $loggable->line_item ?? $loggable->indicator_name ?? '—';
            $period  = 'Q' . $loggable->quarter . ' ' . $loggable->year . ' / Mo.' . $loggable->month;
            $project = $loggable->project->title ?? '—';
            $program = $loggable->project->program->code ?? '—';
        }

        return [
            $row->created_at->format('Y-m-d H:i'),
            $row->verifier->name ?? '—',
            ucfirst($row->action),
            $type === 'FinancialTarget' ? 'Financial Target' : ($type === 'PhysicalAccomplishment' ? 'Physical Accomplishment' : $type),
            $program,
            $project,
            $label,
            $period,
            $row->notes ?? '',
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
        return 'Verification Audit';
    }
}
