@extends('layouts.app')

@section('title', 'Edit Goal')
@section('page-title', 'Edit Goal')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-semibold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Goal</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    Current saved amount ({{ number_format((float) $goal->current_amount, 2) }})
                    is calculated from contributions and cannot be edited here.
                </div>

                <form method="POST" action="{{ route('goals.update', $goal) }}" novalidate>
                    @csrf
                    @method('PUT')

                    @include('goals._form', ['goal' => $goal])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update Goal
                        </button>
                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection