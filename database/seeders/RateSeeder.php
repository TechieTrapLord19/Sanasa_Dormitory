<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rate;
use Illuminate\Support\Facades\DB;

class RateSeeder extends Seeder
{

    public function run(): void
    {

        // 1. Daily Rate (Utilities INCLUDED)
        Rate::create([
            'duration_type' => 'Daily',
            'base_price' => 250.00,
            'inclusion' => 'All utilities included (Water, Wi-Fi, Electricity).'
        ]);

        // 2. Weekly Rate (Utilities INCLUDED)
        Rate::create([
            'duration_type' => 'Weekly',
            'base_price' => 1750.00,
            'inclusion' => 'All utilities included (Water, Wi-Fi, Electricity).'
        ]);

        // 3. Monthly Rate (Utilities BILLED SEPARATELY)
        Rate::create([
            'duration_type' => 'Monthly',
            'base_price' => 5000.00,
            'inclusion' => 'Base rent only. Additional Utilities: ₱350 Water + ₱260 Wi-Fi + Metered Electricity.'
        ]);
    }
}