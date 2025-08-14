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
        Schema::table('mentors', function (Blueprint $table) {
            $table->string('stripe_connect_account_id')->nullable()->after('verified');
            $table->enum('connect_account_status', ['pending', 'active', 'rejected', 'restricted'])
                  ->default('pending')->after('stripe_connect_account_id');
            $table->timestamp('connect_account_created_at')->nullable()->after('connect_account_status');
            $table->json('connect_account_metadata')->nullable()->after('connect_account_created_at');
            
            // Add index for faster lookups
            $table->index('stripe_connect_account_id');
            $table->index('connect_account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            $table->dropIndex(['stripe_connect_account_id']);
            $table->dropIndex(['connect_account_status']);
            $table->dropColumn([
                'stripe_connect_account_id',
                'connect_account_status', 
                'connect_account_created_at',
                'connect_account_metadata'
            ]);
        });
    }
};