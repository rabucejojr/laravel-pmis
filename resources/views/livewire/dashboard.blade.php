<div>
    {{-- ── Filters ────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <select wire:model.live="filterYear"
                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
            @foreach($availableYears as $yr)
                <option value="{{ $yr }}" {{ $filterYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterProgram"
                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
            <option value="">All Programs</option>
            @foreach($programs as $prog)
                <option value="{{ $prog->code }}" {{ $filterProgram === $prog->code ? 'selected' : '' }}>
                    {{ $prog->code }} — {{ $prog->name }}
                </option>
            @endforeach
        </select>

        <div wire:loading class="flex items-center text-xs text-gray-400 gap-1.5">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Updating…
        </div>
    </div>

    {{-- ── Summary Cards ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Projects</p>
            <p class="text-3xl font-bold mt-1 text-primary">{{ $summary['totalProjects'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pending Verification</p>
            <p class="text-3xl font-bold mt-1 text-amber-500">{{ $summary['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Verified Entries</p>
            <p class="text-3xl font-bold mt-1 text-green-600">{{ $summary['verified'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Flagged / Rejected</p>
            <p class="text-3xl font-bold mt-1 text-red-500">{{ $summary['flaggedRejected'] }}</p>
        </div>
    </div>

    {{-- ── Row 1: Financial Stacked Bar + Accomplishment Rate Line ───────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- Chart 1: Financial target vs obligated vs disbursed --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                Financial: Target vs Obligated vs Disbursed
            </p>
            <p class="text-xs text-gray-400 mb-3">{{ $filterYear }} · Verified entries</p>

            <div wire:key="chart-financial-{{ $filterYear }}-{{ $filterProgram }}"
                 wire:ignore
                 class="w-full"
                 x-data="{
                     init() {
                         const opts = @js($financialChart);
                         const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                         if (opts.yaxis?.labels) opts.yaxis.labels.formatter = deser(opts.yaxis.labels.formatter);
                         if (opts.tooltip?.y)    opts.tooltip.y.formatter    = deser(opts.tooltip.y.formatter);
                         this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                     }
                 }"
            >
                <div x-ref="chart"></div>
            </div>
        </div>

        {{-- Chart 2: Physical accomplishment rate trend --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                Physical Accomplishment Rate Trend
            </p>
            <p class="text-xs text-gray-400 mb-3">{{ $filterYear }} · Monthly average · Verified entries</p>

            <div wire:key="chart-accomplishment-{{ $filterYear }}-{{ $filterProgram }}"
                 wire:ignore
                 class="w-full"
                 x-data="{
                     init() {
                         const opts = @js($accomplishChart);
                         const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                         if (opts.yaxis?.labels) opts.yaxis.labels.formatter = deser(opts.yaxis.labels.formatter);
                         if (opts.tooltip?.y)    opts.tooltip.y.formatter    = deser(opts.tooltip.y.formatter);
                         this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                     }
                 }"
            >
                <div x-ref="chart"></div>
            </div>
        </div>

    </div>

    {{-- ── Row 2: Status Donut + Top 10 Horizontal Bar ──────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Chart 3: Project status distribution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                Project Status Distribution
            </p>
            <p class="text-xs text-gray-400 mb-3">All projects{{ $filterProgram ? ' · '.$filterProgram : '' }}</p>

            <div wire:key="chart-status-{{ $filterProgram }}"
                 wire:ignore
                 class="w-full"
                 x-data="{
                     init() {
                         const opts = @js($statusChart);
                         const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                         if (opts.dataLabels) opts.dataLabels.formatter = typeof opts.dataLabels.formatter === 'string'
                             ? new Function('val', 'opts', opts.dataLabels.formatter.slice(opts.dataLabels.formatter.indexOf('{')+1, opts.dataLabels.formatter.lastIndexOf('}')))
                             : opts.dataLabels.formatter;
                         if (opts.tooltip?.y) opts.tooltip.y.formatter = deser(opts.tooltip.y.formatter);
                         this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                     }
                 }"
            >
                <div x-ref="chart"></div>
            </div>
        </div>

        {{-- Chart 4: Top 10 projects by accomplishment rate --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                Top 10 Projects by Accomplishment Rate
            </p>
            <p class="text-xs text-gray-400 mb-3">{{ $filterYear }} · Verified physical entries</p>

            <div wire:key="chart-top-projects-{{ $filterYear }}-{{ $filterProgram }}"
                 wire:ignore
                 class="w-full"
                 x-data="{
                     init() {
                         const opts = @js($topProjectsChart);
                         const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                         if (opts.dataLabels) opts.dataLabels.formatter = deser(opts.dataLabels.formatter);
                         if (opts.tooltip?.y) opts.tooltip.y.formatter  = deser(opts.tooltip.y.formatter);
                         this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                     }
                 }"
            >
                <div x-ref="chart"></div>
            </div>
        </div>

    </div>

    {{-- ── Row 3: Per-Program Accomplishment Charts ─────────────────────── --}}
    @foreach($programCodes as $code)
        <div class="mt-8 mb-4 flex items-center gap-3">
            <h3 class="text-sm font-bold uppercase tracking-widest whitespace-nowrap" style="color:#003087;">
                {{ $code }} Accomplishments
            </h3>
            <div class="flex-1 h-px opacity-40" style="background-color:#FDB913;"></div>
        </div>

        @if($code === 'SETUP')
            @if($setupKpiChart)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">SETUP KPI Accomplishment</p>
                    <p class="text-xs text-gray-400 mb-3">{{ $filterYear }} · Target vs Actual · Verified</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                        {{-- Financial KPIs: iFUND + Gross Sales --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-2 font-medium">Financial KPIs (₱)</p>
                            <div wire:key="chart-setup-fin-{{ $filterYear }}-{{ $filterProgram }}"
                                 wire:ignore class="w-full"
                                 x-data="{
                                     init() {
                                         const opts = @js($setupKpiChart['financial']);
                                         const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                                         if (opts.tooltip?.y) opts.tooltip.y.formatter = deser(opts.tooltip.y.formatter);
                                         this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                                     }
                                 }">
                                <div x-ref="chart"></div>
                            </div>
                        </div>

                        {{-- Activity KPIs: Projects + Employment + Trainings --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-2 font-medium">Activity KPIs</p>
                            <div wire:key="chart-setup-act-{{ $filterYear }}-{{ $filterProgram }}"
                                 wire:ignore class="w-full"
                                 x-data="{
                                     init() {
                                         const opts = @js($setupKpiChart['activity']);
                                         const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                                         if (opts.tooltip?.y) opts.tooltip.y.formatter = deser(opts.tooltip.y.formatter);
                                         this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                                     }
                                 }">
                                <div x-ref="chart"></div>
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 py-3 mb-2">No verified SETUP KPI data for {{ $filterYear }}.</p>
            @endif

            {{-- SETUP project-level KPI summary table --}}
            @if(count($setupProjectSummaries))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-2">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Project KPI Summary — {{ $filterYear }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase tracking-wide border-b border-gray-100">
                                <th class="px-4 py-2.5 text-left font-semibold">Project</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Status</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Record</th>
                                <th class="px-3 py-2.5 text-right font-semibold">No. Projects<br><span class="font-normal normal-case text-gray-300">Target / Actual</span></th>
                                <th class="px-3 py-2.5 text-right font-semibold">iFUND (₱)<br><span class="font-normal normal-case text-gray-300">Target / Actual</span></th>
                                <th class="px-3 py-2.5 text-right font-semibold">Gross Sales (₱)<br><span class="font-normal normal-case text-gray-300">Target / Actual</span></th>
                                <th class="px-3 py-2.5 text-right font-semibold">Employment<br><span class="font-normal normal-case text-gray-300">Target / Actual</span></th>
                                <th class="px-3 py-2.5 text-right font-semibold">Trainings<br><span class="font-normal normal-case text-gray-300">Target / Actual</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($setupProjectSummaries as $row)
                            @php
                                $psBadge = match($row['status']) {
                                    'active'       => 'bg-green-100 text-green-700',
                                    'liquidated'   => 'bg-blue-100 text-blue-700',
                                    'unliquidated' => 'bg-amber-100 text-amber-700',
                                    default        => 'bg-gray-100 text-gray-500',
                                };
                                $rsBadge = match($row['record_status'] ?? '') {
                                    'verified' => 'bg-green-100 text-green-700',
                                    'flagged'  => 'bg-amber-100 text-amber-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'pending'  => 'bg-gray-100 text-gray-500',
                                    default    => 'bg-gray-50 text-gray-300',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-800 font-medium max-w-[200px]">
                                    <span title="{{ $row['title'] }}">{{ Str::limit($row['title'], 42) }}</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $psBadge }}">{{ ucfirst($row['status']) }}</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['record_status'])
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $rsBadge }}">{{ ucfirst($row['record_status']) }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-right font-mono text-gray-600">
                                    @if($row['target_num_projects'] !== null)
                                        {{ number_format($row['target_num_projects']) }} / <span class="{{ $row['actual_num_projects'] >= $row['target_num_projects'] ? 'text-green-600 font-semibold' : '' }}">{{ number_format($row['actual_num_projects']) }}</span>
                                    @else <span class="text-gray-300">—</span> @endif
                                </td>
                                <td class="px-3 py-3 text-right font-mono text-gray-600">
                                    @if($row['target_ifund_amount'] !== null)
                                        {{ number_format($row['target_ifund_amount'], 0) }} / <span class="{{ $row['actual_ifund_amount'] >= $row['target_ifund_amount'] ? 'text-green-600 font-semibold' : '' }}">{{ number_format($row['actual_ifund_amount'], 0) }}</span>
                                    @else <span class="text-gray-300">—</span> @endif
                                </td>
                                <td class="px-3 py-3 text-right font-mono text-gray-600">
                                    @if($row['target_gross_sales'] !== null)
                                        {{ number_format($row['target_gross_sales'], 0) }} / <span class="{{ $row['actual_gross_sales'] >= $row['target_gross_sales'] ? 'text-green-600 font-semibold' : '' }}">{{ number_format($row['actual_gross_sales'], 0) }}</span>
                                    @else <span class="text-gray-300">—</span> @endif
                                </td>
                                <td class="px-3 py-3 text-right font-mono text-gray-600">
                                    @if($row['target_employment'] !== null)
                                        {{ number_format($row['target_employment']) }} / <span class="{{ $row['actual_employment'] >= $row['target_employment'] ? 'text-green-600 font-semibold' : '' }}">{{ number_format($row['actual_employment']) }}</span>
                                    @else <span class="text-gray-300">—</span> @endif
                                </td>
                                <td class="px-3 py-3 text-right font-mono text-gray-600">
                                    @if($row['target_trainings'] !== null)
                                        {{ number_format($row['target_trainings']) }} / <span class="{{ $row['actual_trainings'] >= $row['target_trainings'] ? 'text-green-600 font-semibold' : '' }}">{{ number_format($row['actual_trainings']) }}</span>
                                    @else <span class="text-gray-300">—</span> @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-3">

                {{-- Financial chart per program --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Financial Accomplishment</p>
                    <p class="text-xs text-gray-400 mb-3">{{ $filterYear }} · Target vs Disbursed · Verified</p>
                    <div wire:key="chart-prog-fin-{{ $code }}-{{ $filterYear }}-{{ $filterProgram }}"
                         wire:ignore class="w-full"
                         x-data="{
                             init() {
                                 const opts = @js($programFinancialCharts[$code]);
                                 const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                                 if (opts.tooltip?.y) opts.tooltip.y.formatter = deser(opts.tooltip.y.formatter);
                                 this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                             }
                         }">
                        <div x-ref="chart"></div>
                    </div>
                </div>

                {{-- Physical chart per program --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Physical Accomplishment</p>
                    <p class="text-xs text-gray-400 mb-3">{{ $filterYear }} · Target vs Accomplished · Verified</p>
                    <div wire:key="chart-prog-phys-{{ $code }}-{{ $filterYear }}-{{ $filterProgram }}"
                         wire:ignore class="w-full"
                         x-data="{
                             init() {
                                 const opts = @js($programPhysicalCharts[$code]);
                                 const deser = (fn) => typeof fn === 'string' ? new Function('v', fn.slice(fn.indexOf('{')+1, fn.lastIndexOf('}'))) : fn;
                                 if (opts.tooltip?.y) opts.tooltip.y.formatter = deser(opts.tooltip.y.formatter);
                                 this.$nextTick(() => new ApexCharts(this.$refs.chart, opts).render());
                             }
                         }">
                        <div x-ref="chart"></div>
                    </div>
                </div>

            </div>

            {{-- Project-level accomplishment summary table --}}
            @if(isset($programProjectSummaries[$code]) && count($programProjectSummaries[$code]))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-2">
                <div class="px-5 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Project Accomplishment Summary — {{ $filterYear }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase tracking-wide border-b border-gray-100">
                                <th class="px-4 py-2.5 text-left font-semibold">Project</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Status</th>
                                <th class="px-3 py-2.5 text-left font-semibold" style="min-width:180px;">Financial Disbursement</th>
                                <th class="px-3 py-2.5 text-left font-semibold" style="min-width:180px;">Physical Accomplishment</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Pending</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($programProjectSummaries[$code] as $row)
                            @php
                                $psBadge = match($row['status']) {
                                    'active'       => 'bg-green-100 text-green-700',
                                    'liquidated'   => 'bg-blue-100 text-blue-700',
                                    'unliquidated' => 'bg-amber-100 text-amber-700',
                                    default        => 'bg-gray-100 text-gray-500',
                                };
                                $ftBarW  = $row['ft_rate'] !== null ? min((float) $row['ft_rate'], 100) : 0;
                                $paBarW  = $row['pa_rate'] !== null ? min((float) $row['pa_rate'], 100) : 0;
                                $ftColor = $ftBarW >= 90 ? '#16a34a' : ($ftBarW >= 60 ? '#FDB913' : '#003087');
                                $paColor = $paBarW >= 90 ? '#16a34a' : ($paBarW >= 60 ? '#FDB913' : '#003087');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-800 font-medium max-w-[220px]">
                                    <span title="{{ $row['title'] }}">{{ Str::limit($row['title'], 45) }}</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $psBadge }}">{{ ucfirst($row['status']) }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    @if($row['ft_rate'] !== null)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 rounded-full bg-gray-100">
                                                <div class="h-2 rounded-full transition-all" style="width:{{ $ftBarW }}%;background-color:{{ $ftColor }};"></div>
                                            </div>
                                            <span class="w-10 text-right font-mono font-semibold" style="color:{{ $ftColor }}">{{ $row['ft_rate'] }}%</span>
                                        </div>
                                        <p class="text-gray-400 mt-0.5 font-mono">₱{{ number_format($row['ft_disbursed'], 0) }} / ₱{{ number_format($row['ft_target'], 0) }}</p>
                                    @else
                                        <span class="text-gray-300">No verified data</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if($row['pa_rate'] !== null)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 rounded-full bg-gray-100">
                                                <div class="h-2 rounded-full transition-all" style="width:{{ $paBarW }}%;background-color:{{ $paColor }};"></div>
                                            </div>
                                            <span class="w-10 text-right font-mono font-semibold" style="color:{{ $paColor }}">{{ $row['pa_rate'] }}%</span>
                                        </div>
                                        <p class="text-gray-400 mt-0.5 font-mono">{{ number_format($row['pa_accomplished'], 2) }} / {{ number_format($row['pa_target'], 2) }}</p>
                                    @else
                                        <span class="text-gray-300">No verified data</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['pending'] > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">{{ $row['pending'] }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        @endif
    @endforeach

</div>
