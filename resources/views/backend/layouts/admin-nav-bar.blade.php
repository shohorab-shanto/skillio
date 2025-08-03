<nav class="flex-1 px-2 py-6 overflow-y-auto">
    <div style="display: flex; flex-direction: column; gap: 2px;">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i>
            <span class="sidebar-label">Dashboard</span>
        </a>
        
        <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            <span class="sidebar-label">User Management</span>
        </a>
        
        <a href="{{ route('admin.mentors.index') }}" class="sidebar-item {{ request()->routeIs('admin.mentors*') ? 'active' : '' }}">
            <i class="fa-solid fa-chalkboard-user"></i>
            <span class="sidebar-label">Mentor Management</span>
        </a>
        
        <a href="{{ route('admin.courses.index') }}" class="sidebar-item {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i>
            <span class="sidebar-label">Course Management</span>
        </a>
        
        <a href="{{ route('admin.categories.index') }}" class="sidebar-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags"></i>
            <span class="sidebar-label">Categories</span>
        </a>
        
        <a href="{{ route('admin.payments.index') }}" class="sidebar-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i>
            <span class="sidebar-label">Payment Management</span>
        </a>
        
        <a href="{{ route('admin.reports.index') }}" class="sidebar-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span class="sidebar-label">Reports & Analytics</span>
        </a>
        
        <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="fa-solid fa-cog"></i>
            <span class="sidebar-label">System Settings</span>
        </a>
        
        <div style="border-top: 1px solid #f3f4f6; margin: 16px 8px;"></div>
        
        <button onclick="showLogoutModal()" class="sidebar-item w-full text-left">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="sidebar-label">Logout</span>
        </button>
    </div>
</nav>
