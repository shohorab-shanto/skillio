<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class CheckLatestTransfer extends Command
{
    protected $signature = 'check:latest-transfer {--minutes=5 : Check transactions from last N minutes}';
    protected $description = 'Check the latest payment transaction for transfer details';

    public function handle()
    {
        $minutes = $this->option('minutes');
        
        $this->info("🔍 Checking latest transactions from last {$minutes} minutes...");
        $this->newLine();
        
        // Get recent transactions
        $transactions = PaymentTransaction::where('created_at', '>=', now()->subMinutes($minutes))
            ->latest()
            ->limit(5)
            ->get();
            
        if ($transactions->isEmpty()) {
            $this->warn("No transactions found in the last {$minutes} minutes.");
            $this->info("💡 Try making a payment or increase the --minutes parameter");
            return;
        }
        
        foreach ($transactions as $transaction) {
            $this->displayTransactionInfo($transaction);
            $this->newLine();
        }
    }
    
    private function displayTransactionInfo($transaction)
    {
        $this->info("📋 Transaction: {$transaction->transaction_id}");
        $this->line("   Created: {$transaction->created_at->format('Y-m-d H:i:s')}");
        $this->line("   Status: {$transaction->transaction_status}");
        $this->line("   Amount: \${$transaction->gross_amount}");
        $this->line("   Mentor Share: \${$transaction->mentor_amount}");
        
        // Transfer information
        if ($transaction->stripe_transfer_id) {
            $statusIcon = match($transaction->transfer_status) {
                'completed' => '✅',
                'pending' => '⏳',
                'failed' => '❌',
                'cancelled' => '🚫',
                default => '❓'
            };
            
            $this->line("   Transfer ID: {$transaction->stripe_transfer_id}");
            $this->line("   Transfer Status: {$statusIcon} {$transaction->transfer_status}");
            $this->line("   Transfer Amount: \${$transaction->transfer_amount}");
            $this->line("   Destination: {$transaction->transfer_destination_account}");
            
            if ($transaction->transfer_created_at) {
                $this->line("   Transfer Created: {$transaction->transfer_created_at->format('Y-m-d H:i:s')}");
            }
            
            if ($transaction->transfer_completed_at) {
                $this->line("   Transfer Completed: {$transaction->transfer_completed_at->format('Y-m-d H:i:s')}");
            }
            
            if ($transaction->transfer_failure_reason) {
                $this->error("   Failure Reason: {$transaction->transfer_failure_reason}");
            }
            
        } else {
            $this->warn("   ⚠️  No transfer created (mentor may not have Stripe Connect account)");
        }
    }
}
