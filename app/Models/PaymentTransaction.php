<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'transaction_type',
        'transaction_status',
        'gross_amount',
        'stripe_fee',
        'net_amount',
        'mentor_amount',
        'admin_amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'stripe_charge_id',
        'stripe_refund_id',
        'stripe_transfer_id',
        'payment_method_type',
        'payment_method_last4',
        'payment_method_brand',
        'error_message',
        'error_code',
        'payment_attempts',
        'metadata',
        'description',
        'transfer_status',
        'transfer_amount',
        'transfer_destination_account',
        'transfer_created_at',
        'transfer_completed_at',
        'transfer_failure_reason',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'stripe_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'mentor_amount' => 'decimal:2',
        'admin_amount' => 'decimal:2',
        'transfer_amount' => 'decimal:2',
        'metadata' => 'array',
        'transfer_created_at' => 'datetime',
        'transfer_completed_at' => 'datetime',
    ];

    /**
     * Get the enrollments associated with this transaction.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(UserEnrollment::class);
    }

    /**
     * Check if transfer has been created.
     */
    public function hasTransfer(): bool
    {
        return !empty($this->stripe_transfer_id);
    }

    /**
     * Check if transfer is completed.
     */
    public function isTransferCompleted(): bool
    {
        return $this->transfer_status == 'completed';
    }

    /**
     * Check if transfer has failed.
     */
    public function isTransferFailed(): bool
    {
        return $this->transfer_status == 'failed';
    }

    /**
     * Check if transfer is pending.
     */
    public function isTransferPending(): bool
    {
        return $this->transfer_status == 'pending';
    }

    /**
     * Mark transfer as completed.
     */
    public function markTransferCompleted(): void
    {
        $this->update([
            'transfer_status' => 'completed',
            'transfer_completed_at' => now(),
        ]);
    }

    /**
     * Mark transfer as failed.
     */
    public function markTransferFailed(string $reason = null): void
    {
        $this->update([
            'transfer_status' => 'failed',
            'transfer_failure_reason' => $reason,
        ]);
    }

    /**
     * Scope to only include transactions with pending transfers.
     */
    public function scopeWithPendingTransfers($query)
    {
        return $query->where('transfer_status', 'pending')
                    ->whereNotNull('stripe_transfer_id');
    }

    /**
     * Scope to only include transactions with completed transfers.
     */
    public function scopeWithCompletedTransfers($query)
    {
        return $query->where('transfer_status', 'completed');
    }
}
