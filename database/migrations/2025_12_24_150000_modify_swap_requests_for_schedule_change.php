<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mengubah konsep swap_requests dari "tukar dengan orang lain" 
     * menjadi "pengajuan pindah/ubah jadwal sendiri"
     */
    public function up(): void
    {
        // Drop existing indexes and foreign keys first
        Schema::table('swap_requests', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['target_id']);
            $table->dropForeign(['target_assignment_id']);
        });

        // Try to drop indexes if they exist (ignore errors)
        try {
            Schema::table('swap_requests', function (Blueprint $table) {
                $table->dropIndex('idx_swaps_target_status');
            });
        } catch (\Exception $e) {}
        
        try {
            Schema::table('swap_requests', function (Blueprint $table) {
                $table->dropIndex('swap_requests_target_status_index');
            });
        } catch (\Exception $e) {}

        // Drop columns that are not needed
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->dropColumn([
                'target_id',
                'target_assignment_id', 
                'target_response',
                'target_responded_at',
            ]);
        });

        // Rename columns
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->renameColumn('requester_id', 'user_id');
            $table->renameColumn('requester_assignment_id', 'original_assignment_id');
        });

        // Add new columns
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->date('requested_date')->nullable()->after('original_assignment_id');
            $table->tinyInteger('requested_session')->nullable()->after('requested_date');
            $table->string('change_type', 20)->default('reschedule')->after('requested_session');
        });

        // Update status values
        DB::table('swap_requests')
            ->whereIn('status', ['target_approved', 'target_rejected', 'admin_approved', 'admin_rejected'])
            ->update(['status' => 'cancelled']);

        // Rename table
        Schema::rename('swap_requests', 'schedule_change_requests');

        // Update indexes for new structure
        Schema::table('schedule_change_requests', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_schedule_change_user_status');
            $table->index(['status', 'created_at'], 'idx_schedule_change_status_created');
        });
    }

    public function down(): void
    {
        // Rename table back
        Schema::rename('schedule_change_requests', 'swap_requests');

        // Drop new indexes
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->dropIndex('idx_schedule_change_user_status');
            $table->dropIndex('idx_schedule_change_status_created');
        });

        // Drop new columns
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->dropColumn(['requested_date', 'requested_session', 'change_type']);
        });

        // Rename columns back
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->renameColumn('user_id', 'requester_id');
            $table->renameColumn('original_assignment_id', 'requester_assignment_id');
        });

        // Add back old columns
        Schema::table('swap_requests', function (Blueprint $table) {
            $table->foreignId('target_id')->nullable()->constrained('users');
            $table->foreignId('target_assignment_id')->nullable()->constrained('schedule_assignments');
            $table->text('target_response')->nullable();
            $table->timestamp('target_responded_at')->nullable();
        });
    }
};
