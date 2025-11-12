<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
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
            'invoice_id' => ['nullable', 'exists:invoices,invoice_id'],
            'payment_type' => ['required', 'in:Rent/Utility,Security Deposit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:Cash,GCash'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'date_received' => ['required', 'date'],
        ], [
            'booking_id.required' => 'Booking ID is required.',
            'booking_id.exists' => 'The selected booking does not exist.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Payment type must be either "Rent/Utility" or "Security Deposit".',
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

        // Validate invoice_id is required for Rent/Utility payments
        if ($validated['payment_type'] === 'Rent/Utility' && empty($validated['invoice_id'])) {
            throw ValidationException::withMessages([
                'invoice_id' => 'Invoice ID is required for Rent/Utility payments.',
            ]);
        }

        // Validate invoice_id must be null for Security Deposit payments
        if ($validated['payment_type'] === 'Security Deposit' && !empty($validated['invoice_id'])) {
            throw ValidationException::withMessages([
                'invoice_id' => 'Invoice ID must be empty for Security Deposit payments.',
            ]);
        }

        // Use database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Create and save the payment
            $payment = Payment::create([
                'booking_id' => $validated['booking_id'],
                'invoice_id' => $validated['invoice_id'] ?? null,
                'collected_by_user_id' => auth()->user()->user_id,
                'payment_type' => $validated['payment_type'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'date_received' => $validated['date_received'],
            ]);

            // Get the booking
            $booking = Booking::findOrFail($validated['booking_id']);
            $invoice = null;
            $invoicePaid = false;

            // CRITICAL: Update the invoice if this is a Rent/Utility payment
            if ($validated['payment_type'] === 'Rent/Utility' && $validated['invoice_id']) {
                $invoice = Invoice::findOrFail($validated['invoice_id']);

                // Calculate total amount paid for this invoice
                $totalPaid = Payment::where('invoice_id', $invoice->invoice_id)
                    ->sum('amount');

                // Update invoice status if fully paid
                if ($totalPaid >= $invoice->total_due) {
                    $invoice->is_paid = true;
                    $invoice->save();
                    $invoicePaid = true;
                }
            }

            // CRITICAL: Update booking status to 'Active' if both conditions are met:
            // 1. Security deposit is paid (payment_type = 'Security Deposit' for this booking)
            // 2. First invoice (advance rent) is paid
            if ($booking->status === 'Pending Payment') {
                // Check if security deposit is paid
                $securityDepositPaid = Payment::where('booking_id', $booking->booking_id)
                    ->where('payment_type', 'Security Deposit')
                    ->exists();

                // Check if first invoice is paid
                // The first invoice is the one with the earliest date_generated for this booking
                $firstInvoice = Invoice::where('booking_id', $booking->booking_id)
                    ->orderBy('date_generated', 'asc')
                    ->orderBy('invoice_id', 'asc')
                    ->first();

                $firstInvoicePaid = false;
                if ($firstInvoice) {
                    $firstInvoicePaid = $firstInvoice->is_paid;
                }

                // If both security deposit and first invoice are paid, activate the booking
                if ($securityDepositPaid && $firstInvoicePaid) {
                    $booking->status = 'Active';
                    $booking->save();
                }
            }

            DB::commit();

            // Determine success message based on what was updated
            $successMessage = 'Payment recorded successfully.';
            if ($invoicePaid) {
                $successMessage .= ' Invoice marked as paid.';
            }
            if ($booking->status === 'Active') {
                $successMessage .= ' Booking status updated to Active.';
            }

            // Redirect back to invoices page with success message
            return redirect()->route('invoices')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('invoices')
                ->withInput()
                ->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
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
}
