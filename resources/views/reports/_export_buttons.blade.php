{{-- Expects: $exportBaseRoute (e.g. 'reports.income.export') --}}
<div class="d-flex gap-2">
    <div class="dropdown">
        <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download me-1"></i>Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
                <a class="dropdown-item" href="{{ route($exportBaseRoute, array_merge(request()->query(), ['format' => 'csv'])) }}">
                    <i class="bi bi-filetype-csv me-2 text-success"></i>CSV (.csv)
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route($exportBaseRoute, array_merge(request()->query(), ['format' => 'xlsx'])) }}">
                    <i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route($exportBaseRoute, array_merge(request()->query(), ['format' => 'pdf'])) }}">
                    <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF (.pdf)
                </a>
            </li>
        </ul>
    </div>
</div>