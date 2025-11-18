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
        // Drop the existing foreign key constraint first
        // Laravel creates constraint names like: maintenance_logs_asset_id_foreign
        try {
            Schema::table('maintenance_logs', function (Blueprint $table) {
                $table->dropForeign(['asset_id']);
            });
        } catch (\Exception $e) {
            // If constraint doesn't exist or has different name, try SQL Server specific approach
            DB::statement("
                IF EXISTS (
                    SELECT * FROM sys.foreign_keys 
                    WHERE parent_object_id = OBJECT_ID('maintenance_logs') 
                    AND referenced_object_id = OBJECT_ID('assets')
                )
                BEGIN
                    DECLARE @fkName NVARCHAR(128)
                    SELECT @fkName = name FROM sys.foreign_keys 
                    WHERE parent_object_id = OBJECT_ID('maintenance_logs') 
                    AND referenced_object_id = OBJECT_ID('assets')
                    EXEC('ALTER TABLE maintenance_logs DROP CONSTRAINT ' + @fkName)
                END
            ");
        }

        // Make asset_id nullable (SQL Server specific)
        DB::statement('ALTER TABLE maintenance_logs ALTER COLUMN asset_id BIGINT NULL');

        // Re-add the foreign key constraint as nullable
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->foreign('asset_id')->references('asset_id')->on('assets')->onDelete('set null');
        });

        // Add notes column
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('date_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            // Drop notes column
            $table->dropColumn('notes');
        });

        // Drop the foreign key constraint
        try {
            Schema::table('maintenance_logs', function (Blueprint $table) {
                $table->dropForeign(['asset_id']);
            });
        } catch (\Exception $e) {
            // If constraint doesn't exist or has different name, try SQL Server specific approach
            DB::statement("
                IF EXISTS (
                    SELECT * FROM sys.foreign_keys 
                    WHERE parent_object_id = OBJECT_ID('maintenance_logs') 
                    AND referenced_object_id = OBJECT_ID('assets')
                )
                BEGIN
                    DECLARE @fkName NVARCHAR(128)
                    SELECT @fkName = name FROM sys.foreign_keys 
                    WHERE parent_object_id = OBJECT_ID('maintenance_logs') 
                    AND referenced_object_id = OBJECT_ID('assets')
                    EXEC('ALTER TABLE maintenance_logs DROP CONSTRAINT ' + @fkName)
                END
            ");
        }

        // Make asset_id NOT NULL again (SQL Server specific)
        DB::statement('ALTER TABLE maintenance_logs ALTER COLUMN asset_id BIGINT NOT NULL');

        // Re-add the foreign key constraint as NOT NULL
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->foreign('asset_id')->references('asset_id')->on('assets');
        });
    }
};
