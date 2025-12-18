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
        min-width: 150px;
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

    /* Sortable Styles */
    .deposits-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }

    .deposits-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }

    .deposits-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }

    .deposits-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
    }

    /* Table Styles */
    .deposits-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        border: 1px solid #e5e5e5;
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

    /* Low Balance Warning */
    .low-balance-warning {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.7rem;
        color: #dc2626;
        margin-left: 0.25rem;
    }

    .low-balance-warning i {
        font-size: 0.75rem;
    }

    /* Replenish Button */
    .btn-replenish {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        background-color: #dcfce7;
        color: #15803d;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-replenish:hover {
        background-color: #bbf7d0;
        color: #15803d;
    }

    /* Action Buttons Group */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
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
        background-color: #e0f2fe;
        color: #0369a1;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-view:hover {
        background-color: #bae6fd;
        color: #0369a1;
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
    <form method="GET" action="{{ route('security-deposits.index') }}" class="filter-row" id="filterForm">
        <input type="hidden" name="per_page" value="{{ $perPage }}">
        <div class="filter-group">
            <label class="filter-label">Search:</label>
            <input type="text" name="search" id="searchInput" class="filter-input" placeholder="Tenant or room..." value="{{ $search }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">Status:</label>
            <select name="status" class="filter-input" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Held" {{ $status === 'Held' ? 'selected' : '' }}>Held</option>
                <option value="Depleted" {{ $status === 'Depleted' ? 'selected' : '' }}>Depleted</option>
                <option value="Partially Refunded" {{ $status === 'Partially Refunded' ? 'selected' : '' }}>Partially Refunded</option>
                <option value="Refunded" {{ $status === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="Forfeited" {{ $status === 'Forfeited' ? 'selected' : '' }}>Forfeited</option>
            </select>
        </div>
        <a href="{{ route('security-deposits.index') }}" class="btn-filter" style="background-color: #e2e8f0; color: #4a5568; text-decoration: none;">
            Clear
        </a>
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
                    <th class="sortable {{ $sortBy === 'amount_paid' ? 'active' : '' }}" onclick="sortTable('amount_paid')">
                        Paid
                        @if($sortBy === 'amount_paid')
                            <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                        @else
                            <i class="bi bi-arrow-down-up sort-icon"></i>
                        @endif
                    </th>
                    <th class="sortable {{ $sortBy === 'amount_deducted' ? 'active' : '' }}" onclick="sortTable('amount_deducted')">
                        Deducted
                        @if($sortBy === 'amount_deducted')
                            <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                        @else
                            <i class="bi bi-arrow-down-up sort-icon"></i>
                        @endif
                    </th>
                    <th class="sortable {{ $sortBy === 'amount_refunded' ? 'active' : '' }}" onclick="sortTable('amount_refunded')">
                        Refundable
                        @if($sortBy === 'amount_refunded')
                            <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                        @else
                            <i class="bi bi-arrow-down-up sort-icon"></i>
                        @endif
                    </th>
                    <th class="sortable {{ $sortBy === 'status' ? 'active' : '' }}" onclick="sortTable('status')">
                        Status
                        @if($sortBy === 'status')
                            <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                        @else
                            <i class="bi bi-arrow-down-up sort-icon"></i>
                        @endif
                    </th>
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
                        <td class="amount-cell refundable">
                            @php
                                $refundable = $deposit->calculateRefundable();
                                $shortfall = max(0, $deposit->amount_required - $refundable);
                                $needsTopUp = $shortfall > 0 && in_array($deposit->status, ['Held', 'Depleted', 'Pending']);
                            @endphp
                            ₱{{ number_format($refundable, 2) }}
                            @if($shortfall > 0 && !in_array($deposit->status, ['Forfeited', 'Refunded']))
                                <span class="low-balance-warning" title="₱{{ number_format($shortfall, 2) }} below required">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    -₱{{ number_format($shortfall, 2) }}
                                </span>
                            @endif
                        </td>
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
                            <div class="action-buttons">
                                @if($needsTopUp)
                                    <button type="button" class="btn-replenish" data-bs-toggle="modal" data-bs-target="#replenishModal{{ $deposit->security_deposit_id }}">
                                        <i class="bi bi-plus-circle"></i> Replenish
                                    </button>
                                @endif
                                <a href="{{ route('security-deposits.show', $deposit) }}" class="btn-view">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $deposits->firstItem() }} to {{ $deposits->lastItem() }} of {{ $deposits->total() }} deposits
                <span style="margin-left: 1rem;">
                    <label style="font-size: 0.875rem; color: #64748b;">Show:</label>
                    <select id="perPageSelect" style="padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid #e2e8f0; font-size: 0.875rem;">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                    </select>
                </span>
            </div>
            <div class="pagination-links">
                {{ $deposits->appends(array_merge(request()->query(), ['sort_by' => $sortBy, 'sort_dir' => $sortDir]))->links() }}
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-shield"></i>
            <p>No security deposits found.</p>
        </div>
    @endif
</div>

<script>
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

// Per Page dropdown handler
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1'); // Reset to first page
            window.location.href = url.toString();
        });
    }

    // Search input with debounce
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    let searchTimeout;
    if (searchInput && filterForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                filterForm.submit();
            }, 400);
        });
    }
});
</script>

<!-- Replenish Deposit Modals -->
@foreach($deposits as $deposit)
    @php
        $refundable = $deposit->calculateRefundable();
        $shortfall = max(0, $deposit->amount_required - $refundable);
        $needsReplenish = $shortfall > 0 && in_array($deposit->status, ['Held', 'Depleted', 'Pending']);
    @endphp
    @if($needsReplenish)
    <div class="modal fade" id="replenishModal{{ $deposit->security_deposit_id }}" tabindex="-1" aria-labelledby="replenishModalLabel{{ $deposit->security_deposit_id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="replenishModalLabel{{ $deposit->security_deposit_id }}">
                        <i class="bi bi-plus-circle me-2"></i>Replenish Security Deposit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('security-deposits.top-up', $deposit) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Tenant:</strong> {{ $deposit->booking->tenant->first_name ?? '' }} {{ $deposit->booking->tenant->last_name ?? '' }}<br>
                            <strong>Room:</strong> {{ $deposit->booking->room->room_num ?? 'N/A' }}
                        </div>

                        <div class="alert alert-info mb-3">
                            <strong>Current Balance:</strong> ₱{{ number_format($refundable, 2) }}<br>
                            <strong>Required Amount:</strong> ₱{{ number_format($deposit->amount_required, 2) }}<br>
                            <strong>Shortfall:</strong> <span class="text-danger">₱{{ number_format($shortfall, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <label for="replenish_amount{{ $deposit->security_deposit_id }}" class="form-label">Replenish Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number"
                                       class="form-control"
                                       id="replenish_amount{{ $deposit->security_deposit_id }}"
                                       name="amount"
                                       min="1"
                                       step="0.01"
                                       value="{{ $shortfall }}"
                                       required>
                            </div>
                            <small class="text-muted">Suggested: ₱{{ number_format($shortfall, 2) }} to restore full deposit</small>
                        </div>

                        <div class="mb-3">
                            <label for="replenish_method{{ $deposit->security_deposit_id }}" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="replenish_method{{ $deposit->security_deposit_id }}" name="payment_method" required>
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="replenish_notes{{ $deposit->security_deposit_id }}" class="form-label">Notes</label>
                            <textarea class="form-control" id="replenish_notes{{ $deposit->security_deposit_id }}" name="notes" rows="2" placeholder="Optional notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Process
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection

