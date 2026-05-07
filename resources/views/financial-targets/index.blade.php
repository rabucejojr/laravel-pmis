<x-app-layout :title="$project->title . ' — Financial Targets'">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 mb-1">
                <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
                / <a href="{{ route('projects.show', $project) }}" class="hover:underline" style="color:#003087;">{{ Str::limit($project->title, 40) }}</a>
                / Financial Targets
            </p>
            <h2 class="text-2xl font-bold" style="color:#003087;">Financial Targets</h2>
        </div>

        @if(auth()->user()->hasRole(['admin', 'encoder']))
        <div class="flex items-center gap-2 shrink-0">
            {{-- Excel import --}}
            <form method="POST" action="{{ route('projects.financial-targets.import', $project) }}"
                  enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv"
                       class="text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:text-white"
                       style="file:background-color:#003087;">
                <button type="submit"
                        class="px-3 py-2 rounded-lg border text-sm font-medium transition"
                        style="border-color:#003087;color:#003087;"
                        onmouseover="this.style.background='#003087';this.style.color='#fff'"
                        onmouseout="this.style.background='';this.style.color='#003087'">
                    Import
                </button>
            </form>

            <a href="{{ route('projects.financial-targets.create', $project) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
               style="background-color:#003087;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Entry
            </a>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <livewire:financial-target-table :projectId="$project->id" />
    </div>
</x-app-layout>
