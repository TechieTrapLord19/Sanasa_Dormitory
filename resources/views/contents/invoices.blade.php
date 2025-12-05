@extends('layouts.app')

@section('title', 'Invoices Management')

@section('content')
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
        padding: 0.9rem 0.75rem;
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
</style>

<div class="invoices-page">

    @if($errors->any() && !request()->routeIs('payments.store'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following errors:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="invoices-header mb-4 d-flex justify-content-between align-items-center">
        <h1 class="invoices-title">Invoices Management</h1>
        <div class="d-flex gap-2">
            <form action="{{ route('invoices.apply-all-penalties') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-apply-all-penalties" onclick="return confirm('Apply penalties to all overdue invoices?')">
                    <i class="bi bi-clock-history"></i> Apply All Penalties
                </button>
            </form>
            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
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
            <div class="summary-value">₱{{ number_format($financialSnapshot['outstanding'] ?? 0, 2) }}</div>
            <div class="summary-meta">{{ $financialSnapshot['pending_count'] ?? 0 }} invoice(s) require follow-up</div>
        </div>
        <div class="summary-card paid">
            <div class="summary-header">
                <span class="summary-icon"><i class="bi bi-check-circle"></i></span>
                <div class="summary-label">Collected To Date</div>
            </div>
            <div class="summary-value">₱{{ number_format($financialSnapshot['collected'] ?? 0, 2) }}</div>
            <div class="summary-meta">Includes advance and monthly rent payments</div>
        </div>
        <div class="summary-card total">
            <div class="summary-header">
                <span class="summary-icon"><i class="bi bi-receipt"></i></span>
                <div class="summary-label">Total Billed</div>
            </div>
            <div class="summary-value">₱{{ number_format($financialSnapshot['billed'] ?? 0, 2) }}</div>
            <div class="summary-meta">Across {{ $statusCounts['total'] ?? 0 }} invoice(s)</div>
        </div>

    </div>


    <form method="GET" action="{{ route('invoices') }}" class="filters-card">
        @if(request('booking_id'))
            <input type="hidden" name="booking_id" value="{{ request('booking_id') }}">
        @endif
        <div class="status-filters">
            <button type="submit"
                    name="status"
                    value="all"
                    class="status-chip {{ $activeStatus === 'all' ? 'active' : '' }}">
                All ({{ $statusCounts['total'] ?? 0 }})
            </button>
            <button type="submit"
                    name="status"
                    value="pending"
                    class="status-chip {{ $activeStatus === 'pending' ? 'active' : '' }}">
                Pending ({{ $statusCounts['pending'] ?? 0 }})
            </button>
            <button type="submit"
                    name="status"
                    value="paid"
                    class="status-chip {{ $activeStatus === 'paid' ? 'active' : '' }}">
                Paid ({{ $statusCounts['paid'] ?? 0 }})
            </button>
            <button type="submit"
                    name="status"
                    value="partial"
                    class="status-chip {{ $activeStatus === 'partial' ? 'active' : '' }}">
                Partial ({{ $statusCounts['partial'] ?? 0 }})
            </button>
            <button type="submit"
                    name="status"
                    value="cancelled"
                    class="status-chip {{ $activeStatus === 'cancelled' ? 'active' : '' }}">
                Cancelled ({{ $statusCounts['cancelled'] ?? 0 }})
            </button>
        </div>
        <div class="filter-search">
            <input type="text"
                   name="search"
                   id="invoiceSearch"
                   placeholder="Search by tenant name, invoice # or room"
                   value="{{ $searchTerm }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85ZM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0Z"/>
            </svg>
        </div>
        <input type="hidden" name="per_page" value="{{ $perPage }}">
        @if(request('booking_id'))
            <input type="hidden" name="booking_id" value="{{ request('booking_id') }}">
        @endif
    </form>

    <div class="invoices-table-card">
        <div class="table-scroll-container">
            <table class="invoices-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tenant &amp; Room</th>
                        <th>Billing Period</th>
                        <th style="width: 50px;">Type</th>
                        <th>Details</th>
                        <th>Total Due</th>
                        <th>Penalty</th>
                        <th>Collected</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($invoices as $invoice)
                    @php
                        // Check if this is a security deposit invoice
                        // Use loaded relationship (property) instead of method call
                        $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
                        $isSecurityDepositInvoice = ($invoice->rent_subtotal == 0 &&
                                                    !$hasUtilities &&
                                                    $invoice->utility_electricity_fee > 0);

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
                    @endphp
                    <tr id="invoice-{{ $invoice->invoice_id }}"
                        class="{{ isset($highlightInvoiceId) && $highlightInvoiceId == $invoice->invoice_id ? 'highlight-invoice' : '' }}"
                        onclick="window.location='{{ $invoice->booking ? route('bookings.show', $invoice->booking->booking_id) : '#' }}'"
                        style="{{ !$invoice->booking ? 'cursor: default;' : '' }}">
                        <td>
                            <div class="invoice-metadata">
                                <span class="invoice-type">#{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}</span>
                                <span class="invoice-date">Generated {{ optional($invoice->date_generated)->format('M d, Y') ?? '—' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="tenant-meta">
                                <span class="tenant-name">
                                    @if($invoice->booking)
                                        {!! $invoice->booking->tenant_summary !!}
                                    @else
                                        {{ $invoice->tenant_name }}
                                    @endif
                                </span>
                                <span class="tenant-room">
                                    Room {{ $invoice->room_number ?? '—' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="invoice-date">
                                @if($invoice->booking)
                                    {{ optional($invoice->booking->checkin_date)->format('M d') }} - {{ optional($invoice->booking->checkout_date)->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </span>
                        </td>
                        <td class="amount-col" style="white-space: normal; line-height: 1.4;">
                            {{ $invoice->invoice_type }}
                        </td>
                        <td style="font-size: 0.8rem; color: #64748b; line-height: 1.5;">
                            @if($isSecurityDepositInvoice)
                                Security deposit for<br> monthly stay
                            @else
                                @if($invoice->rent_subtotal > 0)
                                    Rent: ₱{{ number_format($invoice->rent_subtotal ?? 0, 2) }}<br>
                                @endif
                                @if($utilitiesTotal > 0)
                                    Utilities: ₱{{ number_format($utilitiesTotal, 2) }}
                                @endif
                            @endif
                        </td>
                        <td class="amount-col">₱{{ number_format($invoice->total_due ?? 0, 2) }}</td>
                        <td>
                            @if($invoice->penalty_amount > 0)
                                <span class="penalty-amount">+₱{{ number_format($invoice->penalty_amount, 2) }}</span>
                            @elseif($invoice->is_overdue)
                                <span class="overdue-indicator">
                                    <i class="bi bi-exclamation-triangle"></i> Overdue
                                </span>
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                            @if($invoice->due_date)
                                <div class="due-date-info {{ $invoice->is_overdue ? 'overdue' : '' }}">
                                    Due: {{ $invoice->due_date->format('M d') }}
                                </div>
                            @endif
                        </td>
                        <td class="amount-col text-success">₱{{ number_format($invoice->total_collected ?? 0, 2) }}</td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $badgeClass }}">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td onclick="event.stopPropagation();">
                            <div class="action-buttons">
                                @if($statusLabel !== 'Paid' && $statusLabel !== 'Canceled')
                                    <button class="btn-add-payment"
                                            data-bs-toggle="modal"
                                            data-bs-target="#recordPaymentModal"
                                            data-invoice="{{ $invoice->invoice_id }}"
                                            data-booking="{{ $invoice->booking_id }}"
                                            data-tenant="{{ $invoice->tenant_name }}"
                                            data-amount="{{ number_format($invoice->remaining_balance, 2) }}">
                                        <i class="bi bi-credit-card"></i>Payment
                                    </button>
                                @endif
                                @if($invoice->payments && $invoice->payments->isNotEmpty())
                                    @php
                                        $latestPayment = $invoice->payments->first();
                                    @endphp
                                    <a href="{{ route('payments.receipt', $latestPayment->payment_id) }}"
                                       class="btn-add-payment"
                                       style="text-decoration: none;">
                                        <i class="bi bi-printer"></i>Receipt
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="no-data-state">
                                <strong>No invoices found</strong>
                                Adjust your filters or switch back to "All" to see every invoice in the system.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        <div class="pagination-left">
            <form method="GET" action="{{ route('invoices') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="{{ $searchTerm }}">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                @if(request('booking_id'))
                    <input type="hidden" name="booking_id" value="{{ request('booking_id') }}">
                @endif
                <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                    @foreach([10, 25, 50] as $option)
                        <option value="{{ $option }}" {{ (int) $perPage === $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="pagination-center">
            <p class="small text-muted mb-0">
                Showing
                <span class="fw-semibold">{{ $invoices->firstItem() ?? 0 }}</span>
                to
                <span class="fw-semibold">{{ $invoices->lastItem() ?? 0 }}</span>
                of
                <span class="fw-semibold">{{ $invoices->total() }}</span>
                results
            </p>
        </div>
        <div class="pagination-right">
            {{ $invoices->appends(['status' => $activeStatus, 'search' => $searchTerm, 'per_page' => $perPage])->links() }}
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
            <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

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
                                   class="form-control @error('amount') is-invalid @enderror"
                                   id="amount"
                                   name="amount"
                                   step="0.01"
                                   min="0.01"
                                   required
                                   placeholder="0.00">
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror"
                                id="payment_method"
                                name="payment_method"
                                required>
                            <option value="">Select payment method...</option>
                            <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="GCash" {{ old('payment_method') === 'GCash' ? 'selected' : '' }}>GCash</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="referenceNumberGroup" style="display: none;">
                        <label for="reference_number" class="form-label">Reference Number <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('reference_number') is-invalid @enderror"
                               id="reference_number"
                               name="reference_number"
                               placeholder="Enter GCash transaction reference">
                        @error('reference_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Required when payment method is GCash</small>
                    </div>

                    <div class="mb-3">
                        <label for="date_received" class="form-label">Date Received <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('date_received') is-invalid @enderror"
                               id="date_received"
                               name="date_received"
                               value="{{ old('date_received', date('Y-m-d')) }}"
                               required>
                        @error('date_received')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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
        document.getElementById('amount').value = amountValue;

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
    @if(isset($highlightInvoiceId) && $highlightInvoiceId)
        const highlightedRow = document.getElementById('invoice-{{ $highlightInvoiceId }}');
        if (highlightedRow) {
            setTimeout(() => {
                highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    @endif

});
</script>
@endsection

