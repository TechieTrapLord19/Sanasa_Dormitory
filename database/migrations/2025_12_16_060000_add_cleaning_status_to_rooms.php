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
        // Drop the old constraint
        DB::statement("
            ALTER TABLE rooms
            DROP CONSTRAINT IF EXISTS CHK_RoomStatus
        ");

        // Add new constraint with all statuses including 'pending' and 'cleaning'
        DB::statement("
            ALTER TABLE rooms
            ADD CONSTRAINT CHK_RoomStatus
            CHECK (status IN ('available', 'pending', 'occupied', 'maintenance', 'cleaning'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update any 'cleaning' rooms back to 'available' first
        DB::table('rooms')->where('status', 'cleaning')->update(['status' => 'available']);

        // Drop the new constraint
        DB::statement("
            ALTER TABLE rooms
            DROP CONSTRAINT IF EXISTS CHK_RoomStatus
        ");

        // Restore previous constraint with pending
        DB::statement("
            ALTER TABLE rooms
            ADD CONSTRAINT CHK_RoomStatus
            CHECK (status IN ('available', 'pending', 'occupied', 'maintenance'))
        ");
    }
};
