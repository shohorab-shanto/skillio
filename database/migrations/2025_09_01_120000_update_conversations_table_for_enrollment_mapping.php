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
        // Check if enrollment_id column exists, if not add it
        if (!Schema::hasColumn('conversations', 'enrollment_id')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->foreignId('enrollment_id')->nullable()->constrained('user_enrollments')->onDelete('cascade');
            });
        }
        
        // Check if enrollment_id index exists, if not add it
        $indexes = \DB::select("SHOW INDEX FROM conversations WHERE Key_name = 'conversations_enrollment_id_index'");
        if (empty($indexes)) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index('enrollment_id');
            });
        }
        
        // Check if enrollment_id unique constraint exists, if not add it
        $uniqueIndexes = \DB::select("SHOW INDEX FROM conversations WHERE Key_name = 'conversations_enrollment_id_unique'");
        if (empty($uniqueIndexes)) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->unique('enrollment_id');
            });
        }
        
        // Check if status column exists, if yes remove it
        if (Schema::hasColumn('conversations', 'status')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Restore status field
            $table->string('status')->default('active');
            
            // Remove enrollment_id field
            $table->dropForeign(['enrollment_id']);
            $table->dropIndex(['enrollment_id']);
            $table->dropUnique(['enrollment_id']);
            $table->dropColumn('enrollment_id');
        });
    }
};
