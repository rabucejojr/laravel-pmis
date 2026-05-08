<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'DOST-SDN PMIS') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased" style="background-color:#F5F7FA;color:#1A1A2E;">

<div x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
    mobileSidebarOpen: false
}" x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))">

    {{-- ── Sidebar ── --}}
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="hidden lg:flex flex-col fixed top-0 left-0 h-full transition-all duration-300 z-30"
        style="background-color:#003087;">

        {{-- Logo / Brand --}}
        <div class="flex items-center gap-3 px-4 h-16 shrink-0" style="border-bottom:1px solid rgba(255,255,255,0.1);">
            <img src="{{ asset('favicon.ico') }}" alt="DOST Logo"
                 class="w-8 h-8 shrink-0 object-contain">
            <span x-show="sidebarOpen" class="text-white font-semibold text-sm leading-tight truncate">
                DOST-SDN<br><span class="font-normal opacity-80 text-xs">PMIS</span>
            </span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-2">
            @php $seg = request()->segment(1); @endphp

            <x-sidebar-link href="{{ route('dashboard') }}" :active="$seg === 'dashboard' || request()->routeIs('dashboard')" icon="grid">
                Dashboard
            </x-sidebar-link>

            {{-- Program sections --}}
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">
                Programs
            </div>

            <x-sidebar-link href="{{ route('projects.index', ['program' => 'SETUP']) }}" :active="$seg === 'projects' && request('program') === 'SETUP'" icon="briefcase">
                SETUP
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('projects.index', ['program' => 'GIA']) }}" :active="$seg === 'projects' && request('program') === 'GIA'" icon="academic-cap">
                GIA
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('projects.index', ['program' => 'CEST']) }}" :active="$seg === 'projects' && request('program') === 'CEST'" icon="users">
                CEST
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('projects.index', ['program' => 'SSCP']) }}" :active="$seg === 'projects' && request('program') === 'SSCP'" icon="light-bulb">
                SSCP
            </x-sidebar-link>

            {{-- Verification --}}
            @if(auth()->user()->hasRole(['admin','encoder','verifier']))
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">
                Workflow
            </div>

            <x-sidebar-link href="{{ route('verification.index') }}" :active="$seg === 'verification'" icon="shield-check">
                Verification
            </x-sidebar-link>
            @endif

            {{-- Reports --}}
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">
                Reports
            </div>

            <x-sidebar-link href="{{ route('reports.index') }}" :active="$seg === 'reports'" icon="document-chart-bar">
                Reports
            </x-sidebar-link>

            {{-- Admin only --}}
            @if(auth()->user()->isAdmin())
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">
                Administration
            </div>

            <x-sidebar-link href="{{ route('users.index') }}" :active="$seg === 'users'" icon="user-group">
                Users
            </x-sidebar-link>
            @endif
        </nav>

        {{-- Collapse toggle --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="flex items-center justify-center h-10 w-full text-white opacity-60 hover:opacity-100 transition shrink-0"
                style="border-top:1px solid rgba(255,255,255,0.1);">
            <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
            </svg>
            <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
            </svg>
        </button>
    </aside>

    {{-- ── Mobile sidebar overlay ── --}}
    <div x-show="mobileSidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/40 lg:hidden"
         style="display:none;"></div>

    <aside x-show="mobileSidebarOpen"
           x-transition:enter="transition-transform ease-out duration-200"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition-transform ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed top-0 left-0 h-full w-64 flex flex-col z-30 lg:hidden"
           style="background-color:#003087;display:none;">

        <div class="flex items-center gap-3 px-4 h-16 shrink-0" style="border-bottom:1px solid rgba(255,255,255,0.1);">
            <img src="{{ asset('favicon.ico') }}" alt="DOST Logo"
                 class="w-8 h-8 shrink-0 object-contain">
            <span class="text-white font-semibold text-sm leading-tight">DOST-SDN<br><span class="font-normal opacity-80 text-xs">PMIS</span></span>
            <button @click="mobileSidebarOpen = false" class="ml-auto text-white opacity-60 hover:opacity-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-2">
            @php $seg = request()->segment(1); @endphp

            <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="grid">
                Dashboard
            </x-sidebar-link>

            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">Programs</div>

            <x-sidebar-link href="{{ route('projects.index', ['program' => 'SETUP']) }}" :active="$seg === 'projects' && request('program') === 'SETUP'" icon="briefcase">SETUP</x-sidebar-link>
            <x-sidebar-link href="{{ route('projects.index', ['program' => 'GIA']) }}" :active="$seg === 'projects' && request('program') === 'GIA'" icon="academic-cap">GIA</x-sidebar-link>
            <x-sidebar-link href="{{ route('projects.index', ['program' => 'CEST']) }}" :active="$seg === 'projects' && request('program') === 'CEST'" icon="users">CEST</x-sidebar-link>
            <x-sidebar-link href="{{ route('projects.index', ['program' => 'SSCP']) }}" :active="$seg === 'projects' && request('program') === 'SSCP'" icon="light-bulb">SSCP</x-sidebar-link>

            @if(auth()->user()->hasRole(['admin','encoder','verifier']))
            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">Workflow</div>
            <x-sidebar-link href="{{ route('verification.index') }}" :active="$seg === 'verification'" icon="shield-check">Verification</x-sidebar-link>
            @endif

            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">Reports</div>
            <x-sidebar-link href="{{ route('reports.index') }}" :active="$seg === 'reports'" icon="document-chart-bar">Reports</x-sidebar-link>

            @if(auth()->user()->isAdmin())
            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(253,185,19,0.7);">Administration</div>
            <x-sidebar-link href="{{ route('users.index') }}" :active="$seg === 'users'" icon="user-group">Users</x-sidebar-link>
            @endif
        </nav>
    </aside>

    {{-- ── Main content wrapper ── --}}
    <div :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-16'" class="flex flex-col min-h-screen transition-all duration-300">

        {{-- ── Topbar ── --}}
        <header class="sticky top-0 z-10 flex items-center h-16 px-4 gap-4 bg-white shadow-sm">

            {{-- Mobile hamburger --}}
            <button @click="mobileSidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <div class="flex-1 text-sm text-gray-500">
                @isset($breadcrumb)
                    {{ $breadcrumb }}
                @else
                    <span class="font-medium" style="color:#003087;">{{ $title ?? 'Dashboard' }}</span>
                @endisset
            </div>

            {{-- Notification bell (admin & encoder only) --}}
            @if(auth()->user()->hasRole(['admin', 'encoder']))
                <livewire:notification-bell />
            @endif

            {{-- User info + logout --}}
            <div class="flex items-center gap-3" x-data="{ open: false }">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                      style="background-color:#003087;color:#FDB913;">
                    {{ strtoupper(auth()->user()->role) }}
                </span>
                <span class="text-sm font-medium hidden sm:block" style="color:#1A1A2E;">
                    {{ auth()->user()->name }}
                </span>
                <div class="relative">
                    <button @click="open = !open"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                            style="background-color:#003087;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-44 rounded-lg shadow-lg bg-white ring-1 ring-black/5 py-1 z-50"
                         style="display:none;">
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            My Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- ── Page content ── --}}
        <main class="flex-1 p-6">
            {{-- Flash alerts --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" class="mb-4"/>
            @endif
            @if(session('warning'))
                <x-alert type="warning" :message="session('warning')" class="mb-4"/>
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" class="mb-4"/>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
