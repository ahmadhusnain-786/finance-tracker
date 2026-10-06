@extends('layouts.app')

@section('title', 'Payment Methods')
@section('page-title', 'Payment Methods')

@section('content')

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('payment-methods.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       class="form-control" placeholder="Search by name...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Scope</label>
                <select name="scope" class="form-select">
                    <option value="">All</option>
                    <option value="mine"   {{ $filters['scope'] === 'mine'   ? 'selected' : '' }}>My methods</option>
                    <option value="system" {{ $filters['scope'] === 'system' ? 'selected' : '' }}>System</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active"   {{ $filters['status'] === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-grid d-md-flex gap-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('payment-methods.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Payment Methods <span class="text-muted">({{ $methods->total() }})</span></span>
        <a href="{{ route('payment-methods.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Method
        </a>
    </div>

    <div class="card-body p-0">
        @if ($methods->isEmpty())
            <x-empty-state
                icon="bi-credit-card"
                title="No payment methods found"
                message="Try clearing the filters, or add your first payment method."
                action-text="New Method"
                :action-url="route('payment-methods.create')" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-center">Used</th>
                            <th>Status</th>
                            <th>Scope</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($methods as $method)
                            <tr>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <i class="bi {{ $method->type->icon() }} text-primary"></i>
                                        <span class="fw-semibold">{{ $method->name }}</span>
                                    </span>
                                    @if ($method->description)
                                        <div class="text-muted small">{{ $method->description }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        {{ $method->type->label() }}
                                    </span>
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $method->income_count + $method->expenses_count }}
                                </td>
                                <td>
                                    @if ($method->is_active)
                                        <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($method->is_system)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">System</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary-emphasis">Mine</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @can('update', $method)
                                        <a href="{{ route('payment-methods.edit', $method) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan

                                    @can('delete', $method)
                                        <form method="POST"
                                              action="{{ route('payment-methods.destroy', $method) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this payment method? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($methods->hasPages())
        <div class="card-footer bg-white">
            {{ $methods->links() }}
        </div>
    @endif
</div>

@endsection