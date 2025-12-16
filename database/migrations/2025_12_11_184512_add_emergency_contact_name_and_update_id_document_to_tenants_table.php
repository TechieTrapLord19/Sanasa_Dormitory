<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Add emergency contact name field after emergency contact number
            $table->string('emer_contact_name', 255)->nullable()->after('emer_contact_num');

            // Rename id_document to id_document_path (stores file path now)
            // Note: We're keeping the same column but it will now store image paths
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('emer_contact_name');
        });
    }
};
