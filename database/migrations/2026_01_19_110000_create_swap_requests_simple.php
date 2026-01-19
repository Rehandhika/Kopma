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
        Schema::create('swap_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('requester_assignment_id');
            $table->unsignedBigInteger('target_assignment_id');
            $table->text('reason');
            $table->enum('status', [
                'pending',
                'target_approved',
                'target_rejected',
                'admin_approved',
                'admin_rejected',
                'cancelled'
            ])->default('pending');
            $table->text('target_response')->nullable();
            $table->timestamp('target_responded_at')->nullable();
            $table->text('admin_response')->nullable();
            $table->unsignedBigInteger('admin_responded_by')->nullable();
            $table->timestamp('admin_responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('requester_id');
            $table->index('target_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_requests');
    }
};
