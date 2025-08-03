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
            // Drop foreign key constraint first
            $table->dropForeign(['sub_category_id']);
            // Drop the index
            $table->dropIndex(['sub_category_id', 'status']);
            // Drop the column
            $table->dropColumn('sub_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add the column back
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');
            // Add the index back
            $table->index(['sub_category_id', 'status']);
        });
    }
};
