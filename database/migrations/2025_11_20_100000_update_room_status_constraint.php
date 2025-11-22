<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('rooms')) {
            return;
        }

        DB::statement("
            ALTER TABLE rooms
            DROP CONSTRAINT IF EXISTS CHK_RoomStatus
        ");

        DB::statement("
            ALTER TABLE rooms
            ADD CONSTRAINT CHK_RoomStatus
            CHECK (status IN ('available', 'pending', 'occupied', 'maintenance'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('rooms')) {
            return;
        }

        DB::statement("
            ALTER TABLE rooms
            DROP CONSTRAINT IF EXISTS CHK_RoomStatus
        ");

        DB::statement("
            ALTER TABLE rooms
            ADD CONSTRAINT CHK_RoomStatus
            CHECK (status IN ('available', 'occupied', 'maintenance'))
        ");
    }
};

