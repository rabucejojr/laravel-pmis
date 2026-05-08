<x-app-layout>
    <x-slot name="title">Budget Breakdown — {{ $project->title }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" style="color:#003087;" class="hover:underline">Projects</a>
        / <a href="{{ route('projects.show', $project) }}" style="color:#003087;" class="hover:underline">{{ Str::limit($project->title, 40) }}</a>
        / Budget Breakdown
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-5">

        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold" style="color:#003087;">Budget Breakdown</h1>
                <p class="text-xs text-gray-400 mt-0.5">{{ $project->title }}</p>
            </div>
            <a href="{{ route('projects.show', $project) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Project
            </a>
        </div>

        {{-- Flash alerts --}}
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if(session('warning'))
            <x-alert type="warning" :message="session('warning')" />
        @endif
        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        {{-- Budget editor card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <livewire:budget-item-editor :project="$project" />
        </div>

    </div>
</x-app-layout>
