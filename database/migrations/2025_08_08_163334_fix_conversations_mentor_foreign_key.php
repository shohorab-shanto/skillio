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
            // Drop existing foreign key constraint
            $table->dropForeign(['mentor_id']);
            
            // Add new foreign key constraint to mentors table
            $table->foreign('mentor_id')->references('id')->on('mentors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop the mentors foreign key
            $table->dropForeign(['mentor_id']);
            
            // Restore original foreign key to users table
            $table->foreign('mentor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
