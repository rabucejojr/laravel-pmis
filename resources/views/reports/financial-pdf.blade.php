<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1A1A2E; background: #fff; }
    .header { background-color: #003087; color: #ffffff; padding: 10px 14px; margin-bottom: 12px; }
    .header h1 { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
    .header p  { font-size: 8px; color: rgba(255,255,255,0.75); }
    .meta { font-size: 8px; color: #666; margin-bottom: 8px; padding: 0 2px; }
    table { width: 100%; border-collapse: collapse; }
    th {
        background-color: #003087;
        color: #ffffff;
        font-size: 8px;
        font-weight: bold;
        padding: 5px 6px;
        text-align: left;
        white-space: nowrap;
    }
    td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8px; vertical-align: top; }
    tr:nth-child(even) td { background-color: #f8f9fb; }
    .num { text-align: right; font-family: monospace; }
    .footer { margin-top: 10px; font-size: 7px; color: #9ca3af; text-align: right; }
</style>
</head>
<body>
<div class="header">
    <h1>Financial Summary Report</h1>
    <p>DOST Surigao del Norte — Project Monitoring and Information System &nbsp;|&nbsp; Verified Entries Only</p>
</div>
<div class="meta">
    Generated: {{ now()->format('F d, Y H:i') }}
    @if(!empty($filters['program'])) &nbsp;|&nbsp; Program: {{ $filters['program'] }} @endif
    @if(!empty($filters['year']))    &nbsp;|&nbsp; Year: {{ $filters['year'] }}         @endif
    &nbsp;|&nbsp; Total records: {{ $rows->count() }}
</div>
<table>
    <thead>
        <tr>
            <th>Program</th>
            <th>Project</th>
            <th>Year</th>
            <th>Qtr</th>
            <th>Mo.</th>
            <th>Line Item</th>
            <th class="num">Target (₱)</th>
            <th class="num">Obligated (₱)</th>
            <th class="num">Disbursed (₱)</th>
            <th>Encoded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        <tr>
            <td>{{ $row->project->program->code ?? '—' }}</td>
            <td>{{ $row->project->title }}</td>
            <td>{{ $row->year }}</td>
            <td>Q{{ $row->quarter }}</td>
            <td>{{ $row->month }}</td>
            <td>{{ $row->line_item }}</td>
            <td class="num">{{ number_format((float)$row->target_amount, 2) }}</td>
            <td class="num">{{ number_format((float)$row->obligated_amount, 2) }}</td>
            <td class="num">{{ number_format((float)$row->disbursed_amount, 2) }}</td>
            <td>{{ $row->encoder->name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:16px;color:#9ca3af;">No records found for the selected filters.</td></tr>
        @endforelse
    </tbody>
</table>
@if($rows->isNotEmpty())
<table style="margin-top:8px;width:auto;margin-left:auto;">
    <tr>
        <th style="background:#003087;color:#fff;padding:4px 8px;">Total Target (₱)</th>
        <th style="background:#003087;color:#fff;padding:4px 8px;">Total Obligated (₱)</th>
        <th style="background:#003087;color:#fff;padding:4px 8px;">Total Disbursed (₱)</th>
    </tr>
    <tr>
        <td class="num" style="padding:4px 8px;border:1px solid #e5e7eb;">{{ number_format($rows->sum(fn($r) => (float)$r->target_amount), 2) }}</td>
        <td class="num" style="padding:4px 8px;border:1px solid #e5e7eb;">{{ number_format($rows->sum(fn($r) => (float)$r->obligated_amount), 2) }}</td>
        <td class="num" style="padding:4px 8px;border:1px solid #e5e7eb;">{{ number_format($rows->sum(fn($r) => (float)$r->disbursed_amount), 2) }}</td>
    </tr>
</table>
@endif
<div class="footer">DOST-SDN PMIS &nbsp;|&nbsp; Confidential — for internal use only</div>
</body>
</html>
