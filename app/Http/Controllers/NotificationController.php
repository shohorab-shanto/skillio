<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => NotificationService::getUnreadCount($user->id),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ]
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $notificationId)
    {
        $user = Auth::user();
        
        $notification = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->find($notificationId);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'unread_count' => NotificationService::getUnreadCount($user->id)
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        $count = NotificationService::markAllAsRead($user->id);

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
            'unread_count' => 0
        ]);
    }

    /**
     * Get unread notifications count for the authenticated user.
     */
    public function getUnreadCount(Request $request)
    {
        $user = Auth::user();
        
        $count = NotificationService::getUnreadCount($user->id);

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Get recent notifications for the notification dropdown.
     */
    public function getRecent(Request $request)
    {
        $user = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'icon_class' => $notification->icon_class,
                'icon_color_class' => $notification->icon_color_class,
                'redirect_url' => $notification->redirect_url,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $notification->created_at->diffForHumans(),
                'is_read' => $notification->isRead(),
            ];
        });

        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => NotificationService::getUnreadCount($user->id)
        ]);
    }
}
