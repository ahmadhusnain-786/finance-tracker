@props([
    'icon'      => 'bi-inbox',
    'title'     => 'Nothing here yet',
    'message'   => '',
    'actionText' => null,
    'actionUrl'  => null,
])

<div class="text-center py-5">
    <i class="bi {{ $icon }} display-4 text-muted"></i>
    <h5 class="fw-semibold mt-3 mb-1">{{ $title }}</h5>
    @if ($message)
        <p class="text-muted small mb-3">{{ $message }}</p>
    @endif
    @if ($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ $actionText }}
        </a>
    @endif
</div>