<nav class="flex-1 px-2 py-6 overflow-y-auto">
    <div style="display: flex; flex-direction: column; gap: 2px;">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i>
            <span class="sidebar-label">Dashboard</span>
        </a>
        
                            <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span class="sidebar-label">Users</span>
                    </a>
        
        <a href="{{ route('admin.courses.index') }}" class="sidebar-item {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
            <i class="fa-solid fa-book"></i>
            <span class="sidebar-label">Courses</span>
        </a>
        
                                    <a href="{{ route('admin.mentors.index') }}" class="sidebar-item {{ request()->routeIs('admin.mentors*') ? 'active' : '' }}">
            <i class="fa-solid fa-chalkboard-teacher"></i>
            <span class="sidebar-label">Mentors</span>
        </a>
        
        <a href="{{ route('admin.categories.index') }}" class="sidebar-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags"></i>
            <span class="sidebar-label">Categories</span>
        </a>
        
        <a href="{{ route('admin.sub-categories.index') }}" class="sidebar-item {{ request()->routeIs('admin.sub-categories*') ? 'active' : '' }}">
            <i class="fa-solid fa-tag"></i>
            <span class="sidebar-label">Sub-Categories</span>
        </a>
        
        <a href="{{ route('admin.transactions.index') }}" class="sidebar-item {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i>
            <span class="sidebar-label">Transactions</span>
        </a>
        
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-cog"></i>
            <span class="sidebar-label">Settings</span>
        </a>
        
        <div style="border-top: 1px solid #f3f4f6; margin: 16px 8px;"></div>
        
        <button onclick="showLogoutModal()" class="sidebar-item w-full text-left">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="sidebar-label">Logout</span>
        </a>
    </div>
</nav>
