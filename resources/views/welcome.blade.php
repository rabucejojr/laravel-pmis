<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SDN PMIS</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif

    <style>
        body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body style="background-color:#F5F7FA;color:#1A1A2E;min-height:100vh;display:flex;flex-direction:column;">

    {{-- ── Top bar ── --}}
    <header style="background-color:#003087;border-bottom:3px solid #FDB913;">
        <div style="max-width:72rem;margin:0 auto;padding:0 1.5rem;height:4rem;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <img src="{{ asset('favicon.ico') }}" alt="DOST Logo" style="width:2.25rem;height:2.25rem;object-fit:contain;flex-shrink:0;">
                <div style="line-height:1.2;">
                    <div style="color:#ffffff;font-weight:600;font-size:0.9rem;letter-spacing:0.02em;">DOST-SDN PMIS</div>
                    <div style="color:rgba(253,185,19,0.85);font-size:0.7rem;letter-spacing:0.04em;text-transform:uppercase;">Surigao del Norte</div>
                </div>
            </div>

            @if (Route::has('login'))
                <nav style="display:flex;align-items:center;gap:0.75rem;">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;background-color:#FDB913;color:#003087;border-radius:0.375rem;font-size:0.8125rem;font-weight:600;text-decoration:none;transition:opacity 0.15s;">
                            <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           style="padding:0.4rem 1rem;color:rgba(255,255,255,0.85);border:1px solid rgba(255,255,255,0.3);border-radius:0.375rem;font-size:0.8125rem;font-weight:500;text-decoration:none;">
                            Log In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               style="padding:0.4rem 1rem;background-color:#FDB913;color:#003087;border-radius:0.375rem;font-size:0.8125rem;font-weight:600;text-decoration:none;">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    {{-- ── Hero ── --}}
    <section style="background-color:#003087;padding:4rem 1.5rem 5rem;">
        <div style="max-width:72rem;margin:0 auto;display:flex;flex-direction:column;align-items:center;text-align:center;gap:1.5rem;">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;background-color:rgba(253,185,19,0.15);border:1px solid rgba(253,185,19,0.35);border-radius:9999px;padding:0.3rem 0.9rem;font-size:0.75rem;color:#FDB913;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                Department of Science and Technology — Surigao del Norte
            </div>
            <h1 style="color:#ffffff;font-size:clamp(1.75rem,5vw,3rem);font-weight:700;line-height:1.2;max-width:44rem;">
                Project Monitoring and<br>Information System
            </h1>
            <p style="color:rgba(255,255,255,0.7);font-size:1rem;max-width:36rem;line-height:1.7;">
                A centralized platform for tracking financial targets and physical accomplishments of DOST-funded projects under SETUP, GIA, CEST, and SSCP programs.
            </p>

            @guest
            <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin-top:0.5rem;">
                <a href="{{ route('login') }}"
                   style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.75rem;background-color:#FDB913;color:#003087;border-radius:0.5rem;font-weight:700;font-size:0.9375rem;text-decoration:none;">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In
                </a>
            </div>
            @endguest
        </div>
    </section>

    {{-- ── Wave divider ── --}}
    <div style="background-color:#003087;line-height:0;">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" style="display:block;width:100%;height:3.5rem;" fill="#F5F7FA">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z"/>
        </svg>
    </div>

    {{-- ── Feature cards ── --}}
    <section style="padding:3.5rem 1.5rem;background-color:#F5F7FA;">
        <div style="max-width:72rem;margin:0 auto;">
            <div style="text-align:center;margin-bottom:2.5rem;">
                <h2 style="font-size:1.5rem;font-weight:700;color:#003087;margin-bottom:0.5rem;">What the system covers</h2>
                <p style="color:#6B7280;font-size:0.9rem;">End-to-end monitoring from data encoding to verified reporting</p>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(15rem,1fr));gap:1.25rem;">

                {{-- Card: Financial Targets --}}
                <div style="background:#ffffff;border-radius:0.75rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.07);border-top:3px solid #003087;">
                    <div style="width:2.5rem;height:2.5rem;background-color:rgba(0,48,135,0.08);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#003087;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 style="font-weight:600;font-size:0.9375rem;color:#1A1A2E;margin-bottom:0.4rem;">Financial Targets</h3>
                    <p style="font-size:0.8125rem;color:#6B7280;line-height:1.6;">Track target, obligated, and disbursed amounts per project, year, and quarter. Enforces budget ceiling rules automatically.</p>
                </div>

                {{-- Card: Physical Accomplishments --}}
                <div style="background:#ffffff;border-radius:0.75rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.07);border-top:3px solid #FDB913;">
                    <div style="width:2.5rem;height:2.5rem;background-color:rgba(253,185,19,0.12);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#b07d00;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 style="font-weight:600;font-size:0.9375rem;color:#1A1A2E;margin-bottom:0.4rem;">Physical Accomplishments</h3>
                    <p style="font-size:0.8125rem;color:#6B7280;line-height:1.6;">Monitor accomplishment rates against targets per indicator. Automatically computes rates and flags over-achievements for review.</p>
                </div>

                {{-- Card: Verification Pipeline --}}
                <div style="background:#ffffff;border-radius:0.75rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.07);border-top:3px solid #059669;">
                    <div style="width:2.5rem;height:2.5rem;background-color:rgba(5,150,105,0.08);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 style="font-weight:600;font-size:0.9375rem;color:#1A1A2E;margin-bottom:0.4rem;">Verification Pipeline</h3>
                    <p style="font-size:0.8125rem;color:#6B7280;line-height:1.6;">Automated rule checks flag entries for verifier review. Only verified data surfaces to viewers and dashboard charts.</p>
                </div>

                {{-- Card: Document Management --}}
                <div style="background:#ffffff;border-radius:0.75rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.07);border-top:3px solid #7C3AED;">
                    <div style="width:2.5rem;height:2.5rem;background-color:rgba(124,58,237,0.08);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#7C3AED;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 style="font-weight:600;font-size:0.9375rem;color:#1A1A2E;margin-bottom:0.4rem;">Document Management</h3>
                    <p style="font-size:0.8125rem;color:#6B7280;line-height:1.6;">Upload GAA, financial plans, and work plans. PDF text is extracted automatically to validate indicator names against project documents.</p>
                </div>

                {{-- Card: Reports --}}
                <div style="background:#ffffff;border-radius:0.75rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.07);border-top:3px solid #DC2626;">
                    <div style="width:2.5rem;height:2.5rem;background-color:rgba(220,38,38,0.08);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 style="font-weight:600;font-size:0.9375rem;color:#1A1A2E;margin-bottom:0.4rem;">Reports & Exports</h3>
                    <p style="font-size:0.8125rem;color:#6B7280;line-height:1.6;">Generate financial summary, physical accomplishment, and verification audit reports as Excel spreadsheets or PDF documents.</p>
                </div>

                {{-- Card: Programs --}}
                <div style="background:#ffffff;border-radius:0.75rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.07);border-top:3px solid #0284C7;">
                    <div style="width:2.5rem;height:2.5rem;background-color:rgba(2,132,199,0.08);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#0284C7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 style="font-weight:600;font-size:0.9375rem;color:#1A1A2E;margin-bottom:0.4rem;">Programs Covered</h3>
                    <p style="font-size:0.8125rem;color:#6B7280;line-height:1.6;">
                        Supports four DOST programs:
                        <span style="font-weight:600;color:#003087;">SETUP</span> (Small Enterprise Technology Upgrading),
                        <span style="font-weight:600;color:#003087;">GIA</span> (Grant-in-Aid),
                        <span style="font-weight:600;color:#003087;">CEST</span> (Community Empowerment through S&T), and
                        <span style="font-weight:600;color:#003087;">SSCP</span> (Smart and Sustainable Communities).
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Role badges ── --}}
    <section style="padding:2.5rem 1.5rem;background-color:#ffffff;border-top:1px solid #E5E7EB;border-bottom:1px solid #E5E7EB;">
        <div style="max-width:72rem;margin:0 auto;text-align:center;">
            <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9CA3AF;font-weight:600;margin-bottom:1.25rem;">System Roles</p>
            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:0.75rem;">
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;background-color:#EDE9FE;color:#5B21B6;border-radius:9999px;font-size:0.8125rem;font-weight:600;">
                    <svg style="width:0.875rem;height:0.875rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    Admin
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;background-color:#DBEAFE;color:#1D4ED8;border-radius:9999px;font-size:0.8125rem;font-weight:600;">
                    <svg style="width:0.875rem;height:0.875rem;" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    Encoder
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;background-color:#D1FAE5;color:#065F46;border-radius:9999px;font-size:0.8125rem;font-weight:600;">
                    <svg style="width:0.875rem;height:0.875rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Verifier
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;background-color:#F3F4F6;color:#374151;border-radius:9999px;font-size:0.8125rem;font-weight:600;">
                    <svg style="width:0.875rem;height:0.875rem;" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                    Viewer
                </span>
            </div>
        </div>
    </section>

    {{-- ── Footer ── --}}
    <footer style="margin-top:auto;background-color:#003087;padding:1.5rem;text-align:center;">
        <p style="color:rgba(255,255,255,0.5);font-size:0.75rem;">
            &copy; {{ date('Y') }} Department of Science and Technology - Surigao del Norte. All rights reserved.
        </p>
        <p style="color:rgba(253,185,19,0.6);font-size:0.7rem;margin-top:0.25rem;letter-spacing:0.04em;text-transform:uppercase;">
            DOST-SDN Project Monitoring and Information System
        </p>
    </footer>

</body>
</html>
