@extends('layouts.app')
@section('title', 'Category Report')
@section('page-title', 'Category Report')

@section('content')
@include('reports._date_filter', ['action' => 'reports.category', 'range' => $range])

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-semibold text-success">Income Categories</span>
                <span class="float-end fw-bold text-success">{{ number_format($data['totals']['income'], 2) }}</span>
            </div>
            <div class="card-body p-0">
                @if (empty($data['income']))
                    <x-empty-state icon="bi-tags" title="No income categories" />
                @else
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Category</th><th class="text-end">Total</th><th class="text-end">%</th></tr></thead>
                        <tbody>
                            @foreach ($data['income'] as $c)
                                <tr>
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-2">
                                            @if ($c['color'])<span class="rounded-circle" style="width:10px;height:10px;background:{{ $c['color'] }}"></span>@endif
                                            @if ($c['icon'])<i class="bi {{ $c['icon'] }}"></i>@endif
                                            <span class="small fw-semibold">{{ $c['name'] }}</span>
                                        </span>
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
            <div class="card-header bg-white">
                <span class="fw-semibold text-danger">Expense Categories</span>
                <span class="float-end fw-bold text-danger">{{ number_format($data['totals']['expense'], 2) }}</span>
            </div>
            <div class="card-body p-0">
                @if (empty($data['expense']))
                    <x-empty-state icon="bi-tags" title="No expense categories" />
                @else
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Category</th><th class="text-end">Total</th><th class="text-end">%</th></tr></thead>
                        <tbody>
                            @foreach ($data['expense'] as $c)
                                <tr>
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-2">
                                            @if ($c['color'])<span class="rounded-circle" style="width:10px;height:10px;background:{{ $c['color'] }}"></span>@endif
                                            @if ($c['icon'])<i class="bi {{ $c['icon'] }}"></i>@endif
                                            <span class="small fw-semibold">{{ $c['name'] }}</span>
                                        </span>
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