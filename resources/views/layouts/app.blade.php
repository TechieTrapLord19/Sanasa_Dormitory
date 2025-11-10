<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sanasa Dormitory') }} - @yield('title')</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

<style>
    body {
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }

    .app-sidebar {
        background-color:rgb(7, 42, 97) !important;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1000;
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
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    a:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }
    a:focus {
        outline: none;

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
            <nav class="flex-grow-1 py-3" style="overflow-y: auto; overflow-x: hidden;">
                <ul class="list-unstyled px-2 mb-0">
                    <li class="mb-1 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Dashboard
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('bookings.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Bookings
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('tenants*') ? 'active' : '' }}">
                        <a href="{{ route('tenants') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Tenants
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('invoices') ? 'active' : '' }}">
                        <a href="{{ route('invoices') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Invoices
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('payments') ? 'active' : '' }}">
                        <a href="{{ route('payments') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Payments
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                        <a href="{{ route('rooms.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Rooms
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('rates.*') ? 'active' : '' }}">
                        <a href="{{ route('rates.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Rates
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('electric-readings') ? 'active' : '' }}">
                        <a href="{{ route('electric-readings') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Electric Readings
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('maintenance-logs') ? 'active' : '' }}">
                        <a href="{{ route('maintenance-logs') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Maintenance Logs
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('asset-inventory') ? 'active' : '' }}">
                        <a href="{{ route('asset-inventory') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            Asset Inventory
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('user-management') ? 'active' : '' }}">
                        <a href="{{ route('user-management') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            User Management
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
</html>
