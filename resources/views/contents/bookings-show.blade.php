@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')


<style>
    .booking-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        flex-shrink: 0;
        gap: 1rem;
    }

    .details-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .status-badge.Paid {
        background-color: #d1fae5;
        color: #065f46;
    }

    .info-section {
        margin-bottom: 1.5rem;
        flex-shrink: 0;
    }

    .info-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.75rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .info-value {
        font-size: 0.95rem;
        color: #2d3748;
        font-weight: 500;
    }

    .charges-summary {
        background-color: #f7fafc;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        flex-shrink: 0;
    }

    .charge-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.6rem;
        font-size: 0.95rem;
    }

    .charge-row.total {
        font-weight: 700;
        font-size: 1.1rem;
        color: #03255b;
        border-top: 2px solid #e2e8f0;
        padding-top: 0.6rem;
        margin-top: 0.6rem;
    }

    .charge-note {
        font-style: italic;
        color: #4a5568;
        font-size: 0.85rem;
        margin-top: 0.75rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .btn-action, .btn-action i {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-action i {
        font-size: 1rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
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

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-action:disabled:hover {
        background-color: inherit;
    }

    .table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
        flex-shrink: 0;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .table thead {
        background-color: #f7fafc;
    }

    .table th {
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        padding: 0.75rem;
        color: #4a5568;
        border-bottom: 1px solid #e2e8f0;
    }

    .table tbody tr:hover {
        background-color: #f7fafc;
    }

    .badge-paid {
        background-color: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-unpaid {
        background-color: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Custom scrollbar styling */
    .booking-details-container::-webkit-scrollbar {
        width: 8px;
    }

    .booking-details-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .booking-details-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .booking-details-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
        .btn-invoice {
        background-color: #8b5cf6;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .btn-invoice i {
        font-size: 1rem;
    }

    .btn-invoice:hover {
        background-color: #7c3aed;
    }
</style>

<div class="booking-details-container">
    <div class="details-header">
        <div>
            <h1 class="details-title">Booking Details</h1>
            <span class="status-badge {{ str_replace(' ', '-', $booking->effective_status) }}">{{ $booking->effective_status }}</span>
        </div>
        <div class="action-buttons">
            @php
                // Check check-in eligibility based on rules:
                // 1. Monthly Rent + Utilities MUST be fully paid (ALL invoices, including extensions)
                // 2. Security Deposit must be at least HALF paid (₱2,500 minimum)
                $rentUtilitiesInvoices = $booking->invoices->filter(function($invoice) {
                    $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
                    return $invoice->rent_subtotal > 0 || $hasUtilities;
                });

                $securityDepositInvoice = $booking->invoices->first(function($invoice) {
                    $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
                    return $invoice->rent_subtotal == 0 &&
                           !$hasUtilities &&
                           $invoice->utility_electricity_fee > 0;
                });

                $canCheckIn = false;
                $checkInMessage = '';

                // Check Monthly Rent + Utilities (aggregate ALL invoices)
                if ($rentUtilitiesInvoices->isEmpty()) {
                    $checkInMessage = 'Monthly Rent + Utilities invoice not found';
                } else {
                    // Sum up total due and payments across ALL rent/utilities invoices
                    $rentUtilitiesDue = $rentUtilitiesInvoices->sum('total_due');
                    $rentUtilitiesPaid = $rentUtilitiesInvoices->sum(function($invoice) {
                        return $invoice->payments->sum('amount');
                    });

                    if ($rentUtilitiesPaid < $rentUtilitiesDue) {
                        $checkInMessage = 'Rent + Utilities must be fully paid (₱' . number_format($rentUtilitiesDue, 2) . ') to check in. Current: ₱' . number_format($rentUtilitiesPaid, 2);
                    } else {
                        // Rent + Utilities is fully paid, check Security Deposit (only if it exists)
                        // Note: Daily/Weekly bookings don't have security deposit invoices
                        if ($securityDepositInvoice) {
                            $securityDepositDue = $securityDepositInvoice->total_due;
                            $securityDepositPaid = $securityDepositInvoice->payments->sum('amount');
                            $requiredMinimum = $securityDepositDue / 2; // Half of security deposit

                            if ($securityDepositPaid < $requiredMinimum) {
                                $checkInMessage = 'Security Deposit must be at least half paid (₱' . number_format($requiredMinimum, 2) . ') to check in. Current: ₱' . number_format($securityDepositPaid, 2);
                            } else {
                                // Both conditions met
                                $canCheckIn = true;
                            }
                        } else {
                            // No security deposit invoice (Daily/Weekly bookings) - allow check-in if rent is fully paid
                            $canCheckIn = true;
                        }
                    }
                }
            @endphp

            {{-- Check-In Button: Only enabled if Monthly Rent + Utilities is fully paid --}}
            {{-- Check-in is always manual - no automatic check-in even when fully paid --}}
            @if(!in_array($booking->status, ['Active', 'Completed', 'Canceled']) && $canCheckIn)
                <form action="{{ route('bookings.checkin', $booking->booking_id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-action btn-checkin">
                        <i class="bi bi-box-arrow-in-right"></i> Check-In Tenant
                    </button>
                </form>
            @elseif(!in_array($booking->status, ['Active', 'Completed', 'Canceled']) && !$canCheckIn)
                <button type="button" class="btn-action btn-checkin" disabled title="{{ $checkInMessage }}">
                    <i class="bi bi-box-arrow-in-right"></i> Check-In Tenant
                </button>
            @endif

            {{-- Check-Out Button: Only for Active status --}}
            @if($booking->effective_status === 'Active')
                <form action="{{ route('bookings.checkout', $booking->booking_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to check out this tenant?');">
                    @csrf
                    <button type="submit" class="btn-action btn-checkout">
                        <i class="bi bi-box-arrow-right"></i> Check-Out Tenant
                    </button>
                </form>
            @endif

            {{-- Generate Renewal Invoice Button: Only for Active status --}}
            @if($booking->effective_status === 'Active')
                <button type="button" class="btn-action btn-invoice" data-bs-toggle="modal" data-bs-target="#renewalInvoiceModal">
                    <i class="bi bi-receipt-cutoff"></i> Generate Renewal Invoice
                </button>
            @endif

            {{-- Generate Electricity Invoice Button: Only for Active monthly bookings (30+ days or has Monthly invoices) --}}
            @if($booking->effective_status === 'Active' && isset($isMonthlyStay) && $isMonthlyStay)
                <button type="button" class="btn-action btn-invoice" data-bs-toggle="modal" data-bs-target="#electricityInvoiceModal" style="background-color: #f59e0b;">
                    <i class="bi bi-lightning-charge"></i> Generate Electricity Invoice
                </button>
            @endif


            {{-- Edit Button: For non-Canceled and non-Completed bookings --}}
            @if($booking->effective_status !== 'Canceled' && $booking->effective_status !== 'Completed')
                <a href="{{ route('bookings.edit', $booking->booking_id) }}" class="btn-action btn-edit">
                    <i class="bi bi-pencil-square"></i> Edit Booking
                </a>
            @endif
        </div>
    </div>

    <!-- Tenant Info -->
    <div class="info-section">
        <h2 class="info-section-title">Tenant Information</h2>
        @php
            $occupants = collect([$booking->tenant, $booking->secondaryTenant])->filter();
        @endphp
        @if($occupants->isEmpty())
            <span class="info-value" style="color: #94a3b8;">No tenants assigned</span>
        @else
            @foreach($occupants as $index => $occupant)
                <div class="info-grid mb-3">
                    <div class="info-item">
                        <span class="info-label">Name @if($occupants->count() > 1) #{{ $index + 1 }} @endif</span>
                        <span class="info-value">{{ $occupant->full_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $occupant->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value">{{ $occupant->contact_num ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Emergency Contact</span>
                        <span class="info-value">{{ $occupant->emer_contact_num ?? 'N/A' }}</span>
                    </div>
                </div>
            @endforeach
        @endif
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
                <span class="info-label">Stay Length</span>
                <span class="info-value">{{ $stayLengthDays }} night(s)</span>
            </div>
            <div class="info-item">
                <span class="info-label">Rate</span>
                <span class="info-value">{{ $booking->rate->duration_type }} &middot; ₱{{ number_format($booking->rate->base_price, 2) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Rate Total (Current Stay)</span>
                <span class="info-value"><strong>₱{{ number_format($chargeSummary['rate_total'], 2) }}</strong></span>
            </div>
            @php
                // Get ALL invoices that have Rent+Utilities (including extension invoices)
                $rentUtilitiesInvoices = $booking->invoices->filter(function($invoice) {
                    return $invoice->rent_subtotal > 0 || $invoice->utility_water_fee > 0 || $invoice->utility_wifi_fee > 0;
                });

                // Get Security Deposit invoice (should only be one)
                $securityDepositInvoice = $booking->invoices->first(function($invoice) {
                    return $invoice->rent_subtotal == 0 &&
                           $invoice->utility_water_fee == 0 &&
                           $invoice->utility_wifi_fee == 0 &&
                           $invoice->utility_electricity_fee > 0;
                });

                // Calculate payment status for Rent + Utilities (aggregate ALL invoices)
                $rentUtilitiesStatus = 'N/A';
                $rentUtilitiesPaid = 0;
                $rentUtilitiesDue = 0;
                if ($rentUtilitiesInvoices->isNotEmpty()) {
                    // Sum up total due and payments across ALL rent/utilities invoices
                    $rentUtilitiesDue = $rentUtilitiesInvoices->sum('total_due');
                    $rentUtilitiesPaid = $rentUtilitiesInvoices->sum(function($invoice) {
                        return $invoice->payments->sum('amount');
                    });

                    if ($rentUtilitiesPaid == 0) {
                        $rentUtilitiesStatus = 'Pending Payment';
                    } elseif ($rentUtilitiesPaid >= $rentUtilitiesDue) {
                        $rentUtilitiesStatus = 'Paid';
                    } else {
                        $rentUtilitiesStatus = 'Partial Payment';
                    }
                }

                // Calculate payment status for Security Deposit
                $securityDepositStatus = 'N/A';
                $securityDepositPaid = 0;
                $securityDepositDue = 0;
                if ($securityDepositInvoice) {
                    $securityDepositDue = $securityDepositInvoice->total_due;
                    $securityDepositPaid = $securityDepositInvoice->payments->sum('amount');
                    if ($securityDepositPaid == 0) {
                        $securityDepositStatus = 'Pending Payment';
                    } elseif ($securityDepositPaid >= $securityDepositDue) {
                        $securityDepositStatus = 'Paid';
                    } else {
                        $securityDepositStatus = 'Partial Payment';
                    }
                }
            @endphp
            <div class="info-item">
                <span class="info-label">Payment Status</span>
                <span class="info-value">
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @if($rentUtilitiesInvoices->isNotEmpty())
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <span style="font-weight: 500; color: #2d3748; min-width: 180px;">Monthly Rent + Utilities:</span>
                                <span class="status-badge {{ str_replace(' ', '-', $rentUtilitiesStatus) }}">{{ $rentUtilitiesStatus }}</span>
                                @if($rentUtilitiesStatus === 'Partial Payment' || $rentUtilitiesStatus === 'Pending Payment')
                                    <span style="font-size: 0.875rem; color: #718096;">
                                        ₱{{ number_format($rentUtilitiesPaid, 2) }} / ₱{{ number_format($rentUtilitiesDue, 2) }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        @if($securityDepositInvoice)
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <span style="font-weight: 500; color: #2d3748; min-width: 180px;">Security Deposit:</span>
                                <span class="status-badge {{ str_replace(' ', '-', $securityDepositStatus) }}">{{ $securityDepositStatus }}</span>
                                @if($securityDepositStatus === 'Partial Payment' || $securityDepositStatus === 'Pending Payment')
                                    <span style="font-size: 0.875rem; color: #718096;">
                                        ₱{{ number_format($securityDepositPaid, 2) }} / ₱{{ number_format($securityDepositDue, 2) }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </span>
            </div>
        </div>
    </div>
    <!-- Charges Summary -->
    <div class="charges-summary">
        <h2 class="info-section-title">Charges To Collect</h2>
        <div class="charge-row">
            <span>Rent ({{ $chargeSummary['duration_type'] }})</span>
            <span>₱{{ number_format($chargeSummary['rate_total'], 2) }}</span>
        </div>
        @if(isset($chargeSummary['utilities']) && count($chargeSummary['utilities']) > 0)
            @foreach($chargeSummary['utilities'] as $utility)
            <div class="charge-row">
                <span>{{ $utility['name'] }}</span>
                <span>₱{{ number_format($utility['amount'], 2) }}</span>
            </div>
            @endforeach
        @endif
        @if($chargeSummary['security_deposit'] > 0)
        <div class="charge-row">
            <span>Security Deposit</span>
            <span>₱{{ number_format($chargeSummary['security_deposit'], 2) }}</span>
        </div>
        @endif
        <div class="charge-row total">
            <span>Total Due</span>
            <span>₱{{ number_format($chargeSummary['total_due'], 2) }}</span>
        </div>
        @if($chargeSummary['note'])
            <div class="charge-note">{{ $chargeSummary['note'] }}</div>
        @endif
    </div>

    <!-- Cancellation Reason (if cancelled) -->
    @if($booking->status === 'Canceled' && $booking->cancellation_reason)
    <div class="info-section">
        <h2 class="info-section-title">Cancellation Information</h2>
        <div class="info-item">
            <span class="info-label">Cancellation Reason</span>
            <span class="info-value">{{ $booking->cancellation_reason }}</span>
        </div>
    </div>
    @endif

    <!-- Refund Section (if cancelled and can be refunded) -->
    @php
        $refundablePayments = collect();
        $totalRefundable = 0;
        if ($booking->canBeRefunded() && $allPayments->where('amount', '>', 0)->isNotEmpty()) {
            $refundablePayments = $allPayments->filter(function($payment) {
                return $payment->canBeRefunded() && $payment->amount > 0;
            });
            $totalRefundable = $refundablePayments->sum('remaining_refundable_amount');
        }
    @endphp

    @if($booking->canBeRefunded() && $refundablePayments->isNotEmpty())
    <div class="info-section">
        <h2 class="info-section-title">Refunds</h2>

        <div class="alert alert-info mb-3">
            <strong>Total Refundable Amount: ₱{{ number_format($totalRefundable, 2) }}</strong>
            <p class="mb-0 mt-2">This booking has payments that can be refunded. Select a payment below to process a refund.</p>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Payment Type</th>
                        <th>Amount Paid</th>
                        <th>Amount Refunded</th>
                        <th>Remaining Refundable</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($refundablePayments as $payment)
                    <tr>
                        <td>{{ $payment->payment_type }}</td>
                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                        <td>₱{{ number_format($payment->total_refunded, 2) }}</td>
                        <td><strong>₱{{ number_format($payment->remaining_refundable_amount, 2) }}</strong></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#refundModal{{ $payment->payment_id }}">
                                <i class="bi bi-arrow-counterclockwise"></i> Process Refund
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Refund History (always show if there are refunds) -->
    @if($booking->refunds->isNotEmpty())
    <div class="info-section">
        <h2 class="info-section-title">Refund History</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference Number</th>
                        <th>Status</th>
                        <th>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->refunds as $refund)
                    <tr>
                        <td>{{ $refund->refund_date->format('M d, Y') }}</td>
                        <td>{{ $refund->payment->payment_type }}</td>
                        <td>₱{{ number_format($refund->refund_amount, 2) }}</td>
                        <td>{{ $refund->refund_method }}</td>
                        <td>{{ $refund->reference_number ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $refund->status === 'Completed' ? 'success' : ($refund->status === 'Processed' ? 'warning' : 'secondary') }}">
                                {{ $refund->status }}
                            </span>
                        </td>
                        <td>{{ $refund->refundedBy->full_name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Refund Modals -->
@if($booking->canBeRefunded() && $refundablePayments->isNotEmpty())
    @foreach($refundablePayments as $payment)
    <div class="modal fade" id="refundModal{{ $payment->payment_id }}" tabindex="-1" aria-labelledby="refundModalLabel{{ $payment->payment_id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel{{ $payment->payment_id }}">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bookings.refund', $booking->booking_id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_id" value="{{ $payment->payment_id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Type</label>
                            <input type="text" class="form-control" value="{{ $payment->payment_type }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Original Amount</label>
                            <input type="text" class="form-control" value="₱{{ number_format($payment->amount, 2) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Already Refunded</label>
                            <input type="text" class="form-control" value="₱{{ number_format($payment->total_refunded, 2) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="refund_amount{{ $payment->payment_id }}" class="form-label">Refund Amount <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="refund_amount{{ $payment->payment_id }}"
                                   name="refund_amount"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $payment->remaining_refundable_amount }}"
                                   value="{{ $payment->remaining_refundable_amount }}"
                                   required>
                            <small class="text-muted">Maximum refundable: ₱{{ number_format($payment->remaining_refundable_amount, 2) }}</small>
                        </div>
                        <div class="mb-3">
                            <label for="refund_method{{ $payment->payment_id }}" class="form-label">Refund Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="refund_method{{ $payment->payment_id }}" name="refund_method" required>
                                <option value="">Select method...</option>
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                            </select>
                        </div>
                        <div class="mb-3" id="reference_number_container{{ $payment->payment_id }}" style="display: none;">
                            <label for="reference_number{{ $payment->payment_id }}" class="form-label">Reference Number <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="reference_number{{ $payment->payment_id }}"
                                   name="reference_number"
                                   placeholder="Enter GCash reference number">
                            <small class="text-muted">Required for GCash refunds</small>
                        </div>
                        <div class="mb-3">
                            <label for="refund_date{{ $payment->payment_id }}" class="form-label">Refund Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="refund_date{{ $payment->payment_id }}"
                                   name="refund_date"
                                   value="{{ now()->toDateString() }}"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="cancellation_reason_refund{{ $payment->payment_id }}" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="cancellation_reason_refund{{ $payment->payment_id }}"
                                      name="cancellation_reason"
                                      rows="3"
                                      required>{{ $booking->cancellation_reason ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-counterclockwise"></i> Process Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const refundMethodSelect = document.getElementById('refund_method{{ $payment->payment_id }}');
            const referenceContainer = document.getElementById('reference_number_container{{ $payment->payment_id }}');
            const referenceInput = document.getElementById('reference_number{{ $payment->payment_id }}');

            if (refundMethodSelect) {
                refundMethodSelect.addEventListener('change', function() {
                    if (this.value === 'GCash') {
                        referenceContainer.style.display = 'block';
                        referenceInput.setAttribute('required', 'required');
                    } else {
                        referenceContainer.style.display = 'none';
                        referenceInput.removeAttribute('required');
                        referenceInput.value = '';
                    }
                });
            }
        });
    </script>
    @endforeach
@endif

<!-- Renewal Invoice Modal -->
@if($booking->effective_status === 'Active')
<div class="modal fade" id="renewalInvoiceModal" tabindex="-1" aria-labelledby="renewalInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewalInvoiceModalLabel">Generate Renewal Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bookings.renew', $booking->booking_id) }}" method="POST" id="renewalInvoiceForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="extension_days" class="form-label">Extension Days <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="extension_days"
                               name="extension_days"
                               value="{{ $booking->rate->duration_type === 'Monthly' ? '30' : '' }}"
                               min="1"
                               required>
                        <small class="text-muted">
                            @if($booking->rate->duration_type === 'Monthly')
                                Default: 30 days. If past due, days past due will be deducted from extension period (within 3-day grace period).
                            @else
                                Enter number of days to extend the booking
                            @endif
                        </small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-receipt-cutoff"></i> Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif

<!-- Electricity Invoice Modal -->
@if($booking->effective_status === 'Active' && isset($isMonthlyStay) && $isMonthlyStay)
<div class="modal fade" id="electricityInvoiceModal" tabindex="-1" aria-labelledby="electricityInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="electricityInvoiceModalLabel">Generate Electricity Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form action="{{ route('bookings.electricity', $booking->booking_id) }}" method="POST" id="electricityInvoiceForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="elec_last_meter_reading" class="form-label">Last Meter Reading (kWh)</label>
                            @if($lastReading)
                                <input type="text"
                                       class="form-control"
                                       id="elec_last_meter_reading"
                                       value="{{ number_format($lastReading->meter_value_kwh, 2) }} kWh on {{ $lastReading->reading_date->format('M d, Y') }}"
                                       readonly
                                       style="background-color: #f8fafc;">
                                <small class="text-muted">Automatically retrieved from Electric Readings logbook</small>
                            @else
                                <input type="text"
                                       class="form-control"
                                       id="elec_last_meter_reading"
                                       value="No previous reading (0.00 kWh)"
                                       readonly
                                       style="background-color: #f8fafc;">
                                <small class="text-muted">No previous reading found. Usage will be calculated from 0.00 kWh.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="elec_new_meter_reading" class="form-label">New Meter Reading (kWh) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="elec_new_meter_reading"
                                   name="new_meter_value_kwh"
                                   step="0.01"
                                   min="0"
                                   placeholder="e.g. 1600.00"
                                   required>
                            <small class="text-muted">Enter the current meter reading (same as Electric Readings page). Usage will be calculated as: New Reading - Last Reading.</small>
                        </div>

                        <div class="mb-3">
                            <label for="elec_electricity_rate_per_kwh" class="form-label">Electricity Rate per kWh (₱) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="elec_electricity_rate_per_kwh"
                                   name="electricity_rate_per_kwh"
                                   step="0.01"
                                   min="0"
                                   placeholder="Enter rate per kWh"
                                   value="{{ old('electricity_rate_per_kwh', $electricityRate ?? '') }}"
                                   required>
                            <small class="text-muted">Current rate: <strong id="currentRateDisplay">₱{{ $electricityRate ? number_format($electricityRate, 2) : 'Not set' }}</strong> (from Electric Readings page)</small>
                        </div>

                        <div class="alert alert-info" id="electricityCalculation" style="display: none;">
                            <strong style="display: block; margin-bottom: 0.75rem;">Electricity Calculation:</strong>
                            <div id="electricityDetails" style="line-height: 1.8;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitElectricityBtn">
                            <i class="bi bi-lightning-charge"></i> <span id="submitBtnText">Generate Electricity Invoice</span>
                        </button>
                    </div>
                </form>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const hasLastReading = {{ $lastReading ? 'true' : 'false' }};
                const lastReadingValue = {{ $lastReading ? $lastReading->meter_value_kwh : 0 }};
                const lastReadingDate = {{ $lastReading ? json_encode($lastReading->reading_date->format('M d, Y')) : 'null' }};
                const rateInput = document.getElementById('elec_electricity_rate_per_kwh');
                const newMeterInput = document.getElementById('elec_new_meter_reading');
                const calculationDiv = document.getElementById('electricityCalculation');
                const detailsDiv = document.getElementById('electricityDetails');
                const submitBtn = document.getElementById('submitElectricityBtn');
                const submitBtnText = document.getElementById('submitBtnText');

                function calculateElectricity() {
                    const rate = parseFloat(rateInput.value) || 0;
                    let kwhUsed = 0;

                    // Calculate usage: new meter reading - last reading
                    if (newMeterInput && newMeterInput.value && newMeterInput.value.trim() !== '') {
                        const newVal = Math.max(0, parseFloat(newMeterInput.value) || 0);
                        const lastVal = hasLastReading ? lastReadingValue : 0;
                        kwhUsed = Math.max(0, newVal - lastVal);
                    }

                    const lastReadingInfoHtml = hasLastReading
                        ? `<div><strong>Last Reading:</strong> ${lastReadingValue.toFixed(2)} kWh (${lastReadingDate ?? ''})</div>`
                        : `<div><strong>Previous Reading:</strong> 0.00 kWh (Brand new meter)</div>`;

                    const currentReadingDisplay = (newMeterInput && newMeterInput.value && newMeterInput.value.trim() !== '')
                        ? `${parseFloat(newMeterInput.value).toFixed(2)} kWh`
                        : '—';

                    if (newMeterInput && newMeterInput.value && newMeterInput.value.trim() !== '') {
                        // Show calculation if new reading is entered
                        const newVal = parseFloat(newMeterInput.value) || 0;
                        const lastVal = hasLastReading ? lastReadingValue : 0;

                        let detailsHtml = `
                            <div style="display: grid; gap: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <span><strong>Last Reading:</strong></span>
                                    <span>${lastVal.toFixed(2)} kWh${hasLastReading ? ` (${lastReadingDate ?? ''})` : ''}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <span><strong>New Reading:</strong></span>
                                    <span>${newVal.toFixed(2)} kWh</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 2px solid rgba(0,0,0,0.2);">
                                    <span><strong>Usage:</strong></span>
                                    <span><strong>${kwhUsed.toFixed(2)} kWh</strong></span>
                                </div>
                        `;

                        if (rate > 0) {
                            const totalFee = kwhUsed * rate;
                            detailsHtml += `
                                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                    <span>Rate:</span>
                                    <span>₱${rate.toFixed(2)} per kWh</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; margin-top: 0.5rem; border-top: 2px solid rgba(0,0,0,0.2); background-color: rgba(13, 110, 253, 0.1); border-radius: 4px; padding-left: 0.75rem; padding-right: 0.75rem;">
                                    <span><strong>Total Electricity Fee:</strong></span>
                                    <span><strong style="font-size: 1.1em; color: #0d6efd;">₱${totalFee.toFixed(2)}</strong></span>
                                </div>
                            `;
                            submitBtnText.textContent = `Add ₱${totalFee.toFixed(2)} to Invoice`;
                        } else {
                            detailsHtml += `
                                <div style="padding: 0.5rem 0; color: #6c757d; font-style: italic;">
                                    Enter electricity rate to calculate total fee
                                </div>
                            `;
                            submitBtnText.textContent = 'Generate Electricity Invoice';
                        }

                        detailsHtml += `</div>`;
                        calculationDiv.style.display = 'block';
                        detailsDiv.innerHTML = detailsHtml;
                    } else {
                        calculationDiv.style.display = 'none';
                        submitBtnText.textContent = 'Generate Electricity Invoice';
                    }
                }

                // Load rate from sessionStorage on page load
                const storedRate = sessionStorage.getItem('electricity_rate_per_kwh');
                const currentRateDisplay = document.getElementById('currentRateDisplay');

                if (storedRate && rateInput) {
                    // Always update from sessionStorage if available (it's the most recent)
                    rateInput.value = storedRate;
                    if (currentRateDisplay) {
                        currentRateDisplay.textContent = '₱' + parseFloat(storedRate).toFixed(2);
                    }
                } else if (rateInput && rateInput.value) {
                    // If there's a value from server, sync it to sessionStorage
                    sessionStorage.setItem('electricity_rate_per_kwh', rateInput.value);
                    if (currentRateDisplay) {
                        currentRateDisplay.textContent = '₱' + parseFloat(rateInput.value).toFixed(2);
                    }
                }

                // Update current rate display when rate input changes
                if (rateInput) {
                    rateInput.addEventListener('input', function() {
                        if (this.value && currentRateDisplay) {
                            currentRateDisplay.textContent = '₱' + parseFloat(this.value).toFixed(2);
                            sessionStorage.setItem('electricity_rate_per_kwh', this.value);
                        }
                        calculateElectricity();
                    });

                    // Trigger calculation on page load
                    calculateElectricity();
                }

                if (newMeterInput) {
                    newMeterInput.addEventListener('input', calculateElectricity);
                }

                // Also trigger calculation when modal is shown
                const modal = document.getElementById('electricityInvoiceModal');
                if (modal) {
                    modal.addEventListener('show.bs.modal', function() {
                        // Re-load rate from sessionStorage when modal opens (most recent)
                        const updatedRate = sessionStorage.getItem('electricity_rate_per_kwh');
                        if (updatedRate && rateInput) {
                            rateInput.value = updatedRate;
                            if (currentRateDisplay) {
                                currentRateDisplay.textContent = '₱' + parseFloat(updatedRate).toFixed(2);
                            }
                        }
                        // Trigger calculation after a short delay to ensure inputs are ready
                        setTimeout(() => {
                            calculateElectricity();
                        }, 100);
                    });
                }
            });
            </script>

        </div>
    </div>
</div>
@endif

@endsection
