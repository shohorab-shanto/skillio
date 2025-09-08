<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'image',
        'created_by_user_id',
        'is_custom',
    ];

    /**
     * Get the category that owns this sub-category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get courses that belong to this sub-category.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'courses_sub_categories');
    }

    /**
     * Get session bookings that belong to this sub-category.
     */
    public function sessionBookings(): BelongsToMany
    {
        return $this->belongsToMany(SessionBooking::class, 'session_bookings_sub_categories');
    }

    /**
     * Get the user who created this sub-category (if custom).
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Scope to get only custom subcategories created by users.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_custom', true);
    }

    /**
     * Scope to get only system-created subcategories.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_custom', false);
    }

    /**
     * Scope to get subcategories created by a specific user.
     */
    public function scopeCreatedByUser($query, $userId)
    {
        return $query->where('created_by_user_id', $userId);
    }
}
