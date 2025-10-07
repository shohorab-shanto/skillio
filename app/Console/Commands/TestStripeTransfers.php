<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mentor;
use App\Models\SessionBooking;
use App\Models\PaymentTransaction;
use App\Models\UserEnrollment;

class TestStripeTransfers extends Command
{
    protected $signature = 'test:stripe-transfers {--mentor-id= : Test specific mentor ID}';
    protected $description = 'Test Stripe Connect transfer functionality';

    public function handle()
    {
        $this->info('🧪 Testing Stripe Connect Transfer Functionality');
        $this->info('===============');
        $this->newLine();

        // 1. Check mentors with Stripe Connect accounts
        $this->checkMentorsWithConnectAccounts();
        
        // 2. Check recent transactions and their transfer status
        $this->checkRecentTransactions();
        
        // 3. Show transfer statistics
        $this->showTransferStats();
        
        // 4. Test specific mentor if provided
        if ($mentorId = $this->option('mentor-id')) {
            $this->testSpecificMentor($mentorId);
        }
    }

    private function checkMentorsWithConnectAccounts()
    {
        $this->info('📊 Mentors with Stripe Connect Accounts:');
        
        $mentors = Mentor::whereNotNull('stripe_connect_account_id')->get();
        
        if ($mentors->isEmpty()) {
            $this->warn('   No mentors have Stripe Connect accounts set up');
            return;
        }
        
        foreach ($mentors as $mentor) {
            $status = $mentor->canReceiveTransfers() ? '✅ Active' : '❌ Inactive';
            $this->line("   - {$mentor->user->name} ({$mentor->stripe_connect_account_id}) - {$status}");
        }
        
        $this->newLine();
    }

    private function checkRecentTransactions()
    {
        $this->info('💳 Recent Payment Transactions (Last 10):');
        
        $transactions = PaymentTransaction::with(['enrollments.enrollable.mentor.user'])
            ->latest()
            ->limit(10)
            ->get();
            
        if ($transactions->isEmpty()) {
            $this->warn('   No transactions found');
            return;
        }
        
        foreach ($transactions as $transaction) {
            $enrollment = $transaction->enrollments->first();
            $mentorName = $enrollment ? $enrollment->enrollable->mentor->user->name : 'Unknown';
            
            $transferStatus = $transaction->transfer_status ?? 'No Transfer';
            $transferAmount = $transaction->transfer_amount ? '$' . number_format($transaction->transfer_amount, 2) : 'N/A';
            
            $statusIcon = match($transferStatus) {
                'completed' => '✅',
                'pending' => '⏳',
                'failed' => '❌',
                'cancelled' => '🚫',
                default => '➖'
            };
            
            $this->line("   {$statusIcon} {$transaction->transaction_id} - {$mentorName} - Transfer: {$transferStatus} ({$transferAmount})");
        }
        
        $this->newLine();
    }

    private function showTransferStats()
    {
        $this->info('📈 Transfer Statistics:');
        
        $totalTransactions = PaymentTransaction::count();
        $transactionsWithTransfers = PaymentTransaction::whereNotNull('stripe_transfer_id')->count();
        $completedTransfers = PaymentTransaction::where('transfer_status', 'completed')->count();
        $pendingTransfers = PaymentTransaction::where('transfer_status', 'pending')->count();
        $failedTransfers = PaymentTransaction::where('transfer_status', 'failed')->count();
        
        $totalTransferAmount = PaymentTransaction::where('transfer_status', 'completed')->sum('transfer_amount');
        
        $this->line("   - Total Transactions: {$totalTransactions}");
        $this->line("   - Transactions with Transfers: {$transactionsWithTransfers}");
        $this->line("   - Completed Transfers: {$completedTransfers}");
        $this->line("   - Pending Transfers: {$pendingTransfers}");
        $this->line("   - Failed Transfers: {$failedTransfers}");
        $this->line("   - Total Transfer Amount: $" . number_format($totalTransferAmount, 2));
        
        $this->newLine();
    }

    private function testSpecificMentor($mentorId)
    {
        $this->info("🔍 Testing Mentor ID: {$mentorId}");
        
        $mentor = Mentor::find($mentorId);
        
        if (!$mentor) {
            $this->error("   Mentor not found!");
            return;
        }
        
        $this->line("   - Name: {$mentor->user->name}");
        $this->line("   - Connect Account: " . ($mentor->stripe_connect_account_id ?? 'Not Set'));
        $this->line("   - Account Status: {$mentor->connect_account_status}");
        $this->line("   - Can Receive Transfers: " . ($mentor->canReceiveTransfers() ? 'YES' : 'NO'));
        
        // Check mentor's recent transactions
        $mentorTransactions = PaymentTransaction::whereHas('enrollments', function($query) use ($mentorId) {
            $query->whereHas('enrollable', function($subQuery) use ($mentorId) {
                $subQuery->where('mentor_id', $mentorId);
            });
        })->latest()->limit(5)->get();
        
        if ($mentorTransactions->isNotEmpty()) {
            $this->line("   - Recent Transactions:");
            foreach ($mentorTransactions as $transaction) {
                $status = $transaction->transfer_status ?? 'No Transfer';
                $amount = $transaction->transfer_amount ? '$' . number_format($transaction->transfer_amount, 2) : 'N/A';
                $this->line("     * {$transaction->transaction_id} - {$status} ({$amount})");
            }
        }
    }
}
