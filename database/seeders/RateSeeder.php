<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rate;
use App\Models\Utility;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Daily Rate
        $dailyRate = Rate::create([
            'rate_name' => 'Daily',
            'duration_type' => 'Daily',
            'base_price' => 250.00,
            'description' => 'Daily rate - Utilities are included',
        ]);

        // 2. Weekly Rate
        $weeklyRate = Rate::create([
            'rate_name' => 'Weekly',
            'duration_type' => 'Weekly',
            'base_price' => 1750.00,
            'description' => 'Weekly rate - Utilities are included',
        ]);

        // 3. Monthly Rate
        $monthlyRate = Rate::create([
            'rate_name' => 'Monthly',
            'duration_type' => 'Monthly',
            'base_price' => 5000.00,
            'description' => 'Monthly rate - Utilities charged separately. Security deposit is 5000, electricity is metered.',
        ]);

        // Add utilities for Monthly rate only
        Utility::create([
            'rate_id' => $monthlyRate->rate_id,
            'name' => 'Water',
            'price' => 350.00,
        ]);

        Utility::create([
            'rate_id' => $monthlyRate->rate_id,
            'name' => 'WiFi',
            'price' => 260.00,
        ]);

        Utility::create([
            'rate_id' => $monthlyRate->rate_id,
            'name' => 'Garbage',
            'price' => 50.00,
        ]);
    }
}
