<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
            'invoice_id' => ['required', 'exists:invoices,invoice_id'],
            'payment_type' => ['required', 'in:Rent/Utility'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:Cash,GCash'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'date_received' => ['required', 'date'],
        ], [
            'booking_id.required' => 'Booking ID is required.',
            'booking_id.exists' => 'The selected booking does not exist.',
            'invoice_id.required' => 'Invoice ID is required.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Payment type must be "Rent/Utility".',
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

        // Use database transaction to ensure data consistency
        DB::beginTransaction();

            // Create and save the payment
            $payment = Payment::create([
                'booking_id' => $validated['booking_id'],
                'invoice_id' => $validated['invoice_id'] ?? null,
                'collected_by_user_id' => Auth::id(),
                'payment_type' => $validated['payment_type'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'date_received' => $validated['date_received'],
            ]);
        try {
            // Get the booking
            $booking = Booking::findOrFail($validated['booking_id']);
            $invoice = null;
            $invoicePaid = false;

            // CRITICAL: Update the invoice if this is a Rent/Utility payment
            if ($validated['payment_type'] === 'Rent/Utility' && $validated['invoice_id']) {
                $invoice = Invoice::with('booking')->findOrFail($validated['invoice_id']);

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
            }

            // Note: Check-in is now manual only - no automatic status change
            // Booking status remains as 'Pending Payment' or 'Reserved' until manually checked in
            // Partial payments are allowed for check-in (handled in check-in process)

            DB::commit();

            // Determine success message based on what was updated
            $successMessage = 'Payment recorded successfully.';
            if ($invoicePaid) {
                $successMessage .= ' Invoice marked as paid.';
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