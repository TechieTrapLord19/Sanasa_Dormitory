<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SecurityDeposit;
use App\Models\Invoice;
use App\Models\Payment;

class SyncSecurityDeposits extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'deposits:sync';

    /**
     * The console command description.
     */
    protected $description = 'Sync existing security deposit payments to the security_deposits table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing existing security deposit data...');

        // Find all security deposit invoices
        // Security deposit: rent_subtotal = 0, no invoice_utilities, utility_electricity_fee > 0 (used as deposit amount)
        $securityDepositInvoices = Invoice::with(['payments', 'booking'])
            ->where('rent_subtotal', 0)
            ->where('utility_electricity_fee', '>', 0)
            ->whereDoesntHave('invoiceUtilities')
            ->get();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($securityDepositInvoices as $invoice) {
            if (!$invoice->booking) {
                $this->warn("Skipping invoice #{$invoice->invoice_id} - no booking found");
                $skipped++;
                continue;
            }

            // Check if security deposit already exists for this booking
            $existing = SecurityDeposit::where('booking_id', $invoice->booking_id)->first();

            $totalPaid = $invoice->payments->sum('amount');

            if ($existing) {
                // Update if amounts differ
                if ($existing->amount_paid != $totalPaid || $existing->amount_required != $invoice->total_due) {
                    $existing->update([
                        'amount_required' => $invoice->total_due,
                        'amount_paid' => $totalPaid,
                        'status' => $totalPaid > 0 ? SecurityDeposit::STATUS_HELD : SecurityDeposit::STATUS_PENDING,
                    ]);
                    $updated++;
                    $this->line("Updated deposit for booking #{$invoice->booking_id}");
                } else {
                    $skipped++;
                }
            } else {
                // Create new security deposit record
                SecurityDeposit::create([
                    'booking_id' => $invoice->booking_id,
                    'invoice_id' => $invoice->invoice_id,
                    'amount_required' => $invoice->total_due,
                    'amount_paid' => $totalPaid,
                    'amount_deducted' => 0,
                    'amount_refunded' => 0,
                    'status' => $totalPaid > 0 ? SecurityDeposit::STATUS_HELD : SecurityDeposit::STATUS_PENDING,
                ]);
                $created++;
                $this->info("Created deposit for booking #{$invoice->booking_id}");
            }
        }

        $this->newLine();
        $this->info("Sync complete!");
        $this->table(
            ['Created', 'Updated', 'Skipped'],
            [[$created, $updated, $skipped]]
        );

        return Command::SUCCESS;
    }
}
