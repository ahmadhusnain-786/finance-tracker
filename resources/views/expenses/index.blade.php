@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Expenses')

@section('content')

{{-- Total card --}}
<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm bg-danger-subtle">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-receipt fs-1 text-danger me-3"></i>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Total Expenses (filtered)</div>
                    <div class="fs-3 fw-bold text-danger">
                        Rs. {{ number_format($total, 2) }}
                    </div>
                    <div class="text-muted small">{{ $count }} record(s)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 d-flex align-items-center justify-content-md-end">
        <a href="{{ route('expenses.create') }}" class="btn btn-danger btn-lg">
            <i class="bi bi-plus-lg me-2"></i>Add Expense
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('expenses.index') }}" class="row g-2">
            <div class="col-md-3">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       class="form-control" placeholder="Search title...">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}"
                            {{ (int) $filters['category_id'] === $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Payment</label>
                <select name="payment_method_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($paymentMethods as $m)
                        <option value="{{ $m->id }}"
                            {{ (int) $filters['payment_method_id'] === $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from_date" value="{{ $filters['from_date'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to_date" value="{{ $filters['to_date'] }}" class="form-control">
            </div>
            <div class="col-md-1">
                <label class="form-label small mb-1">Sort</label>
                <select name="sort" class="form-select">
                    <option value="date_desc"   {{ $filters['sort'] === 'date_desc'   ? 'selected' : '' }}>Newest</option>
                    <option value="date_asc"    {{ $filters['sort'] === 'date_asc'    ? 'selected' : '' }}>Oldest</option>
                    <option value="amount_desc" {{ $filters['sort'] === 'amount_desc' ? 'selected' : '' }}>Amount ↓</option>
                    <option value="amount_asc"  {{ $filters['sort'] === 'amount_asc'  ? 'selected' : '' }}>Amount ↑</option>
                    <option value="title_asc"   {{ $filters['sort'] === 'title_asc'   ? 'selected' : '' }}>Title A-Z</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-funnel me-1"></i>Apply Filters
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- List --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($expenses->isEmpty())
            <x-empty-state
                icon="bi-receipt"
                title="No expense records found"
                message="Try adjusting the filters, or add your first expense."
                action-text="Add Expense"
                :action-url="route('expenses.create')" />
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
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $row)
                            <tr>
                                <td class="text-nowrap">{{ $row->date->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->title }}</div>
                                    @if ($row->is_recurring)
                                        <span class="badge bg-info-subtle text-info-emphasis small">
                                            <i class="bi bi-arrow-repeat me-1"></i>{{ $row->recurring_frequency?->label() }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->category)
                                        <span class="d-inline-flex align-items-center gap-1">
                                            @if ($row->category->icon)
                                                <i class="bi {{ $row->category->icon }}"></i>
                                            @endif
                                            {{ $row->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->paymentMethod)
                                        <i class="bi {{ $row->paymentMethod->type->icon() }} me-1 text-muted"></i>
                                        {{ $row->paymentMethod->name }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <span class="fw-bold text-danger">− Rs. {{ number_format((float) $row->amount, 2) }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    @can('update', $row)
                                        <a href="{{ route('expenses.edit', $row) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $row)
                                        <form method="POST" action="{{ route('expenses.destroy', $row) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this expense record?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($expenses->hasPages())
        <div class="card-footer bg-white">
            {{ $expenses->links() }}
        </div>
    @endif
</div>

@endsection