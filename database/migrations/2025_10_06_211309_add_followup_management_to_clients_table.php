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
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('is_followup_enabled')->default(false)->after('followup_note');
            $table->string('followup_label')->nullable()->after('is_followup_enabled');
            $table->integer('followup_reminder_hours')->default(24)->after('followup_label');
            $table->datetime('last_contacted_at')->nullable()->after('followup_reminder_hours');
            $table->text('followup_notes')->nullable()->after('last_contacted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'is_followup_enabled',
                'followup_label', 
                'followup_reminder_hours',
                'last_contacted_at',
                'followup_notes'
            ]);
        });
    }
};
