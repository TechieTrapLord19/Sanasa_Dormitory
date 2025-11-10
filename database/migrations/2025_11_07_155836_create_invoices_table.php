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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id'); // FK to bookings
            $table->date('date_generated');
            $table->decimal('rent_subtotal', 10, 2);
            $table->decimal('utility_water_fee', 10, 2)->default(0.00);
            $table->decimal('utility_wifi_fee', 10, 2)->default(0.00);
            $table->decimal('utility_electricity_fee', 10, 2)->default(0.00);
            $table->decimal('total_due', 10, 2);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
