<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sanasa Dormitory') }} - @yield('title')</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <style>
    body {
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }

    .app-sidebar {
        background-color: rgb(7, 42, 97) !important;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        width: 256px;
        box-sizing: border-box;
    }

    /* Header (logo) - fixed height */
    .app-sidebar > .border-bottom {
        height: 80px;
        flex-shrink: 0;
    }

    /* Make nav use remaining vertical space and allow internal scrolling */
    .app-sidebar nav {
        flex: 1 1 auto;
        overflow-y: auto;
        padding-top: 8px;
        padding-bottom: 8px;
        -webkit-overflow-scrolling: touch;
        /* reserve a little right padding so the scrollbar doesn't overlap link text */
        padding-right: 10px;
    }

    /* Compact list typography and spacing so items fit within sidebar height */
    .app-sidebar ul {
        margin: 0;
        padding: 0.25rem 0.5rem;
        list-style: none;
        font-size: 0.9rem;             /* slightly smaller base font */
        line-height: 1.1;             /* tighter line height */
    }

    /* Section headers smaller and tighter */
    .app-sidebar .text-white-50.small {
        font-size: 0.72rem;
        letter-spacing: 0.02em;
        padding-left: 0.5rem;
        margin-bottom: 0.4rem;
    }

    /* Compact link paddings to reduce vertical space usage */
    .app-sidebar li.mb-1 > a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.6rem;
        border-radius: 6px;
        font-size: 0.92rem;
        white-space: nowrap;
    }

    /* Reduce hr spacing */
    .app-sidebar hr {
        margin: 0.4rem 0;
        border-color: rgba(255, 255, 255, 0.08);
    }

    /* Keep bottom auth panel fixed height and prevent it from growing */
    .app-sidebar > .border-top {
        flex-shrink: 0;
        padding: 0.6rem;
    }

    .main-content-wrapper {
        margin-left: 256px;
        width: calc(100% - 256px);
        min-height: 100vh;
        overflow-y: auto;
    }

    .active > a {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
    .active > a:hover {
        background-color: rgba(255, 255, 255, 0.3);
    }
    a {
        transition: background-color 0.18s ease, color 0.18s ease;
    }
    a:hover {
        background-color: rgba(255, 255, 255, 0.07);
        color: #ffffff;
    }
    a:focus {
        outline: none;
    }

    /* ---------- Custom scrollbars ---------- */

    /* Sidebar scrollbar (WebKit) */
    .app-sidebar nav::-webkit-scrollbar {
        width: 8px;
    }
    .app-sidebar nav::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.02); /* subtle track that blends with sidebar */
        border-radius: 6px;
    }
    .app-sidebar nav::-webkit-scrollbar-thumb {
        background-color: rgba(255,255,255,0.12); /* light translucent thumb */
        border-radius: 6px;
    }
    .app-sidebar nav::-webkit-scrollbar-thumb:hover {
        background-color: rgba(255,255,255,0.18);
    }

    /* Sidebar scrollbar (Firefox) */
    .app-sidebar nav {
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.12) rgba(255,255,255,0.02);
    }

    /* Main content scrollbar - neutral grey */
    .main-content-wrapper::-webkit-scrollbar {
        width: 10px;
    }
    .main-content-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .main-content-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 6px;
    }
    .main-content-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    .main-content-wrapper {
        scrollbar-color: #c1c1c1 #f1f1f1;
        scrollbar-width: thin;
    }

    /* Optional: prevent layout shift when sidebar scrollbar appears in some browsers */
    @supports (scrollbar-gutter: stable) {
        .app-sidebar { scrollbar-gutter: stable; }
    }
    html {
    scroll-behavior: smooth;
}

    .app-sidebar nav {
        scroll-behavior: smooth;
    }
    .app-sidebar > .border-bottom img {
    transition: opacity 0.2s ease;
    will-change: opacity;
}

    /* Prevent layout shift during image load */
    .app-sidebar > .border-bottom {
        background-color: rgb(7, 42, 97);
    }
</style>

</head>
<body class="bg-white">
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="app-sidebar bg-primary text-white d-flex flex-column" style="width: 256px;">
            <div class="border-bottom d-flex align-items-center" style="height: 80px; flex-shrink: 0;">
                <img src="{{ asset('images/Logo1.png') }}" alt="Sanasa Dormitory" class="img-fluid" style="max-height: 100%; width: 100%;" >
            </div>
            <nav class="flex-grow-1 py-3" style="overflow-y: auto; overflow-x: hidden;" role="navigation" aria-label="Main sidebar">
                <ul class="list-unstyled px-2 mb-0">
                    <!-- MAIN -->
                    <li class="text-white-50 small px-3 mb-2">MAIN</li>
                    <li class="mb-1 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <hr class="my-2 border-white-10">

                    <!-- MANAGEMENT -->
                    <li class="text-white-50 small px-3 mb-2">MANAGEMENT</li>
                    <li class="mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('bookings.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-calendar-check"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('tenants*') ? 'active' : '' }}">
                        <a href="{{ route('tenants') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-people"></i>
                            <span>Tenants</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                        <a href="{{ route('rooms.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-door-closed"></i>
                            <span>Rooms</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('rates.*') ? 'active' : '' }}">
                        <a href="{{ route('rates.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-tag"></i>
                            <span>Rates</span>
                        </a>
                    </li>

                    <hr class="my-2 border-white-10">

                    <!-- FINANCIAL -->
                    <li class="text-white-50 small px-3 mb-2">FINANCIAL</li>
                    <li class="mb-1 {{ request()->routeIs('invoices') ? 'active' : '' }}">
                        <a href="{{ route('invoices') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-receipt"></i>
                            <span>Invoices</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('payments') ? 'active' : '' }}">
                        <a href="{{ route('payments') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>

                    <hr class="my-2 border-white-10">

                    <!-- UTILITIES -->
                    <li class="text-white-50 small px-3 mb-2">UTILITIES</li>
                    <li class="mb-1 {{ request()->routeIs('electric-readings') ? 'active' : '' }}">
                        <a href="{{ route('electric-readings') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-lightning-charge"></i>
                            <span>Electric Readings</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('maintenance-logs') ? 'active' : '' }}">
                        <a href="{{ route('maintenance-logs') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-tools"></i>
                            <span>Maintenance Logs</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('asset-inventory') ? 'active' : '' }}">
                        <a href="{{ route('asset-inventory') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-box-seam"></i>
                            <span>Asset Inventory</span>
                        </a>
                    </li>

                    <hr class="my-2 border-white-10">

                    <!-- ADMIN -->
                    <li class="text-white-50 small px-3 mb-2">ADMIN</li>
                    <li class="mb-1 {{ request()->routeIs('user-management') ? 'active' : '' }}">
                        <a href="{{ route('user-management') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-shield-lock"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                </ul>
            </nav>
            @auth
            <div class="border-top border-primary border-opacity-50 p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle bg-primary bg-opacity-50 d-flex align-items-center justify-content-center text-white fw-semibold" style="width: 40px; height: 40px; font-size: 14px;">
                        {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="text-white fw-medium text-truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div class="text-white-50 small text-truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm w-100 bg-primary bg-opacity-50 border-0">Sign out</button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Main content -->
        <div class="main-content-wrapper">
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="position-fixed bottom-0 end-0 m-3 alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="position-fixed bottom-0 end-0 m-3 alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    // Save sidebar scroll position before navigation
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.app-sidebar nav');
        const logo = document.querySelector('.app-sidebar img');

        // Restore scroll position from sessionStorage
        const savedScrollPos = sessionStorage.getItem('sidebarScrollPos');
        if (savedScrollPos && sidebar) {
            sidebar.scrollTop = parseInt(savedScrollPos);
        }

        // Prevent logo image flicker by preloading
        if (logo) {
            logo.style.opacity = '1';
            logo.style.transition = 'opacity 0.2s ease';
        }

        // Save scroll position when user scrolls
        if (sidebar) {
            sidebar.addEventListener('scroll', function() {
                sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });

            // Save scroll position and disable transitions before page unload
            document.querySelectorAll('.app-sidebar a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Save the current scroll position
                    sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);



                });
            });
        }

        // Use the browser's bfcache to preserve scroll on back button
        window.addEventListener('pageshow', function(event) {
            if (event.persisted && sidebar && savedScrollPos) {
                sidebar.scrollTop = parseInt(sessionStorage.getItem('sidebarScrollPos'));
            }
            if (logo) {
                logo.style.opacity = '1';
            }
        });

        window.addEventListener('pagehide', function(event) {
            if (sidebar) {
                sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            }
        });
    });
</script>
</html>
