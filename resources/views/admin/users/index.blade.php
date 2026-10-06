@extends('admin.layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       class="form-control" placeholder="Name or email...">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Role</label>
                <select name="role" class="form-select">
                    <option value="">All roles</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->value }}" {{ $filters['role'] === $r->value ? 'selected' : '' }}>
                            {{ $r->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active"   {{ $filters['status'] === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- List --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">All Users <span class="text-muted">({{ $users->total() }})</span></span>
    </div>
    <div class="card-body p-0">
        @if ($users->isEmpty())
            <x-empty-state icon="bi-people" title="No users found" message="Try clearing the filters." />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Joined</th>
                            <th class="text-end">Last Login</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr class="{{ ! $u->is_active ? 'opacity-75' : '' }}">
                                <td class="fw-semibold">
                                    <a href="{{ route('admin.users.show', $u) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $u->name }}
                                    </a>
                                </td>
                                <td class="small text-muted">{{ $u->email }}</td>
                                <td>
                                    @if ($u->isAdmin())
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Admin</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">User</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->is_active)
                                        <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end small text-muted">{{ $u->created_at->format('d M Y') }}</td>
                                <td class="text-end small text-muted">
                                    {{ $u->last_login_at?->diffForHumans() ?? 'Never' }}
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.users.show', $u) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if ($u->id !== auth()->id())
                                        <form method="POST"
                                              action="{{ route('admin.users.toggle-active', $u) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('{{ $u->is_active ? 'Deactivate' : 'Activate' }} this user?');">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-{{ $u->is_active ? 'danger' : 'success' }}"
                                                    title="{{ $u->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi bi-{{ $u->is_active ? 'person-slash' : 'person-check' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection