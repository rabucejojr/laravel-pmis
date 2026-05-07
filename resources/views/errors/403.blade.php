<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied — DOST-SDN PMIS</title>
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
            max-width: 480px;
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
            background-color: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap svg { width: 36px; height: 36px; stroke: #dc2626; }
        .code {
            font-size: 4rem;
            font-weight: 700;
            color: #003087;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
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
            margin-bottom: 1.5rem;
        }
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            background-color: #003087;
            color: #FDB913;
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
        .divider {
            border: none;
            border-top: 1px solid #f0f0f0;
            margin: 2rem 0 1.5rem;
        }
        .role-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            text-align: left;
        }
        .role-table th {
            padding: 0.4rem 0.75rem;
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.7rem;
        }
        .role-table td {
            padding: 0.4rem 0.75rem;
            color: #374151;
            border-top: 1px solid #f3f4f6;
        }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; }
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
                      d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-13a9 9 0 110 18A9 9 0 0112 4z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18.364 5.636A9 9 0 015.636 18.364"/>
            </svg>
        </div>

        <div class="code">403</div>
        <div class="title">Access Denied</div>
        <p class="description">
            You don't have permission to view this page.
            @if($exception->getMessage() && $exception->getMessage() !== 'Unauthorized.')
                {{ $exception->getMessage() }}
            @else
                This section is restricted to specific roles within the system.
            @endif
        </p>

        @auth
            <div class="role-badge">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Your role: {{ strtoupper(auth()->user()->role) }}
            </div>
        @endauth

        <div class="actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">Sign In</a>
            @endauth
            <a href="javascript:history.back()" class="btn-secondary">Go Back</a>
        </div>

        <hr class="divider">

        <table class="role-table">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Required Role(s)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Projects / Documents</td>
                    <td><span class="dot" style="background:#003087;"></span>Admin, Encoder</td>
                </tr>
                <tr>
                    <td>Financial &amp; Physical Entry</td>
                    <td><span class="dot" style="background:#003087;"></span>Admin, Encoder</td>
                </tr>
                <tr>
                    <td>Verification Queue</td>
                    <td><span class="dot" style="background:#d97706;"></span>Admin, Verifier</td>
                </tr>
                <tr>
                    <td>Reports &amp; Dashboard</td>
                    <td><span class="dot" style="background:#16a34a;"></span>All roles</td>
                </tr>
                <tr>
                    <td>User Management</td>
                    <td><span class="dot" style="background:#dc2626;"></span>Admin only</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
