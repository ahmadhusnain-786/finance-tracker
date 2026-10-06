@extends('layouts.app')
@section('title', 'Budget Report')
@section('page-title', 'Budget Report')

@section('content')
@include('reports._date_filter', ['action' => 'reports.budget', 'range' => $range])

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Budgeted</div>
        <div class="fs-5 fw-bold text-primary mt-1">{{ $currency }} {{ number_format($data['totals']['limit'], 2) }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Total Spent</div>
        <div class="fs-5 fw-bold text-danger mt-1">{{ $currency }} {{ number_format($data['totals']['spent'], 2) }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Remaining</div>
        <div class="fs-5 fw-bold text-success mt-1">{{ $currency }} {{ number_format(max(0, $data['totals']['limit'] - $data['totals']['spent']), 2) }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small text-uppercase fw-semibold">Alerts</div>
        <div class="mt-1">
            @if ($data['totals']['over'] > 0)<span class="badge bg-danger me-1">{{ $data['totals']['over'] }} over</span>@endif
            @if ($data['totals']['warning'] > 0)<span class="badge bg-warning text-dark">{{ $data['totals']['warning'] }} warn</span>@endif
            @if ($data['totals']['over'] + $data['totals']['warning'] === 0)<span class="badge bg-success-subtle text-success-emphasis">All on track</span>@endif
        </div>
    </div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if (empty($data['rows']))
            <x-empty-state icon="bi-pie-chart" title="No budgets" message="Create a budget to see its report." />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th>Period</th>
                            <th class="text-end">Limit</th>
                            <th class="text-end">Spent</th>
                            <th class="text-end">Remaining</th>
                            <th style="min-width:180px;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['rows'] as $r)
                            <tr class="{{ ! $r['budget']->is_active ? 'opacity-50' : '' }}">
                                <td class="fw-semibold">
                                    @if ($r['budget']->category?->icon)<i class="bi {{ $r['budget']->category->icon }} me-1"></i>@endif
                                    {{ $r['budget']->category?->name ?? '—' }}
                                </td>
                                <td class="small text-muted">
                                    {{ $r['period'][0]->format('d M') }} – {{ $r['period'][1]->format('d M') }}
                                </td>
                                <td class="text-end">{{ number_format($r['limit'], 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($r['spent'], 2) }}</td>
                                <td class="text-end {{ $r['remaining'] > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ number_format($r['remaining'], 2) }}
                                </td>
                                <td>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar {{ $r['bar'] }}" style="width: {{ min(100, $r['pct']) }}%"></div>
                                    </div>
                                    <div class="small text-muted mt-1">{{ number_format($r['pct'], 1) }}% · {{ $r['status'] }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection