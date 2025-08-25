<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | Skillio</title>

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
    <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="hidden">
        @csrf
    </form>
    
    <div class="flex h-screen lg:p-4">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-white shadow-xl rounded-2xl transition-all duration-300 w-64 lg:w-64 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 border border-gray-100 flex flex-col">
            <!-- Logo Section -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <img id="logo" class="h-8 w-auto transition-all duration-300" src="{{ asset('assets/images/logo.png') }}" alt="Skillio" />
                    </a>
                </div>
                <button onclick="toggleSidebar()" class="hidden lg:block text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-angles-left text-lg"></i>
                </button>
                <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            @include('backend.layouts.admin-nav-bar')

            <!-- User Profile Section -->
            <div class="user-profile">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="user-avatar w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold text-purple-700">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="user-info sidebar-label">
                        <p class="user-name">{{ auth()->user()->name }}</p>
                        <p class="user-role">Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="bg-white shadow-sm rounded-2xl mb-6 mx-4 p-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">@yield('header', 'Dashboard')</h1>
                    </div>
                </div>
                
                <!-- Right side header content -->
                <div class="flex items-center space-x-4">
                    @yield('header-right')
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto px-4 pb-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    
    <!-- Custom Scripts Stack -->
    @stack('scripts')
</body>
</html>
