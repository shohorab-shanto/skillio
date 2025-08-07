<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'gdpr_consent',
        'status',
        'apple_id', // Added for Apple authentication
        'google_id', // Added for Google authentication
        'phone',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a mentor.
     *
     * @return bool
     */
    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    /**
     * Check if the user is a regular user/student.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user' || $this->role === 'student';
    }

    /**
     * Get the mentor profile for this user.
     */
    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }

    /**
     * Get reviews written by this user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get reviews received by this user (as a mentor).
     */
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'mentor_id');
    }

    /**
     * Get average rating for this user as a mentor.
     */
    public function averageRating()
    {
        return $this->receivedReviews()->avg('rating');
    }

    /**
     * Get total number of reviews for this user as a mentor.
     */
    public function totalReviews()
    {
        return $this->receivedReviews()->count();
    }

    /**
     * Get courses created by this user (if they are a mentor).
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'mentor_id');
    }

    /**
     * Get session bookings where this user is the student.
     */
    public function sessionBookings()
    {
        return $this->hasMany(SessionBooking::class);
    }

    /**
     * Get booked sessions for this user (same as sessionBookings since user_id is the foreign key).
     */
    public function bookedSessions()
    {
        return $this->sessionBookings();
    }
}
