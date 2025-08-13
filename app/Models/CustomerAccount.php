<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAccount extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_customer_id',
        'stripe_payment_method_id',
        'stripe_subscription_id',
        'status',
        'last_payment_at',
        'last_login_at',
        'metadata',
    ];

    protected $casts = [
        'last_payment_at' => 'datetime',
        'last_login_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the customer account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Update last payment timestamp.
     */
    public function updateLastPayment(): void
    {
        $this->update(['last_payment_at' => now()]);
    }

    /**
     * Get customer email (from user relationship).
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    /**
     * Get customer name (from user relationship).
     */
    public function getNameAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Get customer phone (from user relationship).
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->user->phone;
    }

    /**
     * Get customer address (from user relationship).
     */
    public function getAddressAttribute(): ?string
    {
        return $this->user->address;
    }
}
