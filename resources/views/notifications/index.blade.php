@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <span class="text-muted small">
            {{ $unreadCount }} unread · {{ $notifications->total() }} total
        </span>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('notifications.scan') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-repeat me-1"></i>Scan Now
            </button>
        </form>
        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-check2-all me-1"></i>Mark all read
                </button>
            </form>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('notifications.index') }}" class="d-flex align-items-center gap-2">
            <label class="small text-muted mb-0">Show:</label>
            <select name="filter" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="all"    {{ $filter === 'all'    ? 'selected' : '' }}>All</option>
                <option value="unread" {{ $filter === 'unread' ? 'selected' : '' }}>Unread only</option>
            </select>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($notifications->isEmpty())
            <x-empty-state
                icon="bi-bell-slash"
                title="No notifications"
                message="You're all caught up. We'll let you know when a budget is close to its limit or a goal deadline approaches." />
        @else
            <div class="list-group list-group-flush">
                @foreach ($notifications as $n)
                    @php
                        $data   = $n->data;
                        $isRead = $n->read_at !== null;
                    @endphp
                    <div class="list-group-item d-flex align-items-start gap-3 {{ $isRead ? '' : 'bg-light' }}">
                        <i class="bi {{ $data['icon'] ?? 'bi-bell' }} {{ $data['icon_class'] ?? 'text-secondary' }} fs-4 mt-1"></i>

                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $data['title'] ?? 'Notification' }}</div>
                            <div class="text-muted small">{{ $data['message'] ?? '' }}</div>
                            <div class="text-muted mt-1" style="font-size: 11px;">
                                <i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                                @if ($isRead)
                                    <span class="ms-2"><i class="bi bi-check2 me-1"></i>Read</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-1">
                            @unless ($isRead)
                                <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Open">
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('notifications.destroy', $n->id) }}"
                                  onsubmit="return confirm('Delete this notification?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($notifications->hasPages())
        <div class="card-footer bg-white">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@endsection