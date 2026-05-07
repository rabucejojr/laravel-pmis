<div>
    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-4 items-end">
        <div wire:loading.class="opacity-50" class="transition-opacity">
            <select wire:model.live="filterYear"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Years</option>
                @foreach($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="filterQuarter"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Quarters</option>
                @foreach([1,2,3,4] as $q)
                    <option value="{{ $q }}">Q{{ $q }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="filterMonth"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Months</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        @if(auth()->user()->hasRole(['admin','encoder','verifier']))
        <div>
            <select wire:model.live="filterStatus"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="flagged">Flagged</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        @endif
        <div wire:loading class="text-xs text-gray-400 flex items-center gap-1">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Loading…
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="min-w-full text-sm">
            <thead style="background-color:#F5F7FA;">
                <tr>
                    @php
                        $cols = [
                            'year'                => 'Year',
                            'quarter'             => 'Q',
                            'month'               => 'Month',
                            'indicator_name'      => 'Indicator',
                            'target_value'        => 'Target',
                            'accomplished_value'  => 'Accomplished',
                            'accomplishment_rate' => 'Rate (%)',
                            'verified_status'     => 'Status',
                        ];
                    @endphp
                    @foreach($cols as $field => $label)
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide text-xs whitespace-nowrap cursor-pointer select-none"
                        wire:click="sort('{{ $field }}')">
                        {{ $label }}
                        @if($sortField === $field)
                            {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    @endforeach
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide text-xs">Encoded By</th>
                    @if(auth()->user()->hasRole(['admin','encoder']))
                    <th class="px-4 py-3"></th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($entries as $entry)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-medium" style="color:#1A1A2E;">{{ $entry->year }}</td>
                    <td class="px-4 py-3 text-gray-600">Q{{ $entry->quarter }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::create()->month($entry->month)->format('M') }}</td>
                    <td class="px-4 py-3 font-medium" style="color:#1A1A2E;">{{ $entry->indicator_name }}</td>
                    <td class="px-4 py-3 text-right font-mono text-gray-700">{{ number_format($entry->target_value, 2) }}</td>
                    <td class="px-4 py-3 text-right font-mono text-gray-700">{{ number_format($entry->accomplished_value, 2) }}</td>
                    <td class="px-4 py-3 text-right font-mono">
                        @php $rate = (float) $entry->accomplishment_rate; @endphp
                        <span class="{{ $rate > 150 ? 'text-amber-600 font-semibold' : 'text-gray-700' }}">
                            {{ $entry->accomplishment_rate !== null ? number_format($rate, 2).'%' : '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($entry->verified_status) {
                                'pending'  => 'bg-gray-100 text-gray-600',
                                'verified' => 'bg-green-100 text-green-700',
                                'flagged'  => 'bg-amber-100 text-amber-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge }}">
                            {{ ucfirst($entry->verified_status) }}
                        </span>
                        @if($entry->verification_notes)
                            <span class="ml-1 text-xs text-gray-400" title="{{ $entry->verification_notes }}">⚠</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $entry->encoder->name ?? '—' }}</td>
                    @if(auth()->user()->hasRole(['admin','encoder']))
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('projects.physical-accomplishments.edit', [$entry->project_id, $entry]) }}"
                           class="text-xs font-medium hover:underline" style="color:#003087;">Edit</a>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-10 text-center text-sm text-gray-400">
                        No physical accomplishments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $entries->links() }}
    </div>
</div>
