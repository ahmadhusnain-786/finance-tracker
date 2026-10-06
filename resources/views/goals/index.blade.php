@extends('layouts.app')

@section('title', 'Goals')
@section('page-title', 'Financial Goals')

@section('content')

{{-- Summary strip --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Total Targets</div>
                <div class="fs-5 fw-bold text-primary mt-1">
                    {{ $currency }} {{ number_format($totals['target'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Total Saved</div>
                <div class="fs-5 fw-bold text-success mt-1">
                    {{ $currency }} {{ number_format($totals['saved'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Still Needed</div>
                <div class="fs-5 fw-bold text-warning mt-1">
                    {{ $currency }} {{ number_format($totals['remaining'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Active / Completed</div>
                <div class="fs-5 fw-bold mt-1">
                    <span class="text-primary">{{ $totals['active'] }}</span>
                    <span class="text-muted">/</span>
                    <span class="text-success">{{ $totals['completed'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('goals.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Search goals...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="all"       {{ $filters['status'] === 'all'       ? 'selected' : '' }}>All</option>
                    <option value="active"    {{ $filters['status'] === 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $filters['status'] === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-3 text-md-end">
                <a href="{{ route('goals.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>New Goal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Goal cards --}}
@if ($goals->isEmpty())
    <div class="card border-0 shadow-sm">
        <x-empty-state
            icon="bi-bullseye"
            title="No goals yet"
            message="Create a savings goal to start tracking progress toward it."
            action-text="Create Goal"
            :action-url="route('goals.create')" />
    </div>
@else
    <div class="row g-3">
        @foreach ($goals as $goal)
            @php
                $pct = $goal->progress_percentage;
                $remaining = $goal->remaining_amount;
                $progressClass = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-info');
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 {{ $goal->status->value === 'cancelled' ? 'opacity-75' : '' }}">
                    <div class="card-body d-flex flex-column">

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-bullseye text-primary"></i>
                                    <a href="{{ route('goals.show', $goal) }}"
                                       class="fw-semibold text-decoration-none text-dark">
                                        {{ $goal->name }}
                                    </a>
                                </div>
                                <div class="text-muted small">
                                    @if ($goal->target_date)
                                        Target: {{ $goal->target_date->format('d M Y') }}
                                        @if ($goal->target_date->isPast() && $goal->status->value === 'active')
                                            <span class="badge bg-danger-subtle text-danger-emphasis ms-1">Overdue</span>
                                        @endif
                                    @else
                                        No target date
                                    @endif
                                </div>
                            </div>
                            <span class="badge {{ $goal->status->badgeClass() }}">{{ $goal->status->label() }}</span>
                        </div>

                        <div class="text-center my-3">
                            <div class="display-6 fw-bold text-primary">
                                {{ number_format($pct, 1) }}%
                            </div>
                            <div class="text-muted small">of target reached</div>
                        </div>

                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar {{ $progressClass }}"
                                 style="width: {{ min(100, $pct) }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Saved</span>
                            <span class="fw-semibold text-success">
                                {{ number_format((float) $goal->current_amount, 2) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Remaining</span>
                            <span class="fw-semibold text-warning">
                                {{ number_format($remaining, 2) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-muted">Target</span>
                            <span class="fw-semibold">
                                {{ number_format((float) $goal->target_amount, 2) }}
                            </span>
                        </div>

                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-primary flex-fill">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            @can('update', $goal)
                                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection