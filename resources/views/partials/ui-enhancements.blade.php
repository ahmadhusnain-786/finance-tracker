{{-- UI Enhancements: dark mode styles, auto-dismiss alerts, form double-submit protection --}}

<style>
    /* ========== DARK MODE OVERRIDES ========== */
    [data-bs-theme="dark"] body {
        background-color: #0f172a;
    }
    [data-bs-theme="dark"] .bg-white,
    [data-bs-theme="dark"] .card {
        background-color: #1e293b !important;
        color: #e2e8f0;
    }
    [data-bs-theme="dark"] .card-header,
    [data-bs-theme="dark"] .card-footer {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #e2e8f0;
    }
    [data-bs-theme="dark"] header,
    [data-bs-theme="dark"] footer {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1;
    }
    [data-bs-theme="dark"] .table {
        color: #cbd5e1;
        --bs-table-bg: transparent;
        --bs-table-hover-bg: #334155;
    }
    [data-bs-theme="dark"] .table thead.table-light th,
    [data-bs-theme="dark"] .table-light {
        background-color: #334155 !important;
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    [data-bs-theme="dark"] .table td,
    [data-bs-theme="dark"] .table th {
        border-color: #334155;
    }
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #1e293b;
        border-color: #334155;
        color: #e2e8f0;
    }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        background-color: #1e293b;
        color: #e2e8f0;
        border-color: #3b82f6;
    }
    [data-bs-theme="dark"] .form-control::placeholder {
        color: #64748b;
    }
    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .border-bottom,
    [data-bs-theme="dark"] .border-top,
    [data-bs-theme="dark"] .border-end,
    [data-bs-theme="dark"] .border-start {
        border-color: #334155 !important;
    }
    [data-bs-theme="dark"] .text-dark {
        color: #e2e8f0 !important;
    }
    [data-bs-theme="dark"] .text-muted {
        color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] hr,
    [data-bs-theme="dark"] .dropdown-divider {
        border-color: #334155 !important;
        opacity: 1;
    }
    [data-bs-theme="dark"] .dropdown-menu {
        background-color: #1e293b;
        border-color: #334155;
    }
    [data-bs-theme="dark"] .dropdown-item {
        color: #cbd5e1;
    }
    [data-bs-theme="dark"] .dropdown-item:hover {
        background-color: #334155;
        color: #fff;
    }
    [data-bs-theme="dark"] .modal-content {
        background-color: #1e293b;
        color: #e2e8f0;
    }
    [data-bs-theme="dark"] .list-group-item {
        background-color: #1e293b;
        color: #cbd5e1;
        border-color: #334155;
    }
    [data-bs-theme="dark"] .alert-info    { background-color: #1e3a5f; color: #bae6fd; border-color: #1e40af; }
    [data-bs-theme="dark"] .alert-success { background-color: #14532d; color: #bbf7d0; border-color: #166534; }
    [data-bs-theme="dark"] .alert-warning { background-color: #713f12; color: #fde68a; border-color: #854d0e; }
    [data-bs-theme="dark"] .alert-danger  { background-color: #7f1d1d; color: #fecaca; border-color: #991b1b; }
    [data-bs-theme="dark"] .bg-light {
        background-color: #0f172a !important;
    }
    [data-bs-theme="dark"] .btn-light {
        background-color: #334155;
        border-color: #334155;
        color: #e2e8f0;
    }
    [data-bs-theme="dark"] .btn-light:hover {
        background-color: #475569;
        color: #fff;
    }
    [data-bs-theme="dark"] .progress {
        background-color: #334155;
    }
    [data-bs-theme="dark"] .bg-primary-subtle    { background-color: #1e3a5f !important; color: #93c5fd !important; }
    [data-bs-theme="dark"] .bg-success-subtle    { background-color: #14532d !important; color: #86efac !important; }
    [data-bs-theme="dark"] .bg-danger-subtle     { background-color: #7f1d1d !important; color: #fca5a5 !important; }
    [data-bs-theme="dark"] .bg-warning-subtle    { background-color: #713f12 !important; color: #fcd34d !important; }
    [data-bs-theme="dark"] .bg-info-subtle       { background-color: #164e63 !important; color: #7dd3fc !important; }
    [data-bs-theme="dark"] .bg-secondary-subtle  { background-color: #334155 !important; color: #cbd5e1 !important; }
    [data-bs-theme="dark"] .text-success-emphasis,
    [data-bs-theme="dark"] .text-danger-emphasis,
    [data-bs-theme="dark"] .text-primary-emphasis,
    [data-bs-theme="dark"] .text-warning-emphasis,
    [data-bs-theme="dark"] .text-info-emphasis,
    [data-bs-theme="dark"] .text-secondary-emphasis {
        color: inherit !important;
    }
    [data-bs-theme="dark"] .app-sidebar,
    [data-bs-theme="dark"] .admin-sidebar {
        /* Sidebars already dark, keep as is */
    }

    /* ========== FOCUS-VISIBLE FOR KEYBOARD USERS ========== */
    *:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 3px;
    }
    a:focus-visible,
    button:focus-visible,
    .btn:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
    .btn:focus-visible {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
    }

    /* ========== PRINT STYLES (reports, receipts) ========== */
    @media print {
        .app-sidebar,
        .admin-sidebar,
        header,
        footer,
        .navbar,
        .btn,
        .dropdown,
        .card-footer,
        .no-print,
        #sidebarBackdrop,
        #adminBackdrop {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .card {
            border: 1px solid #dddddd !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
        main {
            padding: 0 !important;
        }
        .container,
        .container-fluid {
            max-width: 100% !important;
        }
    }
</style>

<script>
    // ========== THEME TOGGLE ==========
    (function () {
        document.addEventListener('DOMContentLoaded', function () {

            function updateIcons(theme) {
                document.querySelectorAll('[data-theme-icon]').forEach(icon => {
                    icon.className = theme === 'dark'
                        ? 'bi bi-sun fs-5'
                        : 'bi bi-moon-stars fs-5';
                });
                document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
                    btn.setAttribute('title',
                        theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
                    );
                });
            }

            document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const next = current === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-bs-theme', next);
                    localStorage.setItem('ft-theme', next);
                    updateIcons(next);
                });
            });

            // Sync icon on page load
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateIcons(current);

            // ========== AUTO-DISMISS FLASH ALERTS (5 seconds) ==========
            document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                setTimeout(function () {
                    try {
                        const instance = bootstrap.Alert.getOrCreateInstance(alert);
                        instance.close();
                    } catch (e) {
                        alert.classList.remove('show');
                        setTimeout(() => alert.remove(), 200);
                    }
                }, 5000);
            });

            // ========== FORM DOUBLE-SUBMIT PROTECTION ==========
            document.querySelectorAll('form').forEach(function (form) {
                if (form.dataset.noLoading === 'true') return;

                form.addEventListener('submit', function () {
                    const btn = form.querySelector('button[type="submit"]');
                    if (!btn || btn.disabled) return;

                    // Small delay so validation can stop the submit
                    setTimeout(() => {
                        if (btn.disabled) return; // already disabled
                        btn.disabled = true;
                        const original = btn.innerHTML;
                        btn.dataset.originalHtml = original;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + original;
                    }, 40);
                });
            });
        });
    })();
</script>