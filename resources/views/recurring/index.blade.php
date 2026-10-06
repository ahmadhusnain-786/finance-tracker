@extends('layouts.app')

@section('title', 'Recurring')
@section('page-title', 'Recurring Transactions')

@section('content')

<div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-info-circle me-2"></i>
        The scheduler runs daily at 00:30. You can also trigger it manually.
    </div>
    <form method="POST" action="{{ route('recurring.run') }}" class="m-0">
        @csrf
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-play-fill me-1"></i>Run Due Now
        </button>
    </form>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('recurring.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Search title...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="all"      {{ $filters['status'] === 'all'      ? 'selected' : '' }}>All</option>
                    <option value="active"   {{ $filters['status'] === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
            <div class="col-md-2 text-md-end">
                <a href="{{ route('recurring.create') }}" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i>New
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($templates->isEmpty())
            <x-empty-state
                icon="bi-arrow-repeat"
                title="No recurring templates yet"
                message="Create a recurring template for rent, salary, subscriptions, and more."
                action-text="Create Template"
                :action-url="route('recurring.create')" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Frequency</th>
                            <th>Next Run</th>
                            <th>Last Run</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $t)
                            <tr class="{{ ! $t->is_active ? 'opacity-50' : '' }}">
                                <td class="fw-semibold">
                                    {{ $t->title }}
                                    @if (! $t->is_active)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($t->type->value === 'income')
                                        <span class="badge bg-success-subtle text-success-emphasis">Income</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Expense</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $t->category?->name ?? '—' }}</td>
                                <td><span class="badge bg-info-subtle text-info-emphasis">{{ $t->frequency->label() }}</span></td>
                                <td class="text-nowrap">
                                    {{ $t->next_run_date?->format('d M Y') ?? '—' }}
                                    @if ($t->next_run_date && $t->next_run_date->isPast() && $t->is_active)
                                        <span class="badge bg-warning text-dark ms-1">Due</span>
                                    @endif
                                </td>
                                <td class="text-nowrap text-muted small">
                                    {{ $t->last_run_date?->format('d M Y') ?? 'Never' }}
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($t->type->value === 'income')
                                        <span class="fw-bold text-success">+ {{ number_format((float) $t->amount, 2) }}</span>
                                    @else
                                        <span class="fw-bold text-danger">− {{ number_format((float) $t->amount, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @can('update', $t)
                                        <a href="{{ route('recurring.edit', $t) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $t)
                                        <form method="POST" action="{{ route('recurring.destroy', $t) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this recurring template? Historical entries are not affected.');">
                                            @csrf @method('DELETE')
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

    @if ($templates->hasPages())
        <div class="card-footer bg-white">
            {{ $templates->links() }}
        </div>
    @endif
</div>

@endsection