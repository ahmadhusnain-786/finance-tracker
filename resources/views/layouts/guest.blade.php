<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Home') — {{ config('app.name') }}</title>

    {{-- Theme init: must run before paint --}}
    <script>
        (function () {
            const t = localStorage.getItem('ft-theme')
                || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @include('partials.ui-enhancements')

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <x-navbar />

    <main class="flex-grow-1">
        <div class="container mt-3">
            <x-flash />
        </div>
        @yield('content')
    </main>

    <footer class="bg-dark text-white-50 py-4 mt-auto">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center small">
            <div>
                &copy; {{ date('Y') }} <span class="text-white fw-semibold">{{ config('app.name') }}</span>. All rights reserved.
            </div>
            <div class="mt-2 mt-md-0 d-flex align-items-center gap-3">
                <a href="{{ url('/about') }}" class="text-white-50 text-decoration-none">About</a>
                <a href="{{ url('/features') }}" class="text-white-50 text-decoration-none">Features</a>
                <a href="{{ url('/contact') }}" class="text-white-50 text-decoration-none">Contact</a>
                <x-theme-toggle />
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>