<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of invoices with summary statistics.
     */

public function index(Request $request): View
{
    // Auto-apply penalties if enabled
    $this->autoApplyPenaltiesIfEnabled();

    $activeStatus = $request->input('status', 'all');
    $searchTerm = trim((string) $request->input('search', ''));
    $perPage = (int) $request->input('per_page', 5);
    $bookingId = $request->get('booking_id');

    // Sorting
    $sortBy = $request->input('sort_by', 'date_generated');
    $sortDir = $request->input('sort_dir', 'desc');

    // Validate sort direction
    if (!in_array($sortDir, ['asc', 'desc'], true)) {
        $sortDir = 'desc';
    }

    // Date filtering
    $dateFilter = $request->input('date_filter', 'all');
    $dateFrom = $request->input('date_from');
    $dateTo = $request->input('date_to');

    if (! in_array($perPage, [5, 10, 15, 20], true)) {
        $perPage = 5;
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
        ->withSum('payments as payments_sum', 'amount');

    // Apply sorting
    $allowedSortColumns = ['invoice_id', 'date_generated', 'total_due', 'due_date', 'penalty_amount', 'rent_subtotal'];
    if (in_array($sortBy, $allowedSortColumns, true)) {
        $invoiceQuery->orderBy($sortBy, $sortDir);
        // Add secondary sort by invoice_id for consistency
        if ($sortBy !== 'invoice_id') {
            $invoiceQuery->orderBy('invoice_id', $sortDir);
        }
    } else {
        // Default sorting
        $invoiceQuery->orderByDesc('date_generated')->orderByDesc('invoice_id');
    }

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

    // Date filtering
    if ($dateFilter !== 'all') {
        $now = now();
        switch ($dateFilter) {
            case 'today':
                $invoiceQuery->whereDate('date_generated', $now->toDateString());
                break;
            case 'this_week':
                $invoiceQuery->whereBetween('date_generated', [
                    $now->startOfWeek()->toDateString(),
                    $now->endOfWeek()->toDateString()
                ]);
                break;
            case 'this_month':
                $invoiceQuery->whereYear('date_generated', $now->year)
                    ->whereMonth('date_generated', $now->month);
                break;
            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                $invoiceQuery->whereYear('date_generated', $lastMonth->year)
                    ->whereMonth('date_generated', $lastMonth->month);
                break;
            case 'this_year':
                $invoiceQuery->whereYear('date_generated', $now->year);
                break;
            case 'custom':
                if ($dateFrom) {
                    $invoiceQuery->whereDate('date_generated', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $invoiceQuery->whereDate('date_generated', '<=', $dateTo);
                }
                break;
        }
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
            // Electricity invoices: rent_subtotal = 0 and utility_electricity_fee > 0
            $isElectricityOrSecurityDeposit = ($invoice->rent_subtotal == 0 && $invoice->utility_electricity_fee > 0);

            // Get invoice position for this booking (security deposit is typically 2nd invoice)
            // We'll check if it's one of the first 2 invoices AND if amount matches security deposit ₱5000
            $invoiceCount = Invoice::where('booking_id', $invoice->booking_id)
                ->where('invoice_id', '<=', $invoice->invoice_id)
                ->count();

            $isEarlyInvoice = $invoiceCount <= 2; // First or second invoice for the booking

            // Security deposit is ONLY the 2nd invoice for monthly bookings when amount is exactly ₱5,000.00
            $isSecurityDepositAmount = abs($invoice->utility_electricity_fee - 5000.00) < 0.01;
            $isSecurityDepositInvoice = $isElectricityOrSecurityDeposit && $isEarlyInvoice && $isSecurityDepositAmount;

            if ($isSecurityDepositInvoice) {
                $billingLabel = 'Security Deposit';
                $invoiceType = 'Security Deposit';
            } elseif ($isElectricityOrSecurityDeposit) {
                // Electricity-only invoice (not security deposit) = Electricity Invoice
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

    $financialSnapshot = $this->buildFinancialSnapshot($dateFilter, $dateFrom, $dateTo);
    $highlightInvoiceId = $request->input('highlight');

    return view('contents.invoices', [
        'invoices' => $invoices,
        'statusCounts' => $statusCounts,
        'financialSnapshot' => $financialSnapshot,
        'activeStatus' => $activeStatus,
        'searchTerm' => $searchTerm,
        'perPage' => $perPage,
        'highlightInvoiceId' => $highlightInvoiceId,
        'dateFilter' => $dateFilter,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'sortBy' => $sortBy,
        'sortDir' => $sortDir,
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
     * Accepts optional date filter parameters to filter the financial snapshot.
     */
    protected function buildFinancialSnapshot(string $dateFilter = 'all', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = Invoice::query()
            ->with(['booking'])
            ->withSum('payments as payments_sum', 'amount');

        // Apply date filtering
        if ($dateFilter !== 'all') {
            $now = now();
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('date_generated', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('date_generated', [
                        $now->copy()->startOfWeek()->toDateString(),
                        $now->copy()->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereYear('date_generated', $now->year)
                        ->whereMonth('date_generated', $now->month);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereYear('date_generated', $lastMonth->year)
                        ->whereMonth('date_generated', $lastMonth->month);
                    break;
                case 'this_year':
                    $query->whereYear('date_generated', $now->year);
                    break;
                case 'custom':
                    if ($dateFrom) {
                        $query->whereDate('date_generated', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $query->whereDate('date_generated', '<=', $dateTo);
                    }
                    break;
            }
        }

        $invoiceCollection = $query->get();

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

    /**
     * Auto-apply penalties if the setting is enabled
     */
    private function autoApplyPenaltiesIfEnabled(): void
    {
        if (!Setting::get('auto_apply_penalties', false)) {
            return;
        }

        $overdueInvoices = Invoice::where('is_paid', false)
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $newPenalty = $invoice->calculatePenalty();
            $currentPenalty = $invoice->penalty_amount ?? 0;

            if ($newPenalty > $currentPenalty) {
                $invoice->penalty_amount = $newPenalty;
                $invoice->days_overdue = $invoice->calculated_days_overdue;
                $invoice->save();
            }
        }
    }

    /**
     * Get all payments for the Payment History modal.
     * Returns JSON for AJAX loading.
     */
    public function getAllPayments(Request $request): JsonResponse
    {
        $query = Payment::with([
            'booking.tenant',
            'booking.room',
            'invoice',
            'collectedBy'
        ]);

        // Date filtering (supports same options as Invoices page)
        $dateFilter = $request->input('date_filter', 'all');
        if ($dateFilter !== 'all') {
            $now = now();
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        $now->copy()->startOfWeek(),
                        $now->copy()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereYear('created_at', $now->year)
                        ->whereMonth('created_at', $now->month);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereYear('created_at', $lastMonth->year)
                        ->whereMonth('created_at', $lastMonth->month);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $now->year);
                    break;
                case 'custom':
                    if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->input('date_from'));
                    }
                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->input('date_to'));
                    }
                    break;
            }
        }

        // Tenant search
        if ($request->filled('tenant_search')) {
            $search = $request->input('tenant_search');
            $query->whereHas('booking.tenant', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%");
            });
        }

        // Collected by filter
        if ($request->filled('collected_by')) {
            $query->where('collected_by_user_id', $request->input('collected_by'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['payment_id', 'created_at', 'date_received', 'amount', 'payment_method'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('created_at');
        }

        // Paginate
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 50], true)) {
            $perPage = 10;
        }

        $payments = $query->paginate($perPage);

        // Transform payments for response
        $paymentsData = $payments->through(function ($payment) {
            return [
                'payment_id' => $payment->payment_id,
                'created_at' => $payment->created_at->format('M d, Y h:i A'),
                'date_received' => $payment->date_received->format('M d, Y'),
                'tenant_name' => optional($payment->booking->tenant)->full_name ?? '—',
                'room_number' => optional($payment->booking->room)->room_num ?? '—',
                'amount' => number_format($payment->amount, 2),
                'payment_method' => $payment->payment_method,
                'payment_type' => $payment->payment_type,
                'collected_by' => optional($payment->collectedBy)->full_name ?? '—',
                'invoice_id' => $payment->invoice_id,
                'receipt_url' => route('payments.receipt', $payment->payment_id),
            ];
        });

        // Get list of users for the "Collected By" filter dropdown
        $users = User::orderBy('last_name')->orderBy('first_name')->get()->map(function ($user) {
            return [
                'id' => $user->user_id,
                'name' => $user->full_name,
            ];
        });

        return response()->json([
            'payments' => $paymentsData,
            'users' => $users,
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
            ],
        ]);
    }
}
