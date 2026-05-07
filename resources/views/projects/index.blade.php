<x-app-layout :title="$program ? $program->name . ' Projects' : 'All Projects'">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold" style="color:#003087;">
                {{ $program ? $program->code . ' — ' . $program->name : 'All Projects' }}
            </h2>
            @if($program)
                <p class="text-sm text-gray-500 mt-1">{{ $program->description }}</p>
            @endif
        </div>

        @if(auth()->user()->hasRole(['admin', 'encoder']))
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
           style="background-color:#003087;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Project
        </a>
        @endif
    </div>

    <livewire:project-table :programCode="$program?->code ?? ''" />
</x-app-layout>
