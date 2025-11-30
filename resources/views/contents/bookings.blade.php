@extends('layouts.app')

@section('title', 'Bookings Management')

@section('content')
<style>
    .bookings-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .bookings-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .create-booking-btn {
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
    .create-booking-btn:hover {
        background-color: #021d47;
        color: white;
    }
    .create-booking-btn-icon {
        width: 24px;
        height: 24px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
       .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin: 0;
    }

    /* Tabs Styles */
    .bookings-tabs {
        background-color: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .tab-buttons {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .tab-btn {
        border: 1px solid #cbd5e1;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        background-color: white;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .tab-btn:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .tab-btn.active {
        background: #03255b;
        color: white;
        border-color: #03255b;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    /* Table Styles */
    .bookings-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        border: 1px solid #e5e5e5;
    }

    .bookings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bookings-table thead {
        background-color: #f7fafc;
    }

    .bookings-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .bookings-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .bookings-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .booking-row-clickable {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .booking-row-clickable:hover {
        background-color: #e2e8f0 !important;
    }

    .action-column {
        cursor: default !important;
    }

    .action-column:hover {
        background-color: transparent !important;
    }

    .bookings-table tbody tr:last-child td {
        border-bottom: none;
    }
    .bookings-table thead th {
    padding: 1rem;
    font-weight: 600;
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    }

    /* Status column - center */
    .bookings-table th:nth-child(6),
    .bookings-table td:nth-child(6) {
        text-align: center;
    }

    /* Actions column - center */
    .bookings-table th:nth-child(7),
    .bookings-table td:nth-child(7) {
        text-align: center;
    }
        /* Room Number column - center */
    .bookings-table th:nth-child(2),
    .bookings-table td:nth-child(2) {
        text-align: center;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;

    }

    .status-badge.Reserved {
        background-color: #dbeafe;
        color: #0369a1;
    }

    .status-badge.Active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.Completed {
        background-color: #e5e7eb;
        color: #4b5563;
    }

    .status-badge.Canceled {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .status-badge.Pending-Payment {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.Partial-Payment {
        background-color: #dbeafe;
        color: #0369a1;
    }

    .status-badge.Paid-Payment {
        background-color: #d1fae5;
        color: #065f46;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-view, .btn-cancel, .action-buttons button, .action-buttons a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-view, .btn-cancel {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-view {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-view:hover {
        background-color: #bae6fd;
        color: #0369a1;
    }

    .btn-cancel {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .btn-cancel:hover {
        background-color: #fecaca;
    }

    .search-box {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        min-width: 10px;
        max-width: 300px;

    }

    .search-box:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }
        .filter-btn {
        border: 1px solid #cbd5e1;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        background-color: white;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .filter-btn:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .filter-btn.active {
        background: #03255b;
        color: white;
        border-color: #03255b;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    /* Pagination Styles */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #f8fafc;
        flex-wrap: wrap;
        gap: 1rem;
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
</style>

<div class="bookings-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="bookings-title">Bookings Management</h1>
    <a href="{{ route('bookings.create') }}" class="create-booking-btn">
        <i class="bi bi-plus-circle"></i>
        <span>Create New Booking</span>
    </a>
</div>

<!-- Tabs -->
<div class="bookings-tabs">
    <!-- Search -->
    <div class="mb-3">
        <form method="GET" action="{{ route('bookings.index') }}" class="d-flex align-items-center gap-2">
            <p class="filter-label mb-0">Search:</p>
            <input type="text"
                   class="search-box"
                   name="search"
                   placeholder="Search by tenant name or room number..."
                   value="{{ request('search') }}">
           <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'All') }}">
        </form>
    </div>

    <!-- Filters -->
    <div class="tab-buttons">
        <p class="filter-label mb-0 align-self-center">Filter by Status:</p>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="All">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? 'All') === 'All' ? 'active' : '' }}">
                All ({{ $statusCounts['All'] ?? 0 }})
            </button>
        </form>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="Pending Payment">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? '') === 'Pending Payment' ? 'active' : '' }}">
                Pending ({{ $statusCounts['Pending Payment'] ?? 0 }})
            </button>
        </form>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="Partial Payment">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? '') === 'Partial Payment' ? 'active' : '' }}">
                Partial ({{ $statusCounts['Partial Payment'] ?? 0 }})
            </button>
        </form>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="Paid Payment">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? '') === 'Paid Payment' ? 'active' : '' }}">
                Paid ({{ $statusCounts['Paid Payment'] ?? 0 }})
            </button>
        </form>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="Active">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? '') === 'Active' ? 'active' : '' }}">
                Active ({{ $statusCounts['Active'] ?? 0 }})
            </button>
        </form>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="Completed">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? '') === 'Completed' ? 'active' : '' }}">
                Completed ({{ $statusCounts['Completed'] ?? 0 }})
            </button>
        </form>

        <form method="GET" action="{{ route('bookings.index') }}" class="d-inline-block" style="display:inline-block;">
            <input type="hidden" name="status" value="Canceled">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="filter-btn {{ ($statusFilter ?? '') === 'Canceled' ? 'active' : '' }}">
                Canceled ({{ $statusCounts['Canceled'] ?? 0 }})
            </button>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="bookings-table-container">
    <table class="bookings-table">
        <thead>
            <tr>
                <th>Tenant(s)</th>
                <th>Room Number</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Rate</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr class="booking-row-clickable" data-booking-id="{{ $booking->booking_id }}" style="cursor: pointer;">
                    <td>
                        <strong>{!! $booking->tenant_summary !!}</strong>
                    </td>
                    <td>
                        {{ $booking->room->room_num }}
                    </td>
                    <td>
                        {{ $booking->checkin_date->format('M d, Y') }}
                    </td>
                    <td>
                        {{ $booking->checkout_date->format('M d, Y') }}
                    </td>
                    <td>
                        {{ $booking->rate->duration_type }} - ₱{{ number_format($booking->rate->base_price, 2) }}
                    </td>

                    <td>
                        @php
                            $status = $booking->effective_status;
                        @endphp
                        <span class="status-badge {{ str_replace(' ', '-', $status) }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="action-column" onclick="event.stopPropagation();">
                        <div class="action-buttons">
                            <a href="{{ route('bookings.show', $booking->booking_id) }}" class="btn-view">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @if($booking->effective_status !== 'Canceled' && $booking->effective_status !== 'Completed')
                                <button type="button" class="btn-cancel" data-bs-toggle="modal" data-bs-target="#cancelBookingModal{{ $booking->booking_id }}">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No bookings found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        <div class="pagination-left">
            <form method="GET" action="{{ route('bookings.index') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="{{ $searchTerm }}">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
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
                <span class="fw-semibold">{{ $bookings->firstItem() ?? 0 }}</span>
                to
                <span class="fw-semibold">{{ $bookings->lastItem() ?? 0 }}</span>
                of
                <span class="fw-semibold">{{ $bookings->total() }}</span>
                results
            </p>
        </div>
        <div class="pagination-right">
            {{ $bookings->appends(['status' => $statusFilter, 'search' => $searchTerm, 'per_page' => $perPage])->links() }}
        </div>
    </div>
