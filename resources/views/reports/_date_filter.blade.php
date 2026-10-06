{{-- Reusable date-range filter for all report pages.
     Expects: $range (from resolveRange), $action (route name). --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route($action) }}" class="row g-2 align-items-end" id="reportRangeForm">
            <div class="col-md-3">
                <label class="form-label small mb-1">Date Range</label>
                <select name="range" class="form-select" id="reportRangeSelect">
                    <option value="today"      {{ $range['key'] === 'today'      ? 'selected' : '' }}>Today</option>
                    <option value="this_month" {{ $range['key'] === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $range['key'] === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year"  {{ $range['key'] === 'this_year'  ? 'selected' : '' }}>This Year</option>
                    <option value="last_year"  {{ $range['key'] === 'last_year'  ? 'selected' : '' }}>Last Year</option>
                    <option value="custom"     {{ $range['key'] === 'custom'     ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div class="col-md-3 custom-range-fields"
                 style="{{ $range['key'] === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control"
                       value="{{ request('from', $range['from']->toDateString()) }}">
            </div>
            <div class="col-md-3 custom-range-fields"
                 style="{{ $range['key'] === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control"
                       value="{{ request('to', $range['to']->toDateString()) }}">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Apply
                </button>
            </div>
        </form>

        <div class="text-muted small mt-2">
            <i class="bi bi-calendar-range me-1"></i>
            <strong>{{ $range['label'] }}</strong>
            ({{ $range['from']->format('d M Y') }} — {{ $range['to']->format('d M Y') }})
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sel = document.getElementById('reportRangeSelect');
        const fields = document.querySelectorAll('.custom-range-fields');
        if (!sel) return;
        sel.addEventListener('change', function () {
            fields.forEach(f => f.style.display = (this.value === 'custom') ? '' : 'none');
        });
    });
</script>
@endpush