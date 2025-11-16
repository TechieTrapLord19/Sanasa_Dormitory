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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('user_id')->constrained('users', 'user_id'); // FK to users (caretaker who performed the action)
            $table->string('action'); // e.g., "Created Booking", "Updated Tenant", "Checked In Tenant"
            $table->text('description'); // Detailed description of what was done
            $table->string('model_type')->nullable(); // e.g., "Booking", "Tenant", "Room"
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected record
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
