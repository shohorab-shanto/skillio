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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->text('work_experience')->nullable();
            $table->json('certifications')->nullable(); // Store certifications as JSON
            $table->enum('availability', ['available', 'unavailable'])->default('available');
            $table->json('working_hours')->nullable(); // Store working hours as JSON
            $table->boolean('verified')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('availability');
            $table->index('verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};