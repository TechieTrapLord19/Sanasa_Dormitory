<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room; // <-- 1. Import your Room model
use Illuminate\Support\Facades\DB; // <-- 2. Import the DB facade

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 3. Clear the table first so you don't get duplicates
        DB::table('rooms')->truncate();

        // 4. Loop through 3 floors
        for ($floor = 1; $floor <= 3; $floor++) {

            // 5. Loop 8 times for each room on the floor
            for ($room_num = 1; $room_num <= 8; $room_num++) {

                Room::create([
                    'room_num' => ($floor * 100) + $room_num, // Generates 101, 102... 201, 202...
                    'floor' => $floor,
                    'capacity' => 2, // From your company profile
                    'status' => 'available' // Default status
                ]);
            }
        }
    }
}