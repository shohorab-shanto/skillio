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
        Schema::table('courses', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['mentor_id']);
            
            // Add the correct foreign key constraint
            $table->foreign('mentor_id')->references('id')->on('mentors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign(['mentor_id']);
            
            // Restore the original incorrect constraint
            $table->foreign('mentor_id')->references('user_id')->on('mentors')->onDelete('cascade');
        });
    }
};
