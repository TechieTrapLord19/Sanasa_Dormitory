<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\Payment;
use App\Models\Room;
use App\Models\SecurityDeposit;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // 1. OCCUPANCY RATE
        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('status', 'Active')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // 2. MONTHLY REVENUE (Current Month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $monthlyRevenue = Payment::whereMonth('date_received', $currentMonth)
            ->whereYear('date_received', $currentYear)
            ->sum('amount');

        // 3. OUTSTANDING BALANCE (All unpaid invoices)
        // Get all invoices with their payments
        $invoices = Invoice::with('payments')->get();
        $outstandingBalance = $invoices->sum(function($invoice) {
            $totalPaid = $invoice->payments->sum('amount');
            return max(0, $invoice->total_due - $totalPaid);
        });

        // 4. PENDING MAINTENANCE
        $pendingRepairs = MaintenanceLog::whereNotIn('status', ['Completed', 'Canceled'])
            ->count();

        // 5. ADDITIONAL METRICS
        $totalTenants = Tenant::where('status', 'active')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $totalBookings = Booking::where('status', 'Active')->count();

        // 6. TODAY'S COLLECTIONS
        $todayCollections = Payment::whereDate('date_received', now())->sum('amount');

        // 7. TODAY'S CHECK-INS
        $todayCheckins = Booking::whereDate('checkin_date', now())
            ->with(['tenant', 'room'])
            ->where('status', '!=', 'Completed')
            ->get();

        // 8. TODAY'S CHECK-OUTS
        $todayCheckouts = Booking::whereDate('checkout_date', now())
            ->with(['tenant', 'room'])
            ->where('status', 'Active')
            ->get();

        // 9. UPCOMING EXPIRATIONS (Next 7 days)
        $upcomingExpirations = Booking::whereBetween('checkout_date', [now(), now()->addDays(7)])
            ->where('status', 'Active')
            ->with(['tenant', 'room'])
            ->orderBy('checkout_date')
            ->get();

        // 10. OVERDUE INVOICES (Invoices with unpaid balance where is_paid = false)
        $overdueInvoices = Invoice::with(['booking.tenant', 'booking.room', 'payments'])
            ->where('is_paid', false)
            ->orderBy('date_generated', 'desc')
            ->limit(5)
            ->get()
            ->filter(function($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                return $totalPaid < $invoice->total_due;
            })
            ->values();

        // 11. SECURITY DEPOSIT METRICS
        $totalDepositsHeld = SecurityDeposit::where('status', 'Held')
            ->get()
            ->sum(function($deposit) {
                return $deposit->calculateRefundable();
            });

        $pendingDeposits = SecurityDeposit::whereIn('status', ['Pending', 'Depleted'])->count();

        $depositsNeedingAction = SecurityDeposit::whereHas('booking', function($q) {
            $q->where('status', 'Completed');
        })->whereIn('status', ['Held', 'Depleted'])->count();

        return view('contents.dashboard', compact(
            'totalRooms',
            'occupiedRooms',
            'occupancyRate',
            'monthlyRevenue',
            'outstandingBalance',
            'pendingRepairs',
            'totalTenants',
            'availableRooms',
            'totalBookings',
            'todayCollections',
            'todayCheckins',
            'todayCheckouts',
            'upcomingExpirations',
            'overdueInvoices',
            'totalDepositsHeld',
            'pendingDeposits',
            'depositsNeedingAction'
        ));
    }
}
