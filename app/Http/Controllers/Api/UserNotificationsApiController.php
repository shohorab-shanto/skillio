<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationsApiController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Transform notifications
        $notifications->getCollection()->transform(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'icon_class' => $notification->icon_class ?? 'fa-bell',
                'icon_color_class' => $notification->icon_color_class ?? 'text-blue-500',
                'redirect_url' => $notification->redirect_url,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $notification->created_at->diffForHumans(),
                'is_read' => $notification->isRead(),
                'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
                'data' => $notification->data,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->items(),
                'unread_count' => NotificationService::getUnreadCount($user->id),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ]
            ]
        ]);
    }

    /**
     * Mark a specific notification as read
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
            'data' => [
                'unread_count' => NotificationService::getUnreadCount($user->id)
            ]
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        $updatedCount = NotificationService::markAllAsRead($user->id);

        return response()->json([
            'success' => true,
            'message' => "Marked {$updatedCount} notifications as read",
            'data' => [
                'updated_count' => $updatedCount,
                'unread_count' => NotificationService::getUnreadCount($user->id)
            ]
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request)
    {
        $user = Auth::user();
        
        $unreadCount = NotificationService::getUnreadCount($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * Get recent notifications (last 10)
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
                'icon_class' => $notification->icon_class ?? 'fa-bell',
                'icon_color_class' => $notification->icon_color_class ?? 'text-blue-500',
                'redirect_url' => $notification->redirect_url,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $notification->created_at->diffForHumans(),
                'is_read' => $notification->isRead(),
                'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $formattedNotifications,
                'unread_count' => NotificationService::getUnreadCount($user->id)
            ]
        ]);
    }

    /**
     * Delete a specific notification
     */
    public function destroy(string $notificationId)
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

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
            'data' => [
                'unread_count' => NotificationService::getUnreadCount($user->id)
            ]
        ]);
    }

    /**
     * Delete all notifications for the authenticated user
     */
    public function destroyAll(Request $request)
    {
        $user = Auth::user();
        
        $deletedCount = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} notifications",
            'data' => [
                'deleted_count' => $deletedCount,
                'unread_count' => 0
            ]
        ]);
    }
}
