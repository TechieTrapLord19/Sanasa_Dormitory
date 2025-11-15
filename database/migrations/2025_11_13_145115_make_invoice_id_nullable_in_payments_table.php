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
        // Drop the existing foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        // Make invoice_id nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->change();
        });

        // Re-add the foreign key constraint with nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, handle any NULL invoice_id values (Security Deposit payments)
        // Option 1: Delete Security Deposit payments that have NULL invoice_id
        // Option 2: Set them to a placeholder invoice_id (not recommended)
        // We'll delete them as they shouldn't exist in the old system
        DB::table('payments')
            ->whereNull('invoice_id')
            ->where('payment_type', 'Security Deposit')
            ->delete();

        // Drop the foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        // Make invoice_id required again
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable(false)->change();
        });

        // Re-add the foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
        });
    }
};
