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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col items-center justify-center py-10"
      style="background-color:#003087;">

    {{-- Brand header --}}
    <div class="mb-8 text-center">
        <img src="{{ asset('favicon.ico') }}" alt="DOST Logo" class="w-16 h-16 object-contain mb-4 mx-auto">
        <h1 class="text-white text-xl font-bold tracking-wide">DOST-SDN PMIS</h1>
        <p class="text-white/60 text-xs mt-1">Project Monitoring and Information System</p>
        <p class="text-white/50 text-xs">DOST - Surigao del Norte</p>
    </div>

    {{-- Card --}}
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="h-1" style="background:linear-gradient(to right,#003087,#FDB913);"></div>
        <div class="px-8 py-8">
            {{ $slot }}
        </div>
    </div>

    <p class="mt-8 text-white/30 text-xs text-center">
        &copy; {{ date('Y') }} Department of Science and Technology - Surigao del Norte
    </p>
</body>
</html>
