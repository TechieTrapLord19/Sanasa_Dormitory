<?php

namespace App\Http\Controllers;

use App\Models\SecurityDeposit;
use App\Models\Booking;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\Invoice;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecurityDepositController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of all security deposits
     */
    public function index(Request $request)
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = SecurityDeposit::with(['booking.tenant', 'booking.room', 'processedBy']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('booking.tenant', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhereHas('booking.room', function ($q) use ($search) {
                $q->where('room_num', 'like', "%{$search}%");
            });
        }

        $deposits = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Summary statistics
        $totalHeld = SecurityDeposit::where('status', SecurityDeposit::STATUS_HELD)->sum('amount_paid');
        $totalPending = SecurityDeposit::where('status', SecurityDeposit::STATUS_PENDING)->count();
        $totalRefunded = SecurityDeposit::whereIn('status', [SecurityDeposit::STATUS_REFUNDED, SecurityDeposit::STATUS_PARTIALLY_REFUNDED])
            ->sum('amount_refunded');

        return view('contents.security-deposits-index', compact(
            'deposits',
            'status',
            'search',
            'perPage',
            'totalHeld',
            'totalPending',
            'totalRefunded'
        ));
    }

    /**
     * Show security deposit details for a specific booking
     */
    public function show(SecurityDeposit $securityDeposit)
    {
        $securityDeposit->load(['booking.tenant', 'booking.secondaryTenant', 'booking.room', 'invoice', 'processedBy']);

        // Get outstanding invoices for this booking (for unpaid rent/utilities deductions)
        $outstandingInvoices = Invoice::where('booking_id', $securityDeposit->booking_id)
            ->where('is_paid', false)
            ->get()
            ->filter(function ($invoice) {
                return $invoice->remaining_balance > 0;
            });

        return view('contents.security-deposit-show', compact('securityDeposit', 'outstandingInvoices'));
    }

    /**
     * Apply a deduction to the security deposit
     */
    public function applyDeduction(Request $request, SecurityDeposit $securityDeposit)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $securityDeposit->calculateRefundable(),
            'category' => 'required|in:' . implode(',', SecurityDeposit::getDeductionCategories()),
            'description' => 'nullable|string|max:500',
            'invoice_id' => 'nullable|exists:invoices,invoice_id',
        ]);

        if ($securityDeposit->status === SecurityDeposit::STATUS_REFUNDED ||
            $securityDeposit->status === SecurityDeposit::STATUS_FORFEITED) {
            return back()->with('error', 'Cannot apply deduction to a closed deposit.');
        }

        DB::beginTransaction();
        try {
            $category = $validated['category'];
            $amount = $validated['amount'];
            $description = $validated['description'] ?? null;

            // Apply the deduction to the security deposit record
            $result = $securityDeposit->applyDeduction($amount, $category, $description);

            if (!$result) {
                throw new \Exception('Failed to apply deduction. Amount exceeds available balance.');
            }

            // Categories that apply to invoices (outstanding balances)
            $invoiceCategories = ['Unpaid Rent/Utilities', 'Unpaid Electricity'];

            // Handle based on category
            if (in_array($category, $invoiceCategories)) {
                // Apply to outstanding invoice if specified
                if (!empty($validated['invoice_id'])) {
                    $invoice = Invoice::find($validated['invoice_id']);

                    if ($invoice && $invoice->booking_id === $securityDeposit->booking_id) {
                        // Create a payment record to apply to the invoice
                        Payment::create([
                            'booking_id' => $securityDeposit->booking_id,
                            'invoice_id' => $invoice->invoice_id,
                            'collected_by_user_id' => Auth::id(),
                            'payment_type' => 'Deposit Deduction',
                            'amount' => $amount,
                            'payment_method' => 'Security Deposit',
                            'reference_number' => 'SD-' . $securityDeposit->security_deposit_id . '-' . now()->format('YmdHis'),
                            'date_received' => now(),
                        ]);

                        // Update invoice paid status if fully paid
                        if ($invoice->remaining_balance <= $amount) {
                            $invoice->update(['is_paid' => true]);
                        }
                    }
                }
            } else {
                // For Damages, Cleaning Fee, Other - create a revenue payment
                // Find or get the latest invoice for this booking to attach the payment
                $latestInvoice = Invoice::where('booking_id', $securityDeposit->booking_id)
                    ->orderBy('date_generated', 'desc')
                    ->first();

                Payment::create([
                    'booking_id' => $securityDeposit->booking_id,
                    'invoice_id' => $latestInvoice?->invoice_id,
                    'collected_by_user_id' => Auth::id(),
                    'payment_type' => 'Deposit Deduction',
                    'amount' => $amount,
                    'payment_method' => 'Security Deposit',
                    'reference_number' => 'SD-' . $securityDeposit->security_deposit_id . '-' . now()->format('YmdHis'),
                    'date_received' => now(),
                ]);
            }

            DB::commit();

            // Log activity
            $booking = $securityDeposit->booking;
            $tenantName = $booking->tenant->first_name . ' ' . $booking->tenant->last_name;
            $this->logActivity(
                'Security Deposit Deduction',
                "Applied ₱" . number_format($amount, 2) . " deduction ({$category}) from security deposit for {$tenantName} in Room {$booking->room->room_num}",
                $securityDeposit
            );

            $successMessage = 'Deduction of ₱' . number_format($amount, 2) . ' applied successfully.';
            if (in_array($category, $invoiceCategories) && !empty($validated['invoice_id'])) {
                $successMessage .= ' Payment applied to invoice.';
            } else if (!in_array($category, $invoiceCategories)) {
                $successMessage .= ' Recorded as revenue.';
            }

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to apply deduction: ' . $e->getMessage());
        }
    }

    /**
     * Process refund for the security deposit
     */
    public function processRefund(Request $request, SecurityDeposit $securityDeposit)
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $securityDeposit->calculateRefundable(),
            'refund_method' => 'required|in:Cash,Bank Transfer,GCash,Other',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($securityDeposit->status === SecurityDeposit::STATUS_REFUNDED ||
            $securityDeposit->status === SecurityDeposit::STATUS_FORFEITED) {
            return back()->with('error', 'This deposit has already been processed.');
        }

        DB::beginTransaction();
        try {
            // Update notes if provided
            if ($validated['notes']) {
                $securityDeposit->notes = ($securityDeposit->notes ? $securityDeposit->notes . "\n" : '') .
                    "Refund note: " . $validated['notes'];
            }

            // Process the refund
            $result = $securityDeposit->processRefund(
                $validated['refund_amount'],
                Auth::id()
            );

            if (!$result) {
                throw new \Exception('Failed to process refund');
            }

            // Create a refund record if there's an associated payment
            $depositPayment = Payment::where('payment_type', 'Security Deposit')
                ->whereHas('invoice', function ($q) use ($securityDeposit) {
                    $q->where('booking_id', $securityDeposit->booking_id);
                })
                ->first();

            if ($depositPayment && $validated['refund_amount'] > 0) {
                Refund::create([
                    'payment_id' => $depositPayment->payment_id,
                    'refund_amount' => $validated['refund_amount'],
                    'refund_method' => $validated['refund_method'],
                    'refund_date' => now(),
                    'refunded_by_user_id' => Auth::id(),
                    'cancellation_reason' => $validated['notes'] ?? 'Security deposit refund upon checkout',
                    'status' => 'Completed',
                ]);
            }

            DB::commit();

            // Log activity
            $booking = $securityDeposit->booking;
            $tenantName = $booking->tenant->first_name . ' ' . $booking->tenant->last_name;
            $this->logActivity(
                'Security Deposit Refund',
                "Refunded ₱" . number_format($validated['refund_amount'], 2) . " from security deposit to {$tenantName} in Room {$booking->room->room_num} via {$validated['refund_method']}",
                $securityDeposit
            );

            return back()->with('success', 'Refund of ₱' . number_format($validated['refund_amount'], 2) . ' processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Forfeit the entire deposit
     */
    public function forfeit(Request $request, SecurityDeposit $securityDeposit)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($securityDeposit->status === SecurityDeposit::STATUS_REFUNDED ||
            $securityDeposit->status === SecurityDeposit::STATUS_FORFEITED) {
            return back()->with('error', 'This deposit has already been processed.');
        }

        DB::beginTransaction();
        try {
            // Get the refundable amount before forfeiting
            $forfeitedAmount = $securityDeposit->calculateRefundable();

            // Forfeit the deposit
            $result = $securityDeposit->forfeit($validated['reason'], Auth::id());

            if (!$result) {
                throw new \Exception('Failed to forfeit deposit');
            }

            // Record the forfeited amount as revenue
            if ($forfeitedAmount > 0) {
                $latestInvoice = Invoice::where('booking_id', $securityDeposit->booking_id)
                    ->orderBy('date_generated', 'desc')
                    ->first();

                Payment::create([
                    'booking_id' => $securityDeposit->booking_id,
                    'invoice_id' => $latestInvoice?->invoice_id,
                    'collected_by_user_id' => Auth::id(),
                    'payment_type' => 'Deposit Deduction',
                    'amount' => $forfeitedAmount,
                    'payment_method' => 'Security Deposit',
                    'reference_number' => 'SD-FORFEIT-' . $securityDeposit->security_deposit_id . '-' . now()->format('YmdHis'),
                    'date_received' => now(),
                ]);
            }

            DB::commit();

            // Log activity
            $booking = $securityDeposit->booking;
            $tenantName = $booking->tenant->first_name . ' ' . $booking->tenant->last_name;
            $this->logActivity(
                'Security Deposit Forfeited',
                "Forfeited ₱" . number_format($forfeitedAmount, 2) . " security deposit from {$tenantName} in Room {$booking->room->room_num}. Reason: {$validated['reason']}",
                $securityDeposit
            );

            return back()->with('success', 'Deposit of ₱' . number_format($forfeitedAmount, 2) . ' has been forfeited and recorded as revenue.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to forfeit deposit: ' . $e->getMessage());
        }
    }

    /**
     * Roll over deposit to a new booking (for renewals)
     */
    public function rollover(Request $request, SecurityDeposit $securityDeposit)
    {
        $validated = $request->validate([
            'new_booking_id' => 'required|exists:bookings,booking_id',
            'new_required_amount' => 'nullable|numeric|min:0',
        ]);

        // Verify the new booking belongs to the same tenant
        $newBooking = Booking::find($validated['new_booking_id']);
        if ($newBooking->tenant_id !== $securityDeposit->booking->tenant_id) {
            return back()->with('error', 'New booking must belong to the same tenant.');
        }

        // Check if new booking already has a deposit
        if ($newBooking->securityDeposit) {
            return back()->with('error', 'The new booking already has a security deposit record.');
        }

        $newDeposit = $securityDeposit->rolloverToBooking(
            $validated['new_booking_id'],
            $validated['new_required_amount'] ?? null
        );

        // Log activity
        $booking = $securityDeposit->booking;
        $tenantName = $booking->tenant->first_name . ' ' . $booking->tenant->last_name;
        $this->logActivity(
            'Security Deposit Rollover',
            "Rolled over security deposit (₱" . number_format($securityDeposit->calculateRefundable(), 2) . ") for {$tenantName} from Booking #{$securityDeposit->booking_id} to Booking #{$validated['new_booking_id']}",
            $newDeposit
        );

        return redirect()->route('security-deposits.show', $newDeposit)
            ->with('success', 'Deposit rolled over successfully.');
    }

    /**
     * Top up a security deposit after deductions
     */
    public function topUp(Request $request, SecurityDeposit $securityDeposit)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:Cash,GCash,Bank Transfer',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if deposit can be topped up
        if (in_array($securityDeposit->status, ['Forfeited', 'Refunded'])) {
            return back()->with('error', 'Cannot top up a forfeited or refunded deposit.');
        }

        $amount = floatval($validated['amount']);
        $currentBalance = $securityDeposit->calculateRefundable();
        $required = $securityDeposit->amount_required;

        // Cap the top-up at the shortfall (don't allow over-payment)
        $maxTopUp = max(0, $required - $currentBalance);
        if ($amount > $maxTopUp && $maxTopUp > 0) {
            $amount = $maxTopUp;
        }

        DB::beginTransaction();
        try {
            // Create a payment record for the top-up
            $payment = Payment::create([
                'invoice_id' => $securityDeposit->invoice_id,
                'booking_id' => $securityDeposit->booking_id,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'payment_type' => 'Security Deposit',
                'date_received' => now(),
                'collected_by_user_id' => Auth::id(),
            ]);

            // Update the security deposit amount_paid
            $securityDeposit->amount_paid += $amount;

            // Update status back to Held if it was depleted
            if ($securityDeposit->calculateRefundable() > 0 && $securityDeposit->status !== 'Held') {
                $securityDeposit->status = SecurityDeposit::STATUS_HELD;
            }

            // Add note about the top-up
            $notes = $securityDeposit->notes ?? '';
            $notes .= "\n[" . now()->format('Y-m-d H:i') . "] Top-up: +₱" . number_format($amount, 2) . " via " . $validated['payment_method'];
            if (!empty($validated['notes'])) {
                $notes .= " - " . $validated['notes'];
            }
            $securityDeposit->notes = trim($notes);

            $securityDeposit->save();

            // Update the invoice if needed
            if ($securityDeposit->invoice) {
                $totalPaid = $securityDeposit->invoice->payments()->sum('amount');
                if ($totalPaid >= $securityDeposit->invoice->total_due) {
                    $securityDeposit->invoice->update(['is_paid' => true]);
                }
            }

            DB::commit();

            // Log activity
            $booking = $securityDeposit->booking;
            $tenantName = $booking->tenant->first_name . ' ' . $booking->tenant->last_name;
            $this->logActivity(
                'Security Deposit Top Up',
                "Topped up security deposit by ₱" . number_format($amount, 2) . " for {$tenantName} in Room {$booking->room->room_num} via {$validated['payment_method']}. New balance: ₱" . number_format($securityDeposit->calculateRefundable(), 2),
                $securityDeposit
            );

            return redirect()->route('bookings.show', $securityDeposit->booking_id)
                ->with('success', 'Security deposit topped up by ₱' . number_format($amount, 2) . '. New balance: ₱' . number_format($securityDeposit->calculateRefundable(), 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process top-up: ' . $e->getMessage());
        }
    }

    /**
     * Get deposit management section for booking details (AJAX)
     */
    public function getForBooking(Booking $booking)
    {
        $deposit = $booking->securityDeposit;

        if (!$deposit) {
            return response()->json([
                'exists' => false,
                'message' => 'No security deposit record found for this booking.'
            ]);
        }

        return response()->json([
            'exists' => true,
            'deposit' => [
                'id' => $deposit->security_deposit_id,
                'amount_required' => $deposit->amount_required,
                'amount_paid' => $deposit->amount_paid,
                'amount_deducted' => $deposit->amount_deducted,
                'amount_refunded' => $deposit->amount_refunded,
                'status' => $deposit->status,
                'refundable' => $deposit->calculateRefundable(),
                'payment_percentage' => $deposit->getPaymentPercentage(),
                'deductions' => $deposit->getDeductionsArray(),
            ]
        ]);
    }
}