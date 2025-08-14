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
        Schema::table('payment_transactions', function (Blueprint $table) {
            // stripe_transfer_id already exists, so skip it
            $table->enum('transfer_status', ['pending', 'completed', 'failed', 'cancelled'])
                  ->default('pending')->after('stripe_transfer_id');
            $table->decimal('transfer_amount', 10, 2)->nullable()->after('transfer_status');
            $table->string('transfer_destination_account')->nullable()->after('transfer_amount');
            $table->timestamp('transfer_created_at')->nullable()->after('transfer_destination_account');
            $table->timestamp('transfer_completed_at')->nullable()->after('transfer_created_at');
            $table->text('transfer_failure_reason')->nullable()->after('transfer_completed_at');
            
            // Add indexes for faster lookups (skip stripe_transfer_id as it might already have index)
            $table->index('transfer_status');
            $table->index('transfer_destination_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex(['transfer_status']);
            $table->dropIndex(['transfer_destination_account']);
            $table->dropColumn([
                'transfer_status',
                'transfer_amount',
                'transfer_destination_account',
                'transfer_created_at',
                'transfer_completed_at',
                'transfer_failure_reason'
            ]);
        });
    }
};