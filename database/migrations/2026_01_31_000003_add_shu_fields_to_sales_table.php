<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('cashier_id')->constrained('students');
            $table->unsignedBigInteger('shu_points_earned')->default(0)->after('change_amount');
            $table->unsignedInteger('shu_percentage_bps')->default(0)->after('shu_points_earned');

            $table->index(['student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'date']);
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn(['shu_points_earned', 'shu_percentage_bps']);
        });
    }
};
