<!-- Admin Notification Dropdown -->
<div class="relative">
    <!-- Notification Bell Icon -->
    <button onclick="toggleAdminNotifications()" class="relative p-2 text-gray-600 hover:text-purple-600 transition-colors duration-200">
        <i class="fa-solid fa-bell text-xl"></i>
        <!-- Unread Badge -->
        <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
    </button>
    
    <!-- Notification Dropdown -->
    <div id="admin-notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 hidden z-50">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <button onclick="markAllAdminNotificationsAsRead()" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                    Mark all as read
                </button>
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            <!-- Dynamic Notifications -->
            <div id="admin-notification-list">
                <!-- Notifications will be loaded here -->
            </div>
            
            <!-- Empty State -->
            <div id="admin-no-notifications" class="p-8 text-center hidden">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
                </div>
                <p class="text-gray-500 text-sm">No notifications yet</p>
            </div>
            
            <!-- Loading State -->
            <div id="admin-notification-loading" class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-spinner fa-spin text-gray-400 text-xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Loading notifications...</p>
            </div>
        </div>
        
    </div>
</div>

<script>
let adminNotificationsLoaded = false;
let adminUnreadCount = 0;

// Toggle admin notifications dropdown
function toggleAdminNotifications() {
    const dropdown = document.getElementById('admin-notification-dropdown');
    const isHidden = dropdown.classList.contains('hidden');
    
    if (isHidden) {
        dropdown.classList.remove('hidden');
        if (!adminNotificationsLoaded) {
            loadAdminNotifications();
        }
    } else {
        dropdown.classList.add('hidden');
    }
}

// Load admin notifications from API
async function loadAdminNotifications() {
    try {
        const response = await fetch('/notifications/recent', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            displayAdminNotifications(data.notifications);
            adminUnreadCount = data.unread_count;
            updateAdminNotificationBadge();
            adminNotificationsLoaded = true;
        }
    } catch (error) {
        console.error('Error loading admin notifications:', error);
    }
}

// Display admin notifications in dropdown
function displayAdminNotifications(notifications) {
    const list = document.getElementById('admin-notification-list');
    const loading = document.getElementById('admin-notification-loading');
    const empty = document.getElementById('admin-no-notifications');

    // Hide loading state
    loading.classList.add('hidden');

    if (notifications.length === 0) {
        // Show empty state
        empty.classList.remove('hidden');
        list.innerHTML = '';
        return;
    }

    // Hide empty state
    empty.classList.add('hidden');

    // Generate notification HTML
    const notificationHtml = notifications.map(notification => createAdminNotificationHtml(notification)).join('');
    list.innerHTML = notificationHtml;
}

// Create admin notification HTML
function createAdminNotificationHtml(notification) {
    const unreadIndicator = notification.is_read ? '' : '<span class="w-2 h-2 bg-blue-600 rounded-full"></span>';
    
    return `
        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer" 
             onclick="handleAdminNotificationClick('${notification.id}', '${notification.redirect_url || ''}')">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 ${notification.icon_color_class || 'bg-gray-100 text-gray-600'} rounded-full flex items-center justify-center">
                        <i class="${notification.icon_class || 'fa-solid fa-bell'} text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900 font-medium">${notification.title}</p>
                    <p class="text-xs text-gray-600 mt-1">${notification.message}</p>
                    <p class="text-xs text-gray-500 mt-1">${notification.time_ago}</p>
                </div>
                <div class="flex-shrink-0">
                    ${unreadIndicator}
                </div>
            </div>
        </div>
    `;
}

// Handle admin notification click
async function handleAdminNotificationClick(notificationId, redirectUrl) {
    // Mark as read
    await markAdminNotificationAsRead(notificationId);
    
    // Redirect if URL exists
    if (redirectUrl) {
        window.location.href = redirectUrl;
    }
    
    // Close dropdown
    toggleAdminNotifications();
}

// Mark admin notification as read
async function markAdminNotificationAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            adminUnreadCount = data.unread_count;
            updateAdminNotificationBadge();
        }
    } catch (error) {
        console.error('Error marking admin notification as read:', error);
    }
}

// Mark all admin notifications as read
async function markAllAdminNotificationsAsRead() {
    try {
        const response = await fetch('/notifications/mark-all-read', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            adminUnreadCount = 0;
            updateAdminNotificationBadge();
            
            // Reload notifications to update read status
            loadAdminNotifications();
        }
    } catch (error) {
        console.error('Error marking all admin notifications as read:', error);
    }
}

// Update admin notification badge
function updateAdminNotificationBadge() {
    const badge = document.getElementById('admin-notification-badge');
    if (adminUnreadCount > 0) {
        badge.textContent = adminUnreadCount > 99 ? '99+' : adminUnreadCount;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

// Close admin notification dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('admin-notification-dropdown');
    const button = event.target.closest('button[onclick="toggleAdminNotifications()"]');
    
    if (!button && !event.target.closest('#admin-notification-dropdown')) {
        dropdown.classList.add('hidden');
    }
});

// Setup real-time admin notifications
function setupAdminRealtimeNotifications() {
    if (typeof window.Echo !== 'undefined') {
        const userId = {{ auth()->id() }};
        const channel = window.Echo.private(`user.${userId}`);
        
        channel.listen('.notification.sent', (e) => {
            console.log('New admin notification received:', e);
            
            // Add new notification to the top
            const newNotification = e.notification;
            addNewAdminNotification(newNotification);
            
            // Update unread count
            adminUnreadCount++;
            updateAdminNotificationBadge();
        });
    }
}

// Add new admin notification to the list
function addNewAdminNotification(notification) {
    const list = document.getElementById('admin-notification-list');
    const empty = document.getElementById('admin-no-notifications');
    
    const notificationHtml = createAdminNotificationHtml(notification);
    
    // Add to top of list
    list.insertAdjacentHTML('afterbegin', notificationHtml);
    
    // Hide empty state if it was showing
    empty.classList.add('hidden');
}

// Initialize admin notifications when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupAdminRealtimeNotifications();
});
</script>
