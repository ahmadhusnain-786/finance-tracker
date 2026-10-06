@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')

<div class="alert alert-info border-0 shadow-sm">
    <i class="bi bi-info-circle me-2"></i>
    Each report has its own date filter. Pick a report below to begin.
</div>

@php
    $reportCards = [
        ['title' => 'Income Report',       'desc' => 'All income entries with totals and by-category breakdown.',   'icon' => 'bi-cash-stack',    'class' => 'text-success',  'route' => 'reports.income'],
        ['title' => 'Expense Report',      'desc' => 'All expenses with totals and by-category breakdown.',         'icon' => 'bi-receipt',       'class' => 'text-danger',   'route' => 'reports.expense'],
        ['title' => 'Monthly Report',      'desc' => 'A month-by-month comparison of income and expenses.',         'icon' => 'bi-calendar-month','class' => 'text-primary',  'route' => 'reports.monthly'],
        ['title' => 'Yearly Report',       'desc' => 'Year totals with a side-by-side comparison.',                'icon' => 'bi-calendar3',     'class' => 'text-primary',  'route' => 'reports.yearly'],
        ['title' => 'Category Report',     'desc' => 'Every category ranked by total spend or earning.',            'icon' => 'bi-tags',          'class' => 'text-warning',  'route' => 'reports.category'],
        ['title' => 'Budget Report',       'desc' => 'Performance of each budget across the period.',               'icon' => 'bi-pie-chart',     'class' => 'text-info',     'route' => 'reports.budget'],
        ['title' => 'Savings Report',      'desc' => 'Cumulative savings and savings-rate trends.',                 'icon' => 'bi-piggy-bank',    'class' => 'text-success',  'route' => 'reports.savings'],
        ['title' => 'Financial Summary',   'desc' => 'A single-page executive summary of everything.',              'icon' => 'bi-clipboard-data','class' => 'text-primary',  'route' => 'reports.summary'],
    ];
@endphp

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    @foreach ($reportCards as $card)
        @php $exists = \Illuminate\Support\Facades\Route::has($card['route']); @endphp
        <div class="col">
            <div class="card border-0 shadow-sm h-100 {{ $exists ? '' : 'opacity-75' }}">
                <div class="card-body d-flex flex-column">
                    <div class="{{ $card['class'] }} fs-2 mb-2">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <h5 class="card-title">
                        {{ $card['title'] }}
                        @unless ($exists)
                            <span class="badge bg-secondary-subtle text-secondary-emphasis ms-2 small">Soon</span>
                        @endunless
                    </h5>
                    <p class="card-text text-muted small flex-grow-1">{{ $card['desc'] }}</p>

                    @if ($exists)
                        <a href="{{ route($card['route']) }}" class="btn btn-outline-primary btn-sm align-self-start mt-2">
                            Open <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @else
                        <button type="button" class="btn btn-outline-secondary btn-sm align-self-start mt-2" disabled>
                            Coming in Phase 17B
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection