<nav class="flex-1 px-2 py-6 overflow-y-auto">
    <div style="display: flex; flex-direction: column; gap: 2px;">
        <a href="{{ route('user.dashboard') }}" class="sidebar-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i>
            <span class="sidebar-label">Dashboard</span>
        </a>
        
        <a href="{{ route('user.courses.index') }}" class="sidebar-item {{ request()->routeIs('user.courses*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i>
            <span class="sidebar-label">Browse Courses</span>
        </a>
        
        <a href="{{ route('user.my-courses.index') }}" class="sidebar-item {{ request()->routeIs('user.my-courses*') ? 'active' : '' }}">
            <i class="fa-solid fa-graduation-cap"></i>
            <span class="sidebar-label">My Courses</span>
        </a>
        
        <a href="{{ route('user.sessions.index') }}" class="sidebar-item {{ request()->routeIs('user.sessions*') ? 'active' : '' }}">
            <i class="fa-solid fa-video"></i>
            <span class="sidebar-label">My Sessions</span>
        </a>
        
        <a href="{{ route('user.bookings.index') }}" class="sidebar-item {{ request()->routeIs('user.bookings*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check"></i>
            <span class="sidebar-label">My Bookings</span>
        </a>
        
        <a href="{{ route('user.mentors.index') }}" class="sidebar-item {{ request()->routeIs('user.mentors*') ? 'active' : '' }}">
            <i class="fa-solid fa-chalkboard-user"></i>
            <span class="sidebar-label">Find Mentors</span>
        </a>
        
        <a href="{{ route('chat.index') }}" class="sidebar-item {{ request()->routeIs('chat*') ? 'active' : '' }}">
            <i class="fa-regular fa-comment-dots"></i>
            <span class="sidebar-label">Messages</span>
        </a>
        
        <a href="{{ route('user.payments.index') }}" class="sidebar-item {{ request()->routeIs('user.payments*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i>
            <span class="sidebar-label">Payment History</span>
        </a>
        
        <a href="{{ route('user.profile.edit') }}" class="sidebar-item {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
            <i class="fa-regular fa-circle-user"></i>
            <span class="sidebar-label">Profile</span>
        </a>
        
        <div style="border-top: 1px solid #f3f4f6; margin: 16px 8px;"></div>
        
        <button onclick="showLogoutModal()" class="sidebar-item w-full text-left">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="sidebar-label">Logout</span>
        </button>
    </div>
</nav>
