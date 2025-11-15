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
        white-space: nowrap;
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

    .status-badge.Pending-Payment {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.Partial-Payment {
        background-color: #dbeafe;
        color: #1e40af;
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
                    return $invoice->rent_subtotal > 0 || $invoice->utility_water_fee > 0 || $invoice->utility_wifi_fee > 0;
                });
                
                $securityDepositInvoice = $booking->invoices->first(function($invoice) {
                    return $invoice->rent_subtotal == 0 && 
                           $invoice->utility_water_fee == 0 && 
                           $invoice->utility_wifi_fee == 0 && 
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
                        $checkInMessage = 'Monthly Rent + Utilities must be fully paid (₱' . number_format($rentUtilitiesDue, 2) . ') to check in. Current: ₱' . number_format($rentUtilitiesPaid, 2);
                    } else {
                        // Monthly Rent + Utilities is fully paid, check Security Deposit
                        if (!$securityDepositInvoice) {
                            $checkInMessage = 'Security Deposit invoice not found';
                        } else {
                            $securityDepositDue = $securityDepositInvoice->total_due;
                            $securityDepositPaid = $securityDepositInvoice->payments->sum('amount');
                            $requiredMinimum = $securityDepositDue / 2; // Half of security deposit
                            
                            if ($securityDepositPaid < $requiredMinimum) {
                                $checkInMessage = 'Security Deposit must be at least half paid (₱' . number_format($requiredMinimum, 2) . ') to check in. Current: ₱' . number_format($securityDepositPaid, 2);
                            } else {
                                // Both conditions met
                                $canCheckIn = true;
                            }
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

            {{-- Generate Electricity Invoice Button: Only for Active monthly bookings --}}
            @if($booking->effective_status === 'Active' && $booking->rate->duration_type === 'Monthly')
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
            <span>Rate Total ({{ $chargeSummary['duration_type'] }})</span>
            <span>₱{{ number_format($chargeSummary['rate_total'], 2) }}</span>
        </div>
        @if($chargeSummary['security_deposit'] > 0)
        <div class="charge-row">
            <span>Security Deposit</span>
            <span>₱{{ number_format($chargeSummary['security_deposit'], 2) }}</span>
        </div>
        @endif
        @if($chargeSummary['water_fee'] > 0)
        <div class="charge-row">
            <span>Water</span>
            <span>₱{{ number_format($chargeSummary['water_fee'], 2) }}</span>
        </div>
        @endif
        @if($chargeSummary['wifi_fee'] > 0)
        <div class="charge-row">
            <span>Wi-Fi</span>
            <span>₱{{ number_format($chargeSummary['wifi_fee'], 2) }}</span>
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
</div>

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
@if($booking->effective_status === 'Active' && $booking->rate->duration_type === 'Monthly')
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
                    @php
                        $latestReading = \App\Models\ElectricReading::getLatestReading($booking->room_id);
                        $lastReadingValue = $latestReading ? $latestReading->meter_value_kwh : 0;
                    @endphp
                    <div class="mb-3">
                        <label for="elec_last_meter_reading" class="form-label">Last Meter Reading (kWh) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               id="elec_last_meter_reading" 
                               name="last_meter_reading" 
                               value="{{ $lastReadingValue }}"
                               step="0.01"
                               min="0" 
                               required>
                        <small class="text-muted">Enter the previous meter reading (from last billing period)</small>
                    </div>

                    <div class="mb-3">
                        <label for="elec_current_meter_reading" class="form-label">Current Meter Reading (kWh) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               id="elec_current_meter_reading" 
                               name="current_meter_reading" 
                               step="0.01"
                               min="0" 
                               required>
                        <small class="text-muted">Enter the current meter reading</small>
                    </div>

                    <div class="mb-3">
                        <label for="elec_electricity_rate_per_kwh" class="form-label">Electricity Rate per kWh (₱) <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               id="elec_electricity_rate_per_kwh" 
                               name="electricity_rate_per_kwh" 
                               step="0.01"
                               min="0" 
                               required>
                        <small class="text-muted">Enter the current electricity rate per kilowatt-hour</small>
                    </div>

                    <div class="alert alert-info" id="electricityCalculation">
                        <strong>Electricity Calculation:</strong>
                        <div id="electricityDetails">Enter meter readings and rate to see calculation</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-lightning-charge"></i> Generate Electricity Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lastReadingInput = document.getElementById('elec_last_meter_reading');
    const currentReadingInput = document.getElementById('elec_current_meter_reading');
    const rateInput = document.getElementById('elec_electricity_rate_per_kwh');
    const calculationDiv = document.getElementById('electricityCalculation');
    const detailsDiv = document.getElementById('electricityDetails');

    function calculateElectricity() {
        const lastReading = parseFloat(lastReadingInput.value) || 0;
        const currentReading = parseFloat(currentReadingInput.value) || 0;
        const rate = parseFloat(rateInput.value) || 0;

        if (lastReading > 0 && currentReading > 0 && rate > 0) {
            const kwhUsed = Math.max(0, currentReading - lastReading);
            const totalFee = kwhUsed * rate;

            detailsDiv.innerHTML = `
                <div>Last Reading: ${lastReading.toFixed(2)} kWh</div>
                <div>Current Reading: ${currentReading.toFixed(2)} kWh</div>
                <div>Usage: ${kwhUsed.toFixed(2)} kWh</div>
                <div>Rate: ₱${rate.toFixed(2)} per kWh</div>
                <div><strong>Total Electricity Fee: ₱${totalFee.toFixed(2)}</strong></div>
            `;
            calculationDiv.style.display = 'block';
        } else {
            detailsDiv.innerHTML = 'Enter meter readings and rate to see calculation';
        }
    }

    if (lastReadingInput) lastReadingInput.addEventListener('input', calculateElectricity);
    if (currentReadingInput) currentReadingInput.addEventListener('input', calculateElectricity);
    if (rateInput) rateInput.addEventListener('input', calculateElectricity);
});
</script>
@endif

<!-- Security Deposit Payment Modal -->

@endsection

