@extends('layouts.app')

@section('title', 'Edit Budget')
@section('page-title', 'Edit Budget')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Current period summary --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Current Period</div>
                        <div class="fw-semibold">
                            {{ $summary['period_start']->format('d M Y') }} —
                            {{ $summary['period_end']->format('d M Y') }}
                        </div>
                    </div>
                    <span class="badge {{ $summary['badge_class'] }}">{{ $summary['status'] }}</span>
                </div>

                <div class="d-flex justify-content-between small mt-3 mb-1">
                    <span class="text-muted">Spent</span>
                    <span class="fw-semibold">
                        {{ number_format($summary['spent'], 2) }} / {{ number_format($summary['limit'], 2) }}
                    </span>
                </div>

                <div class="progress" style="height: 10px;">
                    <div class="progress-bar {{ $summary['progress_class'] }}"
                         style="width: {{ min(100, $summary['percentage']) }}%"></div>
                </div>
                <div class="small text-muted mt-1">{{ number_format($summary['percentage'], 1) }}% used</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Budget</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('budgets.update', $budget) }}" novalidate>
                    @csrf
                    @method('PUT')

                    @include('budgets._form', [
                        'budget' => $budget,
                        'categories' => $categories,
                        'usedCategoryIds' => $usedCategoryIds,
                    ])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update Budget
                        </button>
                        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection