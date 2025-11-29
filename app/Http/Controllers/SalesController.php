<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $perPage = $request->input('per_page', 10);

        // Get total REVENUE (Rent/Utility + Deposit Deductions - these are actual income)
        $totalSales = Payment::whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereBetween('date_received', [$startDate, $endDate])
            ->sum('amount');

        // Get revenue transactions only (exclude Security Deposit payments from table)
        $payments = Payment::with(['invoice.booking.tenant', 'invoice.booking.secondaryTenant', 'invoice.booking.room', 'collectedBy'])
            ->whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereBetween('date_received', [$startDate, $endDate])
            ->orderBy('date_received', 'desc')
            ->paginate($perPage);

        // Calculate counts (revenue transactions only)
        $totalTransactions = Payment::whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereBetween('date_received', [$startDate, $endDate])
            ->count();

        // Get outstanding balance (unpaid invoices)
        $outstandingBalance = Invoice::where('is_paid', false)->sum('total_due');

        // Prepare daily income chart data (for Bar Chart) - Revenue only
        $dailyIncome = Payment::select(
                DB::raw('CAST(date_received AS DATE) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereBetween('date_received', [$startDate, $endDate])
            ->groupBy(DB::raw('CAST(date_received AS DATE)'))
            ->orderBy('date')
            ->get();

        $dailyIncomeChart = [
            'labels' => $dailyIncome->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray(),
            'data' => $dailyIncome->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ];

        // Prepare payment type chart data (for Doughnut Chart) - Revenue breakdown
        $rentUtilityTotal = Payment::where('payment_type', 'Rent/Utility')
            ->whereBetween('date_received', [$startDate, $endDate])
            ->sum('amount');

        $depositDeductionTotal = Payment::where('payment_type', 'Deposit Deduction')
            ->whereBetween('date_received', [$startDate, $endDate])
            ->sum('amount');

        $paymentTypeChart = [
            'labels' => ['Rent/Utility', 'Deposit Deduction'],
            'data' => [(float) $rentUtilityTotal, (float) $depositDeductionTotal],
        ];

        return view('contents.sales-index', compact(
            'totalSales',
            'payments',
            'startDate',
            'endDate',
            'totalTransactions',
            'outstandingBalance',
            'perPage',
            'dailyIncomeChart',
            'paymentTypeChart'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Get total REVENUE (Rent/Utility + Deposit Deductions - these are actual income)
        $totalSales = Payment::whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereBetween('date_received', [$startDate, $endDate])
            ->sum('amount');

        // Get total Security Deposits received (liability, not revenue)
        $totalSecurityDeposits = Payment::where('payment_type', 'Security Deposit')
            ->whereBetween('date_received', [$startDate, $endDate])
            ->sum('amount');

        // Get sales by payment type
        $salesByType = Payment::select('payment_type', DB::raw('SUM(amount) as total'))
            ->whereBetween('date_received', [$startDate, $endDate])
            ->groupBy('payment_type')
            ->get()
            ->pluck('total', 'payment_type');

        // Get all payment transactions
        $payments = Payment::with(['invoice.booking.tenant', 'invoice.booking.secondaryTenant', 'invoice.booking.room', 'collectedBy'])
            ->whereBetween('date_received', [$startDate, $endDate])
            ->orderBy('date_received', 'desc')
            ->get();

        $totalTransactions = $payments->count();

        $pdf = Pdf::loadView('contents.sales-report-pdf', compact(
            'totalSales',
            'totalSecurityDeposits',
            'salesByType',
            'payments',
            'startDate',
            'endDate',
            'totalTransactions'
        ));

        $filename = 'Sales_Report_' . $startDate . '_to_' . $endDate . '.pdf';

        return $pdf->download($filename);
    }
}
