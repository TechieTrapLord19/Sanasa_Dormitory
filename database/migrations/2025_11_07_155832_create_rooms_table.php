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
        Schema::create('rooms', function (Blueprint $table) {
        $table->id('room_id');
        $table->string('room_num')->unique();
         $table->string('floor');
        $table->integer('capacity');
        $table->string('status');
        $table->timestamps();
    });
        DB::statement("
            ALTER TABLE rooms
            ADD CONSTRAINT CHK_RoomStatus
            CHECK (status IN ('available', 'occupied', 'maintenance'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 4. ADD THIS: This safely removes the constraint before dropping the table
        if (Schema::hasTable('rooms')) {
            DB::statement("
                ALTER TABLE rooms
                DROP CONSTRAINT IF EXISTS CHK_RoomStatus
            ");
        }

        Schema::dropIfExists('rooms');
    }
};
