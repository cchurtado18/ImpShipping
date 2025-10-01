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
        Schema::table('routes', function (Blueprint $table) {
            $table->date('route_start_date')->nullable()->after('responsible');
            $table->date('route_end_date')->nullable()->after('route_start_date');
            $table->json('states')->nullable()->after('route_end_date');
            $table->boolean('is_active')->default(false)->after('states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['route_start_date', 'route_end_date', 'states', 'is_active']);
        });
    }
};
