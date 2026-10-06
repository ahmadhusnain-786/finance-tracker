@extends('layouts.guest')

@section('title', 'About')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-3">About {{ config('app.name') }}</h1>
            <p class="lead text-muted">
                {{ config('app.name') }} is a personal finance management platform built to help you
                understand, plan, and improve your financial life.
            </p>
            <p>
                It provides a clean, private space for every user to record income, expenses,
                budgets, savings goals, and see the numbers that matter — automatically.
            </p>
            <p class="mb-0">Built with Laravel, MySQL, and Bootstrap 5.</p>
        </div>
    </div>
</div>
@endsection