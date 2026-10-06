@extends('layouts.app')
@section('title', 'Yearly Report')
@section('page-title', 'Yearly Report')

@section('content')
@include('reports._date_filter', ['action' => 'reports.yearly', 'range' => $range])

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if (empty($data['rows']))
            <x-empty-state icon="bi-calendar3" title="No data" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Year</th>
                            <th class="text-end">Income</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Net Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['rows'] as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r['year'] }}</td>
                                <td class="text-end text-success">+ {{ number_format($r['income'], 2) }}</td>
                                <td class="text-end text-danger">− {{ number_format($r['expense'], 2) }}</td>
                                <td class="text-end fw-bold {{ $r['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                                    {{ number_format($r['balance'], 2) }}
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