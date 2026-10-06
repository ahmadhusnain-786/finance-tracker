@extends('layouts.app')

@section('title', 'Income Report')
@section('page-title', 'Income Report')

@section('content')

@include('reports._date_filter', ['action' => 'reports.income', 'range' => $range])


<div class="d-flex justify-content-end mb-3">
    @include('reports._export_buttons', ['exportBaseRoute' => 'reports.income.export'])
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Total Income</div>
                <div class="fs-4 fw-bold text-success mt-1">
                    {{ $currency }} {{ number_format($data['total'], 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Records</div>
                <div class="fs-4 fw-bold mt-1">{{ $data['count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold">Average per Record</div>
                <div class="fs-4 fw-bold mt-1">
                    {{ $currency }} {{ number_format($data['average'], 2) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart + by-category breakdown --}}
<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-semibold">
                    Trend (by {{ $data['series']['by'] === 'day' ? 'day' : 'month' }})
                </span>
            </div>
            <div class="card-body">
                @if (empty($data['series']['labels']) || array_sum($data['series']['values']) === 0.0)
                    <x-empty-state icon="bi-bar-chart-line" title="No income in this range" />
                @else
                    <canvas id="incomeTrendChart" height="110"></canvas>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-semibold">By Category</span>
            </div>
            <div class="card-body">
                @if (empty($data['byCategory']))
                    <x-empty-state icon="bi-tags" title="No categories to show" />
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['byCategory'] as $c)
                                    <tr>
                                        <td>
                                            <span class="d-inline-flex align-items-center gap-2">
                                                @if ($c['color'])
                                                    <span class="rounded-circle" style="width:10px;height:10px;background:{{ $c['color'] }}"></span>
                                                @endif
                                                @if ($c['icon'])
                                                    <i class="bi {{ $c['icon'] }}"></i>
                                                @endif
                                                <span class="small fw-semibold">{{ $c['name'] }}</span>
                                            </span>
                                        </td>
                                        <td class="text-end small">{{ number_format($c['total'], 2) }}</td>
                                        <td class="text-end small text-muted">{{ number_format($c['percentage'], 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end">{{ number_format($data['total'], 2) }}</th>
                                    <th class="text-end">100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Detail table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <span class="fw-semibold">All Income Records</span>
    </div>
    <div class="card-body p-0">
        @if ($data['rows']->isEmpty())
            <x-empty-state
                icon="bi-cash-stack"
                title="No income in this range"
                message="Try widening the date filter." />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Payment</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['rows'] as $row)
                            <tr>
                                <td class="text-nowrap small">{{ $row->date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('income.edit', $row) }}" class="text-decoration-none text-dark">
                                        {{ $row->title }}
                                    </a>
                                </td>
                                <td class="small text-muted">{{ $row->category?->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $row->paymentMethod?->name ?? '—' }}</td>
                                <td class="text-end fw-semibold text-success">
                                    + {{ number_format((float) $row->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end text-success">+ {{ number_format($data['total'], 2) }}</th>
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
    const canvas = document.getElementById('incomeTrendChart');
    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: @json($data['series']['labels']),
            datasets: [{
                label: 'Income',
                data: @json($data['series']['values']),
                backgroundColor: '#22c55e',
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Income: ' + Number(ctx.parsed.y).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        }),
                    },
                },
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (v) => Number(v).toLocaleString() } },
                x: { grid: { display: false } },
            },
        },
    });
});
</script>
@endpush