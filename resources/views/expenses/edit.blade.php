@extends('layouts.app')

@section('title', 'Edit Expense')
@section('page-title', 'Edit Expense')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Expense</h5>
                <span class="text-muted small">Created {{ $expense->created_at->diffForHumans() }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('expenses.update', $expense) }}" novalidate>
                    @csrf
                    @method('PUT')

                    @include('expenses._form', ['expense' => $expense])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-save me-2"></i>Update Expense
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection