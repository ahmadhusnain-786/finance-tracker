@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('categories.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       class="form-control" placeholder="Search by name...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">All types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t->value }}"
                            {{ $filters['type'] === $t->value ? 'selected' : '' }}>
                            {{ $t->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Scope</label>
                <select name="scope" class="form-select">
                    <option value="">All</option>
                    <option value="mine"   {{ $filters['scope'] === 'mine'   ? 'selected' : '' }}>My categories</option>
                    <option value="system" {{ $filters['scope'] === 'system' ? 'selected' : '' }}>System</option>
                </select>
            </div>
            <div class="col-md-2 d-grid d-md-flex gap-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">All Categories <span class="text-muted">({{ $categories->total() }})</span></span>
        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Category
        </a>
    </div>

    <div class="card-body p-0">
        @if ($categories->isEmpty())
            <x-empty-state
                icon="bi-tags"
                title="No categories found"
                message="Try clearing the filters, or create your first custom category."
                action-text="New Category"
                :action-url="route('categories.create')" />
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-center">Used</th>
                            <th>Scope</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-2">
                                        @if ($category->color)
                                            <span class="rounded-circle d-inline-block"
                                                  style="width:12px;height:12px;background:{{ $category->color }}"></span>
                                        @endif
                                        @if ($category->icon)
                                            <i class="bi {{ $category->icon }}"></i>
                                        @endif
                                        <span class="fw-semibold">{{ $category->name }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $category->type->badgeClass() }}">
                                        {{ $category->type->label() }}
                                    </span>
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $category->income_count + $category->expenses_count }}
                                </td>
                                <td>
                                    @if ($category->is_system)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">System</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary-emphasis">Mine</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @can('update', $category)
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan

                                    @can('delete', $category)
                                        <form method="POST"
                                              action="{{ route('categories.destroy', $category) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this category? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($categories->hasPages())
        <div class="card-footer bg-white">
            {{ $categories->links() }}
        </div>
    @endif
</div>

@endsection