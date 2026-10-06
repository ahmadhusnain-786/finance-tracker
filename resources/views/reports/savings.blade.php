@extends('layouts.app')
@section('title', 'Savings Report')
@section('page-title', 'Savings Report')

@section('content')
@include('reports._date_filter', ['action' => 'reports.savings', 'range' => $range])

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Income</div>
        <div class="fs-5 fw-bold text-success mt-1">{{ $currency }} {{ number_format($data['totals']['income'], 2) }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Expenses</div>
        <div class="fs-5 fw-bold text-danger mt-1">{{ $currency }} {{ number_format($data['totals']['expense'], 2) }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Net Savings</div>
        <div class="fs-5 fw-bold mt-1 {{ $data['totals']['savings'] >= 0 ? 'text-primary' : 'text-danger' }}">
            {{ $currency }} {{ number_format(abs($data['totals']['savings']), 2) }}
        </div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Savings Rate</div>
        <div class="fs-5 fw-bold text-info mt-1">{{ number_format($data['totals']['rate'], 1) }}%</div>
    </div></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white"><span class="fw-semibold">Cumulative Savings</span></div>
    <div class="card-body">
        @if (empty($data['rows']))
            <x-empty-state icon="bi-piggy-bank" title="No savings data" />
        @else
            <canvas id="savingsReportChart" height="90"></canvas>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if (empty($data['rows']))
            <x-empty-state icon="bi-piggy-bank" title="No data" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Income</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Monthly Net</th>
                            <th class="text-end">Cumulative</th>
                            <th class="text-end">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['rows'] as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r['label'] }}</td>
                                <td class="text-end text-success">+ {{ number_format($r['income'], 2) }}</td>
                                <td class="text-end text-danger">− {{ number_format($r['expense'], 2) }}</td>
                                <td class="text-end {{ $r['monthly'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($r['monthly'], 2) }}</td>
                                <td class="text-end fw-bold {{ $r['savings'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($r['savings'], 2) }}</td>
                                <td class="text-end small text-muted">{{ number_format($r['rate'], 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
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
    const c = document.getElementById('savingsReportChart');
    if (!c || typeof Chart === 'undefined') return;
    new Chart(c, {
        type: 'line',
        data: {
            labels: @json(array_column($data['rows'], 'label')),
            datasets: [{
                label: 'Cumulative Savings',
                data: @json(array_column($data['rows'], 'savings')),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { ticks: { callback: v => Number(v).toLocaleString() } }, x: { grid: { display: false } } },
        },
    });
});
</script>
@endpush