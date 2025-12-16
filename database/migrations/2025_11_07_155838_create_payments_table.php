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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('invoice_id')->constrained('invoices', 'invoice_id'); // FK to invoices
            $table->foreignId('collected_by_user_id')->constrained('users', 'user_id'); // FK to users (who collected it)
            $table->string('payment_type');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // 'Cash', 'GCash'
            $table->string('reference_number')->nullable();     // e.g., GCash transaction ID
            $table->dateTime('date_received');
            $table->timestamps();
        });
        DB::statement("
            ALTER TABLE payments
            ADD CONSTRAINT CHK_PaymentType
            CHECK (payment_type IN ('Rent/Utility', 'Security Deposit'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // --- 6. UPDATE YOUR DOWN METHOD ---
        if (Schema::hasTable('payments')) {
            DB::statement("
                ALTER TABLE payments
                DROP CONSTRAINT IF EXISTS CHK_PaymentType
            ");
        }
        Schema::dropIfExists('payments');
    }
};
