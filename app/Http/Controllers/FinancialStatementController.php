<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\SecurityDeposit;
use App\Models\Refund;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialStatementController extends Controller
{
    /**
     * Display the financial statement.
     */
    public function index(Request $request)
    {
        // Date filter handling
        $dateFilter = $request->input('date_filter', 'this_month');
        $dateFrom = '';
        $dateTo = '';

        switch ($dateFilter) {
            case 'today':
                $dateFrom = now()->toDateString();
                $dateTo = now()->toDateString();
                break;
            case 'this_week':
                $dateFrom = now()->startOfWeek()->toDateString();
                $dateTo = now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $dateFrom = now()->startOfMonth()->toDateString();
                $dateTo = now()->endOfMonth()->toDateString();
                break;
            case 'this_quarter':
                $dateFrom = now()->firstOfQuarter()->toDateString();
                $dateTo = now()->lastOfQuarter()->toDateString();
                break;
            case 'this_year':
                $dateFrom = now()->startOfYear()->toDateString();
                $dateTo = now()->endOfYear()->toDateString();
                break;
            case 'custom':
                $dateFrom = $request->input('date_from', '');
                $dateTo = $request->input('date_to', '');
                break;
            case 'all':
            default:
                $dateFilter = 'all';
                break;
        }

        // === INCOME CALCULATIONS ===
        
        // Rent Income (from payments on invoices with rent_subtotal > 0)
        $rentIncomeQuery = Payment::whereHas('invoice', function ($q) {
            $q->where('rent_subtotal', '>', 0);
        })->where('payment_type', 'Rent/Utility');
        
        if ($dateFrom && $dateTo) {
            $rentIncomeQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }
        $rentIncome = $rentIncomeQuery->sum('amount');

        // Electricity Income
        $electricityIncomeQuery = Payment::where('payment_type', 'Electricity');
        if ($dateFrom && $dateTo) {
            $electricityIncomeQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }
        $electricityIncome = $electricityIncomeQuery->sum('amount');

        // Forfeited Deposits (from security_deposits where status = 'Forfeited' or 'Depleted')
        $forfeitedDepositsQuery = SecurityDeposit::whereIn('status', ['Forfeited', 'Depleted']);
        if ($dateFrom && $dateTo) {
            $forfeitedDepositsQuery->whereBetween('updated_at', [$dateFrom, $dateTo]);
        }
        $forfeitedDeposits = $forfeitedDepositsQuery->sum('amount_deducted');

        $totalRevenue = $rentIncome + $electricityIncome + $forfeitedDeposits;

        // === EXPENSE CALCULATIONS ===
        
        $expenseQuery = Expense::query();
        if ($dateFrom && $dateTo) {
            $expenseQuery->whereBetween('expense_date', [$dateFrom, $dateTo]);
        }
        $totalExpenses = $expenseQuery->sum('amount');

        // Expenses by category
        $expensesByCategory = Expense::query()
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('expense_date', [$dateFrom, $dateTo]))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Security Deposit Refunds
        $refundsQuery = Refund::query();
        if ($dateFrom && $dateTo) {
            $refundsQuery->whereBetween('refund_date', [$dateFrom, $dateTo]);
        }
        $totalRefunds = $refundsQuery->sum('refund_amount');

        $totalExpensesAndRefunds = $totalExpenses + $totalRefunds;

        // === NET INCOME ===
        $netIncome = $totalRevenue - $totalExpensesAndRefunds;

        // === LIABILITIES (Security Deposits Held) ===
        $securityDepositsHeld = SecurityDeposit::where('status', 'Held')
            ->sum(DB::raw('amount_paid - amount_deducted - amount_refunded'));

        // === OUTSTANDING BALANCE ===
        $outstandingBalance = Invoice::withSum('payments as paid', 'amount')
            ->get()
            ->sum(function ($invoice) {
                return max(0, $invoice->total_due - ($invoice->paid ?? 0));
            });

        // === KPIs ===
        
        // Total Billed (invoices generated in period)
        $totalBilledQuery = Invoice::query();
        if ($dateFrom && $dateTo) {
            $totalBilledQuery->whereBetween('date_generated', [$dateFrom, $dateTo]);
        }
        $totalBilled = $totalBilledQuery->sum('total_due');

        // Total Collected (payments in period)
        $totalCollectedQuery = Payment::query();
        if ($dateFrom && $dateTo) {
            $totalCollectedQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }
        $totalCollected = $totalCollectedQuery->sum('amount');

        // Collection Rate (capped at 100%)
        $collectionRate = $totalBilled > 0 ? min(100, ($totalCollected / $totalBilled) * 100) : 0;

        // Occupancy Rate
        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('status', 'Active')->distinct('room_id')->count('room_id');
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // === RECEIVABLES (Outstanding Invoices) ===
        $receivables = Invoice::with(['booking.tenant', 'booking.room'])
            ->withSum('payments as paid', 'amount')
            ->get()
            ->filter(function ($invoice) {
                $remaining = $invoice->total_due - ($invoice->paid ?? 0);
                return $remaining > 0;
            })
            ->map(function ($invoice) {
                $invoice->remaining_balance = $invoice->total_due - ($invoice->paid ?? 0);
                $invoice->days_overdue = $invoice->due_date 
                    ? max(0, now()->diffInDays($invoice->due_date, false) * -1)
                    : 0;
                return $invoice;
            })
            ->sortByDesc('days_overdue')
            ->take(20);

        // === MONTHLY TRENDS (Last 12 months) ===
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('M Y');

            // Income for this month
            $monthIncome = Payment::whereBetween('date_received', [$monthStart, $monthEnd])
                ->sum('amount');

            // Expenses for this month
            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // Refunds for this month
            $monthRefunds = Refund::whereBetween('refund_date', [$monthStart, $monthEnd])
                ->sum('refund_amount');

            $monthlyTrends[] = [
                'month' => $monthLabel,
                'income' => (float) $monthIncome,
                'expenses' => (float) ($monthExpenses + $monthRefunds),
                'net' => (float) ($monthIncome - $monthExpenses - $monthRefunds),
            ];
        }

        return view('contents.financial-statement', [
            'dateFilter' => $dateFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            // Revenue
            'rentIncome' => $rentIncome,
            'electricityIncome' => $electricityIncome,
            'forfeitedDeposits' => $forfeitedDeposits,
            'totalRevenue' => $totalRevenue,
            // Expenses
            'totalExpenses' => $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'totalRefunds' => $totalRefunds,
            'totalExpensesAndRefunds' => $totalExpensesAndRefunds,
            // Net
            'netIncome' => $netIncome,
            // Liabilities
            'securityDepositsHeld' => $securityDepositsHeld,
            // Outstanding
            'outstandingBalance' => $outstandingBalance,
            // KPIs
            'totalBilled' => $totalBilled,
            'totalCollected' => $totalCollected,
            'collectionRate' => $collectionRate,
            'occupancyRate' => $occupancyRate,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            // Receivables
            'receivables' => $receivables,
            // Trends
            'monthlyTrends' => $monthlyTrends,
        ]);
    }

    /**
     * Export financial statement as PDF.
     */
    public function export(Request $request)
    {
        // Date filter handling (same as index)
        $dateFilter = $request->input('date_filter', 'this_month');
        $dateFrom = '';
        $dateTo = '';
        $periodLabel = 'All Time';

        switch ($dateFilter) {
            case 'today':
                $dateFrom = now()->toDateString();
                $dateTo = now()->toDateString();
                $periodLabel = now()->format('M d, Y');
                break;
            case 'this_week':
                $dateFrom = now()->startOfWeek()->toDateString();
                $dateTo = now()->endOfWeek()->toDateString();
                $periodLabel = now()->startOfWeek()->format('M d') . ' - ' . now()->endOfWeek()->format('M d, Y');
                break;
            case 'this_month':
                $dateFrom = now()->startOfMonth()->toDateString();
                $dateTo = now()->endOfMonth()->toDateString();
                $periodLabel = now()->format('F Y');
                break;
            case 'this_quarter':
                $dateFrom = now()->firstOfQuarter()->toDateString();
                $dateTo = now()->lastOfQuarter()->toDateString();
                $periodLabel = 'Q' . now()->quarter . ' ' . now()->year;
                break;
            case 'this_year':
                $dateFrom = now()->startOfYear()->toDateString();
                $dateTo = now()->endOfYear()->toDateString();
                $periodLabel = now()->format('Y');
                break;
            case 'all':
            default:
                $dateFilter = 'all';
                $periodLabel = 'All Time';
                break;
        }

        // === INCOME CALCULATIONS ===
        $rentIncomeQuery = Payment::whereHas('invoice', function ($q) {
            $q->where('rent_subtotal', '>', 0);
        })->where('payment_type', 'Rent/Utility');
        if ($dateFrom && $dateTo) {
            $rentIncomeQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }
        $rentIncome = $rentIncomeQuery->sum('amount');

        $electricityIncomeQuery = Payment::where('payment_type', 'Electricity');
        if ($dateFrom && $dateTo) {
            $electricityIncomeQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }
        $electricityIncome = $electricityIncomeQuery->sum('amount');

        $forfeitedDepositsQuery = SecurityDeposit::whereIn('status', ['Forfeited', 'Depleted']);
        if ($dateFrom && $dateTo) {
            $forfeitedDepositsQuery->whereBetween('updated_at', [$dateFrom, $dateTo]);
        }
        $forfeitedDeposits = $forfeitedDepositsQuery->sum('amount_deducted');

        $totalRevenue = $rentIncome + $electricityIncome + $forfeitedDeposits;

        // === EXPENSE CALCULATIONS ===
        $expenseQuery = Expense::query();
        if ($dateFrom && $dateTo) {
            $expenseQuery->whereBetween('expense_date', [$dateFrom, $dateTo]);
        }
        $totalExpenses = $expenseQuery->sum('amount');

        $expensesByCategory = Expense::query()
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('expense_date', [$dateFrom, $dateTo]))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $refundsQuery = Refund::query();
        if ($dateFrom && $dateTo) {
            $refundsQuery->whereBetween('refund_date', [$dateFrom, $dateTo]);
        }
        $totalRefunds = $refundsQuery->sum('refund_amount');

        $totalExpensesAndRefunds = $totalExpenses + $totalRefunds;
        $netIncome = $totalRevenue - $totalExpensesAndRefunds;

        // === LIABILITIES ===
        $securityDepositsHeld = SecurityDeposit::where('status', 'Held')
            ->sum(DB::raw('amount_paid - amount_deducted - amount_refunded'));

        // === OUTSTANDING ===
        $outstandingBalance = Invoice::withSum('payments as paid', 'amount')
            ->get()
            ->sum(function ($invoice) {
                return max(0, $invoice->total_due - ($invoice->paid ?? 0));
            });

        // === KPIs ===
        $totalBilledQuery = Invoice::query();
        if ($dateFrom && $dateTo) {
            $totalBilledQuery->whereBetween('date_generated', [$dateFrom, $dateTo]);
        }
        $totalBilled = $totalBilledQuery->sum('total_due');

        $totalCollectedQuery = Payment::query();
        if ($dateFrom && $dateTo) {
            $totalCollectedQuery->whereBetween('date_received', [$dateFrom, $dateTo]);
        }
        $totalCollected = $totalCollectedQuery->sum('amount');

        $collectionRate = $totalBilled > 0 ? min(100, ($totalCollected / $totalBilled) * 100) : 0;

        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('status', 'Active')->distinct('room_id')->count('room_id');
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // === RECEIVABLES ===
        $receivables = Invoice::with(['booking.tenant', 'booking.room'])
            ->withSum('payments as paid', 'amount')
            ->get()
            ->filter(function ($invoice) {
                return ($invoice->total_due - ($invoice->paid ?? 0)) > 0;
            })
            ->map(function ($invoice) {
                $invoice->remaining_balance = $invoice->total_due - ($invoice->paid ?? 0);
                $invoice->days_overdue = $invoice->due_date 
                    ? max(0, now()->diffInDays($invoice->due_date, false) * -1)
                    : 0;
                return $invoice;
            })
            ->sortByDesc('days_overdue')
            ->take(15);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contents.financial-statement-pdf', [
            'periodLabel' => $periodLabel,
            'generatedBy' => auth()->user()->full_name ?? 'System',
            'rentIncome' => $rentIncome,
            'electricityIncome' => $electricityIncome,
            'forfeitedDeposits' => $forfeitedDeposits,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'totalRefunds' => $totalRefunds,
            'totalExpensesAndRefunds' => $totalExpensesAndRefunds,
            'netIncome' => $netIncome,
            'securityDepositsHeld' => $securityDepositsHeld,
            'outstandingBalance' => $outstandingBalance,
            'totalBilled' => $totalBilled,
            'totalCollected' => $totalCollected,
            'collectionRate' => $collectionRate,
            'occupancyRate' => $occupancyRate,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'receivables' => $receivables,
        ]);

        $filename = 'financial-statement-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
