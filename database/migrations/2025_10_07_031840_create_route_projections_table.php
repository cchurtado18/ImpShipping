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
        Schema::create('route_projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->string('projection_type'); // revenue, shipments, expenses, profit
            $table->string('period'); // daily, weekly, monthly
            $table->date('projection_date');
            $table->decimal('projected_value', 10, 2);
            $table->decimal('actual_value', 10, 2)->nullable();
            $table->decimal('variance', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_projections');
    }
};