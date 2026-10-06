@extends('layouts.guest')

@section('title', 'Log In')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        <h1 class="h4 fw-bold mt-2 mb-1">Welcome back</h1>
                        <p class="text-muted small mb-0">Log in to your account.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autofocus autocomplete="username">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required autocomplete="current-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                        </button>
                    </form>

                    <p class="text-center text-muted small mt-4 mb-0">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-decoration-none">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection