@extends('layouts.app')

@section('title', 'Edit Payment Method')
@section('page-title', 'Edit Payment Method')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0">Edit "{{ $method->name }}"</h5>
                <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $method->type->label() }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('payment-methods.update', $method) }}" novalidate>
                    @csrf
                    @method('PUT')

                    @include('payment-methods._form', ['method' => $method, 'types' => $types])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                        <a href="{{ route('payment-methods.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection