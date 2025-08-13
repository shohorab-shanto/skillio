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
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'stripe_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'mentor_amount' => 'decimal:2',
        'admin_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the enrollments associated with this transaction.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(UserEnrollment::class);
    }
}
