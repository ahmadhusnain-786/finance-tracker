@extends('layouts.app')

@section('title', 'New Budget')
@section('page-title', 'New Budget')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Create Budget</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('budgets.store') }}" novalidate>
                    @csrf

                    @include('budgets._form', [
                        'budget' => $budget,
                        'categories' => $categories,
                        'usedCategoryIds' => $usedCategoryIds,
                    ])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Create Budget
                        </button>
                        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection