<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_status',
        'enrollable_type',
        'enrollable_id',
        'amount',
        'currency',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'enrolled_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'progress_percentage',
        'last_accessed_at',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'amount' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the enrollable entity (session or course).
     */
    public function enrollable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the payment transaction for this enrollment.
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    /**
     * Get the conversation for this enrollment.
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    /**
     * Create a conversation for this enrollment.
     */
    public function createConversation()
    {
        if (!$this->conversation) {
            return Conversation::create([
                'enrollment_id' => $this->id,
                'mentor_id' => $this->enrollable->mentor->id,
                'user_id' => $this->user_id,
                'unique_code' => Conversation::generateUniqueCode(),
            ]);
        }
        return $this->conversation;
    }
}
