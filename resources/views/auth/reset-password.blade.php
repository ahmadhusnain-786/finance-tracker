@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-3">Reset your password</h1>

                    <form method="POST" action="{{ route('password.store') }}" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email', $request->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-key me-2"></i>Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection