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
        // Drop the existing foreign key constraint on invoice_id
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        // Make invoice_id nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->change();
        });

        // Add booking_id column with foreign key
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('booking_id')->after('payment_id')->constrained('bookings', 'booking_id');
        });

        // Re-add the foreign key constraint on invoice_id (now nullable)
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop booking_id foreign key and column
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
        });

        // Drop invoice_id foreign key
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        // Make invoice_id required again
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable(false)->change();
        });

        // Re-add the original foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices');
        });
    }
};
