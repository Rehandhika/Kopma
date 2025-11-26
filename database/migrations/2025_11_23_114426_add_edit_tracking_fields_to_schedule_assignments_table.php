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
        Schema::table('schedule_assignments', function (Blueprint $table) {
            $table->foreignId('edited_by')->nullable()->constrained('users')->after('notes');
            $table->timestamp('edited_at')->nullable()->after('edited_by');
            $table->text('edit_reason')->nullable()->after('edited_at');
            $table->json('previous_values')->nullable()->after('edit_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_assignments', function (Blueprint $table) {
            $table->dropForeign(['edited_by']);
            $table->dropColumn(['edited_by', 'edited_at', 'edit_reason', 'previous_values']);
        });
    }
};
