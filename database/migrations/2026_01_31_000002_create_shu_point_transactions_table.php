<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('sale_id')->nullable()->constrained('sales');
            $table->enum('type', ['earn', 'redeem', 'adjust']);
            $table->unsignedInteger('amount')->nullable();
            $table->unsignedInteger('percentage_bps')->default(0);
            $table->bigInteger('points');
            $table->unsignedInteger('cash_amount')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['student_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->unique(['sale_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_point_transactions');
    }
};
