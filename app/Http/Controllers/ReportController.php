<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentHistoryExport;

class ReportController extends Controller
{
    /**
     * Financial summary PDF.
     */
    public function financialSummaryPdf(Request $request)
    {
        $now = Carbon::now('Asia/Manila');
        [$dateFrom, $dateTo] = $this->getDateRange($request);

        $invoicesQuery = Invoice::query();
        $paymentsQuery = Payment::query();

        if ($dateFrom && $dateTo) {
            $invoicesQuery->whereBetween('date_generated', [$dateFrom, $dateTo]);
            $paymentsQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }

        $invoicesTotal = $invoicesQuery->sum('total_due');
        $paymentsTotal = $paymentsQuery->sum('amount');
        $outstanding = max(0, $invoicesTotal - $paymentsTotal);

        $paidQuery = clone $invoicesQuery;
        $partialQuery = clone $invoicesQuery;
        $unpaidQuery = clone $invoicesQuery;

        $paidCount = $paidQuery->whereRaw('(SELECT COALESCE(SUM(amount),0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) >= invoices.total_due')->count();
        $partialCount = $partialQuery->whereRaw('(SELECT COALESCE(SUM(amount),0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) > 0 AND (SELECT COALESCE(SUM(amount),0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) < invoices.total_due')->count();
        $unpaidCount = $unpaidQuery->whereRaw('(SELECT COALESCE(SUM(amount),0) FROM payments WHERE payments.invoice_id = invoices.invoice_id) = 0')->count();

        $paymentsByMethodQuery = clone $paymentsQuery;
        $paymentsByMethod = $paymentsByMethodQuery->selectRaw('payment_method, SUM(amount) as total')->groupBy('payment_method')->get();

        $data = [
            'generated_at' => $now,
            'generated_by' => $request->user(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'invoices_total' => $invoicesTotal,
            'payments_total' => $paymentsTotal,
            'outstanding' => $outstanding,
            'paid_count' => $paidCount,
            'partial_count' => $partialCount,
            'unpaid_count' => $unpaidCount,
            'payments_by_method' => $paymentsByMethod,
        ];

        $pdf = Pdf::loadView('reports.financial-summary', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('financial-summary.pdf');
    }

    /**
     * Payment history PDF.
     */
    public function paymentHistoryPdf(Request $request)
    {
        [$dateFrom, $dateTo] = $this->getDateRange($request);

        $paymentsQuery = Payment::with(['invoice', 'booking.tenant', 'collectedBy']);

        if ($dateFrom && $dateTo) {
            $paymentsQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }

        $payments = $paymentsQuery->orderByDesc('date_received')
            ->limit(500)
            ->get();

        $now = Carbon::now('Asia/Manila');

        $data = [
            'generated_at' => $now,
            'generated_by' => $request->user(),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'payments' => $payments,
        ];

        $pdf = Pdf::loadView('reports.payment-history', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('payment-history.pdf');
    }

    /**
     * Payment history Excel.
     */
    public function paymentHistoryExcel(Request $request)
    {
        [$dateFrom, $dateTo] = $this->getDateRange($request);
        return Excel::download(new PaymentHistoryExport($dateFrom, $dateTo), 'payment-history.xlsx');
    }

    /**
     * Get date range from request based on filter type.
     */
    protected function getDateRange(Request $request): array
    {
        $dateFilter = $request->get('date_filter', 'all');
        $now = Carbon::now('Asia/Manila');

        switch ($dateFilter) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'this_week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                return [$lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth()];
            case 'this_year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            case 'custom':
                $dateFrom = $request->get('date_from');
                $dateTo = $request->get('date_to');
                if ($dateFrom && $dateTo) {
                    return [
                        Carbon::parse($dateFrom, 'Asia/Manila')->startOfDay(),
                        Carbon::parse($dateTo, 'Asia/Manila')->endOfDay()
                    ];
                }
                return [null, null];
            default: // 'all'
                return [null, null];
        }
    }
}
