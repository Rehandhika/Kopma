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
        Schema::table('attendances', function (Blueprint $table) {
            // Add indexes for performance on attendance queries
            $table->index(['check_in', 'check_out'], 'idx_attendance_times');
            $table->index(['date', 'check_in', 'check_out'], 'idx_attendance_today');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_times');
            $table->dropIndex('idx_attendance_today');
        });
    }
};
