@extends('layouts.app')

@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('content')

{{-- Summary strip --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-cash-stack fs-2 text-success me-3"></i>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Income (filtered)</div>
                    <div class="fs-5 fw-bold text-success">
                        + Rs. {{ number_format($totalIncome, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-receipt fs-2 text-danger me-3"></i>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Expenses (filtered)</div>
                    <div class="fs-5 fw-bold text-danger">
                        − Rs. {{ number_format($totalExpense, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-wallet2 fs-2 {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} me-3"></i>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Net Balance</div>
                    <div class="fs-5 fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ $netBalance >= 0 ? '+' : '−' }} Rs. {{ number_format(abs($netBalance), 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('transactions.index') }}" class="row g-2">
            <div class="col-md-3">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       class="form-control" placeholder="Search title...">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">All</option>
                    <option value="income"  {{ $filters['type'] === 'income'  ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ $filters['type'] === 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
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
            <div class="col-md-3">
                <label class="form-label small mb-1">Sort</label>
                <select name="sort" class="form-select">
                    <option value="date_desc"   {{ $filters['sort'] === 'date_desc'   ? 'selected' : '' }}>Newest first</option>
                    <option value="date_asc"    {{ $filters['sort'] === 'date_asc'    ? 'selected' : '' }}>Oldest first</option>
                    <option value="amount_desc" {{ $filters['sort'] === 'amount_desc' ? 'selected' : '' }}>Amount ↓</option>
                    <option value="amount_asc"  {{ $filters['sort'] === 'amount_asc'  ? 'selected' : '' }}>Amount ↑</option>
                    <option value="title_asc"   {{ $filters['sort'] === 'title_asc'   ? 'selected' : '' }}>Title A-Z</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">From date</label>
                <input type="date" name="from_date" value="{{ $filters['from_date'] }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">To date</label>
                <input type="date" name="to_date" value="{{ $filters['to_date'] }}" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-funnel me-1"></i>Apply Filters
                </button>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- List --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <span class="fw-semibold">
            All Transactions <span class="text-muted">({{ $transactions->total() }})</span>
        </span>
    </div>

    <div class="card-body p-0">
        @if ($transactions->isEmpty())
            <x-empty-state
                icon="bi-list-ul"
                title="No transactions found"
                message="Try adjusting the filters, or add income / expenses from their pages." />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:110px;">Date</th>
                            <th style="width:90px;">Type</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Payment</th>
                            <th class="text-end" style="width:160px;">Amount</th>
                            <th class="text-end" style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $row)
                            <tr>
                                <td class="text-nowrap">{{ $row->date->format('d M Y') }}</td>
                                <td>
                                    @if ($row->type === 'income')
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            <i class="bi bi-arrow-down-left me-1"></i>Income
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis">
                                            <i class="bi bi-arrow-up-right me-1"></i>Expense
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $row->title }}</div>
                                    @if ($row->is_recurring)
                                        <span class="badge bg-info-subtle text-info-emphasis small">
                                            <i class="bi bi-arrow-repeat me-1"></i>{{ $row->frequency?->label() }}
                                        </span>
                                    @endif
                                    @if ($row->description)
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($row->description, 60) }}</div>
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
                                    @if ($row->type === 'income')
                                        <span class="fw-bold text-success">
                                            + Rs. {{ number_format((float) $row->amount, 2) }}
                                        </span>
                                    @else
                                        <span class="fw-bold text-danger">
                                            − Rs. {{ number_format((float) $row->amount, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ $row->edit_route }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ $row->delete_route }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this {{ $row->type }} record?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($transactions->hasPages())
        <div class="card-footer bg-white">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

@endsection