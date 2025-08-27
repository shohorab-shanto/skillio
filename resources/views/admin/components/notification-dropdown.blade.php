<!-- Admin Notification Dropdown -->
<div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0 }" x-init="
    // Fetch initial notifications
    fetchNotifications();
    
    // Listen for real-time notifications
    Echo.private('user.' + {{ auth()->id() }})
        .listen('notification.sent', (e) => {
            notifications.unshift(e.notification);
            unreadCount++;
        });
">
    <!-- Notification Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-purple-600 transition-colors duration-200">
        <i class="fa-solid fa-bell text-xl"></i>
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0" x-text="unreadCount" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
        </span>
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" @click.away="open = false" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-50">
        
        <!-- Header -->
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <button @click="markAllAsRead()" 
                        class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                    Mark all as read
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="p-6 text-center text-gray-500">
                    <i class="fa-solid fa-bell-slash text-3xl mb-3"></i>
                    <p>No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors duration-200 cursor-pointer"
                     @click="handleNotificationClick(notification)">
                    
                    <!-- Notification Icon -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                 :class="getNotificationIconClass(notification.type)">
                                <i :class="getNotificationIcon(notification.type)" class="text-sm"></i>
                            </div>
                        </div>
                        
                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 mt-2" x-text="formatTime(notification.created_at)"></p>
                        </div>
                        
                        <!-- Unread Indicator -->
                        <div x-show="!notification.read_at" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-100">
            <a href="#" class="block text-center text-sm text-purple-600 hover:text-purple-700 font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function fetchNotifications() {
    fetch('/notifications/recent')
        .then(response => response.json())
        .then(data => {
            this.notifications = data.notifications || [];
            this.unreadCount = data.unread_count || 0;
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.notifications.forEach(n => n.read_at = new Date().toISOString());
            this.unreadCount = 0;
        }
    })
    .catch(error => console.error('Error marking notifications as read:', error));
}

function handleNotificationClick(notification) {
    // Mark as read
    fetch(`/notifications/${notification.id}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notification.read_at = new Date().toISOString();
            if (this.unreadCount > 0) this.unreadCount--;
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));

    // Redirect based on notification type
    if (notification.entity_type === 'App\\Models\\Course') {
        window.location.href = `/admin/courses/${notification.entity_id}`;
    } else if (notification.entity_type === 'App\\Models\\SessionBooking') {
        window.location.href = `/admin/sessions/${notification.entity_id}`;
    }
    
    this.open = false;
}

function getNotificationIcon(type) {
    const icons = {
        'course_created': 'fa-solid fa-plus',
        'course_updated': 'fa-solid fa-edit',
        'course_updated_reapproval': 'fa-solid fa-exclamation-triangle',
        'course_approved': 'fa-solid fa-check',
        'course_rejected': 'fa-solid fa-times',
        'course_disapproved': 'fa-solid fa-ban',
        'session_booking': 'fa-solid fa-calendar',
        'course_enrollment': 'fa-solid fa-user-graduate',
        'new_message': 'fa-solid fa-comment'
    };
    return icons[type] || 'fa-solid fa-bell';
}

function getNotificationIconClass(type) {
    const classes = {
        'course_created': 'bg-blue-100 text-blue-600',
        'course_updated': 'bg-yellow-100 text-yellow-600',
        'course_updated_reapproval': 'bg-orange-100 text-orange-600',
        'course_approved': 'bg-green-100 text-green-600',
        'course_rejected': 'bg-red-100 text-red-600',
        'course_disapproved': 'bg-red-100 text-red-600',
        'session_booking': 'bg-purple-100 text-purple-600',
        'course_enrollment': 'bg-indigo-100 text-indigo-600',
        'new_message': 'bg-blue-100 text-blue-600'
    };
    return classes[type] || 'bg-gray-100 text-gray-600';
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return date.toLocaleDateString();
}
</script>
