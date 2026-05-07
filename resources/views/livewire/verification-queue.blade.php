<div>
    {{-- Summary badges --}}
    <div class="flex flex-wrap gap-4 mb-5">
        <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-block"></span>
            <span>Pending: <strong>{{ $totalPending }}</strong></span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-50 text-amber-700 text-sm font-medium">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
            <span>Flagged: <strong>{{ $totalFlagged }}</strong></span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <select wire:model.live="filterStatus"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="flagged">Flagged</option>
        </select>

        <select wire:model.live="filterType"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All types</option>
            <option value="financial">Financial Target</option>
            <option value="physical">Physical Accomplishment</option>
        </select>

        <select wire:model.live="filterYear"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All years</option>
            @foreach($availableYears as $yr)
                <option value="{{ $yr }}">{{ $yr }}</option>
            @endforeach
        </select>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="relative overflow-x-auto rounded-xl border border-gray-100">
        <div wire:loading class="absolute inset-0 bg-white/60 flex items-center justify-center z-10 rounded-xl">
            <svg class="animate-spin w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 cursor-pointer select-none" wire:click="sort('type')">
                        Type
                        @if($sortField === 'type')
                            {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th class="px-4 py-3 cursor-pointer select-none" wire:click="sort('project')">
                        Project
                        @if($sortField === 'project')
                            {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th class="px-4 py-3 cursor-pointer select-none" wire:click="sort('period')">
                        Period
                        @if($sortField === 'period')
                            {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th class="px-4 py-3">Entry / Indicator</th>
                    <th class="px-4 py-3">Value</th>
                    <th class="px-4 py-3 cursor-pointer select-none" wire:click="sort('encoder')">
                        Encoder
                        @if($sortField === 'encoder')
                            {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 cursor-pointer select-none" wire:click="sort('created_at')">
                        Submitted
                        @if($sortField === 'created_at')
                            {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($entries as $row)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($row['type'] === 'financial')
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Financial</span>
                        @else
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Physical</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 max-w-[180px]">
                        <div class="font-medium text-gray-800 truncate" title="{{ $row['project'] }}">{{ $row['project'] }}</div>
                        <div class="text-xs text-gray-400">{{ $row['program'] }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $row['period'] }}</td>
                    <td class="px-4 py-3 max-w-[200px]">
                        <div class="truncate" title="{{ $row['label'] }}">{{ $row['label'] }}</div>
                        @if($row['notes'])
                            <div class="mt-1 text-xs text-amber-600" title="{{ $row['notes'] }}">
                                ⚠ {{ Str::limit($row['notes'], 60) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-xs whitespace-nowrap">{{ $row['extra'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['encoder'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $badge = match($row['status']) {
                                'pending'  => 'bg-gray-100 text-gray-600',
                                'flagged'  => 'bg-amber-100 text-amber-700',
                                'verified' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge }}">
                            {{ ucfirst($row['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($row['created_at'])->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            {{-- Verify: available for pending and flagged --}}
                            <button wire:click="openConfirm({{ $row['id'] }}, '{{ $row['type'] }}', 'verified')"
                                    wire:loading.attr="disabled"
                                    wire:target="openConfirm({{ $row['id'] }}, '{{ $row['type'] }}', 'verified')"
                                    class="px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Verify
                            </button>

                            {{-- Flag: only for pending entries --}}
                            @if($row['status'] === 'pending')
                            <button wire:click="openConfirm({{ $row['id'] }}, '{{ $row['type'] }}', 'flagged')"
                                    wire:loading.attr="disabled"
                                    wire:target="openConfirm({{ $row['id'] }}, '{{ $row['type'] }}', 'flagged')"
                                    class="px-2 py-1 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Flag
                            </button>
                            @endif

                            {{-- Reject: available for pending and flagged --}}
                            <button wire:click="openConfirm({{ $row['id'] }}, '{{ $row['type'] }}', 'rejected')"
                                    wire:loading.attr="disabled"
                                    wire:target="openConfirm({{ $row['id'] }}, '{{ $row['type'] }}', 'rejected')"
                                    class="px-2 py-1 rounded text-xs font-medium bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Reject
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm">
                        No entries in the queue.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($entries->hasPages())
    <div class="mt-4">
        {{ $entries->links() }}
    </div>
    @endif

    {{-- Confirmation Modal --}}
    @if($activeId)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         wire:click.self="cancelConfirm">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold mb-1" style="color:#003087;">
                @if($activeAction === 'verified') ✅ Verify Entry
                @elseif($activeAction === 'flagged') ⚠️ Flag Entry
                @else ❌ Reject Entry
                @endif
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                @if($activeAction === 'verified') This entry will be marked as verified and made visible to all roles.
                @elseif($activeAction === 'flagged') This entry will be flagged for further review. The encoder will be notified.
                @else This entry will be rejected. The encoder will be notified.
                @endif
            </p>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Notes
                    @if($activeAction !== 'verified') <span class="text-gray-400">(optional)</span> @endif
                </label>
                <textarea wire:model="notesInput" rows="3"
                          placeholder="Add notes for the encoder…"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                @error('notesInput')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <button wire:click="cancelConfirm"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Cancel
                </button>
                <button wire:click="submitAction"
                        wire:loading.attr="disabled"
                        wire:target="submitAction"
                        class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color:{{ $activeAction === 'verified' ? '#16a34a' : ($activeAction === 'flagged' ? '#d97706' : '#dc2626') }};"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <span wire:loading.remove wire:target="submitAction">Confirm {{ ucfirst($activeAction) }}</span>
                    <span wire:loading wire:target="submitAction">Processing…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
