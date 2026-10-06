@extends('layouts.app')

@section('title', $goal->name)
@section('page-title', 'Goal Details')

@section('content')
@php
    $pct = $goal->progress_percentage;
    $remaining = $goal->remaining_amount;
    $progressClass = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-info');
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $goal->name }}</h4>
                        @if ($goal->target_date)
                            <div class="text-muted small">
                                <i class="bi bi-calendar-event me-1"></i>
                                Target: {{ $goal->target_date->format('d M Y') }}
                                @if ($goal->target_date->isPast() && $goal->status->value === 'active')
                                    <span class="badge bg-danger-subtle text-danger-emphasis ms-1">Overdue</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <span class="badge {{ $goal->status->badgeClass() }} fs-6">{{ $goal->status->label() }}</span>
                </div>

                @if ($goal->description)
                    <p class="text-muted">{{ $goal->description }}</p>
                @endif

                <div class="row text-center my-4">
                    <div class="col">
                        <div class="text-muted small text-uppercase">Saved</div>
                        <div class="fs-4 fw-bold text-success">
                            {{ $currency }} {{ number_format((float) $goal->current_amount, 2) }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-muted small text-uppercase">Target</div>
                        <div class="fs-4 fw-bold">
                            {{ $currency }} {{ number_format((float) $goal->target_amount, 2) }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-muted small text-uppercase">Remaining</div>
                        <div class="fs-4 fw-bold text-warning">
                            {{ $currency }} {{ number_format($remaining, 2) }}
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold">{{ number_format($pct, 1) }}%</span>
                    <span class="text-muted small">{{ $goal->contributions->count() }} contribution(s)</span>
                </div>
                <div class="progress" style="height: 14px;">
                    <div class="progress-bar {{ $progressClass }}" style="width: {{ min(100, $pct) }}%"></div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-end gap-2">
                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
                @can('update', $goal)
                    <a href="{{ route('goals.edit', $goal) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                @endcan
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <span class="fw-semibold">Contribution History</span>
            </div>
            <div class="card-body p-0">
                @if ($goal->contributions->isEmpty())
                    <x-empty-state
                        icon="bi-cash-coin"
                        title="No contributions yet"
                        message="Add your first contribution using the form on the right." />
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($goal->contributions as $c)
                                    <tr>
                                        <td class="text-nowrap">{{ $c->contribution_date->format('d M Y') }}</td>
                                        <td class="text-muted small">{{ $c->note ?: '—' }}</td>
                                        <td class="text-end fw-semibold text-success">
                                            + {{ number_format((float) $c->amount, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <form method="POST"
                                                  action="{{ route('goals.contributions.destroy', [$goal, $c]) }}"
                                                  onsubmit="return confirm('Delete this contribution?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <span class="fw-semibold">
                    <i class="bi bi-plus-circle me-1 text-success"></i>Add Contribution
                </span>
            </div>
            <div class="card-body">
                @can('contribute', $goal)
                    <form method="POST" action="{{ route('goals.contributions.store', $goal) }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01"
                                   id="amount" name="amount"
                                   value="{{ old('amount') }}"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   required autofocus>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="contribution_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="contribution_date" name="contribution_date"
                                   value="{{ old('contribution_date', now()->toDateString()) }}"
                                   max="{{ now()->toDateString() }}"
                                   class="form-control @error('contribution_date') is-invalid @enderror"
                                   required>
                            @error('contribution_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea id="note" name="note" rows="2"
                                      class="form-control @error('note') is-invalid @enderror"
                                      maxlength="500"
                                      placeholder="Optional">{{ old('note') }}</textarea>
                            @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-lg me-2"></i>Add Contribution
                        </button>
                    </form>
                @else
                    <p class="text-muted small mb-0">
                        You don't have permission to contribute to this goal.
                    </p>
                @endcan
            </div>
        </div>
    </div>
</div>

@endsection