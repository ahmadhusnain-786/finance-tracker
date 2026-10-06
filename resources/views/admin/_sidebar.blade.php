<aside class="admin-sidebar p-3" id="adminSidebar">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center brand text-decoration-none mb-4 px-2">
        <i class="bi bi-shield-lock fs-4 me-2"></i>{{ config('app.name') }} <span class="badge bg-danger ms-2">Admin</span>
    </a>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Overview
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="bi bi-people"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') }}">
                <i class="bi bi-tags"></i> Default Categories
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.index') }}">
                <i class="bi bi-gear"></i> System Settings
            </a>
        </li>
    </ul>

    <hr class="text-secondary">

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="bi bi-arrow-left-circle"></i> Back to App
            </a>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</aside>