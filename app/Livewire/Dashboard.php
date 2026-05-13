<?php

namespace App\Livewire;

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\Program;
use App\Models\Project;
use App\Models\SetupAccomplishment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public string $filterYear    = '';
    public string $filterProgram = '';

    public function mount(): void
    {
        $this->filterYear = (string) date('Y');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function year(): int
    {
        return $this->filterYear !== '' ? (int) $this->filterYear : (int) date('Y');
    }

    private function programScope($query)
    {
        return $query->when(
            $this->filterProgram !== '',
            fn ($q) => $q->whereHas('project.program', fn ($q2) => $q2->where('code', $this->filterProgram))
        );
    }

    private function projectProgramScope($query)
    {
        return $query->when(
            $this->filterProgram !== '',
            fn ($q) => $q->whereHas('program', fn ($q2) => $q2->where('code', $this->filterProgram))
        );
    }

    // ── Summary card counts ──────────────────────────────────────────────────

    private function summaryCards(): array
    {
        $projectQuery = Project::query();
        $this->projectProgramScope($projectQuery);
        $totalProjects = $projectQuery->count();

        $pendingFT = FinancialTarget::where('verified_status', 'pending');
        $pendingPA = PhysicalAccomplishment::where('verified_status', 'pending');
        $this->programScope($pendingFT);
        $this->programScope($pendingPA);
        $pending = $pendingFT->count() + $pendingPA->count();

        $verifiedFT = FinancialTarget::where('verified_status', 'verified');
        $verifiedPA = PhysicalAccomplishment::where('verified_status', 'verified');
        $this->programScope($verifiedFT);
        $this->programScope($verifiedPA);
        $verified = $verifiedFT->count() + $verifiedPA->count();

        $flaggedFT = FinancialTarget::whereIn('verified_status', ['flagged', 'rejected']);
        $flaggedPA = PhysicalAccomplishment::whereIn('verified_status', ['flagged', 'rejected']);
        $this->programScope($flaggedFT);
        $this->programScope($flaggedPA);
        $flaggedRejected = $flaggedFT->count() + $flaggedPA->count();

        return compact('totalProjects', 'pending', 'verified', 'flaggedRejected');
    }

    // ── Chart 1: Financial target vs obligated vs disbursed (Stacked Bar) ───

    private function financialChartOptions(): array
    {
        $year = $this->year();

        $rows = FinancialTarget::query()
            ->select(
                'quarter',
                DB::raw('SUM(target_amount) as total_target'),
                DB::raw('SUM(obligated_amount) as total_obligated'),
                DB::raw('SUM(disbursed_amount) as total_disbursed'),
            )
            ->where('verified_status', 'verified')
            ->where('year', $year)
            ->when(
                $this->filterProgram !== '',
                fn ($q) => $q->whereHas('project.program', fn ($q2) => $q2->where('code', $this->filterProgram))
            )
            ->groupBy('quarter')
            ->orderBy('quarter')
            ->get()
            ->keyBy('quarter');

        $quarters = [1, 2, 3, 4];
        $target    = array_map(fn ($q) => round((float) ($rows[$q]->total_target    ?? 0), 2), $quarters);
        $obligated = array_map(fn ($q) => round((float) ($rows[$q]->total_obligated ?? 0), 2), $quarters);
        $disbursed = array_map(fn ($q) => round((float) ($rows[$q]->total_disbursed ?? 0), 2), $quarters);

        return [
            'chart'       => ['type' => 'bar', 'stacked' => true, 'height' => 300, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'      => [
                ['name' => 'Target',    'data' => array_values($target)],
                ['name' => 'Obligated', 'data' => array_values($obligated)],
                ['name' => 'Disbursed', 'data' => array_values($disbursed)],
            ],
            'xaxis'       => ['categories' => ['Q1', 'Q2', 'Q3', 'Q4']],
            'colors'      => ['#003087', '#FDB913', '#16a34a'],
            'plotOptions' => ['bar' => ['horizontal' => false, 'borderRadius' => 4]],
            'dataLabels'  => ['enabled' => false],
            'legend'      => ['position' => 'top', 'fontSize' => '12px'],
            'yaxis'       => ['labels' => ['formatter' => 'function(v){return "₱"+v.toLocaleString()}']],
            'tooltip'     => ['y' => ['formatter' => 'function(v){return "₱"+v.toLocaleString()}']],
            'grid'        => ['borderColor' => '#f0f0f0'],
        ];
    }

    // ── Chart 2: Physical accomplishment rate trend (Line) ───────────────────

    private function accomplishmentChartOptions(): array
    {
        $year = $this->year();

        $rows = PhysicalAccomplishment::query()
            ->select('month', DB::raw('AVG(accomplishment_rate) as avg_rate'))
            ->where('verified_status', 'verified')
            ->where('year', $year)
            ->whereNotNull('accomplishment_rate')
            ->when(
                $this->filterProgram !== '',
                fn ($q) => $q->whereHas('project.program', fn ($q2) => $q2->where('code', $this->filterProgram))
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $rates = array_map(fn ($m) => round((float) ($rows[$m]->avg_rate ?? 0), 2), range(1, 12));

        return [
            'chart'       => ['type' => 'line', 'height' => 300, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'      => [['name' => 'Avg Accomplishment Rate (%)', 'data' => array_values($rates)]],
            'xaxis'       => ['categories' => $monthLabels],
            'colors'      => ['#003087'],
            'stroke'      => ['curve' => 'smooth', 'width' => 3],
            'markers'     => ['size' => 4],
            'dataLabels'  => ['enabled' => false],
            'yaxis'       => ['labels' => ['formatter' => 'function(v){return v+"%"}'], 'min' => 0],
            'tooltip'     => ['y' => ['formatter' => 'function(v){return v+"%"}']],
            'annotations' => [
                'yaxis' => [[
                    'y'           => 100,
                    'borderColor' => '#16a34a',
                    'label'       => ['text' => '100%', 'style' => ['color' => '#16a34a']],
                ]],
            ],
            'grid'        => ['borderColor' => '#f0f0f0'],
        ];
    }

    // ── Chart 3: Project status distribution (Donut) ─────────────────────────

    private function statusChartOptions(): array
    {
        $rows = Project::query()
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->when(
                $this->filterProgram !== '',
                fn ($q) => $q->whereHas('program', fn ($q2) => $q2->where('code', $this->filterProgram))
            )
            ->groupBy('status')
            ->get()
            ->pluck('cnt', 'status');

        $statuses = ['active', 'liquidated', 'unliquidated'];
        $counts   = array_map(fn ($s) => (int) ($rows[$s] ?? 0), $statuses);
        $labels   = array_map('ucfirst', $statuses);

        return [
            'chart'     => ['type' => 'donut', 'height' => 300, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'    => array_values($counts),
            'labels'    => $labels,
            'colors'    => ['#16a34a', '#003087', '#d97706'],
            'legend'    => ['position' => 'bottom', 'fontSize' => '12px'],
            'dataLabels'=> ['enabled' => true, 'formatter' => 'function(val,opts){return opts.w.globals.series[opts.seriesIndex]}'],
            'plotOptions'=> ['pie' => ['donut' => ['size' => '65%']]],
            'tooltip'   => ['y' => ['formatter' => 'function(v){return v+" projects"}']],
        ];
    }

    // ── Chart 4: Top 10 projects by accomplishment rate (Horizontal Bar) ─────

    private function topProjectsChartOptions(): array
    {
        $year = $this->year();

        $rows = PhysicalAccomplishment::query()
            ->select('project_id', DB::raw('AVG(accomplishment_rate) as avg_rate'))
            ->where('verified_status', 'verified')
            ->where('year', $year)
            ->whereNotNull('accomplishment_rate')
            ->when(
                $this->filterProgram !== '',
                fn ($q) => $q->whereHas('project.program', fn ($q2) => $q2->where('code', $this->filterProgram))
            )
            ->groupBy('project_id')
            ->orderByDesc('avg_rate')
            ->limit(10)
            ->get();

        if ($rows->isEmpty()) {
            return [
                'chart'  => ['type' => 'bar', 'height' => 300, 'width' => '100%', 'toolbar' => ['show' => false]],
                'series' => [['name' => 'Avg Rate (%)', 'data' => []]],
                'xaxis'  => ['categories' => []],
                'noData' => ['text' => 'No verified data for this period'],
            ];
        }

        $projectIds = $rows->pluck('project_id');
        $titles     = Project::whereIn('id', $projectIds)->pluck('title', 'id');

        $names  = $rows->map(fn ($r) => Str::limit($titles[$r->project_id] ?? 'Unknown', 28))->toArray();
        $rates  = $rows->map(fn ($r) => round((float) $r->avg_rate, 2))->toArray();
        $height = max(300, count($rows) * 38 + 60);

        return [
            'chart'       => [
                'type'               => 'bar',
                'height'             => $height,
                'width'              => '100%',
                'toolbar'            => ['show' => false],
                'parentHeightOffset' => 0,
            ],
            'series'      => [['name' => 'Avg Rate (%)', 'data' => array_values($rates)]],
            'xaxis'       => ['categories' => array_values($names), 'max' => 150],
            'colors'      => ['#003087'],
            'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4, 'barHeight' => '55%']],
            'dataLabels'  => ['enabled' => true, 'formatter' => 'function(v){return v+"%"}'],
            'yaxis'       => [
                'labels' => [
                    'style'    => ['fontSize' => '11px'],
                    'maxWidth' => 160,
                ],
            ],
            'tooltip'     => ['x' => ['show' => true], 'y' => ['formatter' => 'function(v){return v+"%"}']],
            'grid'        => ['borderColor' => '#f0f0f0', 'padding' => ['right' => 16]],
        ];
    }

    // ── Project accomplishment summaries (table data) ────────────────────────

    private function programProjectSummaries(string $code): array
    {
        $year       = $this->year();
        $projects   = Project::whereHas('program', fn ($q) => $q->where('code', $code))
                        ->orderBy('title')->get(['id', 'title', 'status']);

        if ($projects->isEmpty()) return [];

        $ids = $projects->pluck('id');

        $ftAgg = FinancialTarget::whereIn('project_id', $ids)
            ->where('year', $year)->where('verified_status', 'verified')
            ->groupBy('project_id')
            ->selectRaw('project_id, SUM(target_amount) as t, SUM(disbursed_amount) as d')
            ->get()->keyBy('project_id');

        $paAgg = PhysicalAccomplishment::whereIn('project_id', $ids)
            ->where('year', $year)->where('verified_status', 'verified')
            ->groupBy('project_id')
            ->selectRaw('project_id, SUM(target_value) as t, SUM(accomplished_value) as a')
            ->get()->keyBy('project_id');

        $pendingFT = FinancialTarget::whereIn('project_id', $ids)
            ->whereIn('verified_status', ['pending', 'flagged'])
            ->groupBy('project_id')->selectRaw('project_id, COUNT(*) as cnt')
            ->pluck('cnt', 'project_id');

        $pendingPA = PhysicalAccomplishment::whereIn('project_id', $ids)
            ->whereIn('verified_status', ['pending', 'flagged'])
            ->groupBy('project_id')->selectRaw('project_id, COUNT(*) as cnt')
            ->pluck('cnt', 'project_id');

        return $projects->map(function ($p) use ($ftAgg, $paAgg, $pendingFT, $pendingPA) {
            $ft = $ftAgg[$p->id]  ?? null;
            $pa = $paAgg[$p->id]  ?? null;

            $ftT  = (float) ($ft->t ?? 0);
            $ftD  = (float) ($ft->d ?? 0);
            $paT  = (float) ($pa->t ?? 0);
            $paA  = (float) ($pa->a ?? 0);

            return [
                'title'          => $p->title,
                'status'         => $p->status,
                'ft_target'      => $ftT,
                'ft_disbursed'   => $ftD,
                'ft_rate'        => $ftT > 0 ? min(round($ftD / $ftT * 100, 1), 999.9) : null,
                'pa_target'      => $paT,
                'pa_accomplished'=> $paA,
                'pa_rate'        => $paT > 0 ? min(round($paA / $paT * 100, 1), 999.9) : null,
                'pending'        => ((int) ($pendingFT[$p->id] ?? 0)) + ((int) ($pendingPA[$p->id] ?? 0)),
            ];
        })->toArray();
    }

    private function setupProjectSummaries(): array
    {
        $year     = $this->year();
        $projects = Project::whereHas('program', fn ($q) => $q->where('code', 'SETUP'))
                      ->orderBy('title')->get(['id', 'title', 'status']);

        if ($projects->isEmpty()) return [];

        $saRows = SetupAccomplishment::whereIn('project_id', $projects->pluck('id'))
            ->where('year', $year)->get()->keyBy('project_id');

        return $projects->map(function ($p) use ($saRows) {
            $sa = $saRows[$p->id] ?? null;
            return [
                'title'               => $p->title,
                'status'              => $p->status,
                'record_status'       => $sa?->verified_status,
                'target_num_projects' => $sa?->target_num_projects,
                'actual_num_projects' => $sa?->actual_num_projects,
                'target_ifund_amount' => $sa?->target_ifund_amount,
                'actual_ifund_amount' => $sa?->actual_ifund_amount,
                'target_gross_sales'  => $sa?->target_gross_sales,
                'actual_gross_sales'  => $sa?->actual_gross_sales,
                'target_employment'   => $sa?->target_employment,
                'actual_employment'   => $sa?->actual_employment,
                'target_trainings'    => $sa?->target_trainings,
                'actual_trainings'    => $sa?->actual_trainings,
            ];
        })->toArray();
    }

    // ── Chart 5: Per-program financial accomplishment (Horizontal Bar) ──────

    private function programFinancialChartOptions(string $code): array
    {
        $year = $this->year();

        $rows = FinancialTarget::query()
            ->select('project_id',
                DB::raw('SUM(target_amount) as total_target'),
                DB::raw('SUM(disbursed_amount) as total_disbursed'),
            )
            ->where('verified_status', 'verified')
            ->where('year', $year)
            ->whereHas('project.program', fn ($q) => $q->where('code', $code))
            ->groupBy('project_id')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'chart'  => ['type' => 'bar', 'height' => 200, 'width' => '100%', 'toolbar' => ['show' => false]],
                'series' => [['name' => 'Target Amount', 'data' => []], ['name' => 'Disbursed Amount', 'data' => []]],
                'xaxis'  => ['categories' => []],
                'noData' => ['text' => "No verified financial data for {$year}"],
            ];
        }

        $projectIds = $rows->pluck('project_id');
        $titles     = Project::whereIn('id', $projectIds)->pluck('title', 'id');

        $names     = $rows->map(fn ($r) => Str::limit($titles[$r->project_id] ?? 'Unknown', 30))->toArray();
        $targets   = $rows->map(fn ($r) => round((float) $r->total_target, 2))->toArray();
        $disbursed = $rows->map(fn ($r) => round((float) $r->total_disbursed, 2))->toArray();
        $height    = max(200, count($rows) * 45 + 80);

        return [
            'chart'       => ['type' => 'bar', 'height' => $height, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'      => [
                ['name' => 'Target Amount',   'data' => array_values($targets)],
                ['name' => 'Disbursed Amount', 'data' => array_values($disbursed)],
            ],
            'xaxis'       => ['categories' => array_values($names)],
            'colors'      => ['#003087', '#FDB913'],
            'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4, 'barHeight' => '60%']],
            'dataLabels'  => ['enabled' => false],
            'legend'      => ['position' => 'top', 'fontSize' => '12px'],
            'yaxis'       => ['labels' => ['style' => ['fontSize' => '11px'], 'maxWidth' => 160]],
            'tooltip'     => ['y' => ['formatter' => 'function(v){return "₱"+v.toLocaleString()}']],
            'grid'        => ['borderColor' => '#f0f0f0'],
        ];
    }

    // ── Chart 6: Per-program physical accomplishment (Horizontal Bar) ────────

    private function programPhysicalChartOptions(string $code): array
    {
        $year = $this->year();

        $rows = PhysicalAccomplishment::query()
            ->select('project_id',
                DB::raw('SUM(target_value) as total_target'),
                DB::raw('SUM(accomplished_value) as total_accomplished'),
            )
            ->where('verified_status', 'verified')
            ->where('year', $year)
            ->whereHas('project.program', fn ($q) => $q->where('code', $code))
            ->groupBy('project_id')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'chart'  => ['type' => 'bar', 'height' => 200, 'width' => '100%', 'toolbar' => ['show' => false]],
                'series' => [['name' => 'Target', 'data' => []], ['name' => 'Accomplished', 'data' => []]],
                'xaxis'  => ['categories' => []],
                'noData' => ['text' => "No verified physical data for {$year}"],
            ];
        }

        $projectIds   = $rows->pluck('project_id');
        $titles       = Project::whereIn('id', $projectIds)->pluck('title', 'id');

        $names        = $rows->map(fn ($r) => Str::limit($titles[$r->project_id] ?? 'Unknown', 30))->toArray();
        $targets      = $rows->map(fn ($r) => round((float) $r->total_target, 2))->toArray();
        $accomplished = $rows->map(fn ($r) => round((float) $r->total_accomplished, 2))->toArray();
        $height       = max(200, count($rows) * 45 + 80);

        return [
            'chart'       => ['type' => 'bar', 'height' => $height, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'      => [
                ['name' => 'Target',       'data' => array_values($targets)],
                ['name' => 'Accomplished', 'data' => array_values($accomplished)],
            ],
            'xaxis'       => ['categories' => array_values($names)],
            'colors'      => ['#003087', '#16a34a'],
            'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4, 'barHeight' => '60%']],
            'dataLabels'  => ['enabled' => false],
            'legend'      => ['position' => 'top', 'fontSize' => '12px'],
            'yaxis'       => ['labels' => ['style' => ['fontSize' => '11px'], 'maxWidth' => 160]],
            'tooltip'     => ['y' => ['formatter' => 'function(v){return v.toLocaleString()}']],
            'grid'        => ['borderColor' => '#f0f0f0'],
        ];
    }

    // ── Chart 7: SETUP KPI target vs actual (split sub-charts) ──────────────

    private function setupKpiChartOptions(): ?array
    {
        $year = $this->year();

        $totals = SetupAccomplishment::verified()
            ->where('year', $year)
            ->selectRaw('
                SUM(target_num_projects)  as t_projects,
                SUM(actual_num_projects)  as a_projects,
                SUM(target_ifund_amount)  as t_ifund,
                SUM(actual_ifund_amount)  as a_ifund,
                SUM(target_gross_sales)   as t_sales,
                SUM(actual_gross_sales)   as a_sales,
                SUM(target_employment)    as t_employment,
                SUM(actual_employment)    as a_employment,
                SUM(target_trainings)     as t_trainings,
                SUM(actual_trainings)     as a_trainings
            ')
            ->first();

        if (! $totals || (int) $totals->t_projects === 0 && (float) $totals->t_ifund === 0.0) {
            return null;
        }

        $financial = [
            'chart'       => ['type' => 'bar', 'height' => 180, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'      => [
                ['name' => 'Target',  'data' => [round((float) $totals->t_ifund, 2), round((float) $totals->t_sales, 2)]],
                ['name' => 'Actual',  'data' => [round((float) $totals->a_ifund, 2), round((float) $totals->a_sales, 2)]],
            ],
            'xaxis'       => ['categories' => ['iFUND Amount', 'Gross Sales']],
            'colors'      => ['#003087', '#FDB913'],
            'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4, 'barHeight' => '60%']],
            'dataLabels'  => ['enabled' => false],
            'legend'      => ['position' => 'top', 'fontSize' => '12px'],
            'yaxis'       => ['labels' => ['style' => ['fontSize' => '11px']]],
            'tooltip'     => ['y' => ['formatter' => 'function(v){return "₱"+v.toLocaleString()}']],
            'grid'        => ['borderColor' => '#f0f0f0'],
        ];

        $activity = [
            'chart'       => ['type' => 'bar', 'height' => 220, 'width' => '100%', 'toolbar' => ['show' => false]],
            'series'      => [
                ['name' => 'Target', 'data' => [(int) $totals->t_projects, (int) $totals->t_employment, (int) $totals->t_trainings]],
                ['name' => 'Actual', 'data' => [(int) $totals->a_projects, (int) $totals->a_employment, (int) $totals->a_trainings]],
            ],
            'xaxis'       => ['categories' => ['No. of Projects', 'Employment Generated', 'Trainings Conducted']],
            'colors'      => ['#003087', '#16a34a'],
            'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4, 'barHeight' => '60%']],
            'dataLabels'  => ['enabled' => false],
            'legend'      => ['position' => 'top', 'fontSize' => '12px'],
            'yaxis'       => ['labels' => ['style' => ['fontSize' => '11px']]],
            'tooltip'     => ['y' => ['formatter' => 'function(v){return v.toLocaleString()}']],
            'grid'        => ['borderColor' => '#f0f0f0'],
        ];

        return compact('financial', 'activity');
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $summary          = $this->summaryCards();
        $financialChart   = $this->financialChartOptions();
        $accomplishChart  = $this->accomplishmentChartOptions();
        $statusChart      = $this->statusChartOptions();
        $topProjectsChart = $this->topProjectsChartOptions();

        $programs       = Program::where('is_active', true)->orderBy('code')->get();
        $availableYears = $this->availableYears();

        $programCodes = $this->filterProgram !== ''
            ? [$this->filterProgram]
            : $programs->pluck('code')->all();

        $programFinancialCharts  = [];
        $programPhysicalCharts   = [];
        $programProjectSummaries = [];
        $setupKpiChart           = null;
        $setupProjectSummaries   = [];

        foreach ($programCodes as $code) {
            if ($code !== 'SETUP') {
                $programFinancialCharts[$code]  = $this->programFinancialChartOptions($code);
                $programPhysicalCharts[$code]   = $this->programPhysicalChartOptions($code);
                $programProjectSummaries[$code] = $this->programProjectSummaries($code);
            } else {
                $setupKpiChart         = $this->setupKpiChartOptions();
                $setupProjectSummaries = $this->setupProjectSummaries();
            }
        }

        return view('livewire.dashboard', [
            'summary'                => $summary,
            'financialChart'         => $financialChart,
            'accomplishChart'        => $accomplishChart,
            'statusChart'            => $statusChart,
            'topProjectsChart'       => $topProjectsChart,
            'programs'               => $programs,
            'availableYears'         => $availableYears,
            'programCodes'           => $programCodes,
            'programFinancialCharts' => $programFinancialCharts,
            'programPhysicalCharts'  => $programPhysicalCharts,
            'setupKpiChart'          => $setupKpiChart,
            'programProjectSummaries'=> $programProjectSummaries,
            'setupProjectSummaries'  => $setupProjectSummaries,
        ]);
    }

    private function availableYears(): \Illuminate\Support\Collection
    {
        $ftYears = FinancialTarget::distinct()->orderBy('year')->pluck('year');
        $paYears = PhysicalAccomplishment::distinct()->orderBy('year')->pluck('year');
        $saYears = SetupAccomplishment::distinct()->orderBy('year')->pluck('year');

        $years = $ftYears->merge($paYears)->merge($saYears)->unique()->sort()->values();

        // Always include current year even if no data yet
        $currentYear = (int) date('Y');
        if (! $years->contains($currentYear)) {
            $years->push($currentYear);
            $years = $years->sort()->values();
        }

        return $years;
    }
}
