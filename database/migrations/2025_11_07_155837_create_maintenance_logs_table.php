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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('asset_id')->constrained('assets', 'asset_id'); // FK to assets
            $table->foreignId('logged_by_user_id')->constrained('users', 'user_id'); // FK to users
            $table->text('description');
            $table->date('date_reported');
            $table->string('status'); // 'Pending', 'In Progress', 'Completed', 'Cancelled'
            $table->date('date_completed')->nullable();
            $table->timestamps();
        });

        // Add CHECK constraint for status (SQL Server ENUM equivalent)
        DB::statement("
            ALTER TABLE maintenance_logs
            ADD CONSTRAINT CHK_MaintenanceStatus
            CHECK (status IN ('Pending', 'In Progress', 'Completed', 'Cancelled'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('maintenance_logs')) {
            DB::statement("
                ALTER TABLE maintenance_logs
                DROP CONSTRAINT IF EXISTS CHK_MaintenanceStatus
            ");
        }
        Schema::dropIfExists('maintenance_logs');
    }
};
