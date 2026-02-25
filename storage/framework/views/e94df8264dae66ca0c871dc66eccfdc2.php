<?php $__env->startSection('title', 'Invoices Management'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .invoices-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .invoices-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .invoices-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
    }

    .add-invoice-btn {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.85rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        transition: background-color 0.3s ease;
        box-shadow: 0 10px 24px rgba(3, 37, 91, 0.25);
    }
    .add-invoice-btn:hover {
        background-color: #021b44;
        color: white;
    }
    .add-invoice-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .summary-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e5e5;
        height: 100%;
        transition: all 0.2s ease-in-out;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .summary-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .summary-icon {
        font-size: 1.25rem;
        opacity: 0.6;
    }
    .summary-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        margin: 0;
        letter-spacing: 0.5px;
    }
    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }
    .summary-card.total .summary-value {
        color: #3b82f6;
    }
    .summary-card.paid .summary-value {
        color: #10b981;
    }
    .summary-card.pending .summary-value {
        color: #f59e0b;
    }
    .summary-card.overdue .summary-value {
        color: #dc2626;
    }
    .summary-meta {
        font-size: 0.78rem;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 0.5rem;
    }
    .info-banner {
        background-color: #ecf4ff;
        border: 1px solid #c9ddff;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        color: #1d3a6d;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    .info-banner-icon {
        font-size: 1.5rem;
        line-height: 1;
    }
    .filters-card {
        background-color: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }
    .status-filters {
        display: inline-flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }
    .status-chip {
        border: 1px solid #cbd5e1;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        background-color: white;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .status-chip:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }
    .status-chip.active {
        background: #03255b;
        color: white;
        border-color: #03255b;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }
    .filter-search {
        flex-grow: 1;
        min-width: 240px;
        max-width: 320px;
        position: relative;
    }
    .filter-search input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid #d0d7e2;
        padding: 0.55rem 2.75rem 0.55rem 1.1rem;
        font-size: 0.92rem;
        color: #1f2937;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }
    .filter-search input:focus {
        outline: none;
        border-color: #03255b;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }
    .filter-search svg {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
    }
    .invoices-table-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        margin-bottom: 0;
    }

    .table-scroll-container {
        overflow-x: auto;
        overflow-y: visible;
    }

    .invoices-table {
        width: 100%;
        min-width: 100%;
        border-collapse: collapse;
        table-layout: auto;
    }
    .invoices-table thead {
        background: #f8fafc;
    }
    .invoices-table th {
        padding: 0.9rem 0.5rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 10;
    }
    .invoices-table th:first-child {
        width: 120px;
        max-width: 120px;
    }
    .invoices-table td {
        padding: 0.75rem 0.5rem;
    }
    .invoices-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }
    .invoices-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }
    .invoices-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }
    .invoices-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
    }
    .invoices-table td {
        padding: 0.75rem 0.75rem;
        font-size: 0.85rem;
        color: #1f2937;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }
    .invoices-table tbody tr {
        cursor: pointer;
    }
    .invoices-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .badge-status.paid {
        background: #dcfce7;
        color: #15803d;
    }
    .badge-status.pending {
        background: #fef3c7;
        color: #c26a09;
    }
    .badge-status.partial {
        background: #e0f2fe;
        color: #0369a1;
    }
    .badge-status.canceled {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-status.overdue {
        background: #fee2e2;
        color: #991b1b;
    }
    .overdue-indicator {
        font-size: 0.7rem;
        color: #dc2626;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .penalty-amount {
        color: #dc2626;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .due-date-info {
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .due-date-info.overdue {
        color: #dc2626;
        font-weight: 600;
    }
    .btn-apply-penalty {
        padding: 0.3rem 0.6rem;
        border: none;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #fef3c7;
        color: #c26a09;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .btn-apply-penalty:hover {
        background-color: #fcd34d;
    }
    .btn-apply-all-penalties {
        background-color: #fef3c7;
        color: #c26a09;
        border: 1px solid #fcd34d;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .btn-apply-all-penalties:hover {
        background-color: #fcd34d;
        color: #92400e;
    }
    .tenant-meta {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .tenant-name {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.9rem;
    }
    .tenant-name {
        color: #0f172a;
    }
    .tenant-room {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 500;
    }
    .amount-col {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: #1f2937;
    }
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .btn-add-payment {
        padding: 0.4rem 0.75rem;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #10b981;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .btn-add-payment i {
        font-size: 0.9rem;
    }
    .btn-add-payment:hover {
        background-color: #059669;
        color: white;
    }
    .invoice-metadata {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }
    .invoice-type {
        font-weight: 600;
        font-size: 0.8rem;
        color: #1e293b;
    }
    .invoice-date {
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 500;
    }
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #ffffff;
        flex-wrap: wrap;
        gap: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }
    .pagination-wrapper .form-select {
        width: auto;
        border-radius: 999px;
        min-width: 70px;
    }
    .pagination-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .pagination-center {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .pagination-right {
        display: flex;
        align-items: center;
    }
    /* Fix pagination styling */
    .pagination-wrapper .pagination {
        margin: 0;
        display: flex;
        list-style: none;
        gap: 0.25rem;
    }
    .pagination-wrapper .pagination .page-item {
        margin: 0;
    }
    .pagination-wrapper .pagination .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        text-decoration: none;
        background-color: white;
        font-size: 0.875rem;
        min-width: 38px;
        text-align: center;
        display: inline-block;
        transition: all 0.2s ease;
    }
    .pagination-wrapper .pagination .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #03255b;
    }
    .pagination-wrapper .pagination .page-item.active .page-link {
        background-color: #03255b;
        border-color: #03255b;
        color: white;
        font-weight: 600;
    }
    .pagination-wrapper .pagination .page-item.disabled .page-link {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .pagination-wrapper .pagination .page-link:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }
    /* Hide the large chevron icons if they exist */
    .pagination-wrapper svg {
        display: none !important;
    }
    /* Hide the "Showing X to Y" text from Laravel pagination since we display it manually */
    .pagination-wrapper nav > div:first-child {
        display: none !important; /* Hide mobile pagination */
    }
    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important; /* Hide the "Showing X to Y" text div */
    }
    /* Show only the pagination controls (ul.pagination) */
    .pagination-wrapper nav > div:last-child > div:last-child {
        display: block !important;
    }
    /* Style our custom "Showing X to Y" text */
    .pagination-center .small {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }
    .pagination-center .fw-semibold {
        font-weight: 600;
        color: #0f172a;
    }
    .no-data-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
        font-size: 0.95rem;
    }
    .no-data-state strong {
        display: block;
        color: #0f172a;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .legend {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 999px;
        background-color: #f1f5f9;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
    }
    @media (max-width: 992px) {
        .invoices-table th:nth-child(4),
        .invoices-table td:nth-child(4),
        .invoices-table th:nth-child(5),
        .invoices-table td:nth-child(5),
        .invoices-table th:nth-child(6),
        .invoices-table td:nth-child(6) {
            display: none;
        }
    }
    @media (max-width: 768px) {
        .invoices-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .add-invoice-btn {
            width: 100%;
            justify-content: center;
        }
        .filters-card {
            flex-direction: column;
            align-items: stretch;
        }
    }

    /* Highlight animation for invoice row */
    .highlight-invoice {
        animation: highlightFade 3s ease-out;
    }

    @keyframes highlightFade {
        0% {
            background-color: #fef3c7;
        }
        100% {
            background-color: transparent;
        }
    }

    /* Date Filter Dropdown */
    .date-filter-dropdown {
        position: relative;
        display: inline-block;
    }

    .date-filter-btn {
        background-color: white;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .date-filter-btn:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }

    .date-filter-btn.active {
        background-color: #03255b;
        border-color: #03255b;
        color: white;
    }

    .date-filter-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        min-width: 250px;
        z-index: 1000;
        display: none;
    }

    .date-filter-menu.show {
        display: block;
    }

    .date-filter-option {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
        font-size: 0.875rem;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }

    .date-filter-option:last-child {
        border-bottom: none;
    }

    .date-filter-option:hover {
        background-color: #f8fafc;
    }

    .date-filter-option.active {
        background-color: #e0f2fe;
        color: #03255b;
        font-weight: 600;
    }

    .custom-date-inputs {
        padding: 1rem;
        border-top: 1px solid #e2e8f0;
        display: none;
    }

    .custom-date-inputs.show {
        display: block;
    }

    .custom-date-inputs label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.25rem;
        display: block;
    }

    .custom-date-inputs input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
    }

    .custom-date-inputs button {
        width: 100%;
        padding: 0.5rem;
        background-color: #03255b;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
    }

    .custom-date-inputs button:hover {
        background-color: #021d47;
    }
