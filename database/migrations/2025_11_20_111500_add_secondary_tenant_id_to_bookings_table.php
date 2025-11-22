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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'secondary_tenant_id')) {
                $table->foreignId('secondary_tenant_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('tenants', 'tenant_id')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'secondary_tenant_id')) {
                $table->dropForeign(['secondary_tenant_id']);
                $table->dropColumn('secondary_tenant_id');
            }
        });
    }
};

