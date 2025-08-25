<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero actualizamos los valores existentes a los nuevos estados
        DB::statement("UPDATE shipments SET shipment_status = 'por_recepcionar' WHERE shipment_status = 'lead'");
        DB::statement("UPDATE shipments SET shipment_status = 'entregado' WHERE shipment_status = 'delivered'");
        DB::statement("UPDATE shipments SET shipment_status = 'por_recepcionar' WHERE shipment_status = 'ready'");
        DB::statement("UPDATE shipments SET shipment_status = 'cancelled' WHERE shipment_status = 'cancelled'");

        // Luego modificamos la columna para aceptar los nuevos valores
        Schema::table('shipments', function (Blueprint $table) {
            $table->enum('shipment_status', [
                'por_recepcionar',
                'recepcionado', 
                'dejado_almacen',
                'en_nicaragua',
                'entregado',
                'cancelled'
            ])->default('por_recepcionar')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertimos los valores a los originales
        DB::statement("UPDATE shipments SET shipment_status = 'lead' WHERE shipment_status = 'por_recepcionar'");
        DB::statement("UPDATE shipments SET shipment_status = 'delivered' WHERE shipment_status = 'entregado'");
        DB::statement("UPDATE shipments SET shipment_status = 'ready' WHERE shipment_status = 'recepcionado'");

        Schema::table('shipments', function (Blueprint $table) {
            $table->enum('shipment_status', ['lead', 'ready', 'delivered', 'cancelled'])->default('lead')->change();
        });
    }
};
