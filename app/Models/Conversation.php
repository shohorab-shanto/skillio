<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'mentor_id',
        'user_id',
        'unique_code',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the mentor that owns the conversation.
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    /**
     * Get the mentor user through the mentor relationship.
     */
    public function mentorUser()
    {
        return $this->mentor->user ?? null;
    }

    /**
     * Get the user (student) that owns the conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all messages for the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message for the conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get unread messages count for a specific user.
     */
    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Check if user can access this conversation.
     */
    public function canAccess($userId)
    {
        // Check if user is the student in this conversation
        if ($this->user_id == $userId) {
            return true;
        }
        
        // Check if user is the mentor in this conversation
        $mentor = $this->mentor;
        if ($mentor && $mentor->user_id == $userId) {
            return true;
        }
        
        return false;
    }

    /**
     * Generate a unique 15-digit code for the conversation.
     */
    public static function generateUniqueCode()
    {
        do {
            $code = str_pad(random_int(0, 999999999999999), 15, '0', STR_PAD_LEFT);
        } while (self::where('unique_code', $code)->exists());
        
        return $code;
    }

    /**
     * Find conversation by unique code.
     */
    public static function findByCode($code)
    {
        return self::where('unique_code', $code)->first();
    }

    /**
     * Boot method to automatically generate unique code on creation.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($conversation) {
            if (empty($conversation->unique_code)) {
                $conversation->unique_code = self::generateUniqueCode();
            }
        });
    }
}
