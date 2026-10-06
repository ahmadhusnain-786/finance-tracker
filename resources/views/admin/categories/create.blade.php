@extends('admin.layouts.app')

@section('title', 'New System Category')
@section('page-title', 'New System Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0">Create System Category</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}" novalidate>
                    @csrf

                    @include('admin.categories._form', ['category' => $category, 'types' => $types])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-save me-2"></i>Create
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection