<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Sanasa Dormitory</title>
    <link rel="icon" type="image/png" href="{{ asset('images/loginimage.png') }}">

    <!-- Preconnect to CDNs for faster loading -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    <!-- Try CDN first (online), fallback to local (offline) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" onerror="this.onerror=null;this.href='{{ asset('build/assets/bootstrap-app-40oqglNi.css') }}'">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet" media="print" onload="this.media='all';this.onerror=null;">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" onerror="this.onerror=null;">

    <!-- Local styles always loaded -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<body class="bg-white">
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="app-sidebar bg-primary text-white d-flex flex-column" style="width: 256px;">
            <div class="border-bottom border-secondary border-opacity-50 d-flex align-items-center" style="height: 80px; flex-shrink: 0;">
                <img src="{{ asset('images/Logo1.png') }}" alt="Sanasa Dormitory" class="img-fluid" style="max-height: 100%; width: 100%;" loading="eager" decoding="async">
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

                    <hr class="my-2 border-white-10">

                    <!-- UTILITIES -->
                    <li class="text-white-50 small px-3 mb-2">UTILITIES</li>
                    <li class="mb-1 {{ request()->routeIs('activity-logs') ? 'active' : '' }}">
                        <a href="{{ route('activity-logs') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-clock-history"></i>
                            <span>Activity Logs</span>
                        </a>
                    </li>
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
                    @if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
                    <li class="text-white-50 small px-3 mb-2">ADMIN</li>
                    <li class="mb-1 {{ request()->routeIs('user-management') ? 'active' : '' }}">
                        <a href="{{ route('user-management') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-shield-lock"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
            @auth
            <div class="border-top border-secondary border-opacity-50 p-3">
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

    <!-- Bootstrap JS - Try CDN first, fallback to local -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer onerror="this.onerror=null;this.src='{{ asset('build/assets/app-BhWFyjkN.js') }}'"></script>

</body>
<script>
    // Optimized sidebar scroll persistence
    (function() {
        'use strict';
        const SCROLL_KEY = 'sidebarScrollPos';
        const sidebar = document.querySelector('.app-sidebar nav');
        const logo = document.querySelector('.app-sidebar img');

        if (!sidebar) return;

        // Restore scroll position
        const savedPos = sessionStorage.getItem(SCROLL_KEY);
        if (savedPos) {
            sidebar.scrollTop = parseInt(savedPos, 10);
        }

        // Optimize logo loading
        if (logo) {
            logo.style.opacity = '1';
        }

        // Throttle scroll event for better performance
        let scrollTimeout;
        sidebar.addEventListener('scroll', function() {
            if (scrollTimeout) {
                window.cancelAnimationFrame(scrollTimeout);
            }
            scrollTimeout = window.requestAnimationFrame(function() {
                sessionStorage.setItem(SCROLL_KEY, sidebar.scrollTop);
            });
        }, { passive: true });

        // Save position on navigation
        document.querySelectorAll('.app-sidebar a').forEach(function(link) {
            link.addEventListener('click', function() {
                sessionStorage.setItem(SCROLL_KEY, sidebar.scrollTop);
            }, { passive: true });
        });

        // Handle bfcache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                const pos = sessionStorage.getItem(SCROLL_KEY);
                if (pos && sidebar) {
                    sidebar.scrollTop = parseInt(pos, 10);
                }
                if (logo) {
                    logo.style.opacity = '1';
                }
            }
        }, { passive: true });

        window.addEventListener('pagehide', function() {
            sessionStorage.setItem(SCROLL_KEY, sidebar.scrollTop);
        }, { passive: true });
    })();
</script>
</html>
