<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\SecurityDeposit;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Get all system notifications/alerts
     */
    public function getAllNotifications(): array
    {
        $notifications = [];

        // 1. Upcoming Checkouts (Next 3 days)
        $upcomingCheckouts = $this->getUpcomingCheckouts();
        foreach ($upcomingCheckouts as $booking) {
            $daysUntil = now()->startOfDay()->diffInDays($booking->checkout_date->startOfDay(), false);
            $urgency = $daysUntil <= 0 ? 'danger' : ($daysUntil <= 1 ? 'warning' : 'info');
            $timeText = $daysUntil <= 0 ? 'Today' : ($daysUntil == 1 ? 'Tomorrow' : "In {$daysUntil} days");

            $notifications[] = [
                'id' => 'checkout_' . $booking->booking_id,
                'type' => 'checkout',
                'urgency' => $urgency,
                'icon' => 'bi-box-arrow-right',
                'title' => 'Upcoming Check-out',
                'message' => "{$booking->tenant->full_name} - Room {$booking->room->room_num}",
                'detail' => $timeText . ' (' . $booking->checkout_date->format('M d, Y') . ')',
                'link' => route('bookings.show', $booking->booking_id),
                'created_at' => $booking->checkout_date,
            ];
        }

        // 2. Expiring Leases (Next 7 days for monthly tenants)
        $expiringLeases = $this->getExpiringLeases();
        foreach ($expiringLeases as $booking) {
            $daysUntil = now()->startOfDay()->diffInDays($booking->checkout_date->startOfDay(), false);
            $urgency = $daysUntil <= 3 ? 'warning' : 'info';

            $notifications[] = [
                'id' => 'lease_' . $booking->booking_id,
                'type' => 'lease',
                'urgency' => $urgency,
                'icon' => 'bi-calendar-x',
                'title' => 'Lease Expiring Soon',
                'message' => "{$booking->tenant->full_name} - Room {$booking->room->room_num}",
                'detail' => "Expires " . $booking->checkout_date->format('M d, Y') . " ({$daysUntil} days)",
                'link' => route('bookings.show', $booking->booking_id),
                'created_at' => $booking->checkout_date,
            ];
        }

        // 3. Low Security Deposit Balances
        $lowDeposits = $this->getLowSecurityDeposits();
        foreach ($lowDeposits as $deposit) {
            $balance = $deposit->calculateRefundable();
            $percentage = ($balance / $deposit->amount_required) * 100;
            $urgency = $percentage <= 25 ? 'danger' : 'warning';

            $notifications[] = [
                'id' => 'deposit_' . $deposit->deposit_id,
                'type' => 'deposit',
                'urgency' => $urgency,
                'icon' => 'bi-shield-exclamation',
                'title' => 'Low Security Deposit',
                'message' => "{$deposit->booking->tenant->full_name} - Room {$deposit->booking->room->room_num}",
                'detail' => "Balance: ₱" . number_format($balance, 2) . " / ₱" . number_format($deposit->amount_required, 2) . " (" . round($percentage) . "%)",
                'link' => route('security-deposits.show', $deposit->deposit_id),
                'created_at' => $deposit->updated_at,
            ];
        }

        // 4. Pending Maintenance Requests
        $pendingMaintenance = $this->getPendingMaintenance();
        foreach ($pendingMaintenance as $log) {
            $daysPending = $log->date_reported->diffInDays(now());
            $urgency = $daysPending >= 7 ? 'danger' : ($daysPending >= 3 ? 'warning' : 'info');

            $notifications[] = [
                'id' => 'maintenance_' . $log->log_id,
                'type' => 'maintenance',
                'urgency' => $urgency,
                'icon' => 'bi-tools',
                'title' => 'Pending Maintenance',
                'message' => $log->asset_location,
                'detail' => "Reported " . $log->date_reported->diffForHumans(),
                'link' => route('maintenance-logs'),
                'created_at' => $log->date_reported,
            ];
        }

        // 5. Overdue Invoices
        $overdueInvoices = $this->getOverdueInvoices();
        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = $invoice->date_generated->diffInDays(now());
            $urgency = $daysOverdue >= 14 ? 'danger' : ($daysOverdue >= 7 ? 'warning' : 'info');
            $unpaid = $invoice->total_due - $invoice->payments->sum('amount');

            $notifications[] = [
                'id' => 'invoice_' . $invoice->invoice_id,
                'type' => 'invoice',
                'urgency' => $urgency,
                'icon' => 'bi-receipt',
                'title' => 'Unpaid Invoice',
                'message' => "{$invoice->booking->tenant->full_name} - Room {$invoice->booking->room->room_num}",
                'detail' => "₱" . number_format($unpaid, 2) . " unpaid ({$daysOverdue} days)",
                'link' => route('invoices.show', $invoice->invoice_id),
                'created_at' => $invoice->date_generated,
            ];
        }

        // 6. Deposits Needing Refund (Completed bookings with held deposits)
        $depositsToRefund = $this->getDepositsNeedingRefund();
        foreach ($depositsToRefund as $deposit) {
            $balance = $deposit->calculateRefundable();

            $notifications[] = [
                'id' => 'refund_' . $deposit->deposit_id,
                'type' => 'refund',
                'urgency' => 'warning',
                'icon' => 'bi-cash-stack',
                'title' => 'Deposit Awaiting Refund',
                'message' => "{$deposit->booking->tenant->full_name}",
                'detail' => "₱" . number_format($balance, 2) . " to refund - Checked out " . $deposit->booking->checkout_date->format('M d'),
                'link' => route('security-deposits.show', $deposit->deposit_id),
                'created_at' => $deposit->booking->checkout_date,
            ];
        }

        // Sort by urgency (danger first, then warning, then info) and then by date
        usort($notifications, function($a, $b) {
            $urgencyOrder = ['danger' => 0, 'warning' => 1, 'info' => 2];
            $urgencyDiff = ($urgencyOrder[$a['urgency']] ?? 3) - ($urgencyOrder[$b['urgency']] ?? 3);
            if ($urgencyDiff !== 0) return $urgencyDiff;
            return $a['created_at'] <=> $b['created_at'];
        });

        return $notifications;
    }

    /**
     * Get notification counts by type
     */
    public function getNotificationCounts(): array
    {
        return [
            'checkout' => $this->getUpcomingCheckouts()->count(),
            'lease' => $this->getExpiringLeases()->count(),
            'deposit' => $this->getLowSecurityDeposits()->count(),
            'maintenance' => $this->getPendingMaintenance()->count(),
            'invoice' => $this->getOverdueInvoices()->count(),
            'refund' => $this->getDepositsNeedingRefund()->count(),
        ];
    }

    /**
     * Get total notification count
     */
    public function getTotalCount(): int
    {
        $counts = $this->getNotificationCounts();
        return array_sum($counts);
    }

    /**
     * Get urgent notification count (danger + warning)
     */
    public function getUrgentCount(): int
    {
        $notifications = $this->getAllNotifications();
        return collect($notifications)->whereIn('urgency', ['danger', 'warning'])->count();
    }

    // ============ Private Helper Methods ============

    private function getUpcomingCheckouts(): Collection
    {
        return Booking::whereBetween('checkout_date', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
            ->where('status', 'Active')
            ->with(['tenant', 'room'])
            ->orderBy('checkout_date')
            ->get();
    }

    private function getExpiringLeases(): Collection
    {
        // Monthly bookings expiring in next 7 days (but not in next 3 days - those are checkouts)
        return Booking::whereBetween('checkout_date', [now()->addDays(4)->startOfDay(), now()->addDays(14)->endOfDay()])
            ->where('status', 'Active')
            ->whereHas('rate', function($q) {
                $q->where('duration_type', 'Monthly');
            })
            ->with(['tenant', 'room', 'rate'])
            ->orderBy('checkout_date')
            ->get();
    }

    private function getLowSecurityDeposits(): Collection
    {
        return SecurityDeposit::whereIn('status', ['Held', 'Depleted'])
            ->whereHas('booking', function($q) {
                $q->where('status', 'Active');
            })
            ->with(['booking.tenant', 'booking.room'])
            ->get()
            ->filter(function($deposit) {
                $balance = $deposit->calculateRefundable();
                $percentage = ($balance / $deposit->amount_required) * 100;
                return $percentage < 50; // Less than 50% remaining
            });
    }

    private function getPendingMaintenance(): Collection
    {
        return MaintenanceLog::whereNotIn('status', ['Completed', 'Canceled'])
            ->with('asset.room')
            ->orderBy('date_reported', 'asc')
            ->limit(10)
            ->get();
    }

    private function getOverdueInvoices(): Collection
    {
        return Invoice::with(['booking.tenant', 'booking.room', 'payments'])
            ->where('is_paid', false)
            ->whereHas('booking', function($q) {
                $q->whereNotIn('status', ['Canceled', 'Completed']);
            })
            ->orderBy('date_generated', 'asc')
            ->limit(10)
            ->get()
            ->filter(function($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                return $totalPaid < $invoice->total_due;
            });
    }

    private function getDepositsNeedingRefund(): Collection
    {
        return SecurityDeposit::whereIn('status', ['Held', 'Depleted'])
            ->whereHas('booking', function($q) {
                $q->where('status', 'Completed');
            })
            ->with(['booking.tenant', 'booking.room'])
            ->get()
            ->filter(function($deposit) {
                return $deposit->calculateRefundable() > 0;
            });
    }
}
