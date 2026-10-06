@extends('layouts.app')

@section('title', 'Edit Income')
@section('page-title', 'Edit Income')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Income</h5>
                <span class="text-muted small">Created {{ $income->created_at->diffForHumans() }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('income.update', $income) }}" novalidate>
                    @csrf
                    @method('PUT')

                    @include('income._form', ['income' => $income])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update Income
                        </button>
                        <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection