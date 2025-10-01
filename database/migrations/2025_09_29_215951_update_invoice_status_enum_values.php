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
        // Primero eliminar la columna existente
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_status');
        });
        
        // Agregar la columna con los nuevos valores
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('invoice_status', ['pending', 'cancelled_by_cash', 'cancelled_by_transfer'])->default('pending')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los valores anteriores
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_status');
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('invoice_status', ['pending', 'paid_cash', 'paid_transfer', 'cancelled'])->default('pending')->after('status');
        });
    }
};
