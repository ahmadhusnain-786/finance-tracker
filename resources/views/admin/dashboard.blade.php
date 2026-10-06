@extends('admin.layouts.app')

@section('title', 'Admin Overview')
@section('page-title', 'System Overview')

@section('content')

@php
    $usersRouteExists = \Illuminate\Support\Facades\Route::has('admin.users.index');
@endphp

{{-- Top stat cards --}}
<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Total Users</div>
                    <div class="fs-3 fw-bold mt-1">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-muted small">
                        <span class="text-success">{{ $stats['active_users'] }} active</span>
                        · <span class="text-danger">{{ $stats['inactive_users'] }} inactive</span>
                    </div>
                </div>
                <div class="rounded-circle bg-primary-subtle p-3">
                    <i class="bi bi-people-fill fs-4 text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Admins / Users</div>
                    <div class="fs-3 fw-bold mt-1">
                        <span class="text-danger">{{ $stats['admin_users'] }}</span>
                        <span class="text-muted">/</span>
                        <span class="text-primary">{{ $stats['regular_users'] }}</span>
                    </div>
                    <div class="text-muted small">New today: {{ $stats['users_today'] }}</div>
                </div>
                <div class="rounded-circle bg-danger-subtle p-3">
                    <i class="bi bi-shield-lock-fill fs-4 text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Transactions</div>
                    <div class="fs-3 fw-bold mt-1">
                        {{ number_format($stats['total_income_records'] + $stats['total_expense_records']) }}
                    </div>
                    <div class="text-muted small">
                        {{ $stats['total_income_records'] }} income · {{ $stats['total_expense_records'] }} expense
                    </div>
                </div>
                <div class="rounded-circle bg-success-subtle p-3">
                    <i class="bi bi-list-ul fs-4 text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Budgets / Goals</div>
                    <div class="fs-3 fw-bold mt-1">
                        {{ $stats['total_budgets'] }} <span class="text-muted">/</span> {{ $stats['total_goals'] }}
                    </div>
                    <div class="text-muted small">Logged in today: {{ $stats['logged_in_today'] }}</div>
                </div>
                <div class="rounded-circle bg-warning-subtle p-3">
                    <i class="bi bi-pie-chart-fill fs-4 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Recent users --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Registrations</span>
                @if ($usersRouteExists)
                    <a href="{{ route('admin.users.index') }}" class="small text-decoration-none">View all</a>
                @endif
            </div>
            <div class="card-body p-0">
                @if ($recentUsers->isEmpty())
                    <x-empty-state icon="bi-people" title="No users yet" />
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="text-end">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentUsers as $u)
                                    <tr>
                                        <td>
                                            @if (\Illuminate\Support\Facades\Route::has('admin.users.show'))
                                                <a href="{{ route('admin.users.show', $u) }}"
                                                   class="text-decoration-none fw-semibold text-dark">
                                                    {{ $u->name }}
                                                </a>
                                            @else
                                                <span class="fw-semibold">{{ $u->name }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $u->email }}</td>
                                        <td>
                                            @if ($u->isAdmin())
                                                <span class="badge bg-danger-subtle text-danger-emphasis">Admin</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">User</span>
                                            @endif
                                        </td>
                                        <td class="text-end small text-muted">{{ $u->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent logins --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-semibold">Recent Logins</span>
            </div>
            <div class="card-body p-0">
                @if ($recentLogins->isEmpty())
                    <x-empty-state icon="bi-box-arrow-in-right" title="No logins recorded yet" />
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-end">Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentLogins as $u)
                                    <tr>
                                        <td>
                                            @if (\Illuminate\Support\Facades\Route::has('admin.users.show'))
                                                <a href="{{ route('admin.users.show', $u) }}"
                                                   class="text-decoration-none fw-semibold text-dark">
                                                    {{ $u->name }}
                                                </a>
                                            @else
                                                <span class="fw-semibold">{{ $u->name }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $u->email }}</td>
                                        <td class="text-end small text-muted">{{ $u->last_login_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Top active users (by record count — no amounts revealed) --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white">
        <span class="fw-semibold">Most Active Users</span>
        <span class="text-muted small ms-2">Ranked by number of records — not amounts</span>
    </div>
    <div class="card-body p-0">
        @if ($topUsers->isEmpty())
            <x-empty-state icon="bi-bar-chart" title="No activity yet" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Income</th>
                            <th class="text-center">Expenses</th>
                            <th class="text-center">Budgets</th>
                            <th class="text-center">Goals</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topUsers as $u)
                            <tr>
                                <td>
                                    @if (\Illuminate\Support\Facades\Route::has('admin.users.show'))
                                        <a href="{{ route('admin.users.show', $u) }}"
                                           class="text-decoration-none fw-semibold text-dark">
                                            {{ $u->name }}
                                        </a>
                                    @else
                                        <span class="fw-semibold">{{ $u->name }}</span>
                                    @endif
                                </td>
                                <td class="text-center small">{{ $u->income_count }}</td>
                                <td class="text-center small">{{ $u->expenses_count }}</td>
                                <td class="text-center small">{{ $u->budgets_count }}</td>
                                <td class="text-center small">{{ $u->financial_goals_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection