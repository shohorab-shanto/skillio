<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Course;
use App\Models\SessionBooking;
use App\Models\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Create a course enrollment notification for mentor.
     */
    public static function createCourseEnrollmentNotification(User $user, Course $course): void
    {
        $mentor = $course->mentor->user;
        
        $notification = Notification::create([
            'id' => Str::uuid(),
            'type' => 'course_enrollment',
            'notifiable_type' => User::class,
            'notifiable_id' => $mentor->id,
            'entity_type' => Course::class,
            'entity_id' => $course->id,
            'title' => 'New Course Enrollment',
            'message' => "{$user->name} enrolled in your course '{$course->title}'",
            'data' => [
                'user_name' => $user->name,
                'course_title' => $course->title,
                'course_id' => $course->id,
            ]
        ]);

        // Broadcast real-time notification
        self::broadcastNotification($notification);
    }

    /**
     * Create a session booking notification for mentor.
     */
    public static function createSessionBookingNotification(User $user, SessionBooking $session): void
    {
        $mentor = $session->mentor->user;
        
        // Format date and time properly
        $formattedDate = $session->date->format('M d, Y');
        $formattedTime = $session->start_time->format('H:i') . ' - ' . $session->end_time->format('H:i');
        
        $notification = Notification::create([
            'id' => Str::uuid(),
            'type' => 'session_booking',
            'notifiable_type' => User::class,
            'notifiable_id' => $mentor->id,
            'entity_type' => SessionBooking::class,
            'entity_id' => $session->id,
            'title' => 'New Session Booking',
            'message' => "{$user->name} booked your session on {$formattedDate} at {$formattedTime}",
            'data' => [
                'user_name' => $user->name,
                'session_date' => $formattedDate,
                'session_time' => $formattedTime,
                'session_id' => $session->id,
            ]
        ]);

        // Broadcast real-time notification
        self::broadcastNotification($notification);
    }

    /**
     * Create a new message notification when user is not active.
     */
    public static function createNewMessageNotification(Message $message, User $recipient): void
    {
        // Check if recipient is active in the conversation
        if (self::isUserActiveInConversation($recipient->id, $message->conversation_id)) {
            return; // Don't create notification if user is active
        }

        $sender = $message->sender;
        $conversation = $message->conversation;
        
        // Determine if recipient is mentor or user
        $isRecipientMentor = $conversation->mentor->user_id === $recipient->id;
        $otherPartyName = $isRecipientMentor ? $conversation->user->name : $conversation->mentor->user->name;

        $notification = Notification::create([
            'id' => Str::uuid(),
            'type' => 'new_message',
            'notifiable_type' => User::class,
            'notifiable_id' => $recipient->id,
            'entity_type' => Message::class,
            'entity_id' => $message->id,
            'title' => 'New Message',
            'message' => "{$otherPartyName} sent you a message",
            'data' => [
                'sender_name' => $otherPartyName,
                'message_preview' => Str::limit($message->content, 50),
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
            ]
        ]);

        // Broadcast real-time notification
        self::broadcastNotification($notification);
    }

    /**
     * Create a course approval notification for mentor.
     */
    public static function createCourseApprovalNotification(Course $course, string $action): void
    {
        $mentor = $course->mentor->user;
        
        $notification = Notification::create([
            'id' => Str::uuid(),
            'type' => $action, // course_approved, course_rejected, course_disapproved
            'notifiable_type' => User::class,
            'notifiable_id' => $mentor->id,
            'entity_type' => Course::class,
            'entity_id' => $course->id,
            'title' => ucfirst(str_replace('course_', '', $action)) . ' Course',
            'message' => "Your course '{$course->title}' has been " . str_replace('course_', '', $action),
            'data' => [
                'course_title' => $course->title,
                'course_id' => $course->id,
                'action' => $action,
            ]
        ]);

        // Broadcast real-time notification
        self::broadcastNotification($notification);
    }

    /**
     * Create a notification for admin when mentor creates a new course.
     */
    public static function createCourseCreatedNotification(Course $course): void
    {
        // Get all admin users
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            $notification = Notification::create([
                'id' => Str::uuid(),
                'type' => 'course_created',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'entity_type' => Course::class,
                'entity_id' => $course->id,
                'title' => 'New Course Created',
                'message' => "Mentor {$course->mentor->user->name} created a new course '{$course->title}'",
                'data' => [
                    'mentor_name' => $course->mentor->user->name,
                    'course_title' => $course->title,
                    'course_id' => $course->id,
                    'action' => 'course_created',
                ]
            ]);

            // Broadcast real-time notification
            self::broadcastNotification($notification);
        }
    }

    /**
     * Create a notification for admin when mentor updates a course.
     */
    public static function createCourseUpdatedNotification(Course $course, bool $needsReapproval = false): void
    {
        // Get all admin users
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            $action = $needsReapproval ? 'course_updated_reapproval' : 'course_updated';
            $message = $needsReapproval 
                ? "Mentor {$course->mentor->user->name} updated course '{$course->title}' - requires reapproval"
                : "Mentor {$course->mentor->user->name} updated course '{$course->title}'";
            
            $notification = Notification::create([
                'id' => Str::uuid(),
                'type' => $action,
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'entity_type' => Course::class,
                'entity_id' => $course->id,
                'title' => $needsReapproval ? 'Course Updated - Reapproval Required' : 'Course Updated',
                'message' => $message,
                'data' => [
                    'mentor_name' => $course->mentor->user->name,
                    'course_title' => $course->title,
                    'course_id' => $course->id,
                    'action' => $action,
                    'needs_reapproval' => $needsReapproval,
                ]
            ]);

            // Broadcast real-time notification
            self::broadcastNotification($notification);
        }
    }

    /**
     * Check if user is active in a conversation.
     * User is considered active if they've read recent messages within the last 2 minutes.
     */
    public static function isUserActiveInConversation(int $userId, int $conversationId): bool
    {
        // Get the most recent message from the other person in this conversation
        $recentMessage = Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->latest()
            ->first();

        if (!$recentMessage) {
            return false; // No messages to read, consider inactive
        }

        // Check if user has read the recent message within last 2 minutes
        // This means they're likely still active in the conversation
        return $recentMessage->read_at && $recentMessage->read_at->diffInMinutes(now()) < 2;
    }

    /**
     * Broadcast notification via WebSocket.
     */
    public static function broadcastNotification(Notification $notification): void
    {
        // Broadcast to the specific user's private channel
        broadcast(new \App\Events\NotificationSent($notification));
    }

    /**
     * Mark notification as read.
     */
    public static function markAsRead(string $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsRead(int $userId): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread notifications count for a user.
     */
    public static function getUnreadCount(int $userId): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
