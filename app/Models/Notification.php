<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\Course;
use App\Models\SessionBooking;
use App\Models\Message;
use App\Models\Conversation;

class Notification extends DatabaseNotification
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'entity_type',
        'entity_id',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity that owns the notification.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the entity that the notification is about.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Check if the notification is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if the notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Get the redirect URL based on entity type.
     */
    public function getRedirectUrlAttribute(): ?string
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        switch ($this->entity_type) {
            case Course::class:
                return $this->getCourseRedirectUrl();
            case SessionBooking::class:
                // For session bookings, redirect to conversation page
                return $this->getSessionBookingConversationUrl();
            case Message::class:
                // For messages, we need to get the conversation
                $message = Message::find($this->entity_id);
                return $message ? route('chat.show', $message->conversation->unique_code) : null;
            default:
                return null;
        }
    }

    /**
     * Get conversation URL for session booking.
     */
    private function getSessionBookingConversationUrl(): ?string
    {
        $session = SessionBooking::find($this->entity_id);
        if (!$session) {
            return null;
        }

        // Find existing conversation between user and mentor
        $conversation = Conversation::where('mentor_id', $session->mentor_id)
            ->where('user_id', $session->user_id)
            ->first();

        if ($conversation) {
            return route('chat.show', $conversation->unique_code);
        }

        // If no conversation exists, create one and return the URL
        $newConversation = Conversation::create([
            'mentor_id' => $session->mentor_id,
            'user_id' => $session->user_id,
            'last_message_at' => now(),
        ]);

        return route('chat.show', $newConversation->unique_code);
    }

    /**
     * Get course redirect URL based on user role.
     */
    private function getCourseRedirectUrl(): ?string
    {
        $course = Course::find($this->entity_id);
        if (!$course) {
            return null;
        }

        // Check if the current user is the mentor of this course
        if (auth()->check() && auth()->user()->mentor && auth()->user()->mentor->id === $course->mentor_id) {
            // Mentor viewing their own course
            return route('mentor.courses.show', $course->id);
        } else {
            // Admin or other users viewing course
            return route('admin.courses.show', $course->id);
        }
    }

    /**
     * Get the icon class based on notification type.
     */
    public function getIconClassAttribute(): string
    {
        return match($this->type) {
            'course_enrollment', 'session_booking' => 'fa-solid fa-user-plus',
            'new_message' => 'fa-solid fa-message',
            'course_approved' => 'fa-solid fa-check',
            'course_rejected', 'course_disapproved' => 'fa-solid fa-times',
            default => 'fa-solid fa-bell'
        };
    }

    /**
     * Get the icon color class based on notification type.
     */
    public function getIconColorClassAttribute(): string
    {
        return match($this->type) {
            'course_enrollment', 'session_booking' => 'bg-green-100 text-green-600',
            'new_message' => 'bg-blue-100 text-blue-600',
            'course_approved' => 'bg-green-100 text-green-600',
            'course_rejected', 'course_disapproved' => 'bg-red-100 text-red-600',
            default => 'bg-gray-100 text-gray-600'
        };
    }
}
