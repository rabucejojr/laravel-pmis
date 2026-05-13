<x-app-layout :title="$project->title . ' — SETUP KPI Accomplishments'">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 mb-1">
                <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
                / <a href="{{ route('projects.show', $project) }}" class="hover:underline" style="color:#003087;">{{ Str::limit($project->title, 40) }}</a>
                / SETUP KPI Accomplishments
            </p>
            <h2 class="text-2xl font-bold" style="color:#003087;">SETUP KPI Accomplishments</h2>
        </div>

        @if(auth()->user()->hasRole(['admin', 'encoder']))
        <a href="{{ route('projects.setup-accomplishments.create', $project) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition shrink-0"
           style="background-color:#003087;"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Entry
        </a>
        @endif
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" class="mb-4" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" class="mb-4" />
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($entries->isEmpty())
            <div class="text-center py-16 text-gray-400 text-sm">
                No SETUP KPI records yet.
                @if(auth()->user()->hasRole(['admin', 'encoder']))
                    <br><span class="text-xs">Click <strong>Add Entry</strong> to record annual KPI accomplishments.</span>
                @endif
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Year</th>
                        <th class="px-4 py-3 text-center">No. of Projects</th>
                        <th class="px-4 py-3 text-center">iFUND Amount</th>
                        <th class="px-4 py-3 text-center">Gross Sales</th>
                        <th class="px-4 py-3 text-center">Employment</th>
                        <th class="px-4 py-3 text-center">Trainings</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($entries as $entry)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-semibold" style="color:#003087;">{{ $entry->year }}</td>

                        <td class="px-4 py-3 text-center">
                            <span class="text-gray-400 text-xs">T:</span> {{ number_format($entry->target_num_projects) }}
                            <span class="mx-1 text-gray-300">→</span>
                            <span class="font-medium">{{ number_format($entry->actual_num_projects) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs font-mono">
                            <span class="text-gray-400">T:</span> ₱{{ number_format($entry->target_ifund_amount, 0) }}<br>
                            <span class="font-medium">A: ₱{{ number_format($entry->actual_ifund_amount, 0) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs font-mono">
                            <span class="text-gray-400">T:</span> ₱{{ number_format($entry->target_gross_sales, 0) }}<br>
                            <span class="font-medium">A: ₱{{ number_format($entry->actual_gross_sales, 0) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-gray-400 text-xs">T:</span> {{ number_format($entry->target_employment) }}
                            <span class="mx-1 text-gray-300">→</span>
                            <span class="font-medium">{{ number_format($entry->actual_employment) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-gray-400 text-xs">T:</span> {{ number_format($entry->target_trainings) }}
                            <span class="mx-1 text-gray-300">→</span>
                            <span class="font-medium">{{ number_format($entry->actual_trainings) }}</span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            @php
                                $badge = match($entry->verified_status) {
                                    'verified' => 'bg-green-100 text-green-700',
                                    'flagged'  => 'bg-amber-100 text-amber-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge }}">
                                {{ ucfirst($entry->verified_status) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if(auth()->user()->hasRole(['admin', 'encoder']))
                                <a href="{{ route('projects.setup-accomplishments.edit', [$project, $entry]) }}"
                                   class="text-xs font-medium" style="color:#003087;">Edit</a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                <form method="POST"
                                      action="{{ route('projects.setup-accomplishments.destroy', [$project, $entry]) }}"
                                      onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $entries->links() }}
        </div>
        @endif
        @endif
    </div>
</x-app-layout>
