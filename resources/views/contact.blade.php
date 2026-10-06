@extends('layouts.guest')

@section('title', 'Contact')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h1 class="fw-bold mb-3">Contact</h1>
            <p class="text-muted">Have a question or feedback? We'd love to hear from you.</p>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-envelope-fill text-primary me-3 fs-4"></i>
                            <span>support@financetracker.local</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-primary me-3 fs-4"></i>
                            <span>Pakistan</span>
                        </li>
                    </ul>
                </div>
            </div>

            <p class="text-muted small mt-3 mb-0">A contact form will be added in a later phase.</p>
        </div>
    </div>
</div>
@endsection