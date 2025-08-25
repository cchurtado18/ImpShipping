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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->text('us_address');
            $table->string('us_phone');
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['en_seguimiento', 'confirmado', 'proxima_ruta', 'ruta_cancelada'])->default('en_seguimiento');
            $table->timestamps();
            
            $table->index(['status', 'full_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
