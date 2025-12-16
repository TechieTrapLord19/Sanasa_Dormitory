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
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the scheduled time columns (we don't need them for boarding house)
            if (Schema::hasColumn('bookings', 'checkin_time')) {
                $table->dropColumn('checkin_time');
            }
            if (Schema::hasColumn('bookings', 'checkout_time')) {
                $table->dropColumn('checkout_time');
            }

            // Add actual timestamp columns - records when the action was performed
            $table->timestamp('checked_in_at')->nullable()->after('checkout_date');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'checked_out_at']);
            $table->time('checkin_time')->nullable()->after('checkin_date');
            $table->time('checkout_time')->nullable()->after('checkout_date');
        });
    }
};
