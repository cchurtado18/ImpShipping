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
        Schema::table('route_expenses', function (Blueprint $table) {
            // Verificar si los campos ya existen antes de agregarlos
            if (!Schema::hasColumn('route_expenses', 'expense_type')) {
                $table->enum('expense_type', ['fuel', 'freight', 'warehouse', 'taxes', 'toll', 'per_diem', 'last_mile', 'other'])->after('route_id');
            }
            
            if (!Schema::hasColumn('route_expenses', 'expense_date')) {
                $table->date('expense_date')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('route_expenses', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('expense_date');
            }
            
            if (!Schema::hasColumn('route_expenses', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            }
            
            if (!Schema::hasColumn('route_expenses', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            }
            
            if (!Schema::hasColumn('route_expenses', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            
            if (!Schema::hasColumn('route_expenses', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->after('approved_at');
            }
            
            if (!Schema::hasColumn('route_expenses', 'location')) {
                $table->string('location')->nullable()->after('receipt_number');
            }
            
            if (!Schema::hasColumn('route_expenses', 'notes')) {
                $table->text('notes')->nullable()->after('location');
            }
        });
        
        // Actualizar datos existentes si hay alguno
        \DB::table('route_expenses')->whereNull('expense_date')->update(['expense_date' => now()->toDateString()]);
        \DB::table('route_expenses')->whereNull('status')->update(['status' => 'pending']);
        
        // Si existe category, copiar a expense_type
        if (Schema::hasColumn('route_expenses', 'category')) {
            \DB::table('route_expenses')->update(['expense_type' => \DB::raw('category')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_expenses', function (Blueprint $table) {
            $table->dropColumn([
                'expense_type', 'expense_date', 'status', 'created_by', 
                'approved_by', 'approved_at', 'receipt_number', 'location', 'notes'
            ]);
        });
    }
};