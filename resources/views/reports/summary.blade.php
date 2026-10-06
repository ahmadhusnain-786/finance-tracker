@extends('layouts.app')
@section('title', 'Financial Summary')
@section('page-title', 'Financial Summary')

@section('content')
@include('reports._date_filter', ['action' => 'reports.summary', 'range' => $range])

<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Income</div>
        <div class="fs-4 fw-bold text-success mt-1">{{ $currency }} {{ number_format($data['income'], 2) }}</div>
        <div class="text-muted small">{{ $data['income_count'] }} record(s)</div>
    </div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Expenses</div>
        <div class="fs-4 fw-bold text-danger mt-1">{{ $currency }} {{ number_format($data['expense'], 2) }}</div>
        <div class="text-muted small">{{ $data['expense_count'] }} record(s)</div>
    </div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Net Balance</div>
        <div class="fs-4 fw-bold mt-1 {{ $data['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
            {{ $currency }} {{ number_format(abs($data['balance']), 2) }}
        </div>
    </div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Savings Rate</div>
        <div class="fs-4 fw-bold text-info mt-1">{{ number_format($data['savings_rate'], 1) }}%</div>
    </div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><span class="fw-semibold text-success">Top Income Sources</span></div>
            <div class="card-body p-0">
                @if (empty($data['top_income']))
                    <x-empty-state icon="bi-cash-stack" title="No income in this range" />
                @else
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Category</th><th class="text-end">Total</th><th class="text-end">%</th></tr></thead>
                        <tbody>
                            @foreach ($data['top_income'] as $c)
                                <tr>
                                    <td class="small fw-semibold">
                                        @if ($c['icon'])<i class="bi {{ $c['icon'] }} me-1"></i>@endif
                                        {{ $c['name'] }}
                                    </td>
                                    <td class="text-end small">{{ number_format($c['total'], 2) }}</td>
                                    <td class="text-end small text-muted">{{ number_format($c['percentage'], 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><span class="fw-semibold text-danger">Top Expense Categories</span></div>
            <div class="card-body p-0">
                @if (empty($data['top_expense']))
                    <x-empty-state icon="bi-receipt" title="No expenses in this range" />
                @else
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Category</th><th class="text-end">Total</th><th class="text-end">%</th></tr></thead>
                        <tbody>
                            @foreach ($data['top_expense'] as $c)
                                <tr>
                                    <td class="small fw-semibold">
                                        @if ($c['icon'])<i class="bi {{ $c['icon'] }} me-1"></i>@endif
                                        {{ $c['name'] }}
                                    </td>
                                    <td class="text-end small">{{ number_format($c['total'], 2) }}</td>
                                    <td class="text-end small text-muted">{{ number_format($c['percentage'], 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection