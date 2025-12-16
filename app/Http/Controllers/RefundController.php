<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class RefundController extends Controller
{
    use LogsActivity;

    /**
     * Store a newly created refund
     */
    public function store(Request $request, string $bookingId)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,payment_id',
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_method' => 'required|in:Cash,GCash',
            'reference_number' => 'required_if:refund_method,GCash|nullable|string|max:255',
            'refund_date' => 'required|date',
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $booking = Booking::findOrFail($bookingId);

        // Check if booking can be refunded
        if (!$booking->canBeRefunded()) {
            return back()->withErrors(['error' => 'This booking cannot be refunded. Only cancelled bookings that haven\'t been checked in can be refunded.']);
        }

        $payment = Payment::findOrFail($request->payment_id);

        // Check if payment belongs to this booking
        if ($payment->booking_id != $booking->booking_id) {
            return back()->withErrors(['error' => 'Payment does not belong to this booking.']);
        }

        // Check if refund amount is valid
        $remainingRefundable = $payment->remaining_refundable_amount;
        if ($request->refund_amount > $remainingRefundable) {
            return back()->withErrors(['error' => "Refund amount cannot exceed remaining refundable amount (₱" . number_format($remainingRefundable, 2) . ")."]);
        }

        DB::beginTransaction();
        try {
            // Get the invoice for this payment (if exists)
            $invoice = $payment->invoice;

            // Create refund record
            $refund = Refund::create([
                'payment_id' => $payment->payment_id,
                'refunded_by_user_id' => Auth::id(),
                'refund_amount' => $request->refund_amount,
                'refund_method' => $request->refund_method,
                'reference_number' => $request->reference_number,
                'refund_date' => $request->refund_date,
                'cancellation_reason' => $request->cancellation_reason,
                'status' => 'Processed',
            ]);

            // Check if this is a security deposit payment and update the security deposit record
            if ($payment->payment_type === 'Security Deposit' && $booking->securityDeposit) {
                $booking->securityDeposit->processRefund($request->refund_amount, Auth::id());
            }

            // Check if payment is fully refunded
            $payment->refresh();
            if ($payment->total_refunded >= $payment->amount) {
                // If invoice exists and is fully refunded, mark as unpaid
                if ($invoice) {
                    $invoice->refresh();
                    $totalRefundedForInvoice = $invoice->total_refunded;
                    if ($totalRefundedForInvoice >= $invoice->total_due) {
                        $invoice->update(['is_paid' => false]);
                    }
                }
            }

            DB::commit();

            $booking->load('tenant', 'room');
            $tenant = $booking->tenant;
            $room = $booking->room;
            $this->logActivity(
                'Processed Refund',
                "Processed refund of ₱" . number_format($refund->refund_amount, 2) . " via {$refund->refund_method}" .
                ($refund->reference_number ? " (Ref: {$refund->reference_number})" : "") .
                " for booking #{$booking->booking_id} - {$tenant->full_name} in room {$room->room_num}. Reason: {$refund->cancellation_reason}",
                $refund
            );

            return back()->with('success', 'Refund processed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process refund: ' . $e->getMessage()]);
        }
    }
}

