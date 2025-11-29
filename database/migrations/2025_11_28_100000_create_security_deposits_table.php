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
        Schema::create('security_deposits', function (Blueprint $table) {
            $table->id('security_deposit_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices', 'invoice_id')->onDelete('set null');
            $table->decimal('amount_required', 10, 2); // The required deposit amount
            $table->decimal('amount_paid', 10, 2)->default(0); // Amount actually paid by tenant
            $table->decimal('amount_deducted', 10, 2)->default(0); // Total deductions applied
            $table->decimal('amount_refunded', 10, 2)->default(0); // Amount refunded to tenant
            $table->string('status')->default('Pending'); // Pending, Held, Partially Refunded, Refunded, Forfeited
            $table->text('deduction_reason')->nullable(); // JSON or text description of deductions
            $table->text('notes')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users', 'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_deposits');
    }
};