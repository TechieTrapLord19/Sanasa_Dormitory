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
        // Drop the old CHECK constraint and add a new one with Electricity type
        DB::unprepared("
            ALTER TABLE payments DROP CONSTRAINT IF EXISTS CK_payments_payment_type;
            ALTER TABLE payments ADD CONSTRAINT CK_payments_payment_type 
            CHECK (payment_type IN ('Rent/Utility', 'Security Deposit', 'Electricity', 'Deposit Deduction'));
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old constraint (without Electricity)
        DB::unprepared("
            ALTER TABLE payments DROP CONSTRAINT IF EXISTS CK_payments_payment_type;
            ALTER TABLE payments ADD CONSTRAINT CK_payments_payment_type 
            CHECK (payment_type IN ('Rent/Utility', 'Security Deposit', 'Deposit Deduction'));
        ");
    }
};
