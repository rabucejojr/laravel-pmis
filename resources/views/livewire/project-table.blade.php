<div>
    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input wire:model.live.debounce.400ms="search"
                   type="text" placeholder="Title, agency, location…"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2"
                   style="focus:ring-color:#003087;">
        </div>

        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
            <select wire:model.live="programCode" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                <option value="">All Programs</option>
                @foreach($programs as $prog)
                    <option value="{{ $prog->code }}">{{ $prog->code }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select wire:model.live="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="suspended">Suspended</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>

        <div wire:loading class="text-xs text-gray-400 flex items-center gap-1 self-center">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Loading…
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color:#F5F7FA;">
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 cursor-pointer select-none"
                            wire:click="sort('title')">
                            <div class="flex items-center gap-1">
                                Project Title
                                @if($sortField === 'title')
                                    <span class="text-xs">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 cursor-pointer select-none"
                            wire:click="sort('program_id')">
                            <div class="flex items-center gap-1">
                                Program
                                @if($sortField === 'program_id')
                                    <span class="text-xs">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500">Agency</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 cursor-pointer select-none"
                            wire:click="sort('end_date')">
                            <div class="flex items-center gap-1">
                                End Date
                                @if($sortField === 'end_date')
                                    <span class="text-xs">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 cursor-pointer select-none"
                            wire:click="sort('total_approved_budget')">
                            <div class="flex items-center gap-1">
                                Budget
                                @if($sortField === 'total_approved_budget')
                                    <span class="text-xs">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 cursor-pointer select-none"
                            wire:click="sort('status')">
                            <div class="flex items-center gap-1">
                                Status
                                @if($sortField === 'status')
                                    <span class="text-xs">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium" style="color:#1A1A2E;">
                            <a href="{{ route('projects.show', $project) }}"
                               class="hover:underline" style="color:#003087;">
                                {{ $project->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                  style="background-color:#003087;color:#FDB913;">
                                {{ $project->program->code }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-48 truncate">{{ $project->implementing_agency }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $project->end_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">₱ {{ number_format($project->total_approved_budget, 2) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($project->status) {
                                    'active'     => 'bg-green-100 text-green-700',
                                    'completed'  => 'bg-blue-100 text-blue-700',
                                    'suspended'  => 'bg-amber-100 text-amber-700',
                                    'terminated' => 'bg-red-100 text-red-700',
                                    default      => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-xs text-gray-500 hover:text-gray-700">View</a>
                                @can('update', $project)
                                <a href="{{ route('projects.edit', $project) }}"
                                   class="text-xs" style="color:#003087;">Edit</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                            No projects found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $projects->links() }}
        </div>
        @endif
    </div>
</div>
