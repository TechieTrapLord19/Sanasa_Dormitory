@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
    }

    /* Top Stats Cards */
    .stats-row {
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e5e5;
        height: 100%;
        transition: all 0.2s ease-in-out;
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

    .stat-card.outstanding .stat-value {
        color: #dc2626;
    }

    .stat-card.available .stat-value {
        color: #10b981;
    }

    .stat-card.movement .stat-value {
        color: #3b82f6;
    }

    .stat-card .stat-icon {
        font-size: 1.5rem;
        opacity: 0.6;
        margin-bottom: 0.5rem;
    }

    /* Main Content Cards */
    .content-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e5e5;
        height: 100%;
        transition: all 0.2s ease-in-out;
    }

    .content-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .content-card h5 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
    }

    /* Pending Payments Table */
    .payments-table {
        width: 100%;
        margin: 0;
    }

    .payments-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .payments-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .payments-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .total-due {
        font-weight: 700;
        color: #dc2626;
        font-size: 1rem;
    }

    .btn-view-invoice {
        padding: 0.375rem 1rem;
        font-size: 0.8125rem;
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .btn-view-invoice:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    /* Upcoming Checkouts */
    .checkout-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .checkout-item {
        padding: 1rem;
        border-left: 3px solid #e5e7eb;
        background-color: #fafafa;
        margin-bottom: 0.75rem;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }

    .checkout-item:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .checkout-item.today {
        border-left-color: #eab308;
        background-color: #fefce8;
    }

    .checkout-item .tenant-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .checkout-item .checkout-info {
        font-size: 0.875rem;
        color: #64748b;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .checkout-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        background-color: #eab308;
        color: #713f12;
    }

    /* Quick Booking Pills */
    .quick-booking-section {
        margin-top: 2rem;
    }

    .room-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .room-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.625rem 1.25rem;
        background-color: #10b981;
        color: white;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }

    .room-pill:hover {
        background-color: #059669;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .room-pill i {
        margin-right: 0.5rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        opacity: 0.4;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-card .stat-value {
            font-size: 1.5rem;
        }

        .payments-table {
            font-size: 0.875rem;
        }

        .payments-table th,
        .payments-table td {
            padding: 0.5rem;
        }

        .room-pills {
            gap: 0.5rem;
        }

        .room-pill {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
        }
    }
</style>

<!-- Top Stats Row -->
<div class="stats-row">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card outstanding">
                <div class="stat-icon">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <h6>Outstanding Balance</h6>
                <p class="stat-value">₱{{ number_format($outstandingBalance, 2) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card available">
                <div class="stat-icon">
                    <i class="bi bi-house-door"></i>
                </div>
                <h6>Rooms Available</h6>
                <p class="stat-value">{{ $availableRooms }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card movement">
                <div class="stat-icon">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h6>Movement Today</h6>
                <p class="stat-value">{{ $todayCheckins->count() + $todayCheckouts->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="row g-3 mb-3">
    <!-- Left Column: Pending Payments Table (2/3 width) -->
    <div class="col-lg-8">
        <div class="content-card">
            <h5><i class="bi bi-clock-history me-2"></i>Pending Payments</h5>

            @if($overdueInvoices->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p>No pending payments</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table payments-table mb-0">
                        <thead>
                            <tr>
                                <th>Tenant Name</th>
                                <th>Room</th>
                                <th>Invoice Date</th>
                                <th>Total Due</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->booking->tenant->full_name ?? 'Unknown' }}</td>
                                    <td>{{ $invoice->booking->room->room_num ?? 'N/A' }}</td>
                                    <td>{{ $invoice->date_generated->format('M d, Y') }}</td>
                                    <td class="total-due">₱{{ number_format($invoice->total_due, 2) }}</td>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice->invoice_id) }}" class="btn btn-view-invoice btn-sm">
                                            <i class="bi bi-eye me-1"></i>View Invoice
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Upcoming Checkouts (1/3 width) -->
    <div class="col-lg-4">
        <div class="content-card">
            <h5><i class="bi bi-calendar-event me-2"></i>Upcoming Check-outs</h5>

            @php
                $upcomingCheckouts = \App\Models\Booking::whereBetween('checkout_date', [now(), now()->addDays(3)])
                    ->where('status', 'Active')
                    ->with(['tenant', 'room'])
                    ->orderBy('checkout_date')
                    ->get();
            @endphp

            @if($upcomingCheckouts->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p>No check-outs in the next 3 days</p>
                </div>
            @else
                <ul class="checkout-list">
                    @foreach($upcomingCheckouts as $booking)
                        @php
                            $isToday = $booking->checkout_date->isToday();
                        @endphp
                        <li class="checkout-item {{ $isToday ? 'today' : '' }}">
                            <div class="tenant-name">
                                {{ $booking->tenant->full_name ?? 'Unknown' }}
                                @if($isToday)
                                    <span class="checkout-badge ms-2">Today</span>
                                @endif
                            </div>
                            <div class="checkout-info">
                                <span><i class="bi bi-door-open me-1"></i>Room {{ $booking->room->room_num }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $booking->checkout_date->format('M d, Y') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<!-- Bottom Section: Quick Booking -->
<div class="quick-booking-section">
    <div class="content-card">
        <h5><i class="bi bi-plus-circle me-2"></i>Quick Booking</h5>

        @php
            $availableRoomsList = \App\Models\Room::where('status', 'available')
                ->orderBy('room_num')
                ->get();
        @endphp

        @if($availableRoomsList->isEmpty())
            <div class="empty-state">
                <i class="bi bi-house-slash"></i>
                <p>No rooms available for booking</p>
            </div>
        @else
            <div class="room-pills">
                @foreach($availableRoomsList as $room)
                    <a href="{{ route('bookings.index', ['room' => $room->room_id]) }}" class="room-pill">
                        <i class="bi bi-house-door"></i>
                        Room {{ $room->room_num }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
