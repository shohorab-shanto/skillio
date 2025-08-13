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
        Schema::create('user_enrollments', function (Blueprint $table) {
            $table->id();
            
            // User and Enrollment Info
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('enrollment_status', ['pending', 'active', 'completed', 'cancelled', 'refunded'])->default('pending');
            
            // Polymorphic Reference
            $table->string('enrollable_type');  // 'App\Models\SessionBooking' or 'App\Models\Course'
            $table->unsignedBigInteger('enrollable_id'); // ID of the specific session/course
            
            // Payment Information
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method', 50)->nullable(); // 'stripe', 'paypal', etc.
            $table->string('payment_reference', 255)->nullable(); // Stripe payment ID, etc.
            $table->foreignId('payment_transaction_id')->nullable()->constrained()->onDelete('set null'); // Link to payment_transactions table
            
            // Enrollment Details
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Progress Tracking
            $table->decimal('progress_percentage', 5, 2)->default(0.00); // 0.00 to 100.00
            $table->timestamp('last_accessed_at')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id']);
            $table->index(['enrollment_status']);
            $table->index(['enrollable_type', 'enrollable_id']);
            $table->index(['payment_status']);
            $table->index(['payment_transaction_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_enrollments');
    }
};