</style>

<div class="invoices-page">

    <?php if($errors->any() && !request()->routeIs('payments.store')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following errors:
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="invoices-header mb-4 d-flex justify-content-between align-items-center">
        <h1 class="invoices-title">Invoices Management</h1>
        <div class="d-flex gap-2">
            <!-- Date Filter Dropdown -->
            <div class="date-filter-dropdown">
                <button type="button" class="date-filter-btn <?php echo e($dateFilter !== 'all' ? 'active' : ''); ?>" id="dateFilterBtn">
                    <i class="bi bi-calendar3"></i>
                    <span id="dateFilterLabel">
                        <?php if($dateFilter === 'today'): ?>
                            Today
                        <?php elseif($dateFilter === 'this_week'): ?>
                            This Week
                        <?php elseif($dateFilter === 'this_month'): ?>
                            This Month
                        <?php elseif($dateFilter === 'last_month'): ?>
                            Last Month
                        <?php elseif($dateFilter === 'this_year'): ?>
                            This Year
                        <?php elseif($dateFilter === 'custom'): ?>
                            Custom Range
                        <?php else: ?>
                            Date Filter
                        <?php endif; ?>
                    </span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="date-filter-menu" id="dateFilterMenu">
                    <div class="date-filter-option <?php echo e($dateFilter === 'all' ? 'active' : ''); ?>" data-filter="all">
                        <i class="bi bi-infinity"></i> All Time
                    </div>
                    <div class="date-filter-option <?php echo e($dateFilter === 'today' ? 'active' : ''); ?>" data-filter="today">
                        <i class="bi bi-calendar-day"></i> Today
                    </div>
                    <div class="date-filter-option <?php echo e($dateFilter === 'this_week' ? 'active' : ''); ?>" data-filter="this_week">
                        <i class="bi bi-calendar-week"></i> This Week
                    </div>
                    <div class="date-filter-option <?php echo e($dateFilter === 'this_month' ? 'active' : ''); ?>" data-filter="this_month">
                        <i class="bi bi-calendar-month"></i> This Month
                    </div>
                    <div class="date-filter-option <?php echo e($dateFilter === 'last_month' ? 'active' : ''); ?>" data-filter="last_month">
                        <i class="bi bi-calendar-minus"></i> Last Month
                    </div>
                    <div class="date-filter-option <?php echo e($dateFilter === 'this_year' ? 'active' : ''); ?>" data-filter="this_year">
                        <i class="bi bi-calendar-range"></i> This Year
                    </div>
                    <div class="date-filter-option <?php echo e($dateFilter === 'custom' ? 'active' : ''); ?>" data-filter="custom">
                        <i class="bi bi-calendar2-range"></i> Custom Range
                    </div>
                    <div class="custom-date-inputs <?php echo e($dateFilter === 'custom' ? 'show' : ''); ?>" id="customDateInputs">
                        <form method="GET" action="<?php echo e(route('invoices')); ?>" id="customDateForm">
                            <input type="hidden" name="date_filter" value="custom">
                            <input type="hidden" name="status" value="<?php echo e($activeStatus); ?>">
                            <input type="hidden" name="search" value="<?php echo e($searchTerm); ?>">
                            <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                            <?php if(request('booking_id')): ?>
                                <input type="hidden" name="booking_id" value="<?php echo e(request('booking_id')); ?>">
                            <?php endif; ?>
                            <label>From Date</label>
                            <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" required>
                            <label>To Date</label>
                            <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" required>
                            <button type="submit">Apply</button>
                        </form>
                    </div>
                </div>
            </div>


            <form action="<?php echo e(route('invoices.apply-all-penalties')); ?>" method="POST" style="display: inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-apply-all-penalties" onclick="return confirm('Apply penalties to all overdue invoices?')">
                    <i class="bi bi-clock-history"></i> Apply All Penalties
                </button>
            </form>

            <!-- Payment History Button -->
            <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#paymentHistoryModal">
                <i class="bi bi-clock-history"></i> Payment History
            </button>

            <!-- Reports Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="reportsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                    <i class="bi bi-file-earmark-text"></i> Reports
                </button>
                <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('reports.financial-summary.pdf', ['date_filter' => $dateFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo])); ?>" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Financial Summary (PDF)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('reports.payment-history.pdf', ['date_filter' => $dateFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo])); ?>" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Payment History (PDF)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('reports.payment-history.excel', ['date_filter' => $dateFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo])); ?>">
                            <i class="bi bi-file-earmark-excel"></i> Payment History (Excel)
                        </a>
                    </li>
                </ul>
            </div>

            <a href="<?php echo e(route('settings.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 8px;">
                <i class="bi bi-gear"></i> Settings
            </a>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card overdue">
            <div class="summary-header">
                <span class="summary-icon"><i class="bi bi-exclamation-circle"></i></span>
                <div class="summary-label">Outstanding Balance</div>
            </div>
            <div class="summary-value">₱<?php echo e(number_format($financialSnapshot['outstanding'] ?? 0, 2)); ?></div>
            <div class="summary-meta"><?php echo e($financialSnapshot['pending_count'] ?? 0); ?> invoice(s) require follow-up</div>
        </div>
        <div class="summary-card paid">
            <div class="summary-header">
                <span class="summary-icon"><i class="bi bi-check-circle"></i></span>
                <div class="summary-label">Collected To Date</div>
            </div>
            <div class="summary-value">₱<?php echo e(number_format($financialSnapshot['collected'] ?? 0, 2)); ?></div>
            <div class="summary-meta">Includes advance and monthly rent payments</div>
        </div>
        <div class="summary-card total">
            <div class="summary-header">
                <span class="summary-icon"><i class="bi bi-receipt"></i></span>
                <div class="summary-label">Total Billed</div>
            </div>
            <div class="summary-value">₱<?php echo e(number_format($financialSnapshot['billed'] ?? 0, 2)); ?></div>
            <div class="summary-meta">Across <?php echo e($statusCounts['total'] ?? 0); ?> invoice(s)</div>
        </div>

    </div>


    <form method="GET" action="<?php echo e(route('invoices')); ?>" class="filters-card">
        <?php if(request('booking_id')): ?>
            <input type="hidden" name="booking_id" value="<?php echo e(request('booking_id')); ?>">
        <?php endif; ?>
        <input type="hidden" name="date_filter" value="<?php echo e($dateFilter); ?>">
        <?php if($dateFilter === 'custom'): ?>
            <input type="hidden" name="date_from" value="<?php echo e($dateFrom); ?>">
            <input type="hidden" name="date_to" value="<?php echo e($dateTo); ?>">
        <?php endif; ?>
        <div class="status-filters">
            <button type="submit"
                    name="status"
                    value="all"
                    class="status-chip <?php echo e($activeStatus === 'all' ? 'active' : ''); ?>">
                All (<?php echo e($statusCounts['total'] ?? 0); ?>)
            </button>
            <button type="submit"
                    name="status"
                    value="pending"
                    class="status-chip <?php echo e($activeStatus === 'pending' ? 'active' : ''); ?>">
                Pending (<?php echo e($statusCounts['pending'] ?? 0); ?>)
            </button>
            <button type="submit"
                    name="status"
                    value="paid"
                    class="status-chip <?php echo e($activeStatus === 'paid' ? 'active' : ''); ?>">
                Paid (<?php echo e($statusCounts['paid'] ?? 0); ?>)
            </button>
            <button type="submit"
                    name="status"
                    value="partial"
                    class="status-chip <?php echo e($activeStatus === 'partial' ? 'active' : ''); ?>">
                Partial (<?php echo e($statusCounts['partial'] ?? 0); ?>)
            </button>
            <button type="submit"
                    name="status"
                    value="cancelled"
                    class="status-chip <?php echo e($activeStatus === 'cancelled' ? 'active' : ''); ?>">
                Cancelled (<?php echo e($statusCounts['cancelled'] ?? 0); ?>)
            </button>
        </div>
        <div class="filter-search">
            <input type="text"
                   name="search"
                   id="invoiceSearch"
                   placeholder="Search by tenant name, invoice # or room"
                   value="<?php echo e($searchTerm); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85ZM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0Z"/>
            </svg>
        </div>
        <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
        <input type="hidden" name="date_filter" value="<?php echo e($dateFilter); ?>">
        <?php if($dateFilter === 'custom'): ?>
            <input type="hidden" name="date_from" value="<?php echo e($dateFrom); ?>">
            <input type="hidden" name="date_to" value="<?php echo e($dateTo); ?>">
        <?php endif; ?>
        <?php if(request('booking_id')): ?>
            <input type="hidden" name="booking_id" value="<?php echo e(request('booking_id')); ?>">
        <?php endif; ?>
    </form>

    <div class="invoices-table-card">
        <div class="table-scroll-container">
            <table class="invoices-table">
                <thead>
                    <tr>
                        <th class="sortable <?php echo e($sortBy === 'invoice_id' ? 'active' : ''); ?>" onclick="sortTable('invoice_id')" style="width: 120px;">
                            Invoice
                            <?php if($sortBy === 'invoice_id'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th>Tenant &amp; Room</th>
                        <th class="sortable <?php echo e($sortBy === 'date_generated' ? 'active' : ''); ?>" onclick="sortTable('date_generated')">
                            Billing Period
                            <?php if($sortBy === 'date_generated'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th style="width: 50px;">Type</th>
                        <th>Details</th>
                        <th class="sortable <?php echo e($sortBy === 'total_due' ? 'active' : ''); ?>" onclick="sortTable('total_due')">
                            Total Due
                            <?php if($sortBy === 'total_due'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th class="sortable <?php echo e($sortBy === 'penalty_amount' ? 'active' : ''); ?>" onclick="sortTable('penalty_amount')">
                            Penalty
                            <?php if($sortBy === 'penalty_amount'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th>Collected</th>
                        <th>Balance</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        // Check if this is a security deposit invoice
                        // Security deposit is: no rent, no other utilities, utility_electricity_fee = ₱5000 exactly
                        $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
                        $hasOnlyElectricityFee = ($invoice->rent_subtotal == 0 &&
                                                  !$hasUtilities &&
                                                  $invoice->utility_electricity_fee > 0);
                        $isSecurityDepositAmount = abs($invoice->utility_electricity_fee - 5000.00) < 0.01;
                        $isSecurityDepositInvoice = $hasOnlyElectricityFee && $isSecurityDepositAmount;

                        if ($isSecurityDepositInvoice) {
                            $utilitiesTotal = 0; // Security deposit is shown separately
                            $securityDeposit = $invoice->utility_electricity_fee ?? 0;
                        } else {
                            // Calculate utilities total from invoice_utilities table
                            $utilitiesTotal = ($invoice->invoiceUtilities ? $invoice->invoiceUtilities->sum('amount') : 0) + ($invoice->utility_electricity_fee ?? 0);
                            $securityDeposit = 0;
                        }

                        $statusLabel = $invoice->status_label;

                        // Check if overdue (unpaid/partial and past due date)
                        $isOverdue = $invoice->is_overdue;

                        $badgeClass = $statusLabel === 'Paid'
                            ? 'paid'
                            : ($statusLabel === 'Pending'
                                ? ($isOverdue ? 'overdue' : 'pending')
                                : ($statusLabel === 'Canceled'
                                    ? 'canceled'
                                    : ($isOverdue ? 'overdue' : 'partial')));

                        // Override status label if overdue
                        $displayStatus = ($isOverdue && $statusLabel !== 'Paid' && $statusLabel !== 'Canceled')
                            ? 'Overdue'
                            : $statusLabel;
                    ?>
                    <tr id="invoice-<?php echo e($invoice->invoice_id); ?>"
                        class="<?php echo e(isset($highlightInvoiceId) && $highlightInvoiceId == $invoice->invoice_id ? 'highlight-invoice' : ''); ?>"
                        onclick="window.location='<?php echo e($invoice->booking ? route('bookings.show', $invoice->booking->booking_id) : '#'); ?>'"
                        style="<?php echo e(!$invoice->booking ? 'cursor: default;' : ''); ?>">
                        <td>
                            <div class="invoice-metadata">
                                <span class="invoice-type">#<?php echo e(str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT)); ?></span>
                                <span class="invoice-date"><?php echo e(optional($invoice->date_generated)->format('M d, Y') ?? '—'); ?></span>
                                <span class="invoice-date" style="font-size: 0.7rem; color: #94a3b8;"><?php echo e(optional($invoice->created_at)->format('g:i A') ?? ''); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="tenant-meta">
                                <span class="tenant-name">
                                    <?php if($invoice->booking): ?>
                                        <?php echo $invoice->booking->tenant_summary; ?>

                                    <?php else: ?>
                                        <?php echo e($invoice->tenant_name); ?>

                                    <?php endif; ?>
                                </span>
                                <span class="tenant-room">
                                    Room <?php echo e($invoice->room_number ?? '—'); ?>

                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="invoice-date" style="display: block; line-height: 1.3;">
                                <?php if($invoice->booking): ?>
                                    <?php echo e(optional($invoice->booking->checkin_date)->format('M d')); ?> -<br><?php echo e(optional($invoice->booking->checkout_date)->format('M d, Y')); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="amount-col" style="white-space: normal; line-height: 1.4;">
                            <?php echo e($invoice->invoice_type); ?>

                        </td>
                        <td style="font-size: 0.8rem; color: #64748b; line-height: 1.5;">
                            <?php
                                // Detect electricity-only invoice: no rent, has utility_electricity_fee, not security deposit
                                $isElectricityOnlyInvoice = ($invoice->rent_subtotal == 0 && 
                                                           $invoice->utility_electricity_fee > 0 &&
                                                           !$isSecurityDepositInvoice);
                            ?>
                            <?php if($isSecurityDepositInvoice): ?>
                                Security deposit for<br> monthly stay
                            <?php elseif($isElectricityOnlyInvoice): ?>
                                Electricity Bill
                            <?php else: ?>
                                <?php if($invoice->rent_subtotal > 0): ?>
                                    Rent: ₱<?php echo e(number_format($invoice->rent_subtotal ?? 0, 2)); ?><br>
                                <?php endif; ?>
                                <?php if($utilitiesTotal > 0): ?>
                                    Utilities: ₱<?php echo e(number_format($utilitiesTotal, 2)); ?>

                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="amount-col">₱<?php echo e(number_format($invoice->total_due ?? 0, 2)); ?></td>
                        <td>
                            <?php if($invoice->penalty_amount > 0): ?>
                                <span class="penalty-amount">+₱<?php echo e(number_format($invoice->penalty_amount, 2)); ?></span>
                            <?php elseif($invoice->is_overdue): ?>
                                <span class="overdue-indicator">
                                    <i class="bi bi-exclamation-triangle"></i> Overdue
                                </span>
                            <?php else: ?>
                                <span style="color: #94a3b8;">—</span>
                            <?php endif; ?>
                            <?php if($invoice->due_date): ?>
                                <div class="due-date-info <?php echo e($invoice->is_overdue ? 'overdue' : ''); ?>">
                                    Due: <?php echo e($invoice->due_date->format('M d')); ?>

                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="amount-col text-success">₱<?php echo e(number_format($invoice->total_collected ?? 0, 2)); ?></td>
                        <td class="amount-col" style="color: <?php echo e($invoice->remaining_balance > 0 ? '#ef4444' : '#94a3b8'); ?>;">
                            <?php if($invoice->remaining_balance > 0): ?>
                                ₱<?php echo e(number_format($invoice->remaining_balance, 2)); ?>

                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status <?php echo e($badgeClass); ?>">
                                <?php echo e($displayStatus); ?>

                            </span>
                        </td>
                        <td onclick="event.stopPropagation();">
                            <div class="action-buttons">
                                <?php if($statusLabel !== 'Paid' && $statusLabel !== 'Canceled'): ?>
                                    <button class="btn-add-payment"
                                            data-bs-toggle="modal"
                                            data-bs-target="#recordPaymentModal"
                                            data-invoice="<?php echo e($invoice->invoice_id); ?>"
                                            data-booking="<?php echo e($invoice->booking_id); ?>"
                                            data-tenant="<?php echo e($invoice->tenant_name); ?>"
                                            data-amount="<?php echo e(number_format($invoice->remaining_balance, 2)); ?>">
                                        <i class="bi bi-credit-card"></i>Payment
                                    </button>
                                <?php endif; ?>
                                <?php if($invoice->payments && $invoice->payments->isNotEmpty()): ?>
                                    <?php
                                        $latestPayment = $invoice->payments->first();
                                    ?>
                                    <a href="<?php echo e(route('payments.receipt', $latestPayment->payment_id)); ?>"
                                       class="btn-add-payment"
                                       style="text-decoration: none;">
                                        <i class="bi bi-printer"></i>Receipt
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11">
                            <div class="no-data-state">
                                <strong>No invoices found</strong>
                                Adjust your filters or switch back to "All" to see every invoice in the system.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        <div class="pagination-left">
            <form method="GET" action="<?php echo e(route('invoices')); ?>" class="d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="<?php echo e($searchTerm); ?>">
                <input type="hidden" name="status" value="<?php echo e($activeStatus); ?>">
                <?php if(request('booking_id')): ?>
                    <input type="hidden" name="booking_id" value="<?php echo e(request('booking_id')); ?>">
                <?php endif; ?>
                <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                    <?php $__currentLoopData = [5, 10, 15, 20]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php echo e((int) $perPage === $option ? 'selected' : ''); ?>>
                            <?php echo e($option); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
        <div class="pagination-center">
            <p class="small text-muted mb-0">
                Showing
                <span class="fw-semibold"><?php echo e($invoices->firstItem() ?? 0); ?></span>
                to
                <span class="fw-semibold"><?php echo e($invoices->lastItem() ?? 0); ?></span>
                of
                <span class="fw-semibold"><?php echo e($invoices->total()); ?></span>
                results
            </p>
        </div>
        <div class="pagination-right">
            <?php echo e($invoices->appends(['status' => $activeStatus, 'search' => $searchTerm, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'date_filter' => $dateFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo])->links()); ?>

        </div>
    </div>
</div>

<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordPaymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('payments.store')); ?>" method="POST" id="paymentForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Hidden fields -->
                    <input type="hidden" name="booking_id" id="modalBookingId" value="">
                    <input type="hidden" name="invoice_id" id="modalInvoiceId" value="">

                    <!-- Display only fields -->
                    <div class="mb-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="modalInvoiceDisplay" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tenant</label>
                        <input type="text" class="form-control" id="modalTenantName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Outstanding Balance</label>
                        <input type="text" class="form-control" id="modalOutstandingAmount" readonly>
                    </div>

                    <!-- Payment form fields -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number"
                                   class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="amount"
                                   name="amount"
                                   step="0.01"
                                   min="0.01"
                                   max="0"
                                   required
                                   placeholder="0.00"
                                   oninput="validity.valid||(value='');">
                        </div>
                        <div class="invalid-feedback" id="amountError" style="display: none;">
                            Payment amount cannot exceed the outstanding balance.
                        </div>
                        <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="payment_method"
                                name="payment_method"
                                required>
                            <option value="">Select payment method...</option>
                            <option value="Cash" <?php echo e(old('payment_method') === 'Cash' ? 'selected' : ''); ?>>Cash</option>
                            <option value="GCash" <?php echo e(old('payment_method') === 'GCash' ? 'selected' : ''); ?>>GCash</option>
                        </select>
                        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3" id="referenceNumberGroup" style="display: none;">
                        <label for="reference_number" class="form-label">Reference Number <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control <?php $__errorArgs = ['reference_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="reference_number"
                               name="reference_number"
                               placeholder="Enter GCash transaction reference">
                        <?php $__errorArgs = ['reference_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">Required when payment method is GCash</small>
                    </div>

                    <div class="mb-3">
                        <label for="date_received" class="form-label">Date Received <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control <?php $__errorArgs = ['date_received'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="date_received"
                               name="date_received"
                               value="<?php echo e(old('date_received', date('Y-m-d'))); ?>"
                               required>
                        <?php $__errorArgs = ['date_received'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-credit-card"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentModal = document.getElementById('recordPaymentModal');
    const paymentMethodSelect = document.getElementById('payment_method');
    const referenceNumberGroup = document.getElementById('referenceNumberGroup');
    const referenceNumberInput = document.getElementById('reference_number');
    const dateReceivedInput = document.getElementById('date_received');
    const amountInput = document.getElementById('amount');
    const amountError = document.getElementById('amountError');
    const paymentForm = document.getElementById('paymentForm');
    let maxOutstandingBalance = 0;

    // Set default date to today if not set
    if (!dateReceivedInput.value) {
        dateReceivedInput.value = new Date().toISOString().split('T')[0];
    }

    // Show/hide reference number field based on payment method
    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'GCash') {
            referenceNumberGroup.style.display = 'block';
            referenceNumberInput.setAttribute('required', 'required');
        } else {
            referenceNumberGroup.style.display = 'none';
            referenceNumberInput.removeAttribute('required');
            referenceNumberInput.value = '';
        }
    });

    // Validate amount input in real-time
    amountInput.addEventListener('input', function() {
        const enteredAmount = parseFloat(this.value) || 0;

        // Check if amount exceeds outstanding balance
        if (enteredAmount > maxOutstandingBalance) {
            amountInput.classList.add('is-invalid');
            amountError.style.display = 'block';
        } else if (enteredAmount <= 0) {
            amountInput.classList.add('is-invalid');
            amountError.textContent = 'Payment amount must be greater than zero.';
            amountError.style.display = 'block';
        } else {
            amountInput.classList.remove('is-invalid');
            amountError.style.display = 'none';
            amountError.textContent = 'Payment amount cannot exceed the outstanding balance.';
        }
    });

    // Validate form before submission
    paymentForm.addEventListener('submit', function(e) {
        const enteredAmount = parseFloat(amountInput.value) || 0;

        if (enteredAmount <= 0) {
            e.preventDefault();
            amountInput.classList.add('is-invalid');
            amountError.textContent = 'Payment amount must be greater than zero.';
            amountError.style.display = 'block';
            return false;
        }

        if (enteredAmount > maxOutstandingBalance) {
            e.preventDefault();
            amountInput.classList.add('is-invalid');
            amountError.textContent = 'Payment amount cannot exceed the outstanding balance.';
            amountError.style.display = 'block';
            return false;
        }
    });

    // Populate modal when it opens
    paymentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) {
            return;
        }

        const invoiceId = button.getAttribute('data-invoice') || '';
        const bookingId = button.getAttribute('data-booking') || '';
        const tenantName = button.getAttribute('data-tenant') || '';
        const outstanding = button.getAttribute('data-amount') || '0.00';

        // Set hidden fields
        document.getElementById('modalBookingId').value = bookingId;
        document.getElementById('modalInvoiceId').value = invoiceId;

        // Set display fields
        document.getElementById('modalInvoiceDisplay').value = invoiceId ? `#${invoiceId.toString().padStart(5, '0')}` : '';
        document.getElementById('modalTenantName').value = tenantName;
        document.getElementById('modalOutstandingAmount').value = `₱${outstanding}`;

        // Set amount field to outstanding balance (remove ₱ and commas)
        const amountValue = outstanding.replace(/[₱,]/g, '');
        maxOutstandingBalance = parseFloat(amountValue) || 0;
        document.getElementById('amount').value = amountValue;
        document.getElementById('amount').setAttribute('max', maxOutstandingBalance);

        // Reset validation state
        amountInput.classList.remove('is-invalid');
        amountError.style.display = 'none';

        // Reset form fields
        paymentMethodSelect.value = '';
        referenceNumberGroup.style.display = 'none';
        referenceNumberInput.value = '';
        referenceNumberInput.removeAttribute('required');
        dateReceivedInput.value = new Date().toISOString().split('T')[0];
    });

    // Clear form when modal is hidden (only if form was successfully submitted)
    paymentModal.addEventListener('hidden.bs.modal', function (event) {
        // Don't reset if there are validation errors (user might want to see them)
        if (!document.querySelector('.alert-danger')) {
            document.getElementById('paymentForm').reset();
            referenceNumberGroup.style.display = 'none';
            referenceNumberInput.removeAttribute('required');
            dateReceivedInput.value = new Date().toISOString().split('T')[0];
        }
    });

    // Scroll to highlighted invoice if present
    <?php if(isset($highlightInvoiceId) && $highlightInvoiceId): ?>
        const highlightedRow = document.getElementById('invoice-<?php echo e($highlightInvoiceId); ?>');
        if (highlightedRow) {
            setTimeout(() => {
                highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    <?php endif; ?>

    // Date Filter Dropdown
    const dateFilterBtn = document.getElementById('dateFilterBtn');
    const dateFilterMenu = document.getElementById('dateFilterMenu');
    const dateFilterOptions = document.querySelectorAll('.date-filter-option');
    const customDateInputs = document.getElementById('customDateInputs');
    const dateFilterLabel = document.getElementById('dateFilterLabel');

    // Toggle dropdown
    dateFilterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dateFilterMenu.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dateFilterBtn.contains(e.target) && !dateFilterMenu.contains(e.target)) {
            dateFilterMenu.classList.remove('show');
        }
    });

    // Handle date filter options
    dateFilterOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            const filterValue = this.getAttribute('data-filter');

            if (filterValue === 'custom') {
                // Show custom date inputs
                customDateInputs.classList.toggle('show');
                return;
            }

            // Hide custom inputs if visible
            customDateInputs.classList.remove('show');

            // Build URL with current filters
            const url = new URL(window.location.href);
            url.searchParams.set('date_filter', filterValue);

            // Remove custom date params if switching away from custom
            if (filterValue !== 'custom') {
                url.searchParams.delete('date_from');
                url.searchParams.delete('date_to');
            }

            // Navigate to filtered URL
            window.location.href = url.toString();
        });
    });

    // Prevent custom date inputs from closing dropdown
    if (customDateInputs) {
        customDateInputs.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

});

// Sorting function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort_by');
    const currentDir = url.searchParams.get('sort_dir') || 'desc';

    // If clicking the same column, toggle direction
    if (currentSort === column) {
        url.searchParams.set('sort_dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        // New column, default to descending
        url.searchParams.set('sort_by', column);
        url.searchParams.set('sort_dir', 'desc');
    }

    window.location.href = url.toString();
}
</script>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: #03255b; color: white;">
                <h5 class="modal-title" id="paymentHistoryModalLabel">
                    <i class="bi bi-clock-history me-2"></i>Payment History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Filters -->
                <div class="d-flex flex-wrap gap-3 align-items-end mb-4">
                    <!-- Date Filter Dropdown -->
                    <div>
                        <label class="form-label small fw-semibold">Date Filter</label>
                        <select class="form-select" id="phDateFilter" style="min-width: 150px;">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <!-- Custom Date Range (hidden by default) -->
                    <div id="phCustomDateContainer" style="display: none;">
                        <label class="form-label small fw-semibold">From</label>
                        <input type="date" class="form-control form-control-sm" id="phDateFrom">
                    </div>
                    <div id="phCustomDateContainerTo" style="display: none;">
                        <label class="form-label small fw-semibold">To</label>
                        <input type="date" class="form-control form-control-sm" id="phDateTo">
                    </div>
                    <!-- Tenant Search -->
                    <div>
                        <label class="form-label small fw-semibold">Tenant Search</label>
                        <input type="text" class="form-control" id="phTenantSearch" placeholder="Search tenant..." style="min-width: 180px;">
                    </div>
                    <!-- Collected By -->
                    <div>
                        <label class="form-label small fw-semibold">Collected By</label>
                        <select class="form-select" id="phCollectedBy" style="min-width: 150px;">
                            <option value="">All Users</option>
                        </select>
                    </div>
                    <!-- Reset Button -->
                    <div>
                        <button type="button" class="btn btn-outline-secondary" id="phResetFilters">
                            <i class="bi bi-x-circle"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Loading spinner -->
                <div id="phLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading payments...</p>
                </div>

                <!-- Table -->
                <div class="table-responsive" id="phTableContainer">
                    <table class="table table-hover" id="phTable">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th style="cursor: pointer;" onclick="sortPaymentHistory('payment_id')">
                                    ID <i class="bi bi-arrow-down-up small"></i>
                                </th>
                                <th style="cursor: pointer;" onclick="sortPaymentHistory('created_at')">
                                    Date Created <i class="bi bi-arrow-down-up small"></i>
                                </th>
                                <th>Tenant</th>
                                <th>Room</th>
                                <th style="cursor: pointer;" onclick="sortPaymentHistory('amount')">
                                    Amount <i class="bi bi-arrow-down-up small"></i>
                                </th>
                                <th>Type</th>
                                <th style="cursor: pointer;" onclick="sortPaymentHistory('payment_method')">
                                    Method <i class="bi bi-arrow-down-up small"></i>
                                </th>
                                <th>Collected By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="phTableBody">
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3" id="phPagination">
                    <div class="text-muted small" id="phPaginationInfo">Showing 0 to 0 of 0 payments</div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" id="phPrevPage" disabled>
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <span class="align-self-center small" id="phPageInfo">Page 1 of 1</span>
                        <button class="btn btn-sm btn-outline-secondary" id="phNextPage" disabled>
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Payment History Modal Logic
let phCurrentPage = 1;
let phSortBy = 'created_at';
let phSortDir = 'desc';
let phUsersLoaded = false;
let phSearchTimeout = null;

// Load payments when modal opens
document.getElementById('paymentHistoryModal').addEventListener('show.bs.modal', function () {
    loadPaymentHistory();
});

// Date filter dropdown - auto-apply on change
document.getElementById('phDateFilter').addEventListener('change', function() {
    const customFrom = document.getElementById('phCustomDateContainer');
    const customTo = document.getElementById('phCustomDateContainerTo');
    
    if (this.value === 'custom') {
        customFrom.style.display = 'block';
        customTo.style.display = 'block';
    } else {
        customFrom.style.display = 'none';
        customTo.style.display = 'none';
        phCurrentPage = 1;
        loadPaymentHistory();
    }
});

// Custom date inputs - auto-apply on change
document.getElementById('phDateFrom').addEventListener('change', function() {
    phCurrentPage = 1;
    loadPaymentHistory();
});

document.getElementById('phDateTo').addEventListener('change', function() {
    phCurrentPage = 1;
    loadPaymentHistory();
});

// Tenant search - auto-apply with debounce (300ms delay)
document.getElementById('phTenantSearch').addEventListener('input', function() {
    clearTimeout(phSearchTimeout);
    phSearchTimeout = setTimeout(function() {
        phCurrentPage = 1;
        loadPaymentHistory();
    }, 300);
});

// Collected by dropdown - auto-apply on change
document.getElementById('phCollectedBy').addEventListener('change', function() {
    phCurrentPage = 1;
    loadPaymentHistory();
});

// Reset filters button
document.getElementById('phResetFilters').addEventListener('click', function() {
    document.getElementById('phDateFilter').value = 'all';
    document.getElementById('phDateFrom').value = '';
    document.getElementById('phDateTo').value = '';
    document.getElementById('phTenantSearch').value = '';
    document.getElementById('phCollectedBy').value = '';
    document.getElementById('phCustomDateContainer').style.display = 'none';
    document.getElementById('phCustomDateContainerTo').style.display = 'none';
    phCurrentPage = 1;
    phSortBy = 'created_at';
    phSortDir = 'desc';
    loadPaymentHistory();
});

// Pagination buttons
document.getElementById('phPrevPage').addEventListener('click', function() {
    if (phCurrentPage > 1) {
        phCurrentPage--;
        loadPaymentHistory();
    }
});

document.getElementById('phNextPage').addEventListener('click', function() {
    phCurrentPage++;
    loadPaymentHistory();
});

// Sort function
function sortPaymentHistory(column) {
    if (phSortBy === column) {
        phSortDir = phSortDir === 'asc' ? 'desc' : 'asc';
    } else {
        phSortBy = column;
        phSortDir = 'desc';
    }
    loadPaymentHistory();
}

function loadPaymentHistory() {
    const loading = document.getElementById('phLoading');
    const tableContainer = document.getElementById('phTableContainer');
    const tableBody = document.getElementById('phTableBody');

    loading.style.display = 'block';
    tableContainer.style.opacity = '0.5';

    const params = new URLSearchParams({
        page: phCurrentPage,
        sort_by: phSortBy,
        sort_dir: phSortDir,
        per_page: 10
    });

    // Date filter
    const dateFilter = document.getElementById('phDateFilter').value;
    params.append('date_filter', dateFilter);
    
    if (dateFilter === 'custom') {
        const dateFrom = document.getElementById('phDateFrom').value;
        const dateTo = document.getElementById('phDateTo').value;
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
    }

    const tenantSearch = document.getElementById('phTenantSearch').value;
    const collectedBy = document.getElementById('phCollectedBy').value;

    if (tenantSearch) params.append('tenant_search', tenantSearch);
    if (collectedBy) params.append('collected_by', collectedBy);

    fetch(`<?php echo e(route('invoices.all-payments')); ?>?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            tableContainer.style.opacity = '1';

            // Populate users dropdown (once)
            if (!phUsersLoaded && data.users) {
                const select = document.getElementById('phCollectedBy');
                data.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    select.appendChild(option);
                });
                phUsersLoaded = true;
            }

            // Render table
            if (data.payments.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No payments found</td></tr>';
            } else {
                tableBody.innerHTML = data.payments.data.map(payment => `
                    <tr>
                        <td><span class="badge bg-light text-dark">#${payment.payment_id}</span></td>
                        <td>${payment.created_at}</td>
                        <td><strong>${payment.tenant_name}</strong></td>
                        <td>${payment.room_number}</td>
                        <td class="fw-bold text-success">₱${payment.amount}</td>
                        <td><span class="badge ${payment.payment_type === 'Security Deposit' ? 'bg-info' : 'bg-primary'}">${payment.payment_type}</span></td>
                        <td>${payment.payment_method}</td>
                        <td>${payment.collected_by}</td>
                        <td>
                            <a href="${payment.receipt_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-receipt"></i>
                            </a>
                        </td>
                    </tr>
                `).join('');
            }

            // Update pagination
            const pagination = data.pagination;
            document.getElementById('phPaginationInfo').textContent = 
                `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} payments`;
            document.getElementById('phPageInfo').textContent = 
                `Page ${pagination.current_page} of ${pagination.last_page}`;
            document.getElementById('phPrevPage').disabled = pagination.current_page <= 1;
            document.getElementById('phNextPage').disabled = pagination.current_page >= pagination.last_page;
        })
        .catch(error => {
            loading.style.display = 'none';
            tableContainer.style.opacity = '1';
            tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error loading payments</td></tr>';
            console.error('Error loading payment history:', error);
        });
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/contents/invoices.blade.php ENDPATH**/ ?>