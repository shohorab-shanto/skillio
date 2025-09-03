<nav class="flex-1 px-2 py-6 overflow-y-auto">
    <div style="display: flex; flex-direction: column; gap: 2px;">
        <a href="{{ route('user.dashboard') }}" class="sidebar-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i>
            <span class="sidebar-label">{{ __('trans.dashboard') }}</span>
        </a>
        
        <a href="{{ route('user.courses') }}" class="sidebar-item {{ request()->routeIs('user.courses*') ? 'active' : '' }}">
            <i class="fa-solid fa-graduation-cap"></i>
            <span class="sidebar-label">{{ __('trans.courses') }}</span>
        </a>
        
        <a href="{{ route('user.sessions') }}" class="sidebar-item {{ request()->routeIs('user.sessions*') ? 'active' : '' }}">
            <i class="fa-solid fa-video"></i>
            <span class="sidebar-label">{{ __('trans.sessions') }}</span>
        </a>

        <a href="{{ route('user.payments') }}" class="sidebar-item {{ request()->routeIs('user.payments*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i>
            <span class="sidebar-label">{{ __('trans.payment_history') }}</span>
        </a>
        
        <a href="{{ route('chat.index') }}" class="sidebar-item {{ request()->routeIs('chat*') ? 'active' : '' }}">
            <i class="fa-regular fa-comment-dots"></i>
            <span class="sidebar-label">{{ __('trans.chat') }}</span>
        </a>


        <a href="{{ route('user.profile.show') }}" class="sidebar-item {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
            <i class="fa-regular fa-circle-user"></i>
            <span class="sidebar-label">{{ __('trans.profile') }}</span>
        </a>
        <a href="{{ route('user.onboarding.category_service') }}" class="sidebar-item {{ request()->routeIs('user.preferences*') ? 'active' : '' }}">
            <i class="fa-solid fa-sliders"></i>
            <span class="sidebar-label">{{ __('trans.preferences') }}</span>
        </a>
        <div style="border-top: 1px solid #f3f4f6; margin: 16px 8px;"></div>
        
        <button onclick="showLogoutModal()" class="sidebar-item w-full text-left">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="sidebar-label">{{ __('trans.logout') }}</span>
        </button>
    </div>
</nav>
