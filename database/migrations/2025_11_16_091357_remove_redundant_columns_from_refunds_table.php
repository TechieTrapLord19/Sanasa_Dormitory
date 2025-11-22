<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if foreign keys exist before dropping
        if (DB::getDriverName() === 'sqlsrv') {
            // For SQL Server, check and drop foreign keys if they exist
            try {
                DB::statement("
                    IF EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'refunds_booking_id_foreign')
                    ALTER TABLE refunds DROP CONSTRAINT refunds_booking_id_foreign
                ");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            try {
                DB::statement("
                    IF EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'refunds_invoice_id_foreign')
                    ALTER TABLE refunds DROP CONSTRAINT refunds_invoice_id_foreign
                ");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        } else {
            Schema::table('refunds', function (Blueprint $table) {
                // Drop foreign key constraints first
                try {
                    $table->dropForeign(['booking_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                try {
                    $table->dropForeign(['invoice_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }

        Schema::table('refunds', function (Blueprint $table) {
            // Drop the columns
            $table->dropColumn(['booking_id', 'invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQL Server, we need to add columns as nullable first, then update and make NOT NULL
        if (DB::getDriverName() === 'sqlsrv') {
            // Add booking_id as nullable first
            DB::statement("ALTER TABLE refunds ADD booking_id BIGINT NULL");
            
            // Try to populate booking_id from payment->invoice->booking relationship
            // Update refunds with booking_id from invoices through payments
            DB::statement("
                UPDATE r
                SET r.booking_id = i.booking_id
                FROM refunds r
                INNER JOIN payments p ON r.payment_id = p.payment_id
                INNER JOIN invoices i ON p.invoice_id = i.invoice_id
                WHERE i.booking_id IS NOT NULL
            ");
            
            // Add foreign key constraint (allowing NULLs for now since we can't guarantee all records have booking_id)
            DB::statement("
                ALTER TABLE refunds
                ADD CONSTRAINT refunds_booking_id_foreign
                FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
            ");
            
            // Add invoice_id as nullable (it was nullable originally)
            DB::statement("ALTER TABLE refunds ADD invoice_id BIGINT NULL");
            
            // Populate invoice_id from payments
            DB::statement("
                UPDATE r
                SET r.invoice_id = p.invoice_id
                FROM refunds r
                INNER JOIN payments p ON r.payment_id = p.payment_id
                WHERE p.invoice_id IS NOT NULL
            ");
            
            DB::statement("
                ALTER TABLE refunds
                ADD CONSTRAINT refunds_invoice_id_foreign
                FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id)
            ");
        } else {
            Schema::table('refunds', function (Blueprint $table) {
                // Re-add the columns as nullable first
                $table->bigInteger('booking_id')->nullable()->after('refund_id');
                $table->bigInteger('invoice_id')->nullable()->after('payment_id');
            });
            
            // Populate booking_id from payment->invoice->booking
            DB::statement("
                UPDATE refunds r
                INNER JOIN payments p ON r.payment_id = p.payment_id
                INNER JOIN invoices i ON p.invoice_id = i.invoice_id
                SET r.booking_id = i.booking_id
                WHERE i.booking_id IS NOT NULL
            ");
            
            // Populate invoice_id from payments
            DB::statement("
                UPDATE refunds r
                INNER JOIN payments p ON r.payment_id = p.payment_id
                SET r.invoice_id = p.invoice_id
                WHERE p.invoice_id IS NOT NULL
            ");
            
            // Add foreign key constraints
            Schema::table('refunds', function (Blueprint $table) {
                $table->foreign('booking_id')->references('booking_id')->on('bookings');
                $table->foreign('invoice_id')->references('invoice_id')->on('invoices');
            });
        }
    }
};
