            <nav class="flex-1 px-2 py-6 overflow-y-auto">
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <a href="{{ route('mentor.dashboard') }}" class="sidebar-item {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i>
                        <span class="sidebar-label">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('mentor.courses.index') }}" class="sidebar-item">
                        <i class="fa-solid fa-book"></i>
                        <span class="sidebar-label">My Course</span>
                    </a>
                    
                    <a href="{{ route('mentor.time-slots.index') }}" class="sidebar-item {{ request()->routeIs('mentor.time-slots*') ? 'active' : '' }}">
                        <i class="fa-solid fa-business-time"></i>
                        <span class="sidebar-label">Time Slots</span>
                    </a>
                    
                    <a href="{{ route('mentor.earnings') }}" class="sidebar-item {{ request()->routeIs('mentor.earnings*') ? 'active' : '' }}">
                        <i class="fa-solid fa-credit-card"></i>
                        <span class="sidebar-label">Earning History</span>
                    </a>
                    
                    <a href="{{ route('mentor.profile.show') }}" class="sidebar-item">
                        <i class="fa-regular fa-circle-user"></i>
                        <span class="sidebar-label">Profile</span>
                    </a>
                    
                    <a href="{{ route('chat.index') }}" class="sidebar-item {{ request()->routeIs('chat*') ? 'active' : '' }}">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span class="sidebar-label">Student Chat</span>
                    </a>
                    
                    <div style="border-top: 1px solid #f3f4f6; margin: 16px 8px;"></div>
                    
                    <button onclick="showLogoutModal()" class="sidebar-item w-full text-left">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="sidebar-label">Logout</span>
                    </button>
                </div>
            </nav>