<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCancelBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel bookings that have exceeded the 24-hour grace period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for bookings to auto-cancel...');

        // Get bookings that need to be canceled
        // - Created more than 24 hours ago
        // - Status is Pending Payment, Partial Payment, or Paid Payment
        // - Rate duration_type is NOT Daily
        // - Not already canceled or auto-canceled
        $cutoffTime = Carbon::now()->subHours(24);

        $bookingsToCancel = Booking::where('created_at', '<', $cutoffTime)
            ->whereIn('status', ['Pending Payment', 'Partial Payment', 'Paid Payment'])
            ->whereHas('rate', function ($query) {
                $query->whereIn('duration_type', ['Weekly', 'Monthly']); // Exclude Daily
            })
            ->where('status', '!=', 'Canceled')
            ->where('auto_canceled', false)
            ->with('rate')
            ->get();

        if ($bookingsToCancel->isEmpty()) {
            $this->info('No bookings to cancel.');
            return 0;
        }

        $canceledCount = 0;
        foreach ($bookingsToCancel as $booking) {
            $hoursElapsed = Carbon::parse($booking->created_at)->diffInHours(Carbon::now());

            $booking->update([
                'status' => 'Canceled',
                'auto_canceled' => true,
                'auto_cancel_reason' => "Automatically canceled after {$hoursElapsed} hours. Grace period of 24 hours exceeded without payment completion or check-in."
            ]);

            $this->info("Canceled Booking ID {$booking->booking_id} (Created: {$booking->created_at}, Status was: {$booking->status})");
            $canceledCount++;
        }

        $this->info("Successfully auto-canceled {$canceledCount} booking(s).");
        return 0;
    }
}