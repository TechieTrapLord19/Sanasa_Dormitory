<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of invoices with summary statistics.
     */

public function index(Request $request): View
{
    $activeStatus = $request->input('status', 'all');
    $searchTerm = trim((string) $request->input('search', ''));
    $perPage = (int) $request->input('per_page', 10);
    $bookingId = $request->get('booking_id');

    if (! in_array($perPage, [10, 25, 50], true)) {
        $perPage = 10;
    }

    // Get first invoice ID per booking
    $firstInvoiceIds = Invoice::query()
        ->select('booking_id', 'invoice_id', 'date_generated')
        ->get()
        ->groupBy('booking_id')
        ->map(function ($invoices) {
            $first = $invoices->sortBy([
                ['date_generated', 'asc'],
                ['invoice_id', 'asc'],
            ])->first();
            return $first ? $first->invoice_id : null;
        })
        ->filter()
        ->flip()
        ->toArray();

    $invoiceQuery = Invoice::query()
        ->with([
            'booking.tenant',
            'booking.secondaryTenant',
            'booking.room',
            'booking.rate',
            'booking', // Ensure booking is loaded for status check
            'invoiceUtilities', // Load utilities dynamically
            'payments' => function($query) {
                $query->orderByDesc('created_at');
            }
        ])
        ->withSum('payments as payments_sum', 'amount')
        ->orderByDesc('date_generated')
        ->orderByDesc('invoice_id');

    // Filter by booking if booking_id provided
    if ($bookingId) {
        $invoiceQuery->where('booking_id', $bookingId);
    }

    // Filter by status - based on actual payment amounts, not is_paid flag
    if ($activeStatus === 'paid') {
        // Paid: sum of payments >= total_due AND not canceled
        $invoiceQuery->whereHas('booking', function ($query) {
                $query->where('status', '!=', 'Canceled');
            })
            ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) >= invoices.total_due');
    } elseif ($activeStatus === 'pending') {
        // Pending: no payments (sum = 0), and booking not canceled
        $invoiceQuery->whereHas('booking', function ($query) {
                $query->where('status', '!=', 'Canceled');
            })
            ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) = 0');
    } elseif ($activeStatus === 'partial') {
        // Partial: has payments (sum > 0) but less than total_due, and booking not canceled
        $invoiceQuery->whereHas('booking', function ($query) {
                $query->where('status', '!=', 'Canceled');
            })
            ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) > 0')
            ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) < invoices.total_due');
    } elseif ($activeStatus === 'cancelled') {
        // Cancelled: booking is canceled
        $invoiceQuery->whereHas('booking', function ($query) {
            $query->where('status', 'Canceled');
        });
    }

    if ($searchTerm !== '') {
        $invoiceQuery->where(function ($query) use ($searchTerm) {
                $query->where('invoice_id', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('booking.tenant', function ($tenantQuery) use ($searchTerm) {
                    $tenantQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('booking.secondaryTenant', function ($tenantQuery) use ($searchTerm) {
                    $tenantQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('booking.room', function ($roomQuery) use ($searchTerm) {
                    $roomQuery->where('room_num', 'like', '%' . $searchTerm . '%');
                });
        });
    }

    $invoices = $invoiceQuery
        ->paginate($perPage)
        ->withQueryString()
        ->through(function (Invoice $invoice) use ($firstInvoiceIds) {
            $paymentsSum = (float) ($invoice->payments_sum ?? 0);
            $totalCollected = min((float) $invoice->total_due, $paymentsSum);
            $remainingBalance = max(0, (float) $invoice->total_due - $totalCollected);
            $isFirstInvoice = isset($firstInvoiceIds[$invoice->invoice_id]);

            // Check if booking is canceled
            $isCanceled = optional($invoice->booking)->status === 'Canceled';

            $invoice->setAttribute('total_collected', $totalCollected);
            $invoice->setAttribute('remaining_balance', $remainingBalance);
            $invoice->setAttribute(
                'status_label',
                $isCanceled
                    ? 'Canceled'
                    : ($remainingBalance <= 0
                        ? 'Paid'
                        : ($paymentsSum > 0 ? 'Partial' : 'Pending'))
            );
            $invoice->setAttribute('tenant_name', $invoice->booking ? $invoice->booking->tenant_summary : '—');
            $invoice->setAttribute('room_number', optional($invoice->booking?->room)->room_num ?? '—');

            // Determine invoice type and billing label
            $durationType = optional($invoice->booking?->rate)->duration_type ?? 'Monthly';

            // Check if this is a security deposit invoice
            // Security deposit invoices:
            // 1. Have only electricity fee (no rent, no utilities from invoice_utilities)
            // 2. Are one of the first 2 invoices for the booking (created during booking creation)
            // 3. Typically have a fixed amount (MONTHLY_SECURITY_DEPOSIT = 5000.00)
            // Use loaded relationship (property) instead of method call to avoid N+1 queries
            $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
            $hasOnlyElectricityFee = ($invoice->rent_subtotal == 0 &&
                                     !$hasUtilities &&
                                     $invoice->utility_electricity_fee > 0);

            // Get invoice position for this booking (security deposit is typically 2nd invoice)
            // We'll check if it's one of the first 2 invoices OR if amount matches security deposit
            $invoiceCount = Invoice::where('booking_id', $invoice->booking_id)
                ->where('invoice_id', '<=', $invoice->invoice_id)
                ->count();

            $isEarlyInvoice = $invoiceCount <= 2; // First or second invoice for the booking

            // Security deposit is typically the 2nd invoice for monthly bookings, or if amount matches security deposit (₱5,000.00)
            $isSecurityDepositInvoice = $hasOnlyElectricityFee &&
                                       ($isEarlyInvoice || abs($invoice->utility_electricity_fee - 5000.00) < 0.01);

            if ($isSecurityDepositInvoice) {
                $billingLabel = 'Security Deposit';
                $invoiceType = 'Security Deposit';
            } elseif ($hasOnlyElectricityFee) {
                // Electricity-only invoice created later = Electricity Invoice
                $billingLabel = 'Electricity';
                $invoiceType = 'Electricity';
            } else {
                // Regular rent/utility invoice
                if ($isFirstInvoice) {
                    $billingLabel = match($durationType) {
                        'Daily' => 'Daily Rate',
                        'Weekly' => 'Weekly Rate',
                        'Monthly' => 'Monthly Rent + Utilities',
                        default => 'Monthly Rent + Utilities'
                    };
                } else {
                    $billingLabel = match($durationType) {
                        'Daily' => 'Daily Rate',
                        'Weekly' => 'Weekly Rate',
                        'Monthly' => 'Monthly Rent + Utilities + Electricity',
                        default => 'Monthly Rent + Utilities + Electricity'
                    };
                }
                // TYPE should be simpler - just the category
                $invoiceType = match($durationType) {
                    'Daily' => 'Daily Rate',
                    'Weekly' => 'Weekly Rate',
                    'Monthly' => 'Monthly Rent + Utilities',
                    default => 'Monthly Rent + Utilities'
                };
            }

            $invoice->setAttribute('billing_label', $billingLabel);
            $invoice->setAttribute('invoice_type', $invoiceType);
            $invoice->setAttribute('billing_period', optional($invoice->date_generated)->format('F Y') ?? '—');

            return $invoice;
        });

    // Calculate status counts accurately
    $allInvoices = Invoice::with(['booking', 'payments'])
        ->withSum('payments as payments_sum', 'amount')
        ->get();

    $statusCounts = [
        'total' => $allInvoices->count(),
        'paid' => $allInvoices->filter(function ($invoice) {
            $paymentsSum = (float) ($invoice->payments_sum ?? 0);
            $totalDue = (float) $invoice->total_due;
            $isCanceled = optional($invoice->booking)->status === 'Canceled';
            return !$isCanceled && $paymentsSum >= $totalDue && $totalDue > 0;
        })->count(),
        'pending' => $allInvoices->filter(function ($invoice) {
            $paymentsSum = (float) ($invoice->payments_sum ?? 0);
            $totalDue = (float) $invoice->total_due;
            $isCanceled = optional($invoice->booking)->status === 'Canceled';
            return !$isCanceled && $paymentsSum < $totalDue && $paymentsSum == 0;
        })->count(),
        'partial' => $allInvoices->filter(function ($invoice) {
            $paymentsSum = (float) ($invoice->payments_sum ?? 0);
            $totalDue = (float) $invoice->total_due;
            $isCanceled = optional($invoice->booking)->status === 'Canceled';
            return !$isCanceled && $paymentsSum > 0 && $paymentsSum < $totalDue;
        })->count(),
        'cancelled' => $allInvoices->filter(function ($invoice) {
            return optional($invoice->booking)->status === 'Canceled';
        })->count(),
    ];

    $financialSnapshot = $this->buildFinancialSnapshot();
    $highlightInvoiceId = $request->input('highlight');

    return view('contents.invoices', [
        'invoices' => $invoices,
        'statusCounts' => $statusCounts,
        'financialSnapshot' => $financialSnapshot,
        'activeStatus' => $activeStatus,
        'searchTerm' => $searchTerm,
        'perPage' => $perPage,
        'highlightInvoiceId' => $highlightInvoiceId,
    ]);
}

    /**
     * Display a specific invoice (redirects to booking details page).
     */
    public function show($id)
    {
        $invoice = Invoice::with(['booking'])->findOrFail($id);

        // Redirect to the invoices page with the invoice highlighted
        return redirect()->route('invoices', ['highlight' => $id])
            ->with('highlight_invoice', $id);
    }

    /**
     * Build a collection of financial metrics for the invoices dashboard.
     */
    protected function buildFinancialSnapshot(): array
    {
        $invoiceCollection = Invoice::query()
            ->with(['booking'])
            ->withSum('payments as payments_sum', 'amount')
            ->get();

        // Filter out invoices from canceled bookings
        $activeInvoices = $invoiceCollection->filter(function (Invoice $invoice) {
            return optional($invoice->booking)->status !== 'Canceled';
        });

        $billed = $activeInvoices->sum('total_due');

        $collected = $activeInvoices->sum(function (Invoice $invoice) {
            $sum = (float) ($invoice->payments_sum ?? 0);

            return min((float) $invoice->total_due, $sum);
        });

        $outstanding = $activeInvoices->sum(function (Invoice $invoice) {
            $sum = (float) ($invoice->payments_sum ?? 0);

            return max(0, (float) $invoice->total_due - $sum);
        });

        $pendingCount = $activeInvoices->filter(function (Invoice $invoice) {
            $sum = (float) ($invoice->payments_sum ?? 0);

            return $sum < (float) $invoice->total_due;
        })->count();

        return [
            'billed' => $billed,
            'collected' => $collected,
            'outstanding' => $outstanding,
            'pending_count' => $pendingCount,
        ];
    }

    /**
     * Apply penalty to an overdue invoice
     */
    public function applyPenalty($id)
    {
        $invoice = Invoice::with(['booking.tenant'])->findOrFail($id);

        if (!$invoice->is_overdue) {
            return redirect()->back()->with('error', 'This invoice is not overdue.');
        }

        $previousPenalty = $invoice->penalty_amount;
        $invoice->applyPenalty();
        $newPenalty = $invoice->penalty_amount;

        if ($newPenalty > $previousPenalty) {
            $tenantName = $invoice->booking?->tenant?->full_name ?? 'Unknown';
            $this->logActivity(
                'Invoice',
                "Applied late penalty of P" . number_format($newPenalty - $previousPenalty, 2) .
                " to Invoice #{$id} for {$tenantName}"
            );

            return redirect()->back()->with('success',
                'Penalty of P' . number_format($newPenalty - $previousPenalty, 2) . ' applied successfully.');
        }

        return redirect()->back()->with('info', 'No additional penalty to apply.');
    }

    /**
     * Apply penalties to all overdue invoices
     */
    public function applyAllPenalties()
    {
        $overdueInvoices = Invoice::with(['booking.tenant'])
            ->whereHas('booking', function ($query) {
                $query->where('status', '!=', 'Canceled');
            })
            ->where('is_paid', false)
            ->get()
            ->filter(function ($invoice) {
                return $invoice->is_overdue;
            });

        $appliedCount = 0;
        $totalPenalty = 0;

        foreach ($overdueInvoices as $invoice) {
            $previousPenalty = $invoice->penalty_amount ?? 0;
            $invoice->applyPenalty();
            $newPenalty = $invoice->penalty_amount ?? 0;

            if ($newPenalty > $previousPenalty) {
                $appliedCount++;
                $totalPenalty += ($newPenalty - $previousPenalty);
            }
        }

        if ($appliedCount > 0) {
            $this->logActivity(
                'Invoice',
                "Applied penalties to {$appliedCount} overdue invoices. Total: P" . number_format($totalPenalty, 2)
            );

            return redirect()->back()->with('success',
                "Penalties applied to {$appliedCount} invoices. Total: P" . number_format($totalPenalty, 2));
        }

        return redirect()->back()->with('info', 'No overdue invoices require penalties.');
    }
}
