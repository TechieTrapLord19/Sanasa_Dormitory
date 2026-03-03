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
                    <li class="mb-1 {{ request()->routeIs('security-deposits.*') ? 'active' : '' }}">
                        <a href="{{ route('security-deposits.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-shield-check"></i>
                            <span>Security Deposits</span>
                        </a>
                    </li>
                    @if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
                    <li class="mb-1 {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <a href="{{ route('expenses.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-wallet2"></i>
                            <span>Expenses</span>
                        </a>
                    </li>
                    @endif

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

                    <!-- REPORTS -->
                    @if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
                    <li class="text-white-50 small px-3 mb-2">REPORTS</li>
                    <li class="mb-1 {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <a href="{{ route('sales.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Sales & Reports</span>
                        </a>
                    </li>
                    <li class="mb-1 {{ request()->routeIs('financial-statement') ? 'active' : '' }}">
                        <a href="{{ route('financial-statement') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-bar-chart-line"></i>
                            <span>Financial Statement</span>
                        </a>
                    </li>
                    @endif

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
                    <li class="mb-1 {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    @endif

                    <!-- ACCOUNT — available to all users -->
                    <hr class="my-2 border-white-10">
                    <li class="text-white-50 small px-3 mb-2">MY ACCOUNT</li>
                    <li class="mb-1 {{ request()->routeIs('account') ? 'active' : '' }}">
                        <a href="{{ route('account') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-person-circle"></i>
                            <span>My Account</span>
                        </a>
                    </li>

                    <!-- 2FA — available to all users -->
                    <li class="mb-1 {{ request()->routeIs('two-factor.setup') ? 'active' : '' }}">
                        <a href="{{ route('two-factor.setup') }}" class="d-flex align-items-center gap-2 px-3 py-2 rounded text-white text-decoration-none">
                            <i class="bi bi-phone-vibrate"></i>
                            <span>
                                2FA Setup
                                @if(auth()->user()->two_factor_enabled)
                                    <span class="badge bg-success ms-1" style="font-size:0.65rem;">ON</span>
                                @endif
                            </span>
                        </a>
                    </li>
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
            <!-- Top Header Bar with Notifications -->
            @auth
            @php
                $notificationService = app(\App\Services\NotificationService::class);
                $notifications = $notificationService->getAllNotifications();
                $totalNotifications = count($notifications);
                $urgentCount = collect($notifications)->whereIn('urgency', ['danger', 'warning'])->count();
            @endphp
            <div class="top-header-bar">
                <div class="header-left">
                    <span class="current-date">{{ now()->format('l, F d, Y') }} • <span id="current-time">{{ now()->format('g:i A') }}</span></span>
                </div>
                <div class="header-right">
                    <!-- Notification Bell -->
                    <div class="notification-dropdown">
                        <button class="notification-bell" id="notificationBell" onclick="toggleNotifications()">
                            <i class="bi bi-bell"></i>
                            @if($totalNotifications > 0)
                                <span class="notification-badge {{ $urgentCount > 0 ? 'urgent' : '' }}">{{ $totalNotifications > 99 ? '99+' : $totalNotifications }}</span>
                            @endif
                        </button>

                        <div class="notification-panel" id="notificationPanel">
                            <div class="notification-header">
                                <h6><i class="bi bi-bell me-2"></i>Notifications</h6>
                                <span class="notification-count">{{ $totalNotifications }} alerts</span>
                            </div>

                            <div class="notification-body">
                                @if($totalNotifications === 0)
                                    <div class="notification-empty">
                                        <i class="bi bi-check-circle"></i>
                                        <p>All caught up!</p>
                                        <small>No pending notifications</small>
                                    </div>
                                @else
                                    @foreach(array_slice($notifications, 0, 8) as $notification)
                                        <a href="{{ $notification['link'] }}" class="notification-item {{ $notification['urgency'] }}">
                                            <div class="notification-icon">
                                                <i class="bi {{ $notification['icon'] }}"></i>
                                            </div>
                                            <div class="notification-content">
                                                <div class="notification-title">{{ $notification['title'] }}</div>
                                                <div class="notification-message">{{ $notification['message'] }}</div>
                                                <div class="notification-detail">{{ $notification['detail'] }}</div>
                                            </div>
                                        </a>
                                    @endforeach
                                @endif
                            </div>

                            @if($totalNotifications > 8)
                                <div class="notification-footer">
                                    <a href="{{ route('dashboard') }}">View all {{ $totalNotifications }} notifications</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast align-items-center text-bg-warning border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4500">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="toast align-items-center text-bg-info border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Bootstrap JS - Try CDN first, fallback to local -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer onerror="this.onerror=null;this.src='{{ asset('build/assets/app-BhWFyjkN.js') }}'"></script>

</body>
<script>
    // Notification toggle function
    function toggleNotifications() {
        const panel = document.getElementById('notificationPanel');
        const bell = document.getElementById('notificationBell');

        if (panel) {
            panel.classList.toggle('show');
            bell.classList.toggle('active');
        }
    }

    // Close notification panel when clicking outside
    document.addEventListener('click', function(event) {
        const panel = document.getElementById('notificationPanel');
        const bell = document.getElementById('notificationBell');

        if (panel && bell && !bell.contains(event.target) && !panel.contains(event.target)) {
            panel.classList.remove('show');
            bell.classList.remove('active');
        }
    });

    // Initialize Bootstrap toasts
    document.addEventListener('DOMContentLoaded', function() {
        const toastElList = document.querySelectorAll('.toast');
        toastElList.forEach(function(toastEl) {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    });

    // Global toast function for JavaScript use
    window.showToast = function(message, type = 'success', duration = 4000) {
        const container = document.querySelector('.toast-container') || createToastContainer();
        const icons = {
            success: 'bi-check-circle',
            error: 'bi-exclamation-circle',
            warning: 'bi-exclamation-triangle',
            info: 'bi-info-circle'
        };
        const bgClass = type === 'error' ? 'text-bg-danger' : `text-bg-${type}`;
        const btnClass = ['success', 'error', 'danger'].includes(type) ? 'btn-close-white' : '';

        const toastHtml = `
            <div class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icons[type] || icons.info} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close ${btnClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHtml);
        const toastEl = container.lastElementChild;
        const toast = new bootstrap.Toast(toastEl, { delay: duration });
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
    };

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
        return container;
    }

    // Global confirmation dialog
    window.confirmAction = function(message, callback, options = {}) {
        const title = options.title || 'Confirm Action';
        const confirmText = options.confirmText || 'Confirm';
        const cancelText = options.cancelText || 'Cancel';
        const type = options.type || 'warning'; // warning, danger, info

        const bgClass = type === 'danger' ? 'bg-danger' : (type === 'warning' ? 'bg-warning' : 'bg-primary');
        const textClass = type === 'warning' ? 'text-dark' : 'text-white';
        const btnClass = type === 'danger' ? 'btn-danger' : (type === 'warning' ? 'btn-warning' : 'btn-primary');

        const modalHtml = `
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header ${bgClass} ${textClass}">
                            <h5 class="modal-title"><i class="bi bi-question-circle me-2"></i>${title}</h5>
                            <button type="button" class="btn-close ${type !== 'warning' ? 'btn-close-white' : ''}" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancelText}</button>
                            <button type="button" class="btn ${btnClass}" id="confirmActionBtn">${confirmText}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove any existing confirm modal
        const existingModal = document.getElementById('confirmModal');
        if (existingModal) existingModal.remove();

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalEl = document.getElementById('confirmModal');
        const modal = new bootstrap.Modal(modalEl);

        document.getElementById('confirmActionBtn').addEventListener('click', function() {
            modal.hide();
            if (typeof callback === 'function') callback();
        });

        modalEl.addEventListener('hidden.bs.modal', function() {
            modalEl.remove();
        });

        modal.show();
    };

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

<script>
    // Update header time every minute
    (function updateHeaderTime() {
        const timeEl = document.getElementById('current-time');
        if (timeEl) {
            setInterval(function() {
                const now = new Date();
                let hours = now.getHours();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const minutes = now.getMinutes().toString().padStart(2, '0');
                timeEl.textContent = hours + ':' + minutes + ' ' + ampm;
            }, 60000); // Update every minute
        }
    })();
</script>

<script>
    // ── Global Form Submit Protection ─────────────────────────────────────────
    // Prevents double-submission by disabling the submit button immediately
    // after the first click on any POST form in the application.
    //
    // Exclusions (will NOT be locked):
    //   • GET forms (filter/search/sort/pagination — safe to re-submit)
    //   • Forms with data-no-lock attribute (opt-out escape hatch)
    //   • The login form (has its own loading overlay already)
    // ─────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function (form) {
            // Skip GET forms (filters, search, sort) — harmless to re-submit
            if ((form.method || '').toLowerCase() === 'get') return;

            // Skip forms that opted out
            if (form.hasAttribute('data-no-lock')) return;

            // Skip the login form — it has its own loading overlay
            if (form.id === 'loginForm') return;

            form.addEventListener('submit', function () {
                const btn = form.querySelector('[type="submit"]');
                if (!btn || btn.disabled) return;

                // Capture original label so we can restore if needed
                const originalHtml = btn.innerHTML;
                const originalWidth = btn.offsetWidth;

                // Fix width so button doesn't shrink when text changes
                btn.style.minWidth = originalWidth + 'px';

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';

                // Safety net: re-enable after 15 seconds in case something goes wrong
                // (e.g. network error, JS exception, bfcache restore)
                setTimeout(function () {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    btn.style.minWidth = '';
                }, 15000);
            });
        });

        // Re-enable all buttons when navigating back (bfcache restore)
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                document.querySelectorAll('form [type="submit"]').forEach(function (btn) {
                    btn.disabled = false;
                    btn.style.minWidth = '';
                });
            }
        });
    });
</script>

<!-- AFK Idle Logout Modal -->
<div class="modal fade" id="idleWarningModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-body text-center px-4 py-5" style="background:#ffffff;">
                <div style="width:64px;height:64px;border-radius:50%;background:#fff8e1;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                    <i class="bi bi-moon-stars" style="font-size:1.75rem;color:#d97706;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0f172a;">Are you still there?</h5>
                <p class="mb-4" style="font-size:0.9rem;color:#64748b;line-height:1.5;">
                    You've been inactive for a while.<br>For your security, you'll be logged out soon.
                </p>
                <button type="button" id="idleStayBtn" class="btn w-100 py-2 fw-semibold" style="background:#022c6e;color:#fff;border-radius:10px;font-size:0.95rem;">
                    Yes, I'm still here
                </button>
                <button type="button" id="idleLogoutBtn" class="btn w-100 mt-2 py-2 fw-semibold" style="background:transparent;color:#64748b;border:1px solid #e2e8f0;border-radius:10px;font-size:0.9rem;">
                    Log me out
                </button>
            </div>
        </div>
    </div>
</div>
<form id="idleLogoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
<script>
(function () {
    var IDLE_MINUTES = 1, WARN_SECONDS = 60;
    var idleTimer = null, countdownTimer = null, modal = null;
    var stayBtn = document.getElementById('idleStayBtn');
    var logoutBtn = document.getElementById('idleLogoutBtn');
    var logoutForm = document.getElementById('idleLogoutForm');
    var remaining = WARN_SECONDS;

    function resetIdle() {
        clearTimeout(idleTimer);
        idleTimer = setTimeout(showWarning, IDLE_MINUTES * 60 * 1000);
    }

    function showWarning() {
        remaining = WARN_SECONDS;
        if (!modal) modal = new bootstrap.Modal(document.getElementById('idleWarningModal'));
        modal.show();
        countdownTimer = setInterval(function () {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                localStorage.setItem('idle_logout', '1');
                logoutForm.submit();
            }
        }, 1000);
    }

    function dismissWarning() {
        clearInterval(countdownTimer);
        if (modal) modal.hide();
        resetIdle();
    }

    function doLogout() {
        clearInterval(countdownTimer);
        localStorage.setItem('idle_logout', '1');
        logoutForm.submit();
    }

    if (stayBtn) stayBtn.addEventListener('click', dismissWarning);
    if (logoutBtn) logoutBtn.addEventListener('click', doLogout);
    ['mousemove', 'keydown', 'mousedown', 'touchstart', 'scroll', 'click'].forEach(function (e) {
        document.addEventListener(e, resetIdle, { passive: true });
    });
    resetIdle();
})();
</script>
</html>
