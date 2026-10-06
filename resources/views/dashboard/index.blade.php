@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Range filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end" id="rangeForm">
            <div class="col-md-3">
                <label class="form-label small mb-1">Date Range</label>
                <select name="range" class="form-select" id="rangeSelect">
                    <option value="today"      {{ $range['key'] === 'today'      ? 'selected' : '' }}>Today</option>
                    <option value="week"       {{ $range['key'] === 'week'       ? 'selected' : '' }}>This Week</option>
                    <option value="month"      {{ $range['key'] === 'month'      ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $range['key'] === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="year"       {{ $range['key'] === 'year'       ? 'selected' : '' }}>This Year</option>
                    <option value="custom"     {{ $range['key'] === 'custom'     ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-md-3 custom-range-fields"
                 style="{{ $range['key'] === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control"
                       value="{{ request('from', $range['from']->toDateString()) }}">
            </div>
            <div class="col-md-3 custom-range-fields"
                 style="{{ $range['key'] === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control"
                       value="{{ request('to', $range['to']->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Apply
                </button>
            </div>
        </form>

        <div class="text-muted small mt-2">
            <i class="bi bi-calendar-range me-1"></i>
            Showing: <strong>{{ $range['label'] }}</strong>
            ({{ $range['from']->format('d M Y') }} — {{ $range['to']->format('d M Y') }})
            · {{ $metrics['days'] }} day(s)
        </div>
    </div>
</div>

{{-- Stat cards --}}
<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Total Income</div>
                        <div class="fs-4 fw-bold text-success mt-1">
                            {{ $currency }} {{ number_format($metrics['income'], 2) }}
                        </div>
                        <div class="text-muted small mt-1">{{ $metrics['income_count'] }} record(s)</div>
                    </div>
                    <div class="rounded-circle bg-success-subtle p-3">
                        <i class="bi bi-cash-stack fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Total Expenses</div>
                        <div class="fs-4 fw-bold text-danger mt-1">
                            {{ $currency }} {{ number_format($metrics['expenses'], 2) }}
                        </div>
                        <div class="text-muted small mt-1">{{ $metrics['expense_count'] }} record(s)</div>
                    </div>
                    <div class="rounded-circle bg-danger-subtle p-3">
                        <i class="bi bi-receipt fs-4 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Current Balance</div>
                        <div class="fs-4 fw-bold mt-1 {{ $metrics['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ $currency }} {{ number_format(abs($metrics['balance']), 2) }}
                        </div>
                        <div class="text-muted small mt-1">
                            {{ $metrics['balance'] >= 0 ? 'Positive' : 'Overspent' }}
                        </div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3">
                        <i class="bi bi-wallet2 fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Savings Rate</div>
                        <div class="fs-4 fw-bold text-info mt-1">
                            {{ number_format($metrics['savings_rate'], 1) }}%
                        </div>
                        <div class="text-muted small mt-1">
                            Savings: {{ $currency }} {{ number_format($metrics['savings'], 2) }}
                        </div>
                    </div>
                    <div class="rounded-circle bg-info-subtle p-3">
                        <i class="bi bi-piggy-bank fs-4 text-info"></i>
                    </div>
                </div>
                @if ($metrics['income'] > 0)
                    <div class="progress mt-2" style="height: 6px;" role="progressbar"
                         aria-valuenow="{{ $metrics['savings_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-info"
                             style="width: {{ max(0, min(100, $metrics['savings_rate'])) }}%"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- CHARTS --}}
{{-- ===================================================== --}}
<div class="row g-3 mt-3">
    {{-- Income vs Expenses --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Income vs Expenses</span>
                <small class="text-muted">
                    Grouped by {{ ($incomeVsExpense['groupBy'] ?? 'day') === 'day' ? 'day' : 'month' }}
                </small>
            </div>
            <div class="card-body">
                @if (empty($incomeVsExpense['labels']))
                    <x-empty-state
                        icon="bi-bar-chart-line"
                        title="No data for this range"
                        message="Add income or expenses in the selected period to see this chart." />
                @else
                    <canvas id="incomeVsExpenseChart" height="120"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Expense by Category --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-semibold">Expenses by Category</span>
            </div>
            <div class="card-body">
                @if (empty($expenseByCat['labels']))
                    <x-empty-state
                        icon="bi-pie-chart"
                        title="No expense data"
                        message="No expenses were recorded in this period." />
                @else
                    <canvas id="expenseByCategoryChart" height="220"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-3">
    {{-- Savings Trend --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Cumulative Savings Trend</span>
                <small class="text-muted">Net position across the period</small>
            </div>
            <div class="card-body">
                @if (empty($savingsTrend['labels']))
                    <x-empty-state
                        icon="bi-graph-up"
                        title="No savings data yet"
                        message="Record some income and expenses to see your savings trend." />
                @else
                    <canvas id="savingsTrendChart" height="80"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent transactions + quick actions --}}
<div class="row g-3 mt-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Transactions</span>
                <a href="{{ route('transactions.index') }}" class="small text-decoration-none">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if (empty($recent))
                    <x-empty-state
                        icon="bi-list-ul"
                        title="No transactions yet"
                        message="Add income or expenses to see them here." />
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recent as $row)
                                    <tr>
                                        <td class="text-nowrap small">{{ $row['date']->format('d M Y') }}</td>
                                        <td>
                                            @if ($row['type'] === 'income')
                                                <i class="bi bi-arrow-down-left text-success me-1"></i>
                                            @else
                                                <i class="bi bi-arrow-up-right text-danger me-1"></i>
                                            @endif
                                            <a href="{{ $row['edit_url'] }}" class="text-decoration-none text-dark">
                                                {{ $row['title'] }}
                                            </a>
                                        </td>
                                        <td class="small text-muted">{{ $row['category']?->name ?? '—' }}</td>
                                        <td class="text-end text-nowrap">
                                            @if ($row['type'] === 'income')
                                                <span class="fw-semibold text-success">
                                                    + {{ number_format($row['amount'], 2) }}
                                                </span>
                                            @else
                                                <span class="fw-semibold text-danger">
                                                    − {{ number_format($row['amount'], 2) }}
                                                </span>
                                            @endif
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-semibold">Quick Actions</span>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('income.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg me-2"></i>Add Income
                </a>
                <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                    <i class="bi bi-plus-lg me-2"></i>Add Expense
                </a>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-list-ul me-2"></i>View All Transactions
                </a>
                <hr class="my-1">
                <div class="text-muted small mb-1">Daily Averages</div>
                <div class="d-flex justify-content-between small">
                    <span>Income</span>
                    <span class="fw-semibold text-success">
                        {{ number_format($metrics['avg_income_day'], 2) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Expenses</span>
                    <span class="fw-semibold text-danger">
                        {{ number_format($metrics['avg_expense_day'], 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Currency label used in tooltips
    const currency = @json($currency);

    // Helper: format numbers with 2 decimals and thousand separators
    function fmt(n) {
        return currency + ' ' + Number(n).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    // ---------- Show/hide custom range fields ----------
    const select = document.getElementById('rangeSelect');
    const fields = document.querySelectorAll('.custom-range-fields');
    if (select) {
        select.addEventListener('change', function () {
            fields.forEach(f => f.style.display = (this.value === 'custom') ? '' : 'none');
        });
    }

    // ---------- Chart defaults ----------
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#64748b';

    // ---------- 1. Income vs Expenses (bar) ----------
    const iovCanvas = document.getElementById('incomeVsExpenseChart');
    if (iovCanvas) {
        new Chart(iovCanvas, {
            type: 'bar',
            data: {
                labels: @json($incomeVsExpense['labels']),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($incomeVsExpense['income']),
                        backgroundColor: '#22c55e',
                        borderRadius: 4,
                    },
                    {
                        label: 'Expenses',
                        data: @json($incomeVsExpense['expense']),
                        backgroundColor: '#ef4444',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.label + ': ' + fmt(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => Number(v).toLocaleString(),
                        },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }

    // ---------- 2. Expense by Category (doughnut) ----------
    const catCanvas = document.getElementById('expenseByCategoryChart');
    if (catCanvas) {
        new Chart(catCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($expenseByCat['labels']),
                datasets: [{
                    data: @json($expenseByCat['values']),
                    backgroundColor: @json($expenseByCat['colors']),
                    borderColor: '#fff',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '62%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.label + ': ' + fmt(ctx.parsed),
                        },
                    },
                },
            },
        });
    }

    // ---------- 3. Savings Trend (line) ----------
    const savCanvas = document.getElementById('savingsTrendChart');
    if (savCanvas) {
        new Chart(savCanvas, {
            type: 'line',
            data: {
                labels: @json($savingsTrend['labels']),
                datasets: [{
                    label: 'Cumulative Savings',
                    data: @json($savingsTrend['values']),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => 'Savings: ' + fmt(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        ticks: {
                            callback: (v) => Number(v).toLocaleString(),
                        },
                    },
                    x: { grid: { display: false } },
                },
            },
        });
    }
});
</script>
@endpush