@extends('layouts.app')

@section('title', 'Add Income')
@section('page-title', 'Add Income')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>New Income Record</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('income.store') }}" novalidate>
                    @csrf

                    @include('income._form', ['income' => $income])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Save Income
                        </button>
                        <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection