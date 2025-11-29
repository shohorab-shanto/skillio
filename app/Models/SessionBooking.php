<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'mentor_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'fee',
        'currency',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'fee' => 'decimal:2',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            SubCategory::class, 
            'session_bookings_sub_categories',
            'session_booking_id',
            'sub_category_id'
        )->withTimestamps();
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class, 'session_id');
    }

    // Scopes
    public function scopeAvailableSlots($query)
    {
        return $query->whereNull('user_id')->where('status', 'active');
    }

    public function scopeBookedSessions($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeByMentor($query, $mentorId)
    {
        return $query->where('mentor_id', $mentorId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    // Accessors & Mutators
    public function getIsAvailableAttribute(): bool
    {
        return is_null($this->user_id) && $this->status == 'active';
    }

    public function getIsBookedAttribute(): bool
    {
        return !is_null($this->user_id);
    }

    public function getDurationInMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $end->diffInMinutes($start);
    }

    public function getFormattedTimeSlotAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getHasNotStartedAttribute(): bool
    {
        $sessionDateTime = $this->date->format('Y-m-d') . ' ' . $this->start_time->format('H:i:s');
        return $sessionDateTime > now();
    }

    // Methods
    public function bookSession($userId): bool
    {
        if ($this->is_available) {
            $this->update([
                'user_id' => $userId,
                'status' => 'booked'
            ]);
            return true;
        }
        return false;
    }

    public function cancelSession(): bool
    {
        $this->update([
            'user_id' => null,
            'status' => 'cancelled'
        ]);
        return true;
    }

    public function completeSession(): bool
    {
        $this->update(['status' => 'completed']);
        return true;
    }

    public function markAsPaid(): bool
    {
        $this->update(['payment_status' => 'paid']);
        return true;
    }
}
