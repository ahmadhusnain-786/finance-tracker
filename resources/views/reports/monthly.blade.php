@extends('layouts.app')
@section('title', 'Monthly Report')
@section('page-title', 'Monthly Report')

@section('content')
@include('reports._date_filter', ['action' => 'reports.monthly', 'range' => $range])

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Income</div>
        <div class="fs-4 fw-bold text-success mt-1">{{ $currency }} {{ number_format($data['totals']['income'], 2) }}</div>
    </div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Expenses</div>
        <div class="fs-4 fw-bold text-danger mt-1">{{ $currency }} {{ number_format($data['totals']['expense'], 2) }}</div>
    </div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Net Balance</div>
        <div class="fs-4 fw-bold mt-1 {{ $data['totals']['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
            {{ $currency }} {{ number_format(abs($data['totals']['balance']), 2) }}
        </div>
    </div></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white"><span class="fw-semibold">Income vs Expenses by Month</span></div>
    <div class="card-body">
        @if (empty($data['rows']))
            <x-empty-state icon="bi-calendar-month" title="No data in this range" />
        @else
            <canvas id="monthlyChart" height="110"></canvas>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if (empty($data['rows']))
            <x-empty-state icon="bi-calendar-month" title="No data" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Income</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['rows'] as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r['label'] }}</td>
                                <td class="text-end text-success">+ {{ number_format($r['income'], 2) }}</td>
                                <td class="text-end text-danger">− {{ number_format($r['expense'], 2) }}</td>
                                <td class="text-end fw-bold {{ ($r['income'] - $r['expense']) >= 0 ? 'text-primary' : 'text-danger' }}">
                                    {{ number_format($r['income'] - $r['expense'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th class="text-end text-success">+ {{ number_format($data['totals']['income'], 2) }}</th>
                            <th class="text-end text-danger">− {{ number_format($data['totals']['expense'], 2) }}</th>
                            <th class="text-end fw-bold">{{ number_format($data['totals']['balance'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const c = document.getElementById('monthlyChart');
    if (!c || typeof Chart === 'undefined') return;
    new Chart(c, {
        type: 'bar',
        data: {
            labels: @json(array_column($data['rows'], 'label')),
            datasets: [
                { label: 'Income',  data: @json(array_column($data['rows'], 'income')),  backgroundColor: '#22c55e', borderRadius: 4 },
                { label: 'Expense', data: @json(array_column($data['rows'], 'expense')), backgroundColor: '#ef4444', borderRadius: 4 },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => Number(v).toLocaleString() } }, x: { grid: { display: false } } },
        },
    });
});
</script>
@endpush