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
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('maritime_pound_cost', 8, 2)->nullable()->after('client_type');
            $table->decimal('air_pound_cost', 8, 2)->nullable()->after('maritime_pound_cost');
            $table->decimal('cubic_foot_cost', 8, 2)->nullable()->after('air_pound_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['maritime_pound_cost', 'air_pound_cost', 'cubic_foot_cost']);
        });
    }
};
