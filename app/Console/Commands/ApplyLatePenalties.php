<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ApplyLatePenalties extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoices:apply-penalties {--force : Apply even if already applied today}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically apply late payment penalties to overdue invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic penalty application...');

        // Get penalty settings
        $penaltyRate = (float) Setting::get('late_penalty_rate', 1);
        $penaltyType = Setting::get('late_penalty_type', 'percentage');
        $graceDays = (int) Setting::get('late_penalty_grace_days', 0);
        $frequency = Setting::get('late_penalty_frequency', 'daily');

        $this->info("Settings: {$penaltyRate}" . ($penaltyType === 'percentage' ? '%' : ' pesos') . " {$frequency}, {$graceDays} grace days");

        // Get all unpaid invoices that are overdue
        $overdueInvoices = Invoice::where('is_paid', false)
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($overdueInvoices as $invoice) {
            // Calculate the penalty
            $newPenalty = $invoice->calculatePenalty();
            $currentPenalty = $invoice->penalty_amount ?? 0;

            // Only update if penalty has increased (for daily compounding)
            if ($newPenalty > $currentPenalty) {
                $invoice->penalty_amount = $newPenalty;
                $invoice->days_overdue = $invoice->calculated_days_overdue;
                $invoice->save();

                $updated++;
                $this->line("  Invoice #{$invoice->id} (Room {$invoice->room->room_number}): ₱" . number_format($currentPenalty, 2) . " → ₱" . number_format($newPenalty, 2));
            } else {
                $skipped++;
            }
        }

        $this->info("Completed: {$updated} invoices updated, {$skipped} skipped");

        Log::info("Late penalties applied: {$updated} invoices updated, {$skipped} skipped");

        return Command::SUCCESS;
    }
}
