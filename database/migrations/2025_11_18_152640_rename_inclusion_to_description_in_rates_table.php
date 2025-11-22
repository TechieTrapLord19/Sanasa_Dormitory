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
        // For SQL Server compatibility, use raw SQL
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement("EXEC sp_rename 'rates.inclusion', 'description', 'COLUMN'");
        } else {
            Schema::table('rates', function (Blueprint $table) {
                $table->renameColumn('inclusion', 'description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQL Server compatibility, use raw SQL
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement("EXEC sp_rename 'rates.description', 'inclusion', 'COLUMN'");
        } else {
            Schema::table('rates', function (Blueprint $table) {
                $table->renameColumn('description', 'inclusion');
            });
        }
    }
};
