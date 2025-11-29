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
            // Make duration_days nullable
            $table->integer('duration_days')->nullable()->change();
            
            // Add duration_type and duration_hours columns
            $table->enum('duration_type', ['days', 'hours'])->default('days')->after('duration_days');
            $table->integer('duration_hours')->nullable()->after('duration_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['duration_type', 'duration_hours']);
            
            // Make duration_days not nullable again
            $table->integer('duration_days')->nullable(false)->change();
        });
    }
};
