<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with summary statistics.
     */
    public function index(Request $request): View
    {
        $activeStatus = $request->input('status', 'all');
        $searchTerm = trim((string) $request->input('search', ''));
        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        // Get first invoice ID per booking (database-agnostic approach)
        // Efficiently find the first invoice (by date, then ID) for each booking
        // This approach works across all database drivers including SQL Server
        $firstInvoiceIds = Invoice::query()
            ->select('booking_id', 'invoice_id', 'date_generated')
            ->get()
            ->groupBy('booking_id')
            ->map(function ($invoices) {
                // Sort by date_generated ASC, then invoice_id ASC, and return first
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
                'booking.room',
                'booking.rate',
            ])
            ->withSum('payments as payments_sum', 'amount')
            ->orderByDesc('date_generated')
            ->orderByDesc('invoice_id');

        if ($activeStatus === 'paid') {
            $invoiceQuery->where('is_paid', true);
        } elseif ($activeStatus === 'pending') {
            $invoiceQuery->where('is_paid', false);
        }

        if ($searchTerm !== '') {
            $invoiceQuery->where(function ($query) use ($searchTerm) {
                $query->where('invoice_id', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('booking.tenant', function ($tenantQuery) use ($searchTerm) {
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

                // Check if this is the first invoice for the booking
                $isFirstInvoice = isset($firstInvoiceIds[$invoice->invoice_id]);

                $invoice->setAttribute('total_collected', $totalCollected);
                $invoice->setAttribute('remaining_balance', $remainingBalance);
                $invoice->setAttribute(
                    'status_label',
                    $remainingBalance <= 0
                        ? 'Paid'
                        : ($paymentsSum > 0 ? 'Partial' : 'Pending')
                );

                $invoice->setAttribute(
                    'tenant_name',
                    optional($invoice->booking?->tenant)->full_name ?? '—'
                );

                $invoice->setAttribute(
                    'room_number',
                    optional($invoice->booking?->room)->room_num ?? '—'
                );

                $invoice->setAttribute(
                    'billing_label',
                    $isFirstInvoice ? 'Advance Rent' : 'Monthly Rent'
                );

                $invoice->setAttribute(
                    'billing_period',
                    optional($invoice->date_generated)->format('F Y') ?? '—'
                );

                return $invoice;
            });

        $statusCounts = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('is_paid', true)->count(),
            'pending' => Invoice::where('is_paid', false)->count(),
        ];

        $financialSnapshot = $this->buildFinancialSnapshot();

        return view('contents.invoices', [
            'invoices' => $invoices,
            'statusCounts' => $statusCounts,
            'financialSnapshot' => $financialSnapshot,
            'activeStatus' => $activeStatus,
            'searchTerm' => $searchTerm,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Build a collection of financial metrics for the invoices dashboard.
     */
    protected function buildFinancialSnapshot(): array
    {
        $invoiceCollection = Invoice::query()
            ->withSum('payments as payments_sum', 'amount')
            ->get();

        $billed = $invoiceCollection->sum('total_due');

        $collected = $invoiceCollection->sum(function (Invoice $invoice) {
            $sum = (float) ($invoice->payments_sum ?? 0);

            return min((float) $invoice->total_due, $sum);
        });

        $outstanding = $invoiceCollection->sum(function (Invoice $invoice) {
            $sum = (float) ($invoice->payments_sum ?? 0);

            return max(0, (float) $invoice->total_due - $sum);
        });

        $pendingCount = $invoiceCollection->filter(function (Invoice $invoice) {
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
}