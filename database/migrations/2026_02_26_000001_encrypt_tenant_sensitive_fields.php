<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen sensitive tenant columns to text so they can hold
     * AES-256 encrypted values (which are longer than plain strings).
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
            $table->text('contact_num')->nullable()->change();
            $table->text('emer_contact_num')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->text('id_document')->nullable()->change();
            $table->text('emer_contact_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('contact_num')->nullable()->change();
            $table->string('emer_contact_num')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('id_document')->nullable()->change();
            $table->string('emer_contact_name')->nullable()->change();
        });
    }
};
