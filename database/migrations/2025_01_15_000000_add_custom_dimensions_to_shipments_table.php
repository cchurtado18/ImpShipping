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
        Schema::table('shipments', function (Blueprint $table) {
            // Campos para dimensiones personalizadas
            $table->decimal('custom_length', 8, 2)->nullable()->after('box_id'); // Largo personalizado en pulgadas
            $table->decimal('custom_width', 8, 2)->nullable()->after('custom_length'); // Ancho personalizado en pulgadas
            $table->decimal('custom_height', 8, 2)->nullable()->after('custom_width'); // Alto personalizado en pulgadas
            $table->decimal('custom_weight', 8, 2)->nullable()->after('custom_height'); // Peso personalizado en libras
            $table->decimal('custom_weight_rate', 8, 2)->nullable()->after('custom_weight'); // Tarifa por libra personalizada
            $table->decimal('calculated_price', 8, 2)->nullable()->after('custom_weight_rate'); // Precio calculado automáticamente
            $table->boolean('use_calculated_price')->default(true)->after('calculated_price'); // Si usa precio calculado o manual
            $table->decimal('manual_price', 8, 2)->nullable()->after('use_calculated_price'); // Precio manual si no usa calculado
            $table->string('price_mode', 20)->default('calculated')->after('manual_price'); // Modo de precio: 'calculated' o 'manual'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'custom_length',
                'custom_width', 
                'custom_height',
                'custom_weight',
                'custom_weight_rate',
                'calculated_price',
                'use_calculated_price',
                'manual_price',
                'price_mode'
            ]);
        });
    }
};
