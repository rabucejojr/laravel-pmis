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
    .action-verified { color: #16a34a; font-weight: bold; }
    .action-flagged  { color: #d97706; font-weight: bold; }
    .action-rejected { color: #dc2626; font-weight: bold; }
    .footer { margin-top: 10px; font-size: 7px; color: #9ca3af; text-align: right; }
</style>
</head>
<body>
<div class="header">
    <h1>Verification Audit Log</h1>
    <p>DOST Surigao del Norte — Project Monitoring and Information System</p>
</div>
<div class="meta">
    Generated: {{ now()->format('F d, Y H:i') }}
    @if(!empty($filters['action']))    &nbsp;|&nbsp; Action: {{ ucfirst($filters['action']) }}      @endif
    @if(!empty($filters['date_from'])) &nbsp;|&nbsp; From: {{ $filters['date_from'] }}               @endif
    @if(!empty($filters['date_to']))   &nbsp;|&nbsp; To: {{ $filters['date_to'] }}                   @endif
    &nbsp;|&nbsp; Total records: {{ $rows->count() }}
</div>
<table>
    <thead>
        <tr>
            <th>Date &amp; Time</th>
            <th>Verifier</th>
            <th>Action</th>
            <th>Entry Type</th>
            <th>Program</th>
            <th>Project</th>
            <th>Entry / Indicator</th>
            <th>Period</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        @php
            $loggable = $row->loggable;
            $type     = $loggable ? class_basename($loggable) : '—';
            $label    = $loggable ? ($loggable->line_item ?? $loggable->indicator_name ?? '—') : '—';
            $period   = $loggable ? 'Q'.$loggable->quarter.' '.$loggable->year.'/Mo.'.$loggable->month : '—';
            $project  = $loggable?->project?->title ?? '—';
            $program  = $loggable?->project?->program?->code ?? '—';
            $typeLabel = $type === 'FinancialTarget' ? 'Financial' : ($type === 'PhysicalAccomplishment' ? 'Physical' : $type);
        @endphp
        <tr>
            <td style="white-space:nowrap;">{{ $row->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $row->verifier->name ?? '—' }}</td>
            <td class="action-{{ $row->action }}">{{ ucfirst($row->action) }}</td>
            <td>{{ $typeLabel }}</td>
            <td>{{ $program }}</td>
            <td>{{ $project }}</td>
            <td>{{ $label }}</td>
            <td style="white-space:nowrap;">{{ $period }}</td>
            <td>{{ $row->notes ?? '' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:16px;color:#9ca3af;">No records found for the selected filters.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">DOST-SDN PMIS &nbsp;|&nbsp; Confidential — for internal use only</div>
</body>
</html>
