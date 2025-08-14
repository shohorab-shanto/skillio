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
        'type',
        'stripe_connect_account_id',
        'connect_account_status',
        'connect_account_created_at',
        'connect_account_metadata',
    ];

    protected $casts = [
        'certifications' => 'array',
        'working_hours' => 'array',
        'verified' => 'boolean',
        'connect_account_created_at' => 'datetime',
        'connect_account_metadata' => 'array',
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

    /**
     * Get reviews for this mentor.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'mentor_id', 'user_id');
    }

    /**
     * Get average rating for this mentor.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * Get total number of reviews for this mentor.
     */
    public function totalReviews()
    {
        return $this->reviews()->count();
    }

    /**
     * Get rating distribution (count by rating).
     */
    public function ratingDistribution()
    {
        return $this->reviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating');
    }

    /**
     * Get courses created by this mentor.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'mentor_id', 'user_id');
    }

    /**
     * Get approved courses created by this mentor.
     */
    public function approvedCourses(): HasMany
    {
        return $this->courses()->where('status', 'approved');
    }

    /**
     * Get session bookings (time slots) created by this mentor.
     */
    public function sessionBookings(): HasMany
    {
        return $this->hasMany(SessionBooking::class);
    }

    /**
     * Get available time slots created by this mentor.
     */
    public function availableSlots(): HasMany
    {
        return $this->sessionBookings()->availableSlots();
    }

    /**
     * Get booked sessions for this mentor.
     */
    public function bookedSessions(): HasMany
    {
        return $this->sessionBookings()->bookedSessions();
    }

    /**
     * Get total number of courses for this mentor.
     */
    public function totalCourses()
    {
        return $this->courses()->count();
    }

    /**
     * Get total number of approved courses for this mentor.
     */
    public function totalApprovedCourses()
    {
        return $this->approvedCourses()->count();
    }

    /**
     * Get formatted average rating with decimal places.
     */
    public function getFormattedAverageRatingAttribute()
    {
        $rating = $this->averageRating();
        return $rating ? number_format($rating, 1) : '0.0';
    }

    /**
     * Get star rating display (filled and empty stars).
     */
    public function getStarRatingAttribute()
    {
        $rating = $this->averageRating() ?? 0;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return [
            'full_stars' => $fullStars,
            'half_star' => $halfStar,
            'empty_stars' => $emptyStars,
            'rating' => $rating,
            'formatted_rating' => number_format($rating, 1),
        ];
    }

    /**
     * Get recent reviews (latest 5).
     */
    public function recentReviews()
    {
        return $this->reviews()
            ->with('user')
            ->latest()
            ->limit(5);
    }

    /**
     * Get percentage of 5-star reviews.
     */
    public function getFiveStarPercentageAttribute()
    {
        $totalReviews = $this->totalReviews();
        if ($totalReviews == 0) return 0;
        
        $fiveStarReviews = $this->reviews()->where('rating', 5)->count();
        return round(($fiveStarReviews / $totalReviews) * 100, 1);
    }

    /**
     * Check if mentor has excellent reviews (4+ average rating).
     */
    public function hasExcellentReviews()
    {
        return $this->averageRating() >= 4.0;
    }

    /**
     * Get mentor profile summary with reviews.
     */
    public function getProfileSummaryAttribute()
    {
        return [
            'name' => $this->full_name,
            'email' => $this->email,
            'bio' => $this->bio,
            'verified' => $this->verified,
            'available' => $this->isAvailable(),
            'total_courses' => $this->totalApprovedCourses(),
            'total_reviews' => $this->totalReviews(),
            'average_rating' => $this->formatted_average_rating,
            'star_rating' => $this->star_rating,
            'five_star_percentage' => $this->five_star_percentage,
            'has_excellent_reviews' => $this->hasExcellentReviews(),
        ];
    }

    /**
     * Check if mentor has a Stripe Connect account set up.
     */
    public function hasStripeConnectAccount(): bool
    {
        return !empty($this->stripe_connect_account_id);
    }

    /**
     * Check if mentor's Stripe Connect account is active.
     */
    public function hasActiveStripeConnectAccount(): bool
    {
        return $this->hasStripeConnectAccount() && $this->connect_account_status === 'active';
    }

    /**
     * Check if mentor can receive transfers.
     */
    public function canReceiveTransfers(): bool
    {
        return $this->hasActiveStripeConnectAccount();
    }

    /**
     * Scope to only include mentors with active Stripe Connect accounts.
     */
    public function scopeWithActiveStripeAccount($query)
    {
        return $query->where('connect_account_status', 'active')
                    ->whereNotNull('stripe_connect_account_id');
    }
}