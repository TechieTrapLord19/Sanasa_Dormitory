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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('room_id')->constrained('rooms', 'room_id'); // FK to rooms
            $table->foreignId('tenant_id')->constrained('tenants', 'tenant_id'); // FK to tenants
            $table->foreignId('rate_id')->constrained('rates', 'rate_id'); // FK to rates
            $table->foreignId('recorded_by_user_id')->constrained('users', 'user_id'); // FK to users (who made the booking)
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->decimal('total_calculated_fee', 10, 2); // e.g., total rent for the period
            $table->string('status'); // e.g., 'Reserved', 'Active', 'Completed', 'Canceled'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};