<x-app-layout title="Reports">
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">Reports</p>
        <h2 class="text-2xl font-bold" style="color:#003087;">Export Reports</h2>
        <p class="text-sm text-gray-500 mt-1">Download filtered data as Excel (.xlsx) or PDF.</p>
    </div>

    <div class="space-y-6">

        {{-- ── Financial Summary ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold mb-1" style="color:#003087;">Financial Summary</h3>
            <p class="text-xs text-gray-400 mb-4">Verified financial target entries per program / project / period.</p>

            <form class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
                        <select name="program" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Programs</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->code }}">{{ $prog->code }} — {{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Project</label>
                        <select name="project_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Projects</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ Str::limit($proj->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
                        <select name="year" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Years</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Quarter From</label>
                        <select name="quarter_from" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}">Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Quarter To</label>
                        <select name="quarter_to" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}">Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Month From</label>
                        <select name="month_from" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Month To</label>
                        <select name="month_to" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <button formaction="{{ route('reports.financial.excel') }}" formmethod="POST"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                            style="background-color:#003087;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        @csrf
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Excel
                    </button>
                    <button formaction="{{ route('reports.financial.pdf') }}" formmethod="POST"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-semibold transition"
                            style="border-color:#003087;color:#003087;"
                            onmouseover="this.style.background='#003087';this.style.color='#fff'"
                            onmouseout="this.style.background='';this.style.color='#003087'">
                        @csrf
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Download PDF
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Physical Accomplishments ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold mb-1" style="color:#003087;">Physical Accomplishments</h3>
            <p class="text-xs text-gray-400 mb-4">Verified physical accomplishment entries per program / project / period.</p>

            <form class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
                        <select name="program" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Programs</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->code }}">{{ $prog->code }} — {{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Project</label>
                        <select name="project_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Projects</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ Str::limit($proj->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
                        <select name="year" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Years</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Quarter From</label>
                        <select name="quarter_from" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}">Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Quarter To</label>
                        <select name="quarter_to" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}">Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Month From</label>
                        <select name="month_from" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Month To</label>
                        <select name="month_to" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <button formaction="{{ route('reports.physical.excel') }}" formmethod="POST"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                            style="background-color:#003087;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        @csrf
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Excel
                    </button>
                    <button formaction="{{ route('reports.physical.pdf') }}" formmethod="POST"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-semibold transition"
                            style="border-color:#003087;color:#003087;"
                            onmouseover="this.style.background='#003087';this.style.color='#fff'"
                            onmouseout="this.style.background='';this.style.color='#003087'">
                        @csrf
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Download PDF
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Verification Audit Log ──────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold mb-1" style="color:#003087;">Verification Audit Log</h3>
            <p class="text-xs text-gray-400 mb-4">All verification actions (verified / flagged / rejected) with verifier and notes.</p>

            <form class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Verifier</label>
                        <select name="verifier_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Verifiers</option>
                            @foreach($verifiers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                        <select name="action" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Actions</option>
                            <option value="verified">Verified</option>
                            <option value="flagged">Flagged</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
                        <input type="date" name="date_from"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date To</label>
                        <input type="date" name="date_to"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <button formaction="{{ route('reports.audit.excel') }}" formmethod="POST"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                            style="background-color:#003087;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        @csrf
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Excel
                    </button>
                    <button formaction="{{ route('reports.audit.pdf') }}" formmethod="POST"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-semibold transition"
                            style="border-color:#003087;color:#003087;"
                            onmouseover="this.style.background='#003087';this.style.color='#fff'"
                            onmouseout="this.style.background='';this.style.color='#003087'">
                        @csrf
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Download PDF
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
