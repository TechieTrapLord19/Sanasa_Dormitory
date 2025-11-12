@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<style>
    .booking-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .details-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
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

    .info-section {
        margin-bottom: 2rem;
    }

    .info-section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        color: #2d3748;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-checkin {
        background-color: #10b981;
        color: white;
    }

    .btn-checkin:hover {
        background-color: #059669;
    }

    .btn-checkout {
        background-color: #3b82f6;
        color: white;
    }

    .btn-checkout:hover {
        background-color: #2563eb;
    }

    .btn-generate-invoice {
        background-color: #f59e0b;
        color: white;
    }

    .btn-generate-invoice:hover {
        background-color: #d97706;
    }

    .btn-add-payment {
        background-color: #03255b;
        color: white;
    }

    .btn-add-payment:hover {
        background-color: #021d47;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background-color: #f7fafc;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table tbody tr:hover {
        background-color: #f7fafc;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-paid {
        background-color: #d1fae5;
        color: #065f46;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-unpaid {
        background-color: #fee2e2;
        color: #dc2626;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .financial-summary {
        background-color: #f7fafc;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .summary-row.total {
        font-weight: 700;
        font-size: 1.25rem;
        color: #03255b;
        border-top: 2px solid #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }
</style>

<div class="booking-details-container">
    <div class="details-header">
        <div>
            <h1 class="details-title">Booking Details</h1>
            <span class="status-badge {{ $booking->status }}">{{ $booking->status }}</span>
        </div>
        <div class="action-buttons">
            @if($booking->status === 'Reserved')
                <form action="{{ route('bookings.checkin', $booking->booking_id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-action btn-checkin">Check-In Tenant</button>
                </form>
            @endif

            @if($booking->status === 'Active')
                <form action="{{ route('bookings.checkout', $booking->booking_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to check out this tenant?');">
                    @csrf
                    <button type="submit" class="btn-action btn-checkout">Check-Out Tenant</button>
                </form>
            @endif

            @if($booking->status !== 'Canceled' && $booking->status !== 'Completed')
                <a href="{{ route('bookings.edit', $booking->booking_id) }}" class="btn-action btn-edit">Edit Booking</a>
            @endif
        </div>
    </div>

    <!-- Tenant Info -->
    <div class="info-section">
        <h2 class="info-section-title">Tenant Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Name</span>
                <span class="info-value">{{ $booking->tenant->full_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $booking->tenant->email ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Contact Number</span>
                <span class="info-value">{{ $booking->tenant->contact_num ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Emergency Contact</span>
                <span class="info-value">{{ $booking->tenant->emer_contact_num ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Booking Info -->
    <div class="info-section">
        <h2 class="info-section-title">Booking Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Room Number</span>
                <span class="info-value">{{ $booking->room->room_num }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Floor</span>
                <span class="info-value">{{ $booking->room->floor }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Check-in Date</span>
                <span class="info-value">{{ $booking->checkin_date->format('M d, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Check-out Date</span>
                <span class="info-value">{{ $booking->checkout_date->format('M d, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Rate</span>
                <span class="info-value">{{ $booking->rate->duration_type }} - ₱{{ number_format($booking->rate->base_price, 2) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Calculated Fee</span>
                <span class="info-value"><strong>₱{{ number_format($booking->total_calculated_fee, 2) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="financial-summary">
        <h2 class="info-section-title">Financial Summary</h2>
        <div class="summary-row">
            <span>Total Invoiced:</span>
            <span>₱{{ number_format($totalInvoiced, 2) }}</span>
        </div>
        <div class="summary-row">
            <span>Total Paid:</span>
            <span>₱{{ number_format($totalPaid, 2) }}</span>
        </div>
        <div class="summary-row total">
            <span>Remaining Balance:</span>
            <span>₱{{ number_format($totalBalance, 2) }}</span>
        </div>
    </div>


    <!-- Invoices Table -->
    <div class="table-container">
        <h2 class="info-section-title px-3 pt-3">Invoices</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Date Generated</th>
                    <th>Rent Subtotal</th>
                    <th>Utilities</th>
                    <th>Total Due</th>
                    <th>Paid</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>#{{ $invoice->invoice_id }}</td>
                        <td>{{ $invoice->date_generated->format('M d, Y') }}</td>
                        <td>₱{{ number_format($invoice->rent_subtotal, 2) }}</td>
                        <td>
                            ₱{{ number_format($invoice->utility_water_fee + $invoice->utility_wifi_fee + $invoice->utility_electricity_fee, 2) }}
                        </td>
                        <td><strong>₱{{ number_format($invoice->total_due, 2) }}</strong></td>
                        <td>₱{{ number_format($invoice->total_paid, 2) }}</td>
                        <td>
                            @if($invoice->is_paid || $invoice->remaining_balance <= 0)
                                <span class="badge-paid">Paid</span>
                            @else
                                <span class="badge-unpaid">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn-action btn-add-payment" onclick="addPaymentForInvoice({{ $invoice->invoice_id }})">Add Payment</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No invoices found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Payments Table -->
    <div class="table-container">
        <h2 class="info-section-title px-3 pt-3">Payments</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Invoice</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Date Received</th>
                    <th>Collected By</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $allPayments = collect();
                    foreach($invoices as $invoice) {
                        $allPayments = $allPayments->merge($invoice->payments);
                    }
                    $allPayments = $allPayments->sortByDesc('date_received');
                @endphp

                @forelse($allPayments as $payment)
                    <tr>
                        <td>#{{ $payment->payment_id }}</td>
                        <td>Invoice #{{ $payment->invoice_id }}</td>
                        <td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->date_received->format('M d, Y') }}</td>
                        <td>{{ $payment->collectedBy->first_name ?? 'N/A' }} {{ $payment->collectedBy->last_name ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No payments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Generate Invoice Modal (Placeholder - to be implemented) -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1" aria-labelledby="generateInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateInvoiceModalLabel">Generate Monthly Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This feature will be implemented in the Invoices module.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal (Placeholder - to be implemented) -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This feature will be implemented in the Payments module.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function addPaymentForInvoice(invoiceId) {
    // This will be implemented when the Payments module is ready
    document.getElementById('addPaymentModal').querySelector('.modal-body').innerHTML =
        `<p>Add payment for Invoice #${invoiceId}. This feature will be implemented in the Payments module.</p>`;
    new bootstrap.Modal(document.getElementById('addPaymentModal')).show();
}
</script>
@endsection

