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
                'value' => '1', // 1% per day as per caretaker
            ],
            [
                'key' => 'late_penalty_type',
                'value' => 'percentage', // 'percentage' or 'fixed'
            ],
            [
                'key' => 'late_penalty_grace_days',
                'value' => '0', // No grace period - penalty starts immediately after due date
            ],
            [
                'key' => 'late_penalty_frequency',
                'value' => 'daily', // Compounds daily as per caretaker
            ],
            [
                'key' => 'auto_apply_penalties',
                'value' => '1', // Auto-apply enabled by default
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
