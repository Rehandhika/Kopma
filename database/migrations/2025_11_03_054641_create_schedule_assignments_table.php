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
        Schema::create('schedule_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday']);
            $table->enum('session', ['1', '2', '3']);
            $table->date('date'); // Tanggal spesifik
            $table->time('time_start');
            $table->time('time_end');
            $table->enum('status', ['scheduled', 'completed', 'missed', 'swapped', 'excused'])->default('scheduled');
            $table->foreignId('swapped_to_user_id')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index('schedule_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_assignments');
    }
};
