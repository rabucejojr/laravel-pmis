<x-app-layout title="User Management">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 mb-1">Administration</p>
            <h2 class="text-2xl font-bold" style="color:#003087;">User Management</h2>
        </div>
        <a href="{{ route('users.create') }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
           style="background-color:#003087;"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </a>
    </div>

    {{-- Search & filter bar --}}
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search name, email, office…"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
        <select name="role"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Roles</option>
            @foreach(['admin','encoder','verifier','viewer'] as $r)
                <option value="{{ $r }}" {{ $role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-2 rounded-lg border text-sm font-medium transition"
                style="border-color:#003087;color:#003087;"
                onmouseover="this.style.background='#003087';this.style.color='#fff'"
                onmouseout="this.style.background='';this.style.color='#003087'">
            Filter
        </button>
        @if($search || $role)
        <a href="{{ route('users.index') }}"
           class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-500 hover:bg-gray-50">
            Clear
        </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Office</th>
                    <th class="px-5 py-3">Joined</th>
                    <th class="px-5 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                                 style="background-color:#003087;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-medium" style="color:#1A1A2E;">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="text-xs text-gray-400">(you)</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-5 py-3">
                        @php
                            $roleBadge = match($user->role) {
                                'admin'    => 'bg-purple-100 text-purple-700',
                                'encoder'  => 'bg-blue-100 text-blue-700',
                                'verifier' => 'bg-amber-100 text-amber-700',
                                'viewer'   => 'bg-gray-100 text-gray-600',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $roleBadge }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $user->office ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400 whitespace-nowrap">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('users.edit', $user) }}"
                               class="text-xs font-medium hover:underline" style="color:#003087;">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete user {{ addslashes($user->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
