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
        Schema::table('rates', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the CHECK constraint first (SQL Server specific)
        DB::statement("
            DECLARE @constraintName NVARCHAR(128)
            SELECT @constraintName = name
            FROM sys.check_constraints
            WHERE parent_object_id = OBJECT_ID('rates')
            AND parent_column_id = (
                SELECT column_id FROM sys.columns
                WHERE object_id = OBJECT_ID('rates') AND name = 'status'
            )
            IF @constraintName IS NOT NULL
                EXEC('ALTER TABLE rates DROP CONSTRAINT ' + @constraintName)
        ");

        Schema::table('rates', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
