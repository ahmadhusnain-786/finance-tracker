@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-1">Forgot your password?</h1>
                    <p class="text-muted small mb-4">
                        Enter your email and we'll send you a password reset link.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-envelope-arrow-up me-2"></i>Email Reset Link
                        </button>
                    </form>

                    <p class="text-center text-muted small mt-4 mb-0">
                        <a href="{{ route('login') }}" class="text-decoration-none">Back to login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection