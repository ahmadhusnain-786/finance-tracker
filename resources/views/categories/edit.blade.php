@extends('layouts.app')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0">Edit "{{ $category->name }}"</h5>
                <span class="badge {{ $category->type->badgeClass() }}">{{ $category->type->label() }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.update', $category) }}" novalidate>
                    @csrf
                    @method('PUT')

                    @include('categories._form', ['category' => $category, 'types' => $types])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection