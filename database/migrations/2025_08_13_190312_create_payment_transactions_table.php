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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            
            // Transaction Details (no direct enrollment reference needed)
            
            // Transaction Details
            $table->string('transaction_id')->unique()->comment('Unique transaction identifier');
            $table->enum('transaction_type', ['payment', 'refund', 'partial_refund'])->default('payment');
            $table->enum('transaction_status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            
            // Amount Breakdown
            $table->decimal('gross_amount', 10, 2)->comment('Total amount paid by customer');
            $table->decimal('stripe_fee', 10, 2)->default(0.00)->comment('Stripe processing fee');
            $table->decimal('net_amount', 10, 2)->comment('Amount after Stripe fees');
            $table->decimal('mentor_amount', 10, 2)->comment('80% of net amount for mentor');
            $table->decimal('admin_amount', 10, 2)->comment('20% of net amount for admin');
            $table->string('currency', 3)->default('USD');
            
            // Stripe Integration
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_refund_id')->nullable();
            $table->string('stripe_transfer_id')->nullable()->comment('Transfer ID for mentor payout');
            
            // Payment Method
            $table->string('payment_method_type')->nullable()->comment('card, bank_transfer, etc.');
            $table->string('payment_method_last4')->nullable()->comment('Last 4 digits of card');
            $table->string('payment_method_brand')->nullable()->comment('visa, mastercard, etc.');
            
            // Error Handling
            $table->text('error_message')->nullable();
            $table->string('error_code')->nullable();
            $table->integer('payment_attempts')->default(0);
            
            // Metadata
            $table->json('metadata')->nullable()->comment('Additional Stripe metadata');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['transaction_status']);
            $table->index(['stripe_payment_intent_id']);
            $table->index(['stripe_customer_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
