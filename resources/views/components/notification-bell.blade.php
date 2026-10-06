@auth
    @php
        $unread = auth()->user()->unreadNotifications()->count();
        $recent = auth()->user()->notifications()->latest()->limit(5)->get();
    @endphp

    <div class="dropdown me-2">
        <button class="btn btn-light position-relative"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                aria-expanded="false"
                aria-label="Notifications">
            <i class="bi bi-bell fs-5"></i>
            @if ($unread > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unread > 99 ? '99+' : $unread }}
                </span>
            @endif
        </button>

        <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" style="width: 360px; max-width: 92vw;">
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="fw-semibold small">Notifications</span>
                @if ($unread > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">
                            Mark all read
                        </button>
                    </form>
                @endif
            </div>

            <div style="max-height: 360px; overflow-y: auto;">
                @if ($recent->isEmpty())
                    <div class="px-3 py-4 text-center text-muted small">
                        <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                        No notifications yet.
                    </div>
                @else
                    @foreach ($recent as $n)
                        @php
                            $data   = $n->data;
                            $isRead = $n->read_at !== null;
                        @endphp
                        <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="dropdown-item d-flex align-items-start gap-2 py-2 border-bottom text-wrap {{ $isRead ? 'opacity-75' : 'bg-light' }}"
                                    style="white-space: normal;">
                                <i class="bi {{ $data['icon'] ?? 'bi-bell' }} {{ $data['icon_class'] ?? 'text-secondary' }} mt-1"></i>
                                <div class="small">
                                    <div class="fw-semibold">{{ $data['title'] ?? 'Notification' }}</div>
                                    <div class="text-muted">{{ $data['message'] ?? '' }}</div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $n->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </button>
                        </form>
                    @endforeach
                @endif
            </div>

            <a href="{{ route('notifications.index') }}"
               class="dropdown-item text-center small py-2 text-primary text-decoration-none border-top">
                View all notifications
            </a>
        </div>
    </div>
@endauth