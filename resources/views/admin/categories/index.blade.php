@extends('admin.layouts.app')

@section('title', 'Default Categories')
@section('page-title', 'Default Categories')

@section('content')

<div class="alert alert-info border-0 shadow-sm">
    <i class="bi bi-info-circle me-2"></i>
    <strong>System categories</strong> are visible to every user as defaults.
    They cannot be deleted while any user has records in them.
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Category name...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">All types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t->value }}" {{ $filters['type'] === $t->value ? 'selected' : '' }}>
                            {{ $t->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-2 text-md-end">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-danger w-100">
                    <i class="bi bi-plus-lg me-1"></i>New
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <span class="fw-semibold">System Categories <span class="text-muted">({{ $categories->count() }})</span></span>
    </div>
    <div class="card-body p-0">
        @if ($categories->isEmpty())
            <x-empty-state icon="bi-tags" title="No system categories" message="Create default categories that all users can choose from." />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $cat)
                            <tr>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-2">
                                        @if ($cat->color)
                                            <span class="rounded-circle d-inline-block"
                                                  style="width:12px;height:12px;background:{{ $cat->color }}"></span>
                                        @endif
                                        @if ($cat->icon)
                                            <i class="bi {{ $cat->icon }}"></i>
                                        @endif
                                        <span class="fw-semibold">{{ $cat->name }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $cat->type->badgeClass() }}">{{ $cat->type->label() }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.categories.edit', $cat) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this system category? It will be removed for ALL users.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection