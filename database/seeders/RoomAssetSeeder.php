<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Asset;

class RoomAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define standard assets for all rooms
        $standardAssets = [
            [
                'name' => 'Air-condition',
                'condition' => 'Good',
            ],
            [
                'name' => 'Cloth rack',
                'condition' => 'Good',
            ],
            [
                'name' => 'Plastic table',
                'condition' => 'Good',
            ],
            [
                'name' => 'Plastic chair',
                'condition' => 'Good',
                'quantity' => 2, // Special field to create 2 chairs
            ],
        ];

        // Get all rooms
        $rooms = Room::all();

        // For each room, create the standard assets
        foreach ($rooms as $room) {
            foreach ($standardAssets as $assetData) {
                $quantity = $assetData['quantity'] ?? 1;

                // If quantity > 1, create multiple assets with the same name
                for ($i = 0; $i < $quantity; $i++) {
                    Asset::create([
                        'room_id' => $room->room_id,
                        'name' => $assetData['name'],
                        'condition' => $assetData['condition'],
                        'date_acquired' => now(),
                    ]);
                }
            }
        }
    }
}
