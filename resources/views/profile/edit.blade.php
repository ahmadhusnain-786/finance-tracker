@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Profile Information</h5>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center mb-4">
                        @if ($profile->avatar)
                            <img src="{{ asset('storage/' . $profile->avatar) }}"
                                 alt="Avatar" class="rounded-circle me-3"
                                 style="width:72px;height:72px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center me-3"
                                 style="width:72px;height:72px;">
                                <i class="bi bi-person fs-1 text-secondary"></i>
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" id="avatar" name="avatar"
                                   class="form-control @error('avatar') is-invalid @enderror"
                                   accept="image/*">
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">JPG, PNG, or WebP. Max 2 MB.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name (Login Display)</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                        <div class="form-text">To change login name, contact support (kept stable for security).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                               value="{{ old('full_name', $profile->full_name) }}"
                               class="form-control @error('full_name') is-invalid @enderror">
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="phone" name="phone"
                               value="{{ old('phone', $profile->phone) }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="+92 300 1234567">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection