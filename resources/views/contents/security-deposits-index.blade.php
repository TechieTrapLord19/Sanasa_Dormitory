@extends('layouts.app')

@section('title', 'Security Deposits')

@section('content')
<style>
    .deposits-header {
        background-color: white;
        margin-bottom: 2rem;
    }

    .deposits-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    /* Stats Cards - matching sales page */
    .stats-row {
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

    .stat-card.held .stat-value {
        color: #3b82f6;
    }

    .stat-card.held .stat-icon {
        color: #3b82f6;
    }

    .stat-card.pending .stat-value {
        color: #f59e0b;
    }

    .stat-card.pending .stat-icon {
        color: #f59e0b;
    }

    .stat-card.refunded .stat-value {
        color: #10b981;
    }

    .stat-card.refunded .stat-icon {
        color: #10b981;
    }

    .stat-card .stat-icon {
        font-size: 1.5rem;
        opacity: 0.6;
        margin-bottom: 0.5rem;
    }

    /* Filter Styles */
    .deposits-filters {
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
        min-width: 150px;
    }

    .filter-input:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .btn-filter {
        padding: 0.5rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-filter:hover {
        background-color: #021d47;
    }

    /* Table Styles */
    .deposits-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .deposits-table {
        width: 100%;
        border-collapse: collapse;
    }

    .deposits-table th {
        background-color: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .deposits-table td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        font-size: 0.875rem;
    }

    .deposits-table tr:hover {
        background-color: #f8fafc;
    }

    .tenant-info {
        display: flex;
        flex-direction: column;
    }

    .tenant-name {
        font-weight: 600;
        color: #1e293b;
    }

    .room-number {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.held {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-badge.partially-refunded {
        background-color: #fce7f3;
        color: #9d174d;
    }

    .status-badge.refunded {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.forfeited {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-badge.depleted {
        background-color: #fef3c7;
        color: #92400e;
    }



    /* Amount Display */
    .amount-cell {
        font-weight: 600;
        color: #1e293b;
    }

    .amount-cell.refundable {
        color: #10b981;
    }

    .amount-cell.deducted {
        color: #ef4444;
    }

    /* Action Button */
    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #03255b;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-view:hover {
        background-color: #021d47;
        color: white;
    }

    /* Pagination */
    .pagination-container {
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-info {
        color: #64748b;
        font-size: 0.875rem;
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
    }

    .pagination-links a,
    .pagination-links span {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
    }

    .pagination-links a {
        background-color: #f1f5f9;
        color: #475569;
    }

    .pagination-links a:hover {
        background-color: #e2e8f0;
    }

    .pagination-links span.current {
        background-color: #03255b;
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="deposits-header d-flex justify-content-between align-items-center">
    <h1 class="deposits-title">
        <i class="bi bi-shield-check me-2"></i>Security Deposits
    </h1>
</div>

<!-- Stats Cards -->
<div class="row stats-row">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="stat-card held">
            <div class="stat-icon"><i class="bi bi-safe"></i></div>
            <h6>Total Held</h6>
            <p class="stat-value">₱{{ number_format($totalHeld, 2) }}</p>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="stat-card pending">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <h6>Pending Deposits</h6>
            <p class="stat-value">{{ $totalPending }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card refunded">
            <div class="stat-icon"><i class="bi bi-arrow-return-left"></i></div>
            <h6>Total Refunded</h6>
            <p class="stat-value">₱{{ number_format($totalRefunded, 2) }}</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="deposits-filters">
    <form method="GET" action="{{ route('security-deposits.index') }}" class="filter-row">
        <div class="filter-group">
            <label class="filter-label">Search:</label>
            <input type="text" name="search" class="filter-input" placeholder="Tenant or room..." value="{{ $search }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">Status:</label>
            <select name="status" class="filter-input">
                <option value="">All Statuses</option>
                <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Held" {{ $status === 'Held' ? 'selected' : '' }}>Held</option>
                <option value="Depleted" {{ $status === 'Depleted' ? 'selected' : '' }}>Depleted</option>
                <option value="Partially Refunded" {{ $status === 'Partially Refunded' ? 'selected' : '' }}>Partially Refunded</option>
                <option value="Refunded" {{ $status === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="Forfeited" {{ $status === 'Forfeited' ? 'selected' : '' }}>Forfeited</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Per Page:</label>
            <select name="per_page" class="filter-input">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </form>
</div>

<!-- Deposits Table -->
<div class="deposits-table-container">
    @if($deposits->count() > 0)
        <table class="deposits-table">
            <thead>
                <tr>
                    <th>Tenant / Room</th>
                    <th>Required</th>
                    <th>Paid</th>
                    <th>Deducted</th>
                    <th>Refundable</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deposits as $deposit)
                    <tr>
                        <td>
                            <div class="tenant-info">
                                <span class="tenant-name">
                                    {{ $deposit->booking->tenant->first_name ?? 'N/A' }} {{ $deposit->booking->tenant->last_name ?? '' }}
                                </span>
                                <span class="room-number">
                                    Room {{ $deposit->booking->room->room_number ?? 'N/A' }}
                                </span>
                            </div>
                        </td>
                        <td class="amount-cell">₱{{ number_format($deposit->amount_required, 2) }}</td>
                        <td class="amount-cell">₱{{ number_format($deposit->amount_paid, 2) }}</td>
                        <td class="amount-cell deducted">
                            @if($deposit->amount_deducted > 0)
                                -₱{{ number_format($deposit->amount_deducted, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="amount-cell refundable">₱{{ number_format($deposit->calculateRefundable(), 2) }}</td>
                        <td>
                            @php
                                $statusClass = match($deposit->status) {
                                    'Pending' => 'pending',
                                    'Held' => 'held',
                                    'Depleted' => 'depleted',
                                    'Partially Refunded' => 'partially-refunded',
                                    'Refunded' => 'refunded',
                                    'Forfeited' => 'forfeited',
                                    default => 'pending'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $deposit->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('security-deposits.show', $deposit) }}" class="btn-view">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $deposits->firstItem() }} to {{ $deposits->lastItem() }} of {{ $deposits->total() }} deposits
            </div>
            <div class="pagination-links">
                {{ $deposits->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-shield"></i>
            <p>No security deposits found.</p>
        </div>
    @endif
</div>
@endsection
