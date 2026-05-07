<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found — DOST-SDN PMIS</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #F5F7FA;
            color: #1A1A2E;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 3rem 2.5rem;
            max-width: 440px;
            width: 100%;
            text-align: center;
        }
        .brand-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        .brand-bar img { width: 36px; height: 36px; object-fit: contain; }
        .brand-bar span { font-size: 1rem; font-weight: 600; color: #003087; }
        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background-color: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap svg { width: 36px; height: 36px; stroke: #003087; }
        .code {
            font-size: 4rem;
            font-weight: 700;
            color: #003087;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .accent { color: #FDB913; }
        .title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1A1A2E;
            margin-bottom: 0.75rem;
        }
        .description {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .actions { display: flex; flex-direction: column; gap: 0.75rem; }
        .btn-primary {
            display: inline-block;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            background-color: #003087;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.85; }
        .btn-secondary {
            display: inline-block;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            background-color: #fff;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.15s;
        }
        .btn-secondary:hover { background-color: #f9fafb; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand-bar">
            <img src="{{ asset('favicon.ico') }}" alt="DOST Logo">
            <span>DOST-SDN PMIS</span>
        </div>

        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <div class="code">4<span class="accent">0</span>4</div>
        <div class="title">Page Not Found</div>
        <p class="description">
            The page you're looking for doesn't exist or may have been moved.
            Double-check the URL or navigate back to a known section.
        </p>

        <div class="actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">Sign In</a>
            @endauth
            <a href="javascript:history.back()" class="btn-secondary">Go Back</a>
        </div>
    </div>
</body>
</html>
