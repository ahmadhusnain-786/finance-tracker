<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
            <i class="bi bi-wallet2 me-2"></i>{{ config('app.name') }}
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active fw-semibold' : '' }}"
                       href="{{ url('/') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('features') ? 'active fw-semibold' : '' }}"
                       href="{{ url('/features') }}">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('about') ? 'active fw-semibold' : '' }}"
                       href="{{ url('/about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact') ? 'active fw-semibold' : '' }}"
                       href="{{ url('/contact') }}">Contact</a>
                </li>

                @auth
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm px-3">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>