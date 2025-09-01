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
        Schema::table('conversations', function (Blueprint $table) {
            // Remove the old unique constraint on mentor_id and user_id
            // This constraint prevents multiple conversations per mentor-user pair
            // But we now allow multiple conversations (one per enrollment)
            $table->dropUnique(['mentor_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Restore the unique constraint
            $table->unique(['mentor_id', 'user_id']);
        });
    }
};
