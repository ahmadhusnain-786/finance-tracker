@extends('admin.layouts.app')

@section('title', $user->name)
@section('page-title', 'User Details')

@section('content')

<div class="row g-3">

    {{-- Left: user info + edit form --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Account</span>
                @if ($user->isAdmin())
                    <span class="badge bg-danger-subtle text-danger-emphasis">Admin</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">User</span>
                @endif
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle display-3 text-primary"></i>
                    <h5 class="mt-2 mb-0">{{ $user->name }}</h5>
                    <div class="text-muted small">{{ $user->email }}</div>
                </div>

                <hr>

                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Joined</span>
                    <span>{{ $user->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Last login</span>
                    <span>{{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}</span>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Status</span>
                    <span>
                        @if ($user->is_active)
                            <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger-emphasis">Inactive</span>
                        @endif
                    </span>
                </div>

                @if ($user->profile)
                    <hr>
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Profile</div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Full name</span>
                        <span>{{ $user->profile->full_name ?: '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Currency</span>
                        <span>{{ $user->profile->currency }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Timezone</span>
                        <span>{{ $user->profile->timezone }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><span class="fw-semibold">Manage User</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required maxlength="100">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role"
                                class="form-select @error('role') is-invalid @enderror"
                                @if ($user->id === auth()->id()) disabled @endif>
                            <option value="user"  {{ old('role', $user->role->value) === 'user'  ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role->value) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role->value }}">
                            <div class="form-text text-warning">
                                <i class="bi bi-shield-exclamation me-1"></i>
                                You cannot change your own role.
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   @if ($user->id === auth()->id()) disabled @endif>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="{{ $user->is_active ? 1 : 0 }}">
                            <div class="form-text text-warning">
                                <i class="bi bi-shield-exclamation me-1"></i>
                                You cannot deactivate your own account.
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: activity summary --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <span class="fw-semibold">Activity Summary</span>
                <span class="text-muted small ms-2">Counts only — no financial amounts shown</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <div class="border rounded p-3 text-center">
                            <i class="bi bi-cash-stack fs-3 text-success"></i>
                            <div class="fs-4 fw-bold mt-1">{{ $counts['income'] }}</div>
                            <div class="text-muted small">Income records</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="border rounded p-3 text-center">
                            <i class="bi bi-receipt fs-3 text-danger"></i>
                            <div class="fs-4 fw-bold mt-1">{{ $counts['expense'] }}</div>
                            <div class="text-muted small">Expense records</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="border rounded p-3 text-center">
                            <i class="bi bi-pie-chart fs-3 text-warning"></i>
                            <div class="fs-4 fw-bold mt-1">{{ $counts['budgets'] }}</div>
                            <div class="text-muted small">Budgets</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="border rounded p-3 text-center">
                            <i class="bi bi-bullseye fs-3 text-primary"></i>
                            <div class="fs-4 fw-bold mt-1">{{ $counts['goals'] }}</div>
                            <div class="text-muted small">Goals</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="border rounded p-3 text-center">
                            <i class="bi bi-arrow-repeat fs-3 text-info"></i>
                            <div class="fs-4 fw-bold mt-1">{{ $counts['recurring'] }}</div>
                            <div class="text-muted small">Recurring</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info border-0 mt-3 small">
            <i class="bi bi-shield-check me-2"></i>
            <strong>Privacy note:</strong> Even administrators cannot see other users' financial amounts.
            Only the count of records is visible for support and diagnostic purposes.
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Users
            </a>
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}"
                      onsubmit="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this user?');">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'danger' : 'success' }}">
                        <i class="bi bi-{{ $user->is_active ? 'person-slash' : 'person-check' }} me-1"></i>
                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection