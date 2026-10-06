@extends('layouts.guest')

@section('title', 'Features')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Features</h1>
        <p class="text-muted lead">Everything you need to manage your personal finances.</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @php
            $features = [
                ['icon' => 'cash-stack',   'title' => 'Income Tracking',   'desc' => 'Record salary, freelance, business and other income.'],
                ['icon' => 'receipt',      'title' => 'Expense Tracking',  'desc' => 'Categorize every expense and know your real spending.'],
                ['icon' => 'list-ul',      'title' => 'Transactions',      'desc' => 'A unified history of every income and expense.'],
                ['icon' => 'pie-chart',    'title' => 'Budgets',           'desc' => 'Set monthly limits per category with visual progress.'],
                ['icon' => 'bullseye',     'title' => 'Savings Goals',     'desc' => 'Create goals and track contributions automatically.'],
                ['icon' => 'graph-up',     'title' => 'Reports',           'desc' => 'Filter income, expense, and category reports.'],
                ['icon' => 'arrow-repeat', 'title' => 'Recurring Entries', 'desc' => 'Automate rent, salary, and subscriptions.'],
                ['icon' => 'bell',         'title' => 'Notifications',     'desc' => 'Get warned before budgets are exceeded.'],
                ['icon' => 'shield-check', 'title' => 'Private & Secure',  'desc' => 'Your data is isolated. Only you can see it.'],
            ];
        @endphp

        @foreach ($features as $f)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-primary fs-2"><i class="bi bi-{{ $f['icon'] }}"></i></div>
                        <h5 class="card-title mt-3">{{ $f['title'] }}</h5>
                        <p class="card-text text-muted small mb-0">{{ $f['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection