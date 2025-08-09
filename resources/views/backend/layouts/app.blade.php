<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Skillio') | Skillio</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Daisy UI + Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.8.0/dist/full.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Force light mode configuration
        tailwind.config = {
            theme: {
                extend: {}
            },
            daisyui: {
                themes: ["light"],
                darkTheme: "light"
            }
        }
        
        // Ensure light mode is always applied
        document.documentElement.setAttribute('data-theme', 'light');
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    
    <!-- Custom Styles Stack -->
    @stack('styles')
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-purple-50 to-pink-50 min-h-screen">
    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="mobile-menu-overlay hidden" onclick="toggleMobileMenu()"></div>
    
    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="logout-modal-content">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fa-solid fa-right-from-bracket text-2xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
                    Confirm Logout
                </h3>
                <p class="text-gray-600 text-center mb-6">
                    Are you sure you want to logout? You will need to sign in again to access your account.
                </p>
                <div class="flex space-x-3">
                    <button onclick="cancelLogout()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                        Cancel
                    </button>
                    <button onclick="confirmLogout()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors duration-200">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden logout form -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
    
    <div class="flex h-screen lg:p-4">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-white shadow-xl rounded-2xl transition-all duration-300 w-64 lg:w-64 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 border border-gray-100 flex flex-col">
            <!-- Logo Section -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center">
                    <img id="logo" class="h-8 w-auto transition-all duration-300" src="{{ asset('assets/images/logo.png') }}" alt="Skillio" />
                    {{-- <span id="logo-text" class="ml-3 text-xl font-bold text-gray-800 transition-all duration-300">Skillio</span> --}}
                </div>
                <button onclick="toggleSidebar()" class="hidden lg:block text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-angles-left text-lg"></i>
                </button>
                <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            @if (auth()->user()->isAdmin())
                @include('backend.layouts.admin-nav-bar')
            @elseif (auth()->user()->isMentor())
                @include('backend.layouts.mentor-nav-bar')
            @elseif (auth()->user()->isUser())
                @include('backend.layouts.user-nav-bar')
            @endif

            <!-- User Profile Section -->
            <div class="user-profile">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img class="user-avatar" 
                             src="{{ asset('assets/images/user-avatar.svg') }}" 
                             alt="User Avatar" />
                    </div>
                    <div class="user-info sidebar-label">
                        <p class="user-name">
                            {{ Auth::user()->name ?? 'User name' }}
                        </p>
                        <p class="user-role">
                            {{ Auth::user()->role ?? 'mentor' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col lg:ml-0 transition-all duration-300">
            <!-- Top Header (Mobile) -->
            <header class="lg:hidden lg:p-4 lg:pb-0">
                <div class="bg-white lg:rounded-xl lg:shadow-sm lg:border lg:border-gray-100 px-4 py-3 lg:px-4 lg:py-3 shadow-sm border-b border-gray-200 lg:border-b-0">
                    <div class="flex items-center justify-between">
                        <button onclick="toggleMobileMenu()" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <img class="h-8 w-auto" src="{{ asset('assets/images/logo.png') }}" alt="Skillio" />
                        
                        <!-- Notification Icon -->
                        <div class="relative">
                            <button onclick="toggleNotifications()" class="relative text-gray-500 hover:text-gray-700 transition-colors p-2">
                                <i class="fa-solid fa-bell text-lg"></i>
                                <!-- Notification Badge -->
                                <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center hidden">3</span>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 hidden z-50">
                                <div class="p-4 border-b border-gray-100">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <!-- Sample Notifications -->
                                    <div id="notification-list">
                                        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <i class="fa-solid fa-user text-blue-600 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-900">New mentor application received</p>
                                                    <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <i class="fa-solid fa-check text-green-600 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-900">Profile updated successfully</p>
                                                    <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                        <i class="fa-solid fa-star text-yellow-600 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-900">You received a new review</p>
                                                    <p class="text-xs text-gray-500 mt-1">3 days ago</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Empty State -->
                                    <div id="no-notifications" class="p-8 text-center hidden">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm">No notifications yet</p>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="p-4 border-t border-gray-100">
                                    <button onclick="markAllAsRead()" class="w-full text-center text-sm text-purple-600 hover:text-purple-700 font-medium">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto">
                <!-- Page Header Section -->
                
                <div class="px-4 lg:px-8 pt-0 pb-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                @yield('header')
                            </div>
                            
                            <!-- Desktop Notification Icon -->
                            <div class="hidden lg:block relative ml-4">
                                <button onclick="toggleNotifications()" class="relative text-gray-500 hover:text-gray-700 transition-colors p-2">
                                    <i class="fa-solid fa-bell text-lg"></i>
                                    <!-- Notification Badge -->
                                    <span id="notification-badge-desktop" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center hidden">3</span>
                                </button>
                                
                                <!-- Notification Dropdown -->
                                <div id="notification-dropdown-desktop" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 hidden z-50">
                                    <div class="p-4 border-b border-gray-100">
                                        <h3 class="font-semibold text-gray-900">Notifications</h3>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <!-- Sample Notifications -->
                                        <div id="notification-list-desktop">
                                            <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <i class="fa-solid fa-user text-blue-600 text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-900">New mentor application received</p>
                                                        <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                            <i class="fa-solid fa-check text-green-600 text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-900">Profile updated successfully</p>
                                                        <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                            <i class="fa-solid fa-star text-yellow-600 text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-900">You received a new review</p>
                                                        <p class="text-xs text-gray-500 mt-1">3 days ago</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Empty State -->
                                        <div id="no-notifications-desktop" class="p-8 text-center hidden">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm">No notifications yet</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="p-4 border-t border-gray-100">
                                        <button onclick="markAllAsRead()" class="w-full text-center text-sm text-purple-600 hover:text-purple-700 font-medium">
                                            Mark all as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="p-4 lg:p-8  pt-2 lg:pt-2">
                    @include('backend.layouts.message')
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Enhanced JavaScript for Sidebar and Mobile Menu -->
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    
    <!-- Custom Scripts Stack -->
    @stack('scripts')

</body>

</html>
