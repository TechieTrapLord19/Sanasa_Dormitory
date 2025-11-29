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
        // Drop the old constraint and add a new one with additional payment types
        DB::unprepared("
            ALTER TABLE payments
            DROP CONSTRAINT IF EXISTS CHK_PaymentType
        ");

        DB::unprepared("
            ALTER TABLE payments
            ADD CONSTRAINT CHK_PaymentType
            CHECK (payment_type IN ('Rent/Utility', 'Security Deposit', 'Deposit Deduction'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            ALTER TABLE payments
            DROP CONSTRAINT IF EXISTS CHK_PaymentType
        ");

        DB::unprepared("
            ALTER TABLE payments
            ADD CONSTRAINT CHK_PaymentType
            CHECK (payment_type IN ('Rent/Utility', 'Security Deposit'))
        ");
    }
};
