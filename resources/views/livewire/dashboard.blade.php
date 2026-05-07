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
</div>
