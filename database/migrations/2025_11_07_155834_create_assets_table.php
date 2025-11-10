<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // For SQL Server CHECK constraint

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id('asset_id');
            $table->foreignId('room_id')->nullable()->constrained('rooms', 'room_id'); // FK to rooms (nullable if asset can be in storage)
            $table->string('name'); // e.g., 'AC Unit', 'Chair', 'Bed Frame'
            $table->string('condition'); // e.g., 'Good', 'Needs Repair', 'Broken', 'Missing'
            $table->date('date_acquired')->nullable();
            $table->timestamps();
        });

        // Add CHECK constraint for condition (SQL Server ENUM equivalent)
        DB::statement("
            ALTER TABLE assets
            ADD CONSTRAINT CHK_AssetCondition
            CHECK (condition IN ('Good', 'Needs Repair', 'Broken', 'Missing'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assets')) {
            DB::statement("
                ALTER TABLE assets
                DROP CONSTRAINT IF EXISTS CHK_AssetCondition
            ");
        }
        Schema::dropIfExists('assets');
    }
};
