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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentor_id')->nullable()->constrained('mentors', 'user_id')->onDelete('cascade');
            $table->unsignedBigInteger('course_id')->nullable()->comment('Will be constrained when courses table is created');
            $table->tinyInteger('rating')->unsigned()->default(1)->comment('Rating from 1 to 5');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['mentor_id', 'rating']);
            $table->index(['course_id', 'rating']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