</div>

<!-- Cancel Booking Modals -->
@foreach($bookings as $booking)
    @if($booking->effective_status !== 'Canceled' && $booking->effective_status !== 'Completed')
    <div class="modal fade" id="cancelBookingModal{{ $booking->booking_id }}" tabindex="-1" aria-labelledby="cancelBookingModalLabel{{ $booking->booking_id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelBookingModalLabel{{ $booking->booking_id }}">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bookings.destroy', $booking->booking_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This action cannot be undone. The booking will be marked as canceled.
                        </div>
                        <div class="mb-3">
                            <label for="cancellation_reason{{ $booking->booking_id }}" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="cancellation_reason{{ $booking->booking_id }}"
                                      name="cancellation_reason"
                                      rows="4"
                                      placeholder="Enter the reason for cancelling this booking..."
                                      required></textarea>
                            <small class="text-muted">Please provide a detailed reason for the cancellation.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Cancel Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make booking rows clickable to navigate to invoices
    const bookingRows = document.querySelectorAll('.booking-row-clickable');

    bookingRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't navigate if clicking on action buttons or links
            if (e.target.closest('.action-column') ||
                e.target.closest('.btn-view') ||
                e.target.closest('.btn-cancel') ||
                e.target.closest('button') ||
                e.target.closest('a')) {
                return;
            }

            const bookingId = this.getAttribute('data-booking-id');
            if (bookingId) {
                // Navigate to invoices page filtered by booking_id
                window.location.href = '{{ route("invoices") }}?booking_id=' + bookingId;
            }
        });
    });
});
</script>

@endsection

