<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    {{-- Theme init: must run before paint to avoid flash --}}
    <script>
        (function () {
            const t = localStorage.getItem('ft-theme')
                || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        body { background-color: #f4f6f9; }
        .app-sidebar {
            width: 250px;
            min-height: 100vh;
            background: #1e293b;
        }
        .app-sidebar .nav-link {
            color: #cbd5e1;
            border-radius: .5rem;
            padding: .6rem .9rem;
            margin-bottom: .15rem;
            font-size: .95rem;
        }
        .app-sidebar .nav-link:hover {
            background: #334155;
            color: #fff;
        }
        .app-sidebar .nav-link.active {
            background: #2563eb;
            color: #fff;
            font-weight: 600;
        }
        .app-sidebar .nav-link i { width: 22px; display: inline-block; }
        .app-sidebar .brand {
            color: #fff;
            font-weight: 700;
            letter-spacing: .3px;
        }
        @media (max-width: 991.98px) {
            .app-sidebar {
                position: fixed;
                top: 0; left: -260px;
                z-index: 1040;
                transition: left .2s ease-in-out;
            }
            .app-sidebar.show { left: 0; }
            .sidebar-backdrop {
                position: fixed; inset: 0;
                background: rgba(0,0,0,.4);
                z-index: 1039;
                display: none;
            }
            .sidebar-backdrop.show { display: block; }
        }
    </style>

    @include('partials.ui-enhancements')

    @stack('styles')
</head>
<body>

<div class="d-flex">

    {{-- Sidebar --}}
    <aside class="app-sidebar p-3" id="appSidebar">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center brand text-decoration-none mb-4 px-2">
            <i class="bi bi-wallet2 fs-4 me-2"></i>{{ config('app.name') }}
        </a>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @if (auth()->user()?->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-shield-lock"></i> Admin Panel
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('income.*') ? 'active' : '' }}"
                   href="{{ route('income.index') }}">
                    <i class="bi bi-cash-stack"></i> Income
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"
                   href="{{ route('expenses.index') }}">
                    <i class="bi bi-receipt"></i> Expenses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                   href="{{ route('transactions.index') }}">
                    <i class="bi bi-list-ul"></i> Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"
                   href="{{ route('budgets.index') }}">
                    <i class="bi bi-pie-chart"></i> Budgets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}"
                   href="{{ route('goals.index') }}">
                    <i class="bi bi-bullseye"></i> Goals
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('recurring.*') ? 'active' : '' }}"
                   href="{{ route('recurring.index') }}">
                    <i class="bi bi-arrow-repeat"></i> Recurring
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                   href="{{ route('reports.index') }}">
                    <i class="bi bi-graph-up"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                   href="{{ route('categories.index') }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}"
                   href="{{ route('payment-methods.index') }}">
                    <i class="bi bi-credit-card"></i> Payment Methods
                </a>
            </li>
        </ul>

        <hr class="text-secondary">

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                   href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                   href="{{ route('settings.edit') }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" data-no-loading="true">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    {{-- Mobile backdrop --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    {{-- Main --}}
    <div class="flex-grow-1 min-vh-100 d-flex flex-column">

        {{-- Top bar --}}
        <header class="bg-white border-bottom shadow-sm px-3 px-md-4 py-2 d-flex align-items-center">
            <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <h5 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h5>

            <div class="ms-auto d-flex align-items-center gap-2">
                <x-theme-toggle />

                <x-notification-bell />

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'Guest' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.edit') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i>Notifications</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" data-no-loading="true">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-grow-1 p-3 p-md-4">
            <x-flash />
            @yield('content')
        </main>

        <footer class="bg-white border-top text-muted small py-3 px-4">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </footer>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

<script>
    // Sidebar toggle for mobile
    const sidebar   = document.getElementById('appSidebar');
    const backdrop  = document.getElementById('sidebarBackdrop');
    const toggle    = document.getElementById('sidebarToggle');

    function closeSidebar() {
        sidebar?.classList.remove('show');
        backdrop?.classList.remove('show');
    }
    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
    });
    backdrop?.addEventListener('click', closeSidebar);
</script>

@stack('scripts')
</body>
</html>