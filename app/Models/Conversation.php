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
        'enrollment_id',
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
     * Get the enrollment that owns the conversation.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(UserEnrollment::class, 'enrollment_id');
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
     * Check if conversation is active based on enrollment validity.
     */
    public function isConversationActive()
    {
        if (!$this->enrollment) {
            return false;
        }

        $enrollment = $this->enrollment;
        
        if ($enrollment->enrollable_type == 'App\Models\SessionBooking') {
            $session = $enrollment->enrollable;
            $now = now();
            
            // Get raw values to avoid casting issues
            $sessionDate = $session->getRawOriginal('date');
            $sessionStartTime = $session->getRawOriginal('start_time');
            $sessionEndTime = $session->getRawOriginal('end_time');
            
            // Combine date and time to create full datetime
            $sessionStartDateTime = \Carbon\Carbon::parse($sessionDate . ' ' . $sessionStartTime);
            $sessionEndDateTime = \Carbon\Carbon::parse($sessionDate . ' ' . $sessionEndTime);
            
            // Allow chat 1 hour before session and 24 hours after
            $preSessionTime = $sessionStartDateTime->subHour();
            $postSessionTime = $sessionEndDateTime->addDay();
            
            return $now->between($preSessionTime, $postSessionTime);
        }
        
        if ($enrollment->enrollable_type == 'App\Models\Course') {
            $course = $enrollment->enrollable;
            
            if (!$course) {
                return false;
            }
            
            $enrolledAt = $enrollment->enrolled_at;
            
            // Course validity period - use duration_days or duration_hours
            $durationDays = 365; // Default to 1 year
            
            if ($course->duration_type === 'hours' && $course->duration_hours) {
                // Convert hours to days (assuming 8 hours per day)
                $durationDays = ceil($course->duration_hours / 8);
            } elseif ($course->duration_type === 'days' && $course->duration_days) {
                $durationDays = $course->duration_days;
            } elseif ($course->duration_days) {
                // Fallback to duration_days if duration_type not set
                $durationDays = $course->duration_days;
            }
            
            $courseEndDate = $enrolledAt->copy()->addDays($durationDays);
            
            return now()->lte($courseEndDate);
        }
        
        return false;
    }

    /**
     * Get the validity period for this conversation.
     */
    public function getValidityPeriod()
    {
        if (!$this->enrollment) {
            return null;
        }

        $enrollment = $this->enrollment;
        
        if ($enrollment->enrollable_type == 'App\Models\SessionBooking') {
            $session = $enrollment->enrollable;
            
            // Get raw values to avoid casting issues
            $sessionDate = $session->getRawOriginal('date');
            $sessionStartTime = $session->getRawOriginal('start_time');
            $sessionEndTime = $session->getRawOriginal('end_time');
            
            // Combine date and time to create full datetime
            $sessionStartDateTime = \Carbon\Carbon::parse($sessionDate . ' ' . $sessionStartTime);
            $sessionEndDateTime = \Carbon\Carbon::parse($sessionDate . ' ' . $sessionEndTime);
            
            return [
                'start' => $sessionStartDateTime->subHour(),
                'end' => $sessionEndDateTime->addDay(),
                'type' => 'session'
            ];
        }
        
        if ($enrollment->enrollable_type == 'App\Models\Course') {
            $course = $enrollment->enrollable;
            
            if (!$course) {
                return null;
            }
            
            // Calculate duration in days
            $durationDays = 365; // Default to 1 year
            
            if ($course->duration_type === 'hours' && $course->duration_hours) {
                // Convert hours to days (assuming 8 hours per day)
                $durationDays = ceil($course->duration_hours / 8);
            } elseif ($course->duration_type === 'days' && $course->duration_days) {
                $durationDays = $course->duration_days;
            } elseif ($course->duration_days) {
                // Fallback to duration_days if duration_type not set
                $durationDays = $course->duration_days;
            }
            
            $courseEndDate = $enrollment->enrolled_at->copy()->addDays($durationDays);
            
            return [
                'start' => $enrollment->enrolled_at,
                'end' => $courseEndDate,
                'type' => 'course'
            ];
        }
        
        return null;
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
        \Log::info('findByCode called with:', ['code' => $code]);
        
        $conversation = self::where('unique_code', $code)->first();
        
        \Log::info('findByCode result:', [
            'code_searched' => $code,
            'conversation_found' => $conversation ? 'yes' : 'no',
            'conversation_id' => $conversation ? $conversation->id : 'null',
            'conversation_code' => $conversation ? $conversation->unique_code : 'null',
            'mentor_id' => $conversation ? $conversation->mentor_id : 'null',
            'user_id' => $conversation ? $conversation->user_id : 'null'
        ]);
        
        return $conversation;
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
