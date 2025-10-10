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
            // Campos para independencia de rutas
            $table->decimal('total_budget', 10, 2)->nullable()->after('is_active');
            $table->decimal('used_budget', 10, 2)->default(0)->after('total_budget');
            $table->decimal('projected_revenue', 10, 2)->nullable()->after('used_budget');
            $table->decimal('actual_revenue', 10, 2)->default(0)->after('projected_revenue');
            $table->integer('projected_shipments')->nullable()->after('actual_revenue');
            $table->integer('actual_shipments')->default(0)->after('projected_shipments');
            $table->text('notes')->nullable()->after('actual_shipments');
            $table->json('route_goals')->nullable()->after('notes');
            $table->json('performance_metrics')->nullable()->after('route_goals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn([
                'total_budget',
                'used_budget', 
                'projected_revenue',
                'actual_revenue',
                'projected_shipments',
                'actual_shipments',
                'notes',
                'route_goals',
                'performance_metrics'
            ]);
        });
    }
};