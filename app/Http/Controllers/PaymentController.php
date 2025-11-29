<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Booking;
use App\Models\SecurityDeposit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Traits\LogsActivity;

class PaymentController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the form data
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,booking_id'],
            'invoice_id' => ['required', 'exists:invoices,invoice_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:Cash,GCash'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'date_received' => ['required', 'date'],
        ], [
            'booking_id.required' => 'Booking ID is required.',
            'booking_id.exists' => 'The selected booking does not exist.',
            'invoice_id.required' => 'Invoice ID is required.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.01.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Payment method must be either "Cash" or "GCash".',
            'date_received.required' => 'Date received is required.',
            'date_received.date' => 'Date received must be a valid date.',
        ]);

        // Additional validation: reference_number is required if payment_method is GCash
        if ($validated['payment_method'] === 'GCash' && empty($validated['reference_number'])) {
            throw ValidationException::withMessages([
                'reference_number' => 'Reference number is required when payment method is GCash.',
            ]);
        }

        // Determine payment type based on invoice type
        $invoiceForType = Invoice::with('invoiceUtilities')->findOrFail($validated['invoice_id']);
        $hasUtilities = $invoiceForType->invoiceUtilities && $invoiceForType->invoiceUtilities->count() > 0;
        $isSecurityDepositInvoice = ($invoiceForType->rent_subtotal == 0 &&
                                    !$hasUtilities &&
                                    $invoiceForType->utility_electricity_fee > 0);

        $paymentType = $isSecurityDepositInvoice ? 'Security Deposit' : 'Rent/Utility';

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Create and save the payment
            $payment = Payment::create([
                'booking_id' => $validated['booking_id'],
                'invoice_id' => $validated['invoice_id'] ?? null,
                'collected_by_user_id' => Auth::id(),
                'payment_type' => $paymentType,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'date_received' => $validated['date_received'],
            ]);

            // Get the booking with tenant context
            $booking = Booking::with(['tenant', 'secondaryTenant', 'room'])->findOrFail($validated['booking_id']);
            $invoice = null;
            $invoicePaid = false;

            // Update the invoice payment status
            if ($validated['invoice_id']) {
                $invoice = Invoice::with(['booking', 'invoiceUtilities'])->findOrFail($validated['invoice_id']);

                // Prevent payment on canceled bookings
                if (optional($invoice->booking)->status === 'Canceled') {
                    DB::rollBack();
                    return redirect()->route('invoices')
                        ->withInput()
                        ->withErrors(['error' => 'Cannot record payment for a canceled booking.']);
                }

                // Calculate total amount paid for this invoice
                $totalPaid = Payment::where('invoice_id', $invoice->invoice_id)
                    ->sum('amount');

                // Update invoice status if fully paid
                if ($totalPaid >= $invoice->total_due) {
                    $invoice->is_paid = true;
                    $invoice->save();
                    $invoicePaid = true;
                }

                // If this is a security deposit payment, update or create SecurityDeposit record
                if ($paymentType === 'Security Deposit') {
                    $this->updateSecurityDepositRecord($booking, $invoice, $validated['amount']);
                }
            }

            // Note: Check-in is now manual only - no automatic status change
            // Booking status remains as 'Pending Payment' or 'Reserved' until manually checked in
            // Partial payments are allowed for check-in (handled in check-in process)

            DB::commit();

            $description = "Recorded payment of ₱" . number_format($validated['amount'], 2) . " for booking #{$booking->booking_id}";
            $description .= " (Tenant(s): {$booking->tenant_summary}, Room: {$booking->room->room_num}, Method: {$validated['payment_method']})";
            if ($invoicePaid) {
                $description .= " - Invoice marked as paid";
            }
            $this->logActivity('Recorded Payment', $description, $payment);

            // Determine success message based on what was updated
            $successMessage = 'Payment recorded successfully.';
            if ($invoicePaid) {
                $successMessage .= ' Invoice marked as paid.';
            }

            // Check if booking is ready for check-in (all requirements met)
            // Requirements: Monthly Rent fully paid AND Security Deposit at least half paid
            $readyForCheckIn = $this->isBookingReadyForCheckIn($booking->booking_id);

            if ($readyForCheckIn) {
                // Redirect to booking details page for easy check-in
                return redirect()->route('bookings.show', ['id' => $booking->booking_id])
                    ->with('success', $successMessage . ' Booking is now ready for check-in!')
                    ->with('payment_id', $payment->payment_id)
                    ->with('show_checkin', true); // Flag to highlight check-in button
            } else {
                // Stay on invoices page if not ready for check-in yet
                return redirect()->route('invoices')
                    ->with('success', $successMessage);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('invoices')
                ->withInput()
                ->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if a booking is ready for check-in.
     * Requirements:
     * - Monthly Rent + Utilities invoice is FULLY paid
     * - Security Deposit invoice is at least HALF paid
     */
    private function isBookingReadyForCheckIn(int $bookingId): bool
    {
        $invoices = Invoice::where('booking_id', $bookingId)
            ->with('invoiceUtilities')
            ->withSum('payments as payments_sum', 'amount')
            ->get();

        if ($invoices->isEmpty()) {
            return false;
        }

        $rentInvoicePaid = false;
        $securityDepositOk = false;
        $hasRentInvoice = false;
        $hasSecurityDeposit = false;

        foreach ($invoices as $invoice) {
            $totalPaid = (float) ($invoice->payments_sum ?? 0);
            $totalDue = (float) $invoice->total_due;

            // Check if this is a security deposit invoice
            // Security deposit: no rent, no utilities from invoice_utilities, only electricity fee
            $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
            $isSecurityDepositInvoice = ($invoice->rent_subtotal == 0 &&
                                        !$hasUtilities &&
                                        $invoice->utility_electricity_fee > 0);

            if ($isSecurityDepositInvoice) {
                $hasSecurityDeposit = true;
                // Security deposit needs at least half paid
                $halfDue = $totalDue / 2;
                if ($totalPaid >= $halfDue) {
                    $securityDepositOk = true;
                }
            } else {
                // This is a rent/utilities invoice
                $hasRentInvoice = true;
                // Rent invoice needs to be fully paid
                if ($totalPaid >= $totalDue) {
                    $rentInvoicePaid = true;
                }
            }
        }

        // If there's no security deposit invoice, consider it OK
        if (!$hasSecurityDeposit) {
            $securityDepositOk = true;
        }

        // If there's no rent invoice, consider it OK (edge case)
        if (!$hasRentInvoice) {
            $rentInvoicePaid = true;
        }

        return $rentInvoicePaid && $securityDepositOk;
    }

    /**
     * Update or create a SecurityDeposit record when a security deposit payment is made.
     */
    private function updateSecurityDepositRecord(Booking $booking, Invoice $invoice, float $paymentAmount): void
    {
        // Find or create the security deposit record
        $securityDeposit = SecurityDeposit::firstOrNew(
            ['booking_id' => $booking->booking_id],
            [
                'invoice_id' => $invoice->invoice_id,
                'amount_required' => $invoice->total_due,
                'amount_paid' => 0,
                'amount_deducted' => 0,
                'amount_refunded' => 0,
                'status' => SecurityDeposit::STATUS_PENDING,
            ]
        );

        // Update amount paid
        $securityDeposit->amount_paid += $paymentAmount;

        // Update status based on payment
        if ($securityDeposit->amount_paid > 0) {
            $securityDeposit->status = SecurityDeposit::STATUS_HELD;
        }

        // Link to invoice if not already
        if (!$securityDeposit->invoice_id) {
            $securityDeposit->invoice_id = $invoice->invoice_id;
        }

        $securityDeposit->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Show receipt for a payment
     */
    public function showReceipt(string $id)
    {
        $payment = Payment::with([
            'booking.tenant',
            'booking.secondaryTenant',
            'booking.room',
            'booking.rate',
            'invoice',
            'collectedBy'
        ])->findOrFail($id);

        $booking = $payment->booking;
        $tenant = $booking->tenant;
        $room = $booking->room;
        $collectedBy = $payment->collectedBy;
        $invoice = $payment->invoice;
        $occupants = collect([$booking->tenant, $booking->secondaryTenant])->filter();

        // Load invoice utilities if invoice exists
        if ($invoice) {
            $invoice->load('invoiceUtilities');
        }

        // Determine payment type based on invoice (similar to InvoiceController logic)
        $paymentType = 'Rent/Utility'; // Default
        if ($invoice) {
            $durationType = optional($booking->rate)->duration_type ?? 'Monthly';

            // Check if this is a security deposit invoice
            // Use loaded relationship (property) instead of method call
            $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
            $hasOnlyElectricityFee = ($invoice->rent_subtotal == 0 &&
                                     !$hasUtilities &&
                                     $invoice->utility_electricity_fee > 0);

            // Get invoice position for this booking
            $invoiceCount = \App\Models\Invoice::where('booking_id', $booking->booking_id)
                ->where('invoice_id', '<=', $invoice->invoice_id)
                ->count();

            $isEarlyInvoice = $invoiceCount <= 2;

            // Security deposit is typically the 2nd invoice for monthly bookings
            $isSecurityDepositInvoice = $hasOnlyElectricityFee &&
                                       ($isEarlyInvoice || abs($invoice->utility_electricity_fee - 5000.00) < 0.01);

            if ($isSecurityDepositInvoice) {
                $paymentType = 'Security Deposit';
            } elseif ($hasOnlyElectricityFee) {
                $paymentType = 'Electricity';
            } else {
                // Regular rent/utility invoice
                $isFirstInvoice = $invoiceCount == 1;
                if ($isFirstInvoice) {
                    $paymentType = match($durationType) {
                        'Daily' => 'Daily Rate',
                        'Weekly' => 'Weekly Rate',
                        'Monthly' => 'Monthly Rent + Utilities',
                        default => 'Monthly Rent + Utilities'
                    };
                } else {
                    $paymentType = match($durationType) {
                        'Daily' => 'Daily Rate',
                        'Weekly' => 'Weekly Rate',
                        'Monthly' => 'Monthly Rent + Utilities',
                        default => 'Monthly Rent + Utilities'
                    };
                }
            }
        }

        // Generate receipt number: REC-YYYYMMDD-XXXXX
        $receiptNumber = 'REC-' . $payment->date_received->format('Ymd') . '-' . str_pad($payment->payment_id, 5, '0', STR_PAD_LEFT);

        return view('receipts.payment-receipt', compact(
            'payment',
            'booking',
            'tenant',
            'room',
            'collectedBy',
            'receiptNumber',
            'paymentType',
            'invoice',
            'occupants'
        ));
    }
}
