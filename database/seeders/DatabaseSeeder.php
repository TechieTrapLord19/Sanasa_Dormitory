<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class, // <-- Call the UserSeeder here
            RoomSeeder::class, // <-- Call the RoomSeeder here
            TenantSeeder::class, // <-- Call the TenantSeeder here
            RoomAssetSeeder::class, // <-- Call the RoomAssetSeeder here
            RateSeeder::class,      // Add this
            SettingSeeder::class,   // Penalty settings
        ]);
    }
}