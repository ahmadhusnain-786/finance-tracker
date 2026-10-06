@extends('layouts.app')

@section('title', 'Edit Recurring Template')
@section('page-title', 'Edit Recurring Template')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Template</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('recurring.update', $template) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('recurring._form', [
                        'template' => $template,
                        'categories' => $categories,
                        'paymentMethods' => $paymentMethods,
                    ])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update Template
                        </button>
                        <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection