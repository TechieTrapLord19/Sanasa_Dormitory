<?php $__env->startSection('title', 'Booking Details'); ?>

<?php $__env->startSection('content'); ?>


<style>
    .booking-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
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
        font-size: 0.65rem;
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

    .status-badge.Forfeited,
    .status-badge.Depleted {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-badge.Partially-Used {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Payment Method Badges */
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

    /* Payment Type Badges */
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

    .payment-type-badge.other {
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

    .info-section {
        margin-bottom: 1.5rem;
        flex-shrink: 0;
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e5e5;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
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
            <span class="status-badge <?php echo e(str_replace(' ', '-', $booking->effective_status)); ?>"><?php echo e($booking->effective_status); ?></span>
        </div>
        <div class="action-buttons">
            <?php
                // Check check-in eligibility based on rules:
                // 1. Rent + Utilities MUST be fully paid (ALL invoices, including extensions)
                // 2. Security Deposit must be at least HALF paid (₱2,500 minimum) - only for monthly bookings
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

                // Check Rent + Utilities (aggregate ALL invoices)
                if ($rentUtilitiesInvoices->isEmpty()) {
                    $checkInMessage = $chargeSummary['duration_type'] . ' Rent' . (isset($chargeSummary['utilities']) && count($chargeSummary['utilities']) > 0 ? ' + Utilities' : '') . ' invoice not found';
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
            ?>

            
            <?php if(!in_array($booking->status, ['Active', 'Completed', 'Canceled']) && $canCheckIn): ?>
                <form action="<?php echo e(route('bookings.checkin', $booking->booking_id)); ?>" method="POST" style="display: inline;" id="checkinForm">
                    <?php echo csrf_field(); ?>
                    <button type="button" class="btn-action btn-checkin" onclick="confirmAction('Are you sure you want to check-in this tenant? This will mark the booking as active.', function() { document.getElementById('checkinForm').submit(); }, { title: 'Confirm Check-In', confirmText: 'Yes, Check In', type: 'info' })">
                        <i class="bi bi-box-arrow-in-right"></i> Check-In Tenant
                    </button>
                </form>
            <?php elseif(!in_array($booking->status, ['Active', 'Completed', 'Canceled']) && !$canCheckIn): ?>
                <button type="button" class="btn-action btn-checkin" disabled title="<?php echo e($checkInMessage); ?>">
                    <i class="bi bi-box-arrow-in-right"></i> Check-In Tenant
                </button>
            <?php endif; ?>

            
            <?php if($booking->effective_status === 'Active'): ?>
                <form action="<?php echo e(route('bookings.checkout', $booking->booking_id)); ?>" method="POST" style="display: inline;" id="checkoutForm">
                    <?php echo csrf_field(); ?>
                    <button type="button" class="btn-action btn-checkout" onclick="confirmAction('Are you sure you want to check out this tenant? This will mark the booking as completed.', function() { document.getElementById('checkoutForm').submit(); }, { title: 'Confirm Check-Out', confirmText: 'Yes, Check Out', type: 'warning' })">
                        <i class="bi bi-box-arrow-right"></i> Check-Out Tenant
                    </button>
                </form>
            <?php endif; ?>

            
            <?php if($booking->effective_status === 'Active'): ?>
                <button type="button" class="btn-action btn-invoice" data-bs-toggle="modal" data-bs-target="#renewalInvoiceModal">
                    <i class="bi bi-receipt-cutoff"></i> Generate Renewal Invoice
                </button>
            <?php endif; ?>

            
            <?php if($booking->effective_status === 'Active' && isset($isMonthlyStay) && $isMonthlyStay): ?>
                <button type="button" class="btn-action btn-invoice" data-bs-toggle="modal" data-bs-target="#electricityInvoiceModal" style="background-color: #f59e0b;">
                    <i class="bi bi-lightning-charge"></i> Generate Electricity Invoice
                </button>
            <?php endif; ?>


            
            <?php if($booking->effective_status !== 'Canceled' && $booking->effective_status !== 'Completed'): ?>
                <a href="<?php echo e(route('bookings.edit', $booking->booking_id)); ?>" class="btn-action btn-edit">
                    <i class="bi bi-pencil-square"></i> Edit Booking
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Details (Combined) -->
    <div class="info-section">
        <h2 class="info-section-title">Tenant Information</h2>
        <?php
            $occupants = collect([$booking->tenant, $booking->secondaryTenant])->filter();
        ?>
        <?php if($occupants->isEmpty()): ?>
            <span class="info-value" style="color: #94a3b8;">No tenants assigned</span>
        <?php else: ?>
            <?php $__currentLoopData = $occupants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $occupant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="info-grid mb-3">
                    <div class="info-item">
                        <span class="info-label">Name <?php if($occupants->count() > 1): ?> #<?php echo e($index + 1); ?> <?php endif; ?></span>
                        <span class="info-value"><?php echo e($occupant->full_name); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo e($occupant->email ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value"><?php echo e($occupant->contact_num ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Age</span>
                        <span class="info-value"><?php echo e($occupant->birth_date ? $occupant->birth_date->age . ' years old' : 'N/A'); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <h2 class="info-section-title" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">Booking Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Room Number</span>
                <span class="info-value"><?php echo e($booking->room->room_num); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Floor</span>
                <span class="info-value"><?php echo e($booking->room->floor); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Check-in Date and Time</span>
                <?php if($booking->checked_in_at): ?>
                    <span class="info-value" style="color: #10b981; font-weight: 600;"><?php echo e($booking->checked_in_at->format('M d, Y - g:i A')); ?></span>
                <?php else: ?>
                    <span class="info-value"><?php echo e($booking->checkin_date->format('M d, Y')); ?> <small style="color: #6b7280;">(Not yet arrived)</small></span>
                <?php endif; ?>
            </div>
            <div class="info-item">
                <span class="info-label">Check-out Date and Time</span>
                <?php if($booking->checked_out_at): ?>
                    <span class="info-value" style="color: #10b981; font-weight: 600;"><?php echo e($booking->checked_out_at->format('M d, Y - g:i A')); ?></span>
                <?php else: ?>
                    <span class="info-value"><?php echo e($booking->checkout_date->format('M d, Y')); ?> <small style="color: #6b7280;">(Scheduled)</small></span>
                <?php endif; ?>
            </div>
            <div class="info-item">
                <span class="info-label">Stay Length</span>
                <span class="info-value"><?php echo e($stayLengthDays); ?> day(s)</span>
            </div>
        </div>

        </div>
    </div>
    <!-- Charges Summary -->
    <div class="charges-summary">
        <h2 class="info-section-title">Booking Breakdown</h2>
        <div class="charge-row">
            <span>Rent (<?php echo e($chargeSummary['duration_type']); ?>)</span>
            <span>₱<?php echo e(number_format($chargeSummary['rate_total'], 2)); ?></span>
        </div>
        <?php if(isset($chargeSummary['utilities']) && count($chargeSummary['utilities']) > 0): ?>
            <?php $__currentLoopData = $chargeSummary['utilities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $utility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="charge-row">
                <span><?php echo e(str_replace('Garbage', 'Garbage Collection', $utility['name'])); ?></span>
                <span>₱<?php echo e(number_format($utility['amount'], 2)); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if($chargeSummary['security_deposit'] > 0): ?>
        <div class="charge-row">
            <span>Security Deposit</span>
            <span>₱<?php echo e(number_format($chargeSummary['security_deposit'], 2)); ?></span>
        </div>
        <?php endif; ?>
        <div class="charge-row total">
            <span>
                <?php if(in_array($booking->status, ['Pending Payment', 'Reserved'])): ?>
                    Initial Payment Required
                <?php else: ?>
                    Total Amount
                <?php endif; ?>
            </span>
            <span>₱<?php echo e(number_format($chargeSummary['total_due'], 2)); ?></span>
        </div>
        <?php if($chargeSummary['note']): ?>
            <div class="charge-note"><?php echo e($chargeSummary['note']); ?></div>
        <?php endif; ?>
    </div>

    <!-- Payment History -->
    <div class="info-section">
        <h2 class="info-section-title">
            <i class="bi bi-credit-card me-2"></i>Payment History
            <?php if($allPayments->count() > 0): ?>
                <span style="font-weight: 400; font-size: 0.85rem; color: #64748b;">
                    (<?php echo e($allPayments->count()); ?> <?php echo e(Str::plural('payment', $allPayments->count())); ?> · Total: ₱<?php echo e(number_format($allPayments->sum('amount'), 2)); ?>)
                </span>
            <?php endif; ?>
        </h2>
        <?php if($allPayments->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Collected By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $allPayments->sortByDesc('date_received'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $methodClass = match(strtolower($payment->payment_method)) {
                                    'cash' => 'cash',
                                    'gcash' => 'gcash',
                                    'bank transfer', 'bank' => 'bank',
                                    'check', 'cheque' => 'check',
                                    default => 'other'
                                };
                                $typeClass = match(true) {
                                    str_contains(strtolower($payment->payment_type), 'rent') => 'rent',
                                    str_contains(strtolower($payment->payment_type), 'electric') || str_contains(strtolower($payment->payment_type), 'utility') => 'utility',
                                    str_contains(strtolower($payment->payment_type), 'deposit') => 'deposit',
                                    default => 'other'
                                };

                                // Format payment type display name
                                $displayPaymentType = str_replace('Garbage', 'Garbage Collection', $payment->payment_type);

                                // Get invoice and calculate balance
                                $invoice = $payment->invoice;
                                if ($invoice) {
                                    // Get all payments for this invoice up to and including current payment
                                    $paymentsUpToCurrent = $invoice->payments()
                                        ->where('date_received', '<=', $payment->date_received)
                                        ->where(function($q) use ($payment) {
                                            $q->where('date_received', '<', $payment->date_received)
                                              ->orWhere(function($q2) use ($payment) {
                                                  $q2->where('date_received', '=', $payment->date_received)
                                                     ->where('payment_id', '<=', $payment->payment_id);
                                              });
                                        })
                                        ->sum('amount');

                                    $invoiceTotal = $invoice->total_due;
                                    $balanceAfter = $invoiceTotal - $paymentsUpToCurrent;

                                    // Check if this payment completed the invoice (made is_paid = true)
                                    $isPaidAfterThisPayment = $balanceAfter <= 0;
                                } else {
                                    $isPaidAfterThisPayment = false;
                                    $balanceAfter = 0;
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($payment->date_received ? $payment->date_received->format('M d, Y') : 'N/A'); ?></strong>
                                    <br><small class="text-muted"><?php echo e($payment->created_at->format('g:i A')); ?></small>
                                </td>
                                <td>
                                    <span class="payment-type-badge <?php echo e($typeClass); ?>"><?php echo e($displayPaymentType); ?></span>
                                </td>
                                <td style="font-weight: 600; color: #059669;">₱<?php echo e(number_format($payment->amount, 2)); ?></td>
                                <td>
                                    <?php if($invoice): ?>
                                        <?php if($isPaidAfterThisPayment): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($invoice): ?>
                                        <span style="font-weight: 500; <?php echo e($balanceAfter > 0 ? 'color: #dc2626;' : 'color: #059669;'); ?>">
                                            ₱<?php echo e(number_format(max(0, $balanceAfter), 2)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="payment-method-badge <?php echo e($methodClass); ?>">
                                        <?php if($methodClass === 'cash'): ?>
                                            <i class="bi bi-cash"></i>
                                        <?php elseif($methodClass === 'gcash'): ?>
                                            <i class="bi bi-phone"></i>
                                        <?php elseif($methodClass === 'bank'): ?>
                                            <i class="bi bi-bank"></i>
                                        <?php elseif($methodClass === 'check'): ?>
                                            <i class="bi bi-file-text"></i>
                                        <?php else: ?>
                                            <i class="bi bi-credit-card"></i>
                                        <?php endif; ?>
                                        <?php echo e($payment->payment_method); ?>

                                    </span>
                                </td>
                                <td><?php echo e($payment->reference_number ?? '-'); ?></td>
                                <td><?php echo e($payment->collectedBy->full_name ?? 'N/A'); ?></td>
                                <td>
                                    <a href="<?php echo e(route('payments.receipt', $payment->payment_id)); ?>" class="btn-receipt" target="_blank">
                                        <i class="bi bi-printer"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 2rem; color: #64748b;">
                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <p class="mb-0">No payments recorded for this booking yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Cancellation Reason (if cancelled) -->
    <?php if($booking->status === 'Canceled' && $booking->cancellation_reason): ?>
    <div class="info-section">
        <h2 class="info-section-title">Cancellation Information</h2>
        <div class="info-item">
            <span class="info-label">Cancellation Reason</span>
            <span class="info-value"><?php echo e($booking->cancellation_reason); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Refund Section (if cancelled and can be refunded) -->
    <?php
        $refundablePayments = collect();
        $totalRefundable = 0;
        if ($booking->canBeRefunded() && $allPayments->where('amount', '>', 0)->isNotEmpty()) {
            // Check if security deposit is forfeited
            $isDepositForfeited = $booking->securityDeposit && $booking->securityDeposit->status === 'Forfeited';

            $refundablePayments = $allPayments->filter(function($payment) use ($isDepositForfeited) {
                // Exclude Security Deposit payments if the deposit has been forfeited
                if ($isDepositForfeited && $payment->payment_type === 'Security Deposit') {
                    return false;
                }
                return $payment->canBeRefunded() && $payment->amount > 0;
            });
            $totalRefundable = $refundablePayments->sum('remaining_refundable_amount');
        }
    ?>

    <?php if($booking->canBeRefunded() && $refundablePayments->isNotEmpty()): ?>
    <div class="info-section">
        <h2 class="info-section-title">Refunds</h2>

        <div class="alert alert-info mb-3">
            <strong>Total Refundable Amount: ₱<?php echo e(number_format($totalRefundable, 2)); ?></strong>
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
                    <?php $__currentLoopData = $refundablePayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($payment->payment_type); ?></td>
                        <td>₱<?php echo e(number_format($payment->amount, 2)); ?></td>
                        <td>₱<?php echo e(number_format($payment->total_refunded, 2)); ?></td>
                        <td><strong>₱<?php echo e(number_format($payment->remaining_refundable_amount, 2)); ?></strong></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#refundModal<?php echo e($payment->payment_id); ?>">
                                <i class="bi bi-arrow-counterclockwise"></i> Process Refund
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Refund History (always show if there are refunds) -->
    <?php if($booking->refunds->isNotEmpty()): ?>
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
                    <?php $__currentLoopData = $booking->refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($refund->refund_date->format('M d, Y')); ?></td>
                        <td><?php echo e($refund->payment->payment_type); ?></td>
                        <td>₱<?php echo e(number_format($refund->refund_amount, 2)); ?></td>
                        <td><?php echo e($refund->refund_method); ?></td>
                        <td><?php echo e($refund->reference_number ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge bg-<?php echo e($refund->status === 'Completed' ? 'success' : ($refund->status === 'Processed' ? 'warning' : 'secondary')); ?>">
                                <?php echo e($refund->status === 'Processed' ? 'Refunded' : $refund->status); ?>

                            </span>
                        </td>
                        <td><?php echo e($refund->refundedBy->full_name ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Security Deposit Management Section -->
    <?php if($booking->securityDeposit): ?>
    <?php
        $deposit = $booking->securityDeposit;
        $statusClass = match($deposit->status) {
            'Pending' => 'Pending-Payment',
            'Held' => 'Active',
            'Depleted' => 'Depleted',
            'Partially Refunded' => 'Partial-Payment',
            'Refunded' => 'Completed',
            'Forfeited' => 'Canceled',
            default => 'Pending-Payment'
        };
        $shortfall = max(0, $deposit->amount_required - $deposit->calculateRefundable());
    ?>
    <div class="info-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="info-section-title mb-0">
                <i class="bi bi-shield-check me-2"></i>Security Deposit Management
            </h2>
            <div class="d-flex gap-2">
                <?php if($deposit->calculateRefundable() < $deposit->amount_required && in_array($deposit->status, ['Held', 'Depleted', 'Pending'])): ?>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#topUpDepositModal">
                        <i class="bi bi-plus-circle me-1"></i> Top Up Deposit
                    </button>
                <?php endif; ?>
                <a href="<?php echo e(route('security-deposits.show', $booking->securityDeposit)); ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-gear me-1"></i> Manage Deposit
                </a>
            </div>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Required</th>
                        <th>Paid</th>
                        <th>Deducted</th>
                        <th>Refundable Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="status-badge <?php echo e($statusClass); ?>"><?php echo e($deposit->status); ?></span></td>
                        <td>₱<?php echo e(number_format($deposit->amount_required, 2)); ?></td>
                        <td>₱<?php echo e(number_format($deposit->amount_paid, 2)); ?></td>
                        <td style="color: #dc2626;">-₱<?php echo e(number_format($deposit->amount_deducted, 2)); ?></td>
                        <td style="color: <?php echo e($deposit->calculateRefundable() > 0 ? '#059669' : '#dc2626'); ?>; font-weight: 600;">
                            ₱<?php echo e(number_format($deposit->calculateRefundable(), 2)); ?>

                            <?php if($shortfall > 0 && !in_array($deposit->status, ['Forfeited', 'Refunded'])): ?>
                                <small class="text-danger d-block">(₱<?php echo e(number_format($shortfall, 2)); ?> below required)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deductions summary if any -->
        <?php $deductions = $deposit->getDeductionsArray(); ?>
        <?php if(count($deductions) > 0): ?>
            <div class="mt-2">
                <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Applied Deductions:</span>
                <ul class="list-unstyled mb-0 mt-2" style="font-size: 0.875rem;">
                    <?php $__currentLoopData = $deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="mb-1" style="color: #dc2626;">
                            <i class="bi bi-dash-circle me-1"></i>
                            <?php echo e($deduction['category']); ?>: -₱<?php echo e(number_format($deduction['amount'], 2)); ?>

                            <?php if(!empty($deduction['description'])): ?>
                                <small class="text-muted">(<?php echo e($deduction['description']); ?>)</small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Refund Modals -->
<?php if($booking->canBeRefunded() && $refundablePayments->isNotEmpty()): ?>
    <?php $__currentLoopData = $refundablePayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="refundModal<?php echo e($payment->payment_id); ?>" tabindex="-1" aria-labelledby="refundModalLabel<?php echo e($payment->payment_id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel<?php echo e($payment->payment_id); ?>">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('bookings.refund', $booking->booking_id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="payment_id" value="<?php echo e($payment->payment_id); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Type</label>
                            <input type="text" class="form-control" value="<?php echo e($payment->payment_type); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Original Amount</label>
                            <input type="text" class="form-control" value="₱<?php echo e(number_format($payment->amount, 2)); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Already Refunded</label>
                            <input type="text" class="form-control" value="₱<?php echo e(number_format($payment->total_refunded, 2)); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="refund_amount<?php echo e($payment->payment_id); ?>" class="form-label">Refund Amount <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="refund_amount<?php echo e($payment->payment_id); ?>"
                                   name="refund_amount"
                                   step="0.01"
                                   min="0.01"
                                   max="<?php echo e($payment->remaining_refundable_amount); ?>"
                                   value="<?php echo e($payment->remaining_refundable_amount); ?>"
                                   required>
                            <small class="text-muted">Maximum refundable: ₱<?php echo e(number_format($payment->remaining_refundable_amount, 2)); ?></small>
                        </div>
                        <div class="mb-3">
                            <label for="refund_method<?php echo e($payment->payment_id); ?>" class="form-label">Refund Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="refund_method<?php echo e($payment->payment_id); ?>" name="refund_method" required>
                                <option value="">Select method...</option>
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                            </select>
                        </div>
                        <div class="mb-3" id="reference_number_container<?php echo e($payment->payment_id); ?>" style="display: none;">
                            <label for="reference_number<?php echo e($payment->payment_id); ?>" class="form-label">Reference Number <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="reference_number<?php echo e($payment->payment_id); ?>"
                                   name="reference_number"
                                   placeholder="Enter GCash reference number">
                            <small class="text-muted">Required for GCash refunds</small>
                        </div>
                        <div class="mb-3">
                            <label for="refund_date<?php echo e($payment->payment_id); ?>" class="form-label">Refund Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="refund_date<?php echo e($payment->payment_id); ?>"
                                   name="refund_date"
                                   value="<?php echo e(now()->toDateString()); ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="cancellation_reason_refund<?php echo e($payment->payment_id); ?>" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="cancellation_reason_refund<?php echo e($payment->payment_id); ?>"
                                      name="cancellation_reason"
                                      rows="3"
                                      required><?php echo e($booking->cancellation_reason ?? ''); ?></textarea>
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
            const refundMethodSelect = document.getElementById('refund_method<?php echo e($payment->payment_id); ?>');
            const referenceContainer = document.getElementById('reference_number_container<?php echo e($payment->payment_id); ?>');
            const referenceInput = document.getElementById('reference_number<?php echo e($payment->payment_id); ?>');

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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<!-- Renewal Invoice Modal -->
<?php if($booking->effective_status === 'Active'): ?>
<div class="modal fade" id="renewalInvoiceModal" tabindex="-1" aria-labelledby="renewalInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewalInvoiceModalLabel">Generate Renewal Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('bookings.renew', $booking->booking_id)); ?>" method="POST" id="renewalInvoiceForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="extension_days" class="form-label">Extension Days <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="extension_days"
                               name="extension_days"
                               value="<?php echo e($booking->rate->duration_type === 'Monthly' ? '30' : ''); ?>"
                               min="1"
                               max="30"
                               required>
                        <small class="text-muted">
                            <?php if($booking->rate->duration_type === 'Monthly'): ?>
                                Default: 30 days. If past due, days past due will be deducted from extension period (within 3-day grace period).
                            <?php else: ?>
                                Enter number of days to extend the booking (maximum 30 days)
                            <?php endif; ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const extensionDaysInput = document.getElementById('extension_days');

        if (extensionDaysInput) {
            extensionDaysInput.addEventListener('input', function() {
                let value = parseInt(this.value);

                if (value > 30) {
                    this.value = 30;
                } else if (value < 1 && this.value !== '') {
                    this.value = 1;
                }
            });

            extensionDaysInput.addEventListener('change', function() {
                let value = parseInt(this.value);

                if (value > 30) {
                    this.value = 30;
                } else if (value < 1 || isNaN(value)) {
                    this.value = 1;
                }
            });
        }
    });
</script>

<?php endif; ?>

<!-- Electricity Invoice Modal -->
<?php if($booking->effective_status === 'Active' && isset($isMonthlyStay) && $isMonthlyStay): ?>
<div class="modal fade" id="electricityInvoiceModal" tabindex="-1" aria-labelledby="electricityInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="electricityInvoiceModalLabel">Generate Electricity Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form action="<?php echo e(route('bookings.electricity', $booking->booking_id)); ?>" method="POST" id="electricityInvoiceForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="elec_last_meter_reading" class="form-label">Last Meter Reading (kWh)</label>
                            <?php if($lastReading): ?>
                                <input type="text"
                                       class="form-control"
                                       id="elec_last_meter_reading"
                                       value="<?php echo e(number_format($lastReading->meter_value_kwh, 2)); ?> kWh on <?php echo e($lastReading->reading_date->format('M d, Y')); ?>"
                                       readonly
                                       style="background-color: #f8fafc;">
                                <small class="text-muted">Automatically retrieved from Electric Readings logbook</small>
                            <?php else: ?>
                                <input type="text"
                                       class="form-control"
                                       id="elec_last_meter_reading"
                                       value="No previous reading (0.00 kWh)"
                                       readonly
                                       style="background-color: #f8fafc;">
                                <small class="text-muted">No previous reading found. Usage will be calculated from 0.00 kWh.</small>
                            <?php endif; ?>
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
                                   value="<?php echo e(old('electricity_rate_per_kwh', $electricityRate ?? '')); ?>"
                                   required>
                            <small class="text-muted">Current rate: <strong id="currentRateDisplay">₱<?php echo e($electricityRate ? number_format($electricityRate, 2) : 'Not set'); ?></strong> (from Electric Readings page)</small>
                        </div>

                        <div class="mb-3">
                            <label for="elec_usage" class="form-label">Usage (kWh)</label>
                            <input type="text"
                                   class="form-control"
                                   id="elec_usage"
                                   readonly
                                   style="background-color: #f8fafc;">
                        </div>

                        <div class="mb-3">
                            <label for="elec_total" class="form-label">Total Amount (₱)</label>
                            <input type="text"
                                   class="form-control"
                                   id="elec_total"
                                   readonly
                                   style="background-color: #f8fafc; font-weight: 600;">
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
                const hasLastReading = <?php echo e($lastReading ? 'true' : 'false'); ?>;
                const lastReadingValue = <?php echo e($lastReading ? $lastReading->meter_value_kwh : 0); ?>;
                const rateInput = document.getElementById('elec_electricity_rate_per_kwh');
                const newMeterInput = document.getElementById('elec_new_meter_reading');
                const usageInput = document.getElementById('elec_usage');
                const totalInput = document.getElementById('elec_total');
                const submitBtnText = document.getElementById('submitBtnText');

                function calculateElectricity() {
                    if (!rateInput || !newMeterInput || !usageInput || !totalInput) {
                        return;
                    }

                    const rate = parseFloat(rateInput.value) || 0;
                    const newVal = parseFloat(newMeterInput.value) || 0;
                    const lastVal = hasLastReading ? lastReadingValue : 0;
                    const kwhUsed = Math.max(0, newVal - lastVal);
                    const totalFee = kwhUsed * rate;

                    if (newMeterInput.value && newMeterInput.value.trim() !== '') {
                        usageInput.value = kwhUsed.toFixed(2) + ' kWh';
                        if (rate > 0) {
                            totalInput.value = '₱' + totalFee.toFixed(2);
                            submitBtnText.textContent = 'Add ₱' + totalFee.toFixed(2) + ' to Invoice';
                        } else {
                            totalInput.value = '';
                            submitBtnText.textContent = 'Generate Electricity Invoice';
                        }
                    } else {
                        usageInput.value = '';
                        totalInput.value = '';
                        submitBtnText.textContent = 'Generate Electricity Invoice';
                    }
                }

                // Load rate from database. SessionStorage is now just for the current session.
                // Priority: Database value (from PHP) > SessionStorage (if user changed it this session)
                const storedRate = sessionStorage.getItem('electricity_rate_per_kwh');
                const currentRateDisplay = document.getElementById('currentRateDisplay');
                const dbRate = rateInput ? rateInput.value : null;

                // Only use sessionStorage if it has a valid non-zero rate AND the database rate is empty
                // This ensures database value takes priority on page load
                if (storedRate && parseFloat(storedRate) > 0 && (!dbRate || parseFloat(dbRate) <= 0)) {
                    rateInput.value = storedRate;
                    if (currentRateDisplay) {
                        currentRateDisplay.textContent = '₱' + parseFloat(storedRate).toFixed(2);
                    }
                } else if (dbRate && parseFloat(dbRate) > 0) {
                    // Database has a valid rate, use it and update sessionStorage
                    sessionStorage.setItem('electricity_rate_per_kwh', dbRate);
                    if (currentRateDisplay) {
                        currentRateDisplay.textContent = '₱' + parseFloat(dbRate).toFixed(2);
                    }
                }

                // Update on input
                if (rateInput) {
                    const updateRate = function() {
                        if (this.value && currentRateDisplay) {
                            currentRateDisplay.textContent = '₱' + parseFloat(this.value).toFixed(2);
                            sessionStorage.setItem('electricity_rate_per_kwh', this.value);
                        }
                        calculateElectricity();
                    };
                    rateInput.addEventListener('input', updateRate);
                    rateInput.addEventListener('change', updateRate);
                }

                if (newMeterInput) {
                    newMeterInput.addEventListener('input', calculateElectricity);
                    newMeterInput.addEventListener('change', calculateElectricity);
                }

                // Trigger calculation when modal is shown
                const modal = document.getElementById('electricityInvoiceModal');
                if (modal) {
                    modal.addEventListener('show.bs.modal', function() {
                        const updatedRate = sessionStorage.getItem('electricity_rate_per_kwh');
                        if (updatedRate && rateInput) {
                            rateInput.value = updatedRate;
                            if (currentRateDisplay) {
                                currentRateDisplay.textContent = '₱' + parseFloat(updatedRate).toFixed(2);
                            }
                        }
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
<?php endif; ?>

<!-- Top Up Deposit Modal -->
<?php if($booking->securityDeposit && $booking->securityDeposit->calculateRefundable() < $booking->securityDeposit->amount_required && in_array($booking->securityDeposit->status, ['Held', 'Depleted', 'Pending'])): ?>
<div class="modal fade" id="topUpDepositModal" tabindex="-1" aria-labelledby="topUpDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="topUpDepositModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Top Up Security Deposit
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('security-deposits.top-up', $booking->securityDeposit)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <?php
                        $currentBalance = $booking->securityDeposit->calculateRefundable();
                        $required = $booking->securityDeposit->amount_required;
                        $shortfall = max(0, $required - $currentBalance);
                    ?>

                    <div class="alert alert-info mb-3">
                        <strong>Current Balance:</strong> ₱<?php echo e(number_format($currentBalance, 2)); ?><br>
                        <strong>Required Amount:</strong> ₱<?php echo e(number_format($required, 2)); ?><br>
                        <strong>Shortfall:</strong> <span class="text-danger">₱<?php echo e(number_format($shortfall, 2)); ?></span>
                    </div>

                    <div class="mb-3">
                        <label for="topup_amount" class="form-label">Top Up Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number"
                                   class="form-control"
                                   id="topup_amount"
                                   name="amount"
                                   min="1"
                                   step="0.01"
                                   value="<?php echo e($shortfall); ?>"
                                   required>
                        </div>
                        <small class="text-muted">Suggested: ₱<?php echo e(number_format($shortfall, 2)); ?> to restore full deposit</small>
                    </div>

                    <div class="mb-3">
                        <label for="topup_payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="topup_payment_method" name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="topup_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="topup_notes" name="notes" rows="2" placeholder="Optional notes about this top-up"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" style="color: white;">
                        <i class="bi bi-check-circle me-1"></i> Process Top Up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/contents/bookings-show.blade.php ENDPATH**/ ?>