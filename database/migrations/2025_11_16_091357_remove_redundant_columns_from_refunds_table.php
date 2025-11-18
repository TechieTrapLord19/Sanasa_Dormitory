<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['invoice_id']);
        });

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
        Schema::table('refunds', function (Blueprint $table) {
            // Re-add the columns
            $table->foreignId('booking_id')->after('refund_id')->constrained('bookings', 'booking_id');
            $table->foreignId('invoice_id')->nullable()->after('payment_id')->constrained('invoices', 'invoice_id');
        });
    }
};
