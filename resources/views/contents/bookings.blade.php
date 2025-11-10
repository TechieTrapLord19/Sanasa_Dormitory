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

    /* Tabs Styles */
    .bookings-tabs {
        background-color: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .tab-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 0.75rem 1.5rem;
        border: 2px solid #e2e8f0;
        background-color: white;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .tab-btn:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        color: #03255b;
    }

    .tab-btn.active {
        background-color: #03255b;
        color: white;
        border-color: #03255b;
    }

    /* Table Styles */
    .bookings-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
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
        background-color: #f7fafc;
    }

    .bookings-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.Reserved {
        background-color: #dbeafe;
        color: #1e40af;
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

    .action-buttons {
        display: flex;
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
        min-width: 250px;
    }

    .search-box:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }
</style>

<div class="bookings-header">
    <div class="row align-items-center">
        <!-- Left: Title -->
        <div class="col-md-8 d-flex justify-content-start">
            <h1 class="bookings-title">Bookings Management</h1>
        </div>

        <!-- Right: Create Button -->
        <div class="col-md-4 d-flex justify-content-end">
            <a href="{{ route('bookings.create') }}" class="create-booking-btn">
                <span class="create-booking-btn-icon">+</span>
                <span>Create New Booking</span>
            </a>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="bookings-tabs">
    <div class="tab-buttons">
        <a href="{{ route('bookings.index', ['status' => 'Upcoming']) }}" 
           class="tab-btn {{ ($statusFilter ?? 'Upcoming') === 'Upcoming' ? 'active' : '' }}">
            Upcoming ({{ $statusCounts['Upcoming'] ?? 0 }})
        </a>
        <a href="{{ route('bookings.index', ['status' => 'Active']) }}" 
           class="tab-btn {{ ($statusFilter ?? '') === 'Active' ? 'active' : '' }}">
            Active ({{ $statusCounts['Active'] ?? 0 }})
        </a>
        <a href="{{ route('bookings.index', ['status' => 'Completed']) }}" 
           class="tab-btn {{ ($statusFilter ?? '') === 'Completed' ? 'active' : '' }}">
            Completed ({{ $statusCounts['Completed'] ?? 0 }})
        </a>
        <a href="{{ route('bookings.index', ['status' => 'Canceled']) }}" 
           class="tab-btn {{ ($statusFilter ?? '') === 'Canceled' ? 'active' : '' }}">
            Canceled ({{ $statusCounts['Canceled'] ?? 0 }})
        </a>
    </div>
    
    <!-- Search -->
    <div class="mt-3">
        <form method="GET" action="{{ route('bookings.index') }}" class="d-flex gap-2">
            <input type="text" 
                   class="search-box" 
                   name="search" 
                   placeholder="Search by tenant name or room number..." 
                   value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ $statusFilter ?? 'Upcoming' }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="bookings-table-container">
    <table class="bookings-table">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Room Number</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Rate</th>
                <th>Total Fee</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>
                        <strong>{{ $booking->tenant->full_name }}</strong>
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
                        <strong>₱{{ number_format($booking->total_calculated_fee, 2) }}</strong>
                    </td>
                    <td>
                        <span class="status-badge {{ $booking->status }}">
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('bookings.show', $booking->booking_id) }}" class="btn-view">View Details</a>
                            @if($booking->status !== 'Canceled' && $booking->status !== 'Completed')
                                <form action="{{ route('bookings.destroy', $booking->booking_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-cancel">Cancel Booking</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No bookings found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
