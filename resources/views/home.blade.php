@extends('layouts.guest')

@section('title', 'Home')

@section('content')
<div class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="badge bg-primary-subtle text-primary-emphasis mb-3 px-3 py-2">
                <i class="bi bi-stars me-1"></i>Personal Finance, Simplified
            </span>
            <h1 class="display-4 fw-bold lh-sm">
                Take control of your <span class="text-primary">money</span>.
            </h1>
            <p class="lead text-muted mt-3">
                Track income, expenses, budgets, and savings goals — all in one clean dashboard.
            </p>
            <div class="mt-4">
                <a href="{{ url('/register') }}" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-rocket-takeoff me-2"></i>Get Started
                </a>
                <a href="{{ url('/features') }}" class="btn btn-outline-secondary btn-lg px-4 ms-2">See Features</a>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="p-5 bg-white rounded-4 shadow-sm border">
                <i class="bi bi-graph-up-arrow display-1 text-primary"></i>
                <p class="mt-3 mb-0 text-muted">Your finances at a glance.</p>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 mt-5">
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-primary fs-2"><i class="bi bi-cash-coin"></i></div>
                    <h5 class="card-title mt-2">Income & Expenses</h5>
                    <p class="card-text text-muted small">Log every transaction in seconds.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-success fs-2"><i class="bi bi-piggy-bank"></i></div>
                    <h5 class="card-title mt-2">Budgets & Goals</h5>
                    <p class="card-text text-muted small">Track budgets and savings goals with live progress.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-warning fs-2"><i class="bi bi-bar-chart-line"></i></div>
                    <h5 class="card-title mt-2">Reports & Charts</h5>
                    <p class="card-text text-muted small">Understand trends with built-in charts.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection