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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors', 'user_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->string('cover_photo')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0)->comment('Discount percentage (0-100)');
            $table->integer('duration_days')->default(30);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable()->comment('Admin note when rejected');
            $table->boolean('needs_reapproval')->default(false)->comment('True if mentor edits after approval');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['mentor_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['sub_category_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
