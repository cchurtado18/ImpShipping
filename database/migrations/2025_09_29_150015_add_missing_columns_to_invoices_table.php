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
        Schema::table('invoices', function (Blueprint $table) {
            // Add missing columns
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->string('terms')->default('30 Days')->after('due_date');
            
            // Sender information
            $table->string('sender_name')->after('terms');
            $table->string('sender_phone')->after('sender_name');
            $table->text('sender_address')->after('sender_phone');
            
            // Recipient information
            $table->string('recipient_name')->after('sender_address');
            $table->string('recipient_phone')->after('recipient_name');
            $table->text('recipient_address')->after('recipient_phone');
            
            // Service details
            $table->string('service_description')->after('recipient_address');
            $table->integer('quantity')->default(1)->after('service_description');
            $table->decimal('unit_price', 10, 2)->after('quantity');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax');
            $table->decimal('total_amount', 10, 2)->after('tax_amount');
            
            // Additional fields
            $table->foreignId('shipment_id')->nullable()->constrained()->onDelete('set null')->after('client_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('shipment_id');
            
            // Update status enum
            $table->dropColumn('status');
        });
        
        // Add new status column with correct enum values
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft')->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'due_date', 'terms', 'sender_name', 'sender_phone', 'sender_address',
                'recipient_name', 'recipient_phone', 'recipient_address',
                'service_description', 'quantity', 'unit_price', 'tax_amount', 'total_amount',
                'shipment_id', 'user_id', 'status'
            ]);
        });
    }
};
