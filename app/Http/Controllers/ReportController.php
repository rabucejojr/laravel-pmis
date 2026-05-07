<?php

namespace App\Http\Controllers;

use App\Exports\FinancialSummaryExport;
use App\Exports\PhysicalAccomplishmentExport;
use App\Exports\VerificationAuditExport;
use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\Program;
use App\Models\Project;
use App\Models\User;
use App\Models\VerificationLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $programs  = Program::where('is_active', true)->orderBy('code')->get();
        $projects  = Project::orderBy('title')->get();
        $verifiers = User::whereIn('role', ['admin', 'verifier'])->orderBy('name')->get();

        $availableYears = FinancialTarget::distinct()->pluck('year')
            ->merge(PhysicalAccomplishment::distinct()->pluck('year'))
            ->unique()->sort()->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int) date('Y')]);
        }

        return view('reports.index', compact('programs', 'projects', 'verifiers', 'availableYears'));
    }

    // ── Financial ────────────────────────────────────────────────────────────

    public function financialExcel(Request $request)
    {
        $filters  = $this->financialFilters($request);
        $filename = 'financial-summary-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new FinancialSummaryExport($filters), $filename);
    }

    public function financialPdf(Request $request)
    {
        $filters = $this->financialFilters($request);
        $rows    = (new FinancialSummaryExport($filters))->collection();

        $pdf = Pdf::loadView('reports.financial-pdf', ['rows' => $rows, 'filters' => $filters])
            ->setPaper('a4', 'landscape');

        return $pdf->download('financial-summary-' . now()->format('Ymd_His') . '.pdf');
    }

    // ── Physical ─────────────────────────────────────────────────────────────

    public function physicalExcel(Request $request)
    {
        $filters  = $this->physicalFilters($request);
        $filename = 'physical-accomplishments-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PhysicalAccomplishmentExport($filters), $filename);
    }

    public function physicalPdf(Request $request)
    {
        $filters = $this->physicalFilters($request);
        $rows    = (new PhysicalAccomplishmentExport($filters))->collection();

        $pdf = Pdf::loadView('reports.physical-pdf', ['rows' => $rows, 'filters' => $filters])
            ->setPaper('a4', 'landscape');

        return $pdf->download('physical-accomplishments-' . now()->format('Ymd_His') . '.pdf');
    }

    // ── Audit ─────────────────────────────────────────────────────────────────

    public function auditExcel(Request $request)
    {
        $filters  = $this->auditFilters($request);
        $filename = 'verification-audit-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new VerificationAuditExport($filters), $filename);
    }

    public function auditPdf(Request $request)
    {
        $filters = $this->auditFilters($request);
        $rows    = (new VerificationAuditExport($filters))->collection();

        $pdf = Pdf::loadView('reports.audit-pdf', ['rows' => $rows, 'filters' => $filters])
            ->setPaper('a4', 'landscape');

        return $pdf->download('verification-audit-' . now()->format('Ymd_His') . '.pdf');
    }

    // ── Filter helpers ───────────────────────────────────────────────────────

    private function financialFilters(Request $request): array
    {
        return array_filter([
            'program'      => $request->input('program'),
            'project_id'   => $request->input('project_id') ?: null,
            'year'         => $request->input('year') ?: null,
            'quarter_from' => $request->input('quarter_from') ?: null,
            'quarter_to'   => $request->input('quarter_to') ?: null,
            'month_from'   => $request->input('month_from') ?: null,
            'month_to'     => $request->input('month_to') ?: null,
        ]);
    }

    private function physicalFilters(Request $request): array
    {
        return $this->financialFilters($request);
    }

    private function auditFilters(Request $request): array
    {
        return array_filter([
            'verifier_id' => $request->input('verifier_id') ?: null,
            'action'      => $request->input('action'),
            'date_from'   => $request->input('date_from'),
            'date_to'     => $request->input('date_to'),
        ]);
    }
}
