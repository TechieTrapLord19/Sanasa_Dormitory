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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('invoice_id')->constrained('invoices', 'invoice_id'); // FK to invoices
            $table->foreignId('collected_by_user_id')->constrained('users', 'user_id'); // FK to users (who collected it)
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // 'Cash', 'GCash'
            // $table->string('reference_number')->nullable(); // THIS SHOULD BE ADDED BY THE SEPARATE MIGRATION
            $table->date('date_received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
