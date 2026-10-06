@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                        <h1 class="h4 fw-bold mt-2 mb-1">Create your account</h1>
                        <p class="text-muted small mb-0">Start tracking your finances in minutes.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required autofocus autocomplete="name">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autocomplete="username">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Minimum 8 characters, letters and numbers.</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-check me-2"></i>Create Account
                        </button>
                    </form>

                    <p class="text-center text-muted small mt-4 mb-0">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-decoration-none">Log in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection