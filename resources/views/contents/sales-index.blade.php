@extends('layouts.app')

@section('title', 'Sales & Reports')

@section('content')
<style>
    .sales-header {
        background-color: white;
        margin-bottom: 2rem;
    }

    .sales-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .btn-export {
        background-color: #10b981;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .btn-export:hover {
        background-color: #059669;
        color: white;
    }

    /* Filter Styles */
    .sales-filters {
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .filter-row {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin: 0;
        white-space: nowrap;
    }

    .filter-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
    }

    .filter-input:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .btn-filter {
        padding: 0.45rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    .btn-filter:hover {
        background-color: #021d47;
    }

    .preset-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .btn-preset {
        padding: 0.45rem 1.1rem;
        border: 1px solid #cbd5e1;
        background-color: white;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-preset:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .btn-preset.active {
        background: #03255b;
        color: white;
        border-color: #03255b;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    /* Stats Cards */
    .stats-row {
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e5e5;
        height: 100%;
        transition: all 0.2s ease-in-out;
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card h6 {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.75rem;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .stat-card.sales .stat-value {
        color: #10b981;
    }

    .stat-card.sales .stat-icon {
        color: #10b981;
    }

    .stat-card.transactions .stat-value {
        color: #3b82f6;
    }

    .stat-card.transactions .stat-icon {
        color: #3b82f6;
    }

    .stat-card.outstanding .stat-value {
        color: #dc2626;
    }

    .stat-card.outstanding .stat-icon {
        color: #dc2626;
    }

    .stat-card .stat-icon {
        font-size: 1.5rem;
        opacity: 0.6;
        margin-bottom: 0.5rem;
    }

    /* Sortable Styles */
    .sales-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }

    .sales-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }

    .sales-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }

    .sales-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
    }

    /* Table Styles */
    .sales-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .sales-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sales-table thead {
        background-color: #f7fafc;
    }

    .sales-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .sales-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .sales-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .sales-table tbody tr:last-child td {
        border-bottom: none;
    }

    .payment-type-badge {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .payment-type-badge.rent {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .payment-type-badge.utility {
        background-color: #fef3c7;
        color: #92400e;
    }

    .payment-type-badge.deposit {
        background-color: #d1fae5;
        color: #065f46;
    }

    .payment-type-badge.deduction {
        background-color: #fce7f3;
        color: #9d174d;
    }

    .payment-type-badge.other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .payment-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-method-badge.cash {
        background-color: #d1fae5;
        color: #065f46;
    }

    .payment-method-badge.gcash {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .payment-method-badge.bank {
        background-color: #e0e7ff;
        color: #4338ca;
    }

    .payment-method-badge.check {
        background-color: #fef3c7;
        color: #92400e;
    }

    .payment-method-badge.other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .btn-receipt {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        background-color: #03255b;
        color: white;
        border: none;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-receipt:hover {
        background-color: #021d47;
        color: white;
    }

    /* Pagination Styles */
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
        margin-top: 1.5rem;
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
    .pagination-wrapper svg {
        display: none !important;
    }
    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }
    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important;
    }
    .pagination-wrapper nav > div:last-child > div:last-child {
        display: block !important;
    }
    .pagination-center .small {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }
    .pagination-center .fw-semibold {
        font-weight: 600;
        color: #0f172a;
    }

    /* Charts Section */
    .charts-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e5e5;
    }

    .chart-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #03255b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-card h5 i {
        font-size: 1.1rem;
        opacity: 0.7;
    }

    .chart-container {
        position: relative;
        height: 280px;
    }

    .chart-container.doughnut {
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .no-chart-data {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94a3b8;
        text-align: center;
    }

    .no-chart-data i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .no-chart-data p {
        margin: 0;
        font-size: 0.875rem;
    }

    .btn-consolidated {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .btn-consolidated:hover {
        background-color: #021d47;
        color: white;
    }

    .export-buttons {
        display: flex;
        gap: 0.75rem;
    }

    @media (max-width: 992px) {
        .charts-section {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Header -->
<div class="sales-header d-flex justify-content-between align-items-center">
    <h1 class="sales-title">Sales & Reports</h1>
    <a href="{{ route('sales.consolidated', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn-consolidated">
        <i class="bi bi-file-earmark-text"></i> Consolidated Report
    </a>
</div>

<!-- Filter Section -->
<div class="sales-filters">
    <form id="salesFilterForm" action="{{ route('sales.index') }}" method="GET">
        <div class="filter-row">
            <div class="filter-group">
                <label class="filter-label">Start Date</label>
                <input type="date" name="start_date" class="filter-input" value="{{ $startDate }}" required>
            </div>
            <div class="filter-group">
                <label class="filter-label">End Date</label>
                <input type="date" name="end_date" class="filter-input" value="{{ $endDate }}" required>
            </div>
            <button type="submit" class="btn-filter">
                <i class="bi bi-funnel"></i> Apply Filter
            </button>
        </div>
        <div class="preset-buttons">
            <button type="button" class="btn-preset" onclick="setDateRange('today')">Today</button>
            <button type="button" class="btn-preset" onclick="setDateRange('yesterday')">Yesterday</button>
            <button type="button" class="btn-preset" onclick="setDateRange('last7days')">Last 7 Days</button>
            <button type="button" class="btn-preset" onclick="setDateRange('last30days')">Last 30 Days</button>
            <button type="button" class="btn-preset" onclick="setDateRange('thismonth')">This Month</button>
            <button type="button" class="btn-preset" onclick="setDateRange('lastmonth')">Last Month</button>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="row stats-row">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="stat-card sales">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <h6>Total Revenue</h6>
            <p class="stat-value">₱{{ number_format($totalSales, 2) }}</p>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="stat-card transactions">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <h6>Total Transactions</h6>
            <p class="stat-value">{{ number_format($totalTransactions) }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card outstanding">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <h6>Outstanding Balance</h6>
            <p class="stat-value">₱{{ number_format($outstandingBalance, 2) }}</p>
        </div>
    </div>
</div>

<!-- Visual Statistics Charts -->
<div class="charts-section">
    <!-- Income Trend Bar Chart -->
    <div class="chart-card">
        <h5><i class="bi bi-bar-chart-fill"></i> Income Trend</h5>
        <div class="chart-container">
            @if(count($dailyIncomeChart['data']) > 0)
                <canvas id="incomeChart"></canvas>
            @else
                <div class="no-chart-data">
                    <i class="bi bi-graph-up"></i>
                    <p>No income data for selected period</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Breakdown Doughnut Chart -->
    <div class="chart-card">
        <h5><i class="bi bi-pie-chart-fill"></i> Payment Breakdown</h5>
        <div class="chart-container doughnut">
            @if(array_sum($paymentTypeChart['data']) > 0)
                <canvas id="paymentTypeChart"></canvas>
            @else
                <div class="no-chart-data">
                    <i class="bi bi-pie-chart"></i>
                    <p>No payment data for selected period</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Transactions Table -->
<div class="sales-table-container">
    <table class="sales-table">
        <thead>
            <tr>
                <th class="sortable {{ $sortBy === 'date_received' ? 'active' : '' }}" onclick="sortTable('date_received')">
                    Date
                    @if($sortBy === 'date_received')
                        <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                    @else
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    @endif
                </th>
                <th>Booking ID</th>
                <th>Tenant(s)</th>
                <th>Room</th>
                <th class="sortable {{ $sortBy === 'payment_type' ? 'active' : '' }}" onclick="sortTable('payment_type')">
                    Payment Type
                    @if($sortBy === 'payment_type')
                        <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                    @else
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    @endif
                </th>
                <th class="sortable {{ $sortBy === 'payment_method' ? 'active' : '' }}" onclick="sortTable('payment_method')">
                    Method
                    @if($sortBy === 'payment_method')
                        <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                    @else
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    @endif
                </th>
                <th class="sortable {{ $sortBy === 'amount' ? 'active' : '' }}" onclick="sortTable('amount')">
                    Amount
                    @if($sortBy === 'amount')
                        <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                    @else
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    @endif
                </th>
                <th>Collected By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                @php
                    $methodClass = match(strtolower($payment->payment_method ?? '')) {
                        'cash' => 'cash',
                        'gcash' => 'gcash',
                        'bank transfer', 'bank' => 'bank',
                        'check', 'cheque' => 'check',
                        default => 'other'
                    };
                    $typeClass = match(true) {
                        str_contains(strtolower($payment->payment_type ?? ''), 'rent') => 'rent',
                        str_contains(strtolower($payment->payment_type ?? ''), 'electric') || str_contains(strtolower($payment->payment_type ?? ''), 'utility') => 'utility',
                        str_contains(strtolower($payment->payment_type ?? ''), 'deposit') && !str_contains(strtolower($payment->payment_type ?? ''), 'deduction') => 'deposit',
                        str_contains(strtolower($payment->payment_type ?? ''), 'deduction') => 'deduction',
                        default => 'other'
                    };
                @endphp
                <tr>
                    <td>
                        <strong>{{ $payment->date_received->format('M d, Y') }}</strong>
                        <br><small class="text-muted">{{ $payment->created_at->format('g:i A') }}</small>
                    </td>
                    <td>
                        @if($payment->booking_id)
                            <a href="{{ route('bookings.show', $payment->booking_id) }}" style="color: #03255b; font-weight: 600;">
                                #{{ $payment->booking_id }}
                            </a>
                        @else
                            <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->invoice && $payment->invoice->booking)
                            {{ $payment->invoice->booking->tenant_summary }}
                        @else
                            <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->invoice && $payment->invoice->booking && $payment->invoice->booking->room)
                            {{ $payment->invoice->booking->room->room_num }}
                        @else
                            <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span class="payment-type-badge {{ $typeClass }}">{{ $payment->payment_type }}</span>
                    </td>
                    <td>
                        <span class="payment-method-badge {{ $methodClass }}">
                            @if($methodClass === 'cash')
                                <i class="bi bi-cash"></i>
                            @elseif($methodClass === 'gcash')
                                <i class="bi bi-phone"></i>
                            @elseif($methodClass === 'bank')
                                <i class="bi bi-bank"></i>
                            @elseif($methodClass === 'check')
                                <i class="bi bi-file-text"></i>
                            @else
                                <i class="bi bi-credit-card"></i>
                            @endif
                            {{ $payment->payment_method ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="font-weight: 700; color: #059669;">₱{{ number_format($payment->amount, 2) }}</td>
                    <td>
                        @if($payment->collectedBy)
                            {{ $payment->collectedBy->full_name }}
                        @else
                            <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('payments.receipt', $payment->payment_id) }}" class="btn-receipt" target="_blank">
                            <i class="bi bi-printer"></i> Receipt
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #718096; padding: 3rem;">
                        No payment transactions found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-wrapper">
    <div class="pagination-left">
        <form method="GET" action="{{ route('sales.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <label for="perPage" class="text-muted small mb-0">Rows per page</label>
            <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                @foreach([5, 10, 15, 20] as $option)
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
            <span class="fw-semibold">{{ $payments->firstItem() ?? 0 }}</span>
            to
            <span class="fw-semibold">{{ $payments->lastItem() ?? 0 }}</span>
            of
            <span class="fw-semibold">{{ $payments->total() }}</span>
            results
        </p>
    </div>
    <div class="pagination-right">
        {{ $payments->appends(['start_date' => $startDate, 'end_date' => $endDate, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->links() }}
    </div>
</div>

<script>
function setDateRange(preset) {
    const today = new Date();
    let startDate, endDate;

    switch(preset) {
        case 'today':
            startDate = endDate = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = formatDate(yesterday);
            break;
        case 'last7days':
            endDate = formatDate(today);
            const last7 = new Date(today);
            last7.setDate(last7.getDate() - 6);
            startDate = formatDate(last7);
            break;
        case 'last30days':
            endDate = formatDate(today);
            const last30 = new Date(today);
            last30.setDate(last30.getDate() - 29);
            startDate = formatDate(last30);
            break;
        case 'thismonth':
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            startDate = formatDate(firstDay);
            endDate = formatDate(today);
            break;
        case 'lastmonth':
            const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            startDate = formatDate(lastMonthStart);
            endDate = formatDate(lastMonthEnd);
            break;
    }

    document.querySelector('#salesFilterForm input[name="start_date"]').value = startDate;
    document.querySelector('#salesFilterForm input[name="end_date"]').value = endDate;
    document.querySelector('#salesFilterForm').submit();
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Income Trend Bar Chart
    const incomeChartCanvas = document.getElementById('incomeChart');
    if (incomeChartCanvas) {
        const incomeData = @json($dailyIncomeChart);

        new Chart(incomeChartCanvas, {
            type: 'bar',
            data: {
                labels: incomeData.labels,
                datasets: [{
                    label: 'Daily Income (₱)',
                    data: incomeData.data,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barThickness: incomeData.labels.length > 15 ? 'flex' : 32,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#03255b',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.parsed.y.toLocaleString('en-PH', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            },
                            font: { size: 11 },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748b',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }

    // Revenue Breakdown Doughnut Chart (Rent/Utility vs Deposit Deduction)
    const paymentTypeCanvas = document.getElementById('paymentTypeChart');
    if (paymentTypeCanvas) {
        const paymentTypeData = @json($paymentTypeChart);

        new Chart(paymentTypeCanvas, {
            type: 'doughnut',
            data: {
                labels: paymentTypeData.labels,
                datasets: [{
                    data: paymentTypeData.data,
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.85)',  // Green for Rent/Utility
                        'rgba(236, 72, 153, 0.85)'   // Pink for Deposit Deduction
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(236, 72, 153, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12, weight: '500' },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#03255b',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ₱' + context.parsed.toLocaleString('en-PH', { minimumFractionDigits: 2 }) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});

// Sorting function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort_by');
    const currentDir = url.searchParams.get('sort_dir') || 'asc';

    if (currentSort === column) {
        url.searchParams.set('sort_dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        url.searchParams.set('sort_by', column);
        url.searchParams.set('sort_dir', 'asc');
    }

    window.location.href = url.toString();
}
</script>
@endsection

