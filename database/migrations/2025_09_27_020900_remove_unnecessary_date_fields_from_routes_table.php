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
            $table->dropColumn(['collection_start_at', 'cutoff_at', 'departure_at', 'arrival_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->datetime('collection_start_at')->nullable();
            $table->datetime('cutoff_at')->nullable();
            $table->datetime('departure_at')->nullable();
            $table->datetime('arrival_at')->nullable();
        });
    }
};
