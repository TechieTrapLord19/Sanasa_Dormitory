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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['utility_water_fee', 'utility_wifi_fee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('utility_water_fee', 10, 2)->default(0.00)->after('rent_subtotal');
            $table->decimal('utility_wifi_fee', 10, 2)->default(0.00)->after('utility_water_fee');
        });
    }
};
