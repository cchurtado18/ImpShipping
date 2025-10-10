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
        Schema::create('route_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('box_height', 8, 2)->nullable(); // Alto en pulgadas
            $table->decimal('box_width', 8, 2)->nullable(); // Ancho en pulgadas  
            $table->decimal('box_length', 8, 2)->nullable(); // Largo en pulgadas
            $table->text('nicaragua_address'); // Dirección de envío en Nicaragua
            $table->string('nicaragua_phone'); // Número telefónico en Nicaragua
            $table->integer('box_quantity'); // Cantidad de cajas
            $table->text('notes')->nullable(); // Notas adicionales
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_leads');
    }
};
