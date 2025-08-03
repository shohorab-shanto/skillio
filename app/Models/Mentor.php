<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'photo',
        'work_experience',
        'certifications',
        'availability',
        'working_hours',
        'verified',
    ];

    protected $casts = [
        'certifications' => 'array',
        'working_hours' => 'array',
        'verified' => 'boolean',
    ];

    /**
     * Get the user that owns the mentor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include verified mentors.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    /**
     * Scope a query to only include available mentors.
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available');
    }

    /**
     * Check if the mentor is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Check if the mentor is available.
     */
    public function isAvailable(): bool
    {
        return $this->availability === 'available';
    }

    /**
     * Get the mentor's full name from the related user.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Get the mentor's email from the related user.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }
}