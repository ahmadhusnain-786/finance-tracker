@extends('layouts.app')

@section('title', 'New Recurring Template')
@section('page-title', 'New Recurring Template')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Create Template</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('recurring.store') }}" novalidate>
                    @csrf
                    @include('recurring._form', [
                        'template' => $template,
                        'categories' => $categories,
                        'paymentMethods' => $paymentMethods,
                    ])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Create Template
                        </button>
                        <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection