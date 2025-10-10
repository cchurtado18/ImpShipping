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
            // Campos para el estado de recepción
            $table->enum('reception_status', ['pending', 'received', 'loaded', 'in_transit', 'delivered'])->default('pending')->after('payment_status');
            $table->timestamp('received_at')->nullable()->after('reception_status');
            $table->timestamp('loaded_at')->nullable()->after('received_at');
            $table->string('reception_photo_path')->nullable()->after('loaded_at');
            $table->text('reception_notes')->nullable()->after('reception_photo_path');
            $table->foreignId('received_by')->nullable()->constrained('users')->after('reception_notes');
            $table->foreignId('loaded_by')->nullable()->constrained('users')->after('received_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropForeign(['loaded_by']);
            $table->dropColumn([
                'reception_status',
                'received_at',
                'loaded_at',
                'reception_photo_path',
                'reception_notes',
                'received_by',
                'loaded_by'
            ]);
        });
    }
};