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
            $table->date('due_date')->nullable()->after('date_generated');
            $table->decimal('penalty_amount', 10, 2)->default(0.00)->after('total_due');
            $table->integer('days_overdue')->default(0)->after('penalty_amount');
        });

        // Add penalty settings (SQL Server compatible)
        $settings = [
            [
                'key' => 'late_penalty_rate',
                'value' => '5', // 5% penalty
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_penalty_type',
                'value' => 'percentage', // 'percentage' or 'fixed'
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_penalty_grace_days',
                'value' => '7', // 7 days grace period before penalty applies
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_penalty_frequency',
                'value' => 'once', // 'once', 'daily', 'weekly', 'monthly'
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            // Only insert if the key doesn't already exist
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert($setting);
            }
        }

        // Set due_date for existing invoices (date_generated + 7 days by default)
        // MySQL uses DATE_ADD, SQL Server uses DATEADD
        if (config('database.default') === 'mysql') {
            DB::statement("
                UPDATE invoices
                SET due_date = DATE_ADD(date_generated, INTERVAL 7 DAY)
                WHERE due_date IS NULL AND date_generated IS NOT NULL
            ");
        } else {
            DB::statement("
                UPDATE invoices
                SET due_date = DATEADD(day, 7, date_generated)
                WHERE due_date IS NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'penalty_amount', 'days_overdue']);
        });

        DB::table('settings')->whereIn('key', [
            'late_penalty_rate',
            'late_penalty_type',
            'late_penalty_grace_days',
            'late_penalty_frequency',
        ])->delete();
    }
};