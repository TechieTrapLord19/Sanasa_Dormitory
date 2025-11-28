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
        // First, update any NULL values to a valid user_id (use first admin user)
        $firstAdminUser = DB::table('users')->where('role', 'admin')->first();
        if ($firstAdminUser) {
            DB::table('payments')
                ->whereNull('collected_by_user_id')
                ->update(['collected_by_user_id' => $firstAdminUser->user_id]);
        }

        // Drop the existing foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['collected_by_user_id']);
        });

        // Make collected_by_user_id nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('collected_by_user_id')->nullable()->change();
        });

        // Re-add the foreign key constraint (now nullable)
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('collected_by_user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any NULL values to a valid user_id (use first admin user)
        $firstAdminUser = DB::table('users')->where('role', 'admin')->first();
        if ($firstAdminUser) {
            DB::table('payments')
                ->whereNull('collected_by_user_id')
                ->update(['collected_by_user_id' => $firstAdminUser->user_id]);
        }

        // Drop the foreign key
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['collected_by_user_id']);
        });

        // Make collected_by_user_id required again
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('collected_by_user_id')->nullable(false)->change();
        });

        // Re-add the foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('collected_by_user_id')->references('user_id')->on('users');
        });
    }
};
