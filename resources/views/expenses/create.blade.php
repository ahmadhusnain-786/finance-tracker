@extends('layouts.app')

@section('title', 'Add Expense')
@section('page-title', 'Add Expense')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-receipt me-2 text-danger"></i>New Expense Record</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('expenses.store') }}" novalidate>
                    @csrf

                    @include('expenses._form', ['expense' => $expense])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-save me-2"></i>Save Expense
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection