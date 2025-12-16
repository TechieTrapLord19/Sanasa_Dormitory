<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\MaintenanceLog;
use App\Models\SecurityDeposit;
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
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->sum('amount');

        // Get revenue transactions only (exclude Security Deposit payments from table)
        $payments = Payment::with(['invoice.booking.tenant', 'invoice.booking.secondaryTenant', 'invoice.booking.room', 'collectedBy'])
            ->whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->orderBy('date_received', 'desc')
            ->paginate($perPage);

        // Calculate counts (revenue transactions only)
        $totalTransactions = Payment::whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->count();

        // Get outstanding balance (unpaid invoices)
        $outstandingBalance = Invoice::where('is_paid', false)->sum('total_due');

        // Prepare daily income chart data (for Bar Chart) - Revenue only
        $dailyIncome = Payment::select(
                DB::raw('CAST(date_received AS DATE) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->groupBy(DB::raw('CAST(date_received AS DATE)'))
            ->orderBy('date')
            ->get();

        $dailyIncomeChart = [
            'labels' => $dailyIncome->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray(),
            'data' => $dailyIncome->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ];

        // Prepare payment type chart data (for Doughnut Chart) - Revenue breakdown
        $rentUtilityTotal = Payment::where('payment_type', 'Rent/Utility')
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->sum('amount');

        $depositDeductionTotal = Payment::where('payment_type', 'Deposit Deduction')
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
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
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->sum('amount');

        // Get total Security Deposits received (liability, not revenue)
        $totalSecurityDeposits = Payment::where('payment_type', 'Security Deposit')
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->sum('amount');

        // Get sales by payment type
        $salesByType = Payment::select('payment_type', DB::raw('SUM(amount) as total'))
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->groupBy('payment_type')
            ->get()
            ->pluck('total', 'payment_type');

        // Get all payment transactions
        $payments = Payment::with(['invoice.booking.tenant', 'invoice.booking.secondaryTenant', 'invoice.booking.room', 'collectedBy'])
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
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

    /**
     * Export consolidated monthly report PDF
     * Combines: Financial Summary, Room Occupancy, Tenant Summary, Maintenance Logs, Security Deposits
     */
    public function exportConsolidated(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // ============ 1. FINANCIAL SUMMARY ============
        // Total Revenue (Rent/Utility + Deposit Deductions)
        $totalRevenue = Payment::whereIn('payment_type', ['Rent/Utility', 'Deposit Deduction'])
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->sum('amount');

        // Total Security Deposits received (liability)
        $totalSecurityDeposits = Payment::where('payment_type', 'Security Deposit')
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->sum('amount');

        // Revenue by payment type
        $revenueByType = Payment::select('payment_type', DB::raw('SUM(amount) as total'))
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->groupBy('payment_type')
            ->get()
            ->pluck('total', 'payment_type');

        // Payment methods breakdown
        $paymentsByMethod = Payment::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->groupBy('payment_method')
            ->get();

        // Outstanding balance (unpaid invoices)
        $invoicesWithBalance = Invoice::with('payments')->get();
        $outstandingBalance = $invoicesWithBalance->sum(function($invoice) {
            $totalPaid = $invoice->payments->sum('amount');
            return max(0, $invoice->total_due - $totalPaid);
        });

        // Total transactions
        $totalTransactions = Payment::whereDate('date_received', '>=', $startDate)->whereDate('date_received', '<=', $endDate)->count();

        // Get payment transactions for detail table
        $payments = Payment::with(['invoice.booking.tenant', 'invoice.booking.secondaryTenant', 'invoice.booking.room', 'collectedBy'])
            ->whereDate('date_received', '>=', $startDate)
            ->whereDate('date_received', '<=', $endDate)
            ->orderBy('date_received', 'desc')
            ->get();

        // ============ 2. ROOM OCCUPANCY ============
        $totalRooms = Room::count();
        $rooms = Room::with(['activeBooking.tenant', 'activeBooking.secondaryTenant', 'activeBooking.rate'])
            ->orderBy('room_num')
            ->get();

        $roomStats = [
            'total' => $totalRooms,
            'available' => $rooms->where('status', 'available')->count(),
            'occupied' => $rooms->where('status', 'occupied')->count(),
            'pending' => $rooms->where('status', 'pending')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
        ];
        $occupancyRate = $totalRooms > 0 ? round(($roomStats['occupied'] / $totalRooms) * 100, 1) : 0;

        // ============ 3. TENANT SUMMARY ============
        $activeTenants = Tenant::where('status', 'active')->count();
        $inactiveTenants = Tenant::where('status', 'inactive')->count();

        // New tenants in period (based on first booking)
        $newBookingsInPeriod = Booking::whereBetween('checkin_date', [$startDate, $endDate])
            ->with(['tenant', 'room', 'rate'])
            ->orderBy('checkin_date', 'desc')
            ->get();

        // Checkouts in period
        $checkoutsInPeriod = Booking::whereBetween('checkout_date', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->with(['tenant', 'room'])
            ->orderBy('checkout_date', 'desc')
            ->get();

        // ============ 4. MAINTENANCE LOGS ============
        $maintenanceLogs = MaintenanceLog::with(['asset.room', 'loggedBy'])
            ->whereBetween('date_reported', [$startDate, $endDate])
            ->orderBy('date_reported', 'desc')
            ->get();

        $maintenanceStats = [
            'total' => $maintenanceLogs->count(),
            'pending' => $maintenanceLogs->where('status', 'Pending')->count(),
            'in_progress' => $maintenanceLogs->where('status', 'In Progress')->count(),
            'completed' => $maintenanceLogs->where('status', 'Completed')->count(),
            'canceled' => $maintenanceLogs->where('status', 'Canceled')->count(),
        ];

        // ============ 5. SECURITY DEPOSITS ============
        $securityDeposits = SecurityDeposit::with(['booking.tenant', 'booking.room'])
            ->get();

        $depositStats = [
            'total_held' => $securityDeposits->where('status', 'Held')->sum('amount_paid'),
            'total_refunded' => $securityDeposits->sum('amount_refunded'),
            'total_deducted' => $securityDeposits->sum('amount_deducted'),
            'pending_count' => $securityDeposits->where('status', 'Pending')->count(),
            'held_count' => $securityDeposits->where('status', 'Held')->count(),
            'refunded_count' => $securityDeposits->where('status', 'Refunded')->count(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('contents.consolidated-report-pdf', compact(
            'startDate',
            'endDate',
            // Financial
            'totalRevenue',
            'totalSecurityDeposits',
            'revenueByType',
            'paymentsByMethod',
            'outstandingBalance',
            'totalTransactions',
            'payments',
            // Rooms
            'rooms',
            'roomStats',
            'occupancyRate',
            // Tenants
            'activeTenants',
            'inactiveTenants',
            'newBookingsInPeriod',
            'checkoutsInPeriod',
            // Maintenance
            'maintenanceLogs',
            'maintenanceStats',
            // Security Deposits
            'securityDeposits',
            'depositStats'
        ));

        $pdf->setPaper('A4', 'portrait');

        $filename = 'Consolidated_Monthly_Report_' . $startDate . '_to_' . $endDate . '.pdf';

        return $pdf->download($filename);
    }
}
