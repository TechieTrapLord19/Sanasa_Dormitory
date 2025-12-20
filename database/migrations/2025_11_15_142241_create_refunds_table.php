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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id('refund_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id');
            $table->foreignId('payment_id')->constrained('payments', 'payment_id');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices', 'invoice_id');
            $table->foreignId('refunded_by_user_id')->constrained('users', 'user_id');
            $table->decimal('refund_amount', 10, 2);
            $table->string('refund_method'); // 'Cash', 'GCash'
            $table->string('reference_number')->nullable(); // Required for GCash
            $table->date('refund_date');
            $table->text('cancellation_reason');
            $table->string('status'); // 'Pending', 'Processed', 'Completed'
            $table->timestamps();
        });

        // Add CHECK constraint for refund_method
        DB::statement("
            ALTER TABLE refunds
            ADD CONSTRAINT CHK_RefundMethod
            CHECK (refund_method IN ('Cash', 'GCash'))
        ");

        // Add CHECK constraint for status
        DB::statement("
            ALTER TABLE refunds
            ADD CONSTRAINT CHK_RefundStatus
            CHECK (status IN ('Pending', 'Processed', 'Completed'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('refunds')) {
            DB::statement("
                ALTER TABLE refunds
                DROP CONSTRAINT IF EXISTS CHK_RefundMethod
            ");
            DB::statement("
                ALTER TABLE refunds
                DROP CONSTRAINT IF EXISTS CHK_RefundStatus
            ");
        }
        Schema::dropIfExists('refunds');
    }
};