<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>

    {{-- Theme init --}}
    <script>
        (function () {
            const t = localStorage.getItem('ft-theme')
                || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f4f6f9; }
        .admin-sidebar {
            width: 250px;
            min-height: 100vh;
            background: #7f1d1d;
        }
        .admin-sidebar .nav-link {
            color: #fecaca;
            border-radius: .5rem;
            padding: .6rem .9rem;
            margin-bottom: .15rem;
            font-size: .95rem;
        }
        .admin-sidebar .nav-link:hover { background: #991b1b; color: #fff; }
        .admin-sidebar .nav-link.active { background: #dc2626; color: #fff; font-weight: 600; }
        .admin-sidebar .nav-link i { width: 22px; display: inline-block; }
        .admin-sidebar .brand { color: #fff; font-weight: 700; letter-spacing: .3px; }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 0; left: -260px;
                z-index: 1040;
                transition: left .2s ease-in-out;
            }
            .admin-sidebar.show { left: 0; }
            .admin-backdrop {
                position: fixed; inset: 0;
                background: rgba(0,0,0,.4);
                z-index: 1039;
                display: none;
            }
            .admin-backdrop.show { display: block; }
        }
    </style>

    @include('partials.ui-enhancements')

    @stack('styles')
</head>
<body>

<div class="d-flex">
    @include('admin._sidebar')

    <div class="admin-backdrop" id="adminBackdrop"></div>

    <div class="flex-grow-1 min-vh-100 d-flex flex-column">
        <header class="bg-white border-bottom shadow-sm px-3 px-md-4 py-2 d-flex align-items-center">
            <button class="btn btn-light d-lg-none me-2" id="adminSidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <h5 class="mb-0 fw-semibold">
                @yield('page-title', 'Admin')
                <span class="badge bg-danger align-middle ms-2">Admin</span>
            </h5>

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
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-arrow-left-circle me-2"></i>Back to App</a></li>
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

        <main class="flex-grow-1 p-3 p-md-4">
            <x-flash />
            @yield('content')
        </main>

        <footer class="bg-white border-top text-muted small py-3 px-4">
            &copy; {{ date('Y') }} {{ config('app.name') }} — Admin Panel
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar  = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminBackdrop');
    const toggle   = document.getElementById('adminSidebarToggle');
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