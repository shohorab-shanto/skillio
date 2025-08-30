let isMinimized = false;
let isMobileMenuOpen = false;

// Desktop Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");

    if (!isMinimized) {
        // Minimize sidebar
        sidebar.classList.remove("w-64");
        sidebar.classList.add("sidebar-minimized");

        // Change toggle icon
        const toggleIcon = sidebar.querySelector('.fa-angles-left');
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-angles-left');
            toggleIcon.classList.add('fa-angles-right');
        }
    } else {
        // Expand sidebar
        sidebar.classList.remove("sidebar-minimized");
        sidebar.classList.add("w-64");

        // Change toggle icon
        const toggleIcon = sidebar.querySelector('.fa-angles-right');
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-angles-right');
            toggleIcon.classList.add('fa-angles-left');
        }
    }

    isMinimized = !isMinimized;
}

// Mobile Menu Toggle
function toggleMobileMenu() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobile-overlay");

    console.log('Mobile menu toggle clicked', isMobileMenuOpen); // Debug log

    if (!isMobileMenuOpen) {
        // Open mobile menu
        sidebar.classList.remove("-translate-x-full");
        sidebar.classList.add("translate-x-0");
        overlay.classList.remove("hidden");
        document.body.style.overflow = "hidden";
        isMobileMenuOpen = true;
        console.log('Mobile menu opened'); // Debug log
    } else {
        // Close mobile menu
        sidebar.classList.remove("translate-x-0");
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("hidden");
        document.body.style.overflow = "auto";
        isMobileMenuOpen = false;
        console.log('Mobile menu closed'); // Debug log
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobile-overlay");
    const mobileMenuBtns = document.querySelectorAll('[onclick*="toggleMobileMenu"]');

    // Check if click is on mobile menu buttons
    let clickedOnMenuBtn = false;
    mobileMenuBtns.forEach(btn => {
        if (btn.contains(event.target)) {
            clickedOnMenuBtn = true;
        }
    });

    if (window.innerWidth < 1024 && isMobileMenuOpen) {
        if (event.target == overlay || (!sidebar.contains(event.target) && !clickedOnMenuBtn)) {
            toggleMobileMenu();
        }
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024 && isMobileMenuOpen) {
        toggleMobileMenu();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing mobile menu'); // Debug log

    // Reset mobile menu state
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobile-overlay");

    if (window.innerWidth < 1024) {
        sidebar.classList.add("-translate-x-full");
        sidebar.classList.remove("translate-x-0");
        overlay.classList.add("hidden");
        isMobileMenuOpen = false;
        console.log('Mobile view initialized'); // Debug log
    }
});

// Add touch event handlers for better mobile experience
document.addEventListener('touchstart', function(event) {
    // Handle touch events for mobile menu
});

// Logout Modal Functions
function showLogoutModal() {
    const modal = document.getElementById('logout-modal');
    const modalContent = document.getElementById('logout-modal-content');
    
    modal.classList.remove('hidden');
    
    // Trigger animation after a small delay
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    document.body.style.overflow = 'hidden';
}

function cancelLogout() {
    const modal = document.getElementById('logout-modal');
    const modalContent = document.getElementById('logout-modal-content');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

function confirmLogout() {
    document.getElementById('logout-form').submit();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('logout-modal');
    const modalContent = document.getElementById('logout-modal-content');
    
    if (event.target == modal) {
        cancelLogout();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key == 'Escape') {
        const modal = document.getElementById('logout-modal');
        if (!modal.classList.contains('hidden')) {
            cancelLogout();
        }
        
        // Close notification dropdown with Escape key
        closeNotifications();
    }
});

// Notification System
let notificationsOpen = false;
let hasUnreadNotifications = true; // This would come from backend in real implementation

// Initialize notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    updateNotificationBadge();
    
    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdownMobile = document.getElementById('notification-dropdown');
        const dropdownDesktop = document.getElementById('notification-dropdown-desktop');
        const notificationBtns = document.querySelectorAll('[onclick*="toggleNotifications"]');
        
        let clickedOnNotificationBtn = false;
        notificationBtns.forEach(btn => {
            if (btn.contains(event.target)) {
                clickedOnNotificationBtn = true;
            }
        });
        
        if (notificationsOpen && !clickedOnNotificationBtn) {
            if (dropdownMobile && !dropdownMobile.contains(event.target)) {
                closeNotifications();
            }
            if (dropdownDesktop && !dropdownDesktop.contains(event.target)) {
                closeNotifications();
            }
        }
    });
});

function toggleNotifications() {
    if (notificationsOpen) {
        closeNotifications();
    } else {
        openNotifications();
    }
}

function openNotifications() {
    const dropdownMobile = document.getElementById('notification-dropdown');
    const dropdownDesktop = document.getElementById('notification-dropdown-desktop');
    
    if (dropdownMobile) {
        dropdownMobile.classList.remove('hidden');
    }
    if (dropdownDesktop) {
        dropdownDesktop.classList.remove('hidden');
    }
    
    notificationsOpen = true;
}

function closeNotifications() {
    const dropdownMobile = document.getElementById('notification-dropdown');
    const dropdownDesktop = document.getElementById('notification-dropdown-desktop');
    
    if (dropdownMobile) {
        dropdownMobile.classList.add('hidden');
    }
    if (dropdownDesktop) {
        dropdownDesktop.classList.add('hidden');
    }
    
    notificationsOpen = false;
}

function updateNotificationBadge() {
    const badgeMobile = document.getElementById('notification-badge');
    const badgeDesktop = document.getElementById('notification-badge-desktop');
    const notificationListMobile = document.getElementById('notification-list');
    const notificationListDesktop = document.getElementById('notification-list-desktop');
    const noNotificationsMobile = document.getElementById('no-notifications');
    const noNotificationsDesktop = document.getElementById('no-notifications-desktop');
    
    // Count unread notifications (in real app, this would come from backend)
    const unreadCount = getUnreadNotificationCount();
    
    // Update badges
    if (unreadCount > 0) {
        if (badgeMobile) {
            badgeMobile.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badgeMobile.classList.remove('hidden');
        }
        if (badgeDesktop) {
            badgeDesktop.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badgeDesktop.classList.remove('hidden');
        }
        
        // Show notification list
        if (notificationListMobile) notificationListMobile.classList.remove('hidden');
        if (notificationListDesktop) notificationListDesktop.classList.remove('hidden');
        if (noNotificationsMobile) noNotificationsMobile.classList.add('hidden');
        if (noNotificationsDesktop) noNotificationsDesktop.classList.add('hidden');
    } else {
        // Hide badges
        if (badgeMobile) badgeMobile.classList.add('hidden');
        if (badgeDesktop) badgeDesktop.classList.add('hidden');
        
        // Show empty state
        if (notificationListMobile) notificationListMobile.classList.add('hidden');
        if (notificationListDesktop) notificationListDesktop.classList.add('hidden');
        if (noNotificationsMobile) noNotificationsMobile.classList.remove('hidden');
        if (noNotificationsDesktop) noNotificationsDesktop.classList.remove('hidden');
    }
}

function getUnreadNotificationCount() {
    // This is a mock function. In a real application, this would:
    // 1. Make an API call to get notification count
    // 2. Or count unread notifications from DOM
    // 3. Or get from a global variable set by the backend
    
    // For demo purposes, return 3 if has unread notifications, 0 otherwise
    return hasUnreadNotifications ? 3 : 0;
}

function markAllAsRead() {
    // In a real application, this would make an API call to mark notifications as read
    // For demo purposes, we'll just update the UI
    
    // Remove unread indicators (blue dots)
    const unreadDots = document.querySelectorAll('#notification-list .w-2.h-2.bg-blue-600, #notification-list-desktop .w-2.h-2.bg-blue-600');
    unreadDots.forEach(dot => {
        dot.remove();
    });
    
    // Update the global state
    hasUnreadNotifications = false;
    
    // Update badges
    updateNotificationBadge();
    
    // Close dropdown
    closeNotifications();
    
    // Show success message (optional)
    console.log('All notifications marked as read');
}

// Function to add a new notification (for demo purposes)
function addNotification(type, message, time) {
    const notificationListMobile = document.getElementById('notification-list');
    const notificationListDesktop = document.getElementById('notification-list-desktop');
    
    const iconMap = {
        'user': 'fa-user',
        'check': 'fa-check',
        'star': 'fa-star',
        'bell': 'fa-bell'
    };
    
    const colorMap = {
        'user': 'blue',
        'check': 'green',
        'star': 'yellow',
        'bell': 'purple'
    };
    
    const color = colorMap[type] || 'blue';
    const icon = iconMap[type] || 'fa-bell';
    
    const notificationHTML = `
        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-${color}-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid ${icon} text-${color}-600 text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">${message}</p>
                    <p class="text-xs text-gray-500 mt-1">${time}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="w-2 h-2 bg-${color}-600 rounded-full"></span>
                </div>
            </div>
        </div>
    `;
    
    if (notificationListMobile) {
        notificationListMobile.insertAdjacentHTML('afterbegin', notificationHTML);
    }
    if (notificationListDesktop) {
        notificationListDesktop.insertAdjacentHTML('afterbegin', notificationHTML);
    }
    
    // Update the badge
    hasUnreadNotifications = true;
    updateNotificationBadge();
}
