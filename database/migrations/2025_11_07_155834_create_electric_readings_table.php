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
        Schema::create('electric_readings', function (Blueprint $table) {
            $table->id('reading_id');
            $table->foreignId('room_id')->constrained('rooms', 'room_id'); // FK to rooms
            $table->date('reading_date');
            $table->decimal('meter_value_kwh', 8, 2); // Kilowatt-hours
            $table->boolean('is_billed')->default(false); // Whether this reading has been included in an invoice
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_readings');
    }
};
