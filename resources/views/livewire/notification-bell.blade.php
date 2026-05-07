<div wire:poll.30s.visible class="relative" x-data="{ open: false }" @click.outside="open = false">

    {{-- Bell button --}}
    <button @click="open = !open"
            class="relative flex items-center justify-center w-9 h-9 rounded-full text-gray-500 hover:bg-gray-100 transition"
            aria-label="Notifications">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex items-center justify-center min-w-[16px] h-4 px-0.5 rounded-full text-white text-[10px] font-bold leading-none"
                  style="background-color:#dc2626;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 rounded-xl shadow-xl bg-white ring-1 ring-black/5 z-50 overflow-hidden"
         style="display:none;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold" style="color:#003087;">
                Notifications
                @if($unreadCount > 0)
                    <span class="ml-1.5 text-xs font-bold px-1.5 py-0.5 rounded-full text-white"
                          style="background-color:#dc2626;">{{ $unreadCount }}</span>
                @endif
            </span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead"
                        class="text-xs font-medium hover:underline"
                        style="color:#003087;">
                    Mark all as read
                </button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-50">
            @forelse($notifications as $notif)
                @php
                    $data      = $notif->data;
                    $action    = $data['action']     ?? 'updated';
                    $entryType = $data['entry_type'] ?? 'financial';
                    $project   = $data['project']    ?? '—';
                    $label     = $data['label']      ?? '—';
                    $period    = $data['period']      ?? '—';
                    $verifier  = $data['verifier']   ?? '—';
                    $notes     = $data['notes']      ?? null;
                    $projectId = $data['project_id'] ?? null;

                    $actionStyle = match($action) {
                        'verified' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => '✅'],
                        'flagged'  => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => '⚠️'],
                        'rejected' => ['bg' => 'bg-red-100',   'text' => 'text-red-700',   'icon' => '❌'],
                        default    => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600',  'icon' => '🔔'],
                    };

                    $typeLabel = $entryType === 'financial' ? 'Financial' : 'Physical';

                    $entryRoute = $projectId
                        ? ($entryType === 'financial'
                            ? route('projects.financial-targets.index', $projectId)
                            : route('projects.physical-accomplishments.index', $projectId))
                        : null;

                    $isUnread = is_null($notif->read_at);
                @endphp

                <div class="relative flex gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $isUnread ? 'bg-blue-50/40' : '' }}">
                    {{-- Unread dot --}}
                    @if($isUnread)
                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full"
                              style="background-color:#003087;"></span>
                    @endif

                    {{-- Icon --}}
                    <div class="shrink-0 mt-0.5 text-lg leading-none">{{ $actionStyle['icon'] }}</div>

                    {{-- Body --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap mb-0.5">
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full {{ $actionStyle['bg'] }} {{ $actionStyle['text'] }}">
                                {{ ucfirst($action) }}
                            </span>
                            <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500 font-medium">
                                {{ $typeLabel }}
                            </span>
                        </div>

                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $project }}">
                            {{ $project }}
                        </p>
                        <p class="text-xs text-gray-500 truncate" title="{{ $label }}">
                            {{ $label }} · {{ $period }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            By {{ $verifier }} · {{ $notif->created_at->diffForHumans() }}
                        </p>
                        @if($notes)
                            <p class="text-xs text-amber-600 mt-0.5 truncate" title="{{ $notes }}">
                                Note: {{ $notes }}
                            </p>
                        @endif

                        {{-- Actions row --}}
                        <div class="flex items-center gap-3 mt-1.5">
                            @if($entryRoute)
                                <a href="{{ $entryRoute }}"
                                   @click="open = false"
                                   wire:click="markRead('{{ $notif->id }}')"
                                   class="text-xs font-medium hover:underline"
                                   style="color:#003087;">
                                    View entry →
                                </a>
                            @endif
                            @if($isUnread)
                                <button wire:click="markRead('{{ $notif->id }}')"
                                        class="text-xs text-gray-400 hover:text-gray-600 hover:underline">
                                    Mark read
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-sm text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    No notifications yet
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->isNotEmpty())
            <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                <span class="text-xs text-gray-400">Showing last {{ $notifications->count() }} notifications</span>
            </div>
        @endif
    </div>
</div>
