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
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('client_type', ['normal', 'subagency'])->default('normal')->after('email')->index();
        });

        // Set all existing clients to 'normal' type
        DB::table('clients')->update(['client_type' => 'normal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['client_type']);
            $table->dropColumn('client_type');
        });
    }
};
