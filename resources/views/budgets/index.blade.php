@extends('layouts.app')

@section('title', 'Budgets')
@section('page-title', 'Budgets')

@section('content')

{{-- Summary strip --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Total Budgeted</div>
                <div class="fs-5 fw-bold text-primary mt-1">
                    {{ $currency }} {{ number_format($totals['limit'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Total Spent</div>
                <div class="fs-5 fw-bold text-danger mt-1">
                    {{ $currency }} {{ number_format($totals['spent'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Remaining</div>
                <div class="fs-5 fw-bold text-success mt-1">
                    {{ $currency }} {{ number_format($totals['remaining'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Alerts</div>
                <div class="mt-1">
                    @if ($totals['exceeded'] > 0)
                        <span class="badge bg-danger me-1">{{ $totals['exceeded'] }} exceeded</span>
                    @endif
                    @if ($totals['warning'] > 0)
                        <span class="badge bg-warning text-dark">{{ $totals['warning'] }} warning</span>
                    @endif
                    @if ($totals['exceeded'] === 0 && $totals['warning'] === 0)
                        <span class="badge bg-success-subtle text-success-emphasis">All on track</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('budgets.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search by category</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="e.g. Food">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="active"   {{ $filters['status'] === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="all"      {{ $filters['status'] === 'all'      ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-3 text-md-end">
                <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>New Budget
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Budget cards --}}
@if ($budgets->isEmpty())
    <div class="card border-0 shadow-sm">
        <x-empty-state
            icon="bi-pie-chart"
            title="No budgets yet"
            message="Create your first budget to track spending per category."
            action-text="Create Budget"
            :action-url="route('budgets.create')" />
    </div>
@else
    <div class="row g-3">
        @foreach ($budgets as $budget)
            @php $s = $summaries[$budget->id]; @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 {{ ! $budget->is_active ? 'opacity-75' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($budget->category?->icon)
                                        <i class="bi {{ $budget->category->icon }} text-primary"></i>
                                    @endif
                                    <span class="fw-semibold">{{ $budget->category?->name ?? '—' }}</span>
                                </div>
                                <div class="text-muted small">
                                    {{ ucfirst($budget->period) }}
                                    · {{ $s['period_start']->format('d M') }} – {{ $s['period_end']->format('d M') }}
                                </div>
                            </div>
                            <span class="badge {{ $s['badge_class'] }}">{{ $s['status'] }}</span>
                        </div>

                        <div class="d-flex justify-content-between small mt-3 mb-1">
                            <span class="text-muted">Spent</span>
                            <span class="fw-semibold">
                                {{ number_format($s['spent'], 2) }} / {{ number_format($s['limit'], 2) }}
                            </span>
                        </div>

                        <div class="progress" style="height: 10px;" role="progressbar"
                             aria-valuenow="{{ $s['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar {{ $s['progress_class'] }}"
                                 style="width: {{ min(100, $s['percentage']) }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small mt-1">
                            <span class="text-muted">{{ number_format($s['percentage'], 1) }}% used</span>
                            <span class="fw-semibold {{ $s['remaining'] > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $s['remaining'] > 0 ? 'Remaining: ' . number_format($s['remaining'], 2) : 'Over budget' }}
                            </span>
                        </div>

                        @if ($budget->notes)
                            <div class="text-muted small mt-2 fst-italic">{{ $budget->notes }}</div>
                        @endif
                    </div>

                    <div class="card-footer bg-white border-0 pt-0 d-flex justify-content-end gap-2">
                        @can('update', $budget)
                            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('delete', $budget)
                            <form method="POST" action="{{ route('budgets.destroy', $budget) }}"
                                  onsubmit="return confirm('Delete this budget?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection