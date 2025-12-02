<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Penalty Settings
            [
                'key' => 'late_penalty_rate',
                'value' => '5', // 5% or 5 pesos depending on type
            ],
            [
                'key' => 'late_penalty_type',
                'value' => 'percentage', // 'percentage' or 'fixed'
            ],
            [
                'key' => 'late_penalty_grace_days',
                'value' => '7', // 7 days grace period
            ],
            [
                'key' => 'late_penalty_frequency',
                'value' => 'once', // 'once', 'daily', 'weekly', 'monthly'
            ],
            // Invoice Due Date Settings
            [
                'key' => 'invoice_due_days',
                'value' => '15', // Days after invoice generation before due
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
