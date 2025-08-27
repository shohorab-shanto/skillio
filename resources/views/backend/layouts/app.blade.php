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
                    <a href="{{ route('home') }}">
                        <img id="logo" class="h-8 w-auto transition-all duration-300" src="{{ asset('assets/images/logo.png') }}" alt="Skillio" />
                        {{-- <span id="logo-text" class="ml-3 text-xl font-bold text-gray-800 transition-all duration-300">Skillio</span> --}}
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
                        
                        <!-- Language Switcher -->
                        <div class="relative inline-block text-left mr-2">
                            <form method="POST" action="{{ route('lang.switch') }}">
                                @csrf
                                <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center space-x-2 px-2 py-2 text-gray-700 hover:text-purple-700 transition-colors duration-200 focus:outline-none border border-gray-300 rounded-lg hover:border-purple-500">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke-width="2" />
                                        <path stroke-width="2" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20" />
                                    </svg>
                                    <span class="text-xs font-medium">{{ strtoupper(app()->getLocale()) }}</span>
                                </button>
                                <div class="absolute top-full right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                                    <div class="py-2">
                                        <button type="submit" name="lang" value="en" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'en' ? 'bg-purple-50 text-purple-700' : '' }}">
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                                <span>English</span>
                                                @if(app()->getLocale() === 'en')
                                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                        <button type="submit" name="lang" value="hr" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'hr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                                <span>Hrvatski</span>
                                                @if(app()->getLocale() === 'hr')
                                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                        <button type="submit" name="lang" value="sr" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'sr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                                <span>Српски</span>
                                                @if(app()->getLocale() === 'sr')
                                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                        <button type="submit" name="lang" value="sl" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'sl' ? 'bg-purple-50 text-purple-700' : '' }}">
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                                <span>Slovenščina</span>
                                                @if(app()->getLocale() === 'sl')
                                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                        <button type="submit" name="lang" value="mk" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'mk' ? 'bg-purple-50 text-purple-700' : '' }}">
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                                <span>Македонски</span>
                                                @if(app()->getLocale() === 'mk')
                                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
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
                                    <!-- Dynamic Notifications -->
                                    <div id="notification-list">
                                        <!-- Notifications will be loaded here -->
                                    </div>
                                    
                                    <!-- Empty State -->
                                    <div id="no-notifications" class="p-8 text-center hidden">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm">No notifications yet</p>
                                    </div>
                                    
                                    <!-- Loading State -->
                                    <div id="notification-loading" class="p-8 text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fa-solid fa-spinner fa-spin text-gray-400 text-xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm">Loading notifications...</p>
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
                            
                            <!-- Desktop Language Switcher -->
                            <div class="hidden lg:block relative ml-4">
                                <form method="POST" action="{{ route('lang.switch') }}">
                                    @csrf
                                    <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center space-x-2 px-3 py-2 text-gray-700 hover:text-purple-700 transition-colors duration-200 focus:outline-none border border-gray-300 rounded-lg hover:border-purple-500">
                                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" stroke-width="2" />
                                            <path stroke-width="2" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20" />
                                        </svg>
                                        <span class="text-sm font-medium">{{ strtoupper(app()->getLocale()) }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                            <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <div class="absolute top-full right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                                        <div class="py-2">
                                            <button type="submit" name="lang" value="en" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'en' ? 'bg-purple-50 text-purple-700' : '' }}">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                    </svg>
                                                    <span>English</span>
                                                    @if(app()->getLocale() === 'en')
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </button>
                                            <button type="submit" name="lang" value="hr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'hr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                    </svg>
                                                    <span>Hrvatski</span>
                                                    @if(app()->getLocale() === 'hr')
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </button>
                                            <button type="submit" name="lang" value="sr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'sr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                    </svg>
                                                    <span>Српски</span>
                                                    @if(app()->getLocale() === 'sr')
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </button>
                                            <button type="submit" name="lang" value="sl" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'sl' ? 'bg-purple-50 text-purple-700' : '' }}">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                    </svg>
                                                    <span>Slovenščina</span>
                                                    @if(app()->getLocale() === 'sl')
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </button>
                                            <button type="submit" name="lang" value="mk" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'mk' ? 'bg-purple-50 text-purple-700' : '' }}">
                                                <span class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                    </svg>
                                                    <span>Македонски</span>
                                                    @if(app()->getLocale() === 'mk')
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
                                        <!-- Dynamic Notifications -->
                                        <div id="notification-list-desktop">
                                            <!-- Notifications will be loaded here -->
                                        </div>
                                        
                                        <!-- Empty State -->
                                        <div id="no-notifications-desktop" class="p-8 text-center hidden">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fa-solid fa-bell-slash text-gray-400 text-xl"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm">No notifications yet</p>
                                        </div>
                                        
                                        <!-- Loading State -->
                                        <div id="notification-loading-desktop" class="p-8 text-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fa-solid fa-spinner fa-spin text-gray-400 text-xl"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm">Loading notifications...</p>
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
    
    <!-- Notification System JavaScript -->
    <script>
        // Global notification variables
        let notificationsLoaded = false;
        let unreadCount = 0;

        // Initialize notification system
        document.addEventListener('DOMContentLoaded', function() {
            initializeNotifications();
            setupRealtimeNotifications();
        });

        // Initialize notifications
        function initializeNotifications() {
            loadNotifications();
            updateNotificationBadge();
        }

        // Load notifications from API
        async function loadNotifications() {
            try {
                const response = await fetch('/notifications/recent', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    displayNotifications(data.notifications);
                    unreadCount = data.unread_count;
                    updateNotificationBadge();
                    notificationsLoaded = true;
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        // Display notifications in dropdown
        function displayNotifications(notifications) {
            const mobileList = document.getElementById('notification-list');
            const desktopList = document.getElementById('notification-list-desktop');
            const mobileLoading = document.getElementById('notification-loading');
            const desktopLoading = document.getElementById('notification-loading-desktop');
            const mobileEmpty = document.getElementById('no-notifications');
            const desktopEmpty = document.getElementById('no-notifications-desktop');

            // Hide loading states
            mobileLoading.classList.add('hidden');
            desktopLoading.classList.add('hidden');

            if (notifications.length === 0) {
                // Show empty state
                mobileEmpty.classList.remove('hidden');
                desktopEmpty.classList.remove('hidden');
                mobileList.innerHTML = '';
                desktopList.innerHTML = '';
                return;
            }

            // Hide empty states
            mobileEmpty.classList.add('hidden');
            desktopEmpty.classList.add('hidden');

            // Generate notification HTML
            const notificationHtml = notifications.map(notification => createNotificationHtml(notification)).join('');
            
            mobileList.innerHTML = notificationHtml;
            desktopList.innerHTML = notificationHtml;
        }

        // Create notification HTML
        function createNotificationHtml(notification) {
            const unreadIndicator = notification.is_read ? '' : '<span class="w-2 h-2 bg-blue-600 rounded-full"></span>';
            
            return `
                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer" 
                     onclick="handleNotificationClick('${notification.id}', '${notification.redirect_url}')">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 ${notification.icon_color_class} rounded-full flex items-center justify-center">
                                <i class="${notification.icon_class} text-sm"></i>
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

        // Handle notification click
        async function handleNotificationClick(notificationId, redirectUrl) {
            // Mark as read
            await markNotificationAsRead(notificationId);
            
            // Redirect if URL exists
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
            
            // Close dropdown
            toggleNotifications();
        }

        // Mark notification as read
        async function markNotificationAsRead(notificationId) {
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
                    unreadCount = data.unread_count;
                    updateNotificationBadge();
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        // Mark all notifications as read
        async function markAllAsRead() {
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
                    unreadCount = 0;
                    updateNotificationBadge();
                    
                    // Reload notifications to update read status
                    loadNotifications();
                    
                    // Show success message
                    showNotification(data.message, 'success');
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        }

        // Update notification badge
        function updateNotificationBadge() {
            const mobileBadge = document.getElementById('notification-badge');
            const desktopBadge = document.getElementById('notification-badge-desktop');

            if (unreadCount > 0) {
                mobileBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                desktopBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                mobileBadge.classList.remove('hidden');
                desktopBadge.classList.remove('hidden');
            } else {
                mobileBadge.classList.add('hidden');
                desktopBadge.classList.add('hidden');
            }
        }

        // Setup real-time notifications
        function setupRealtimeNotifications() {
            console.log('Setting up real-time notifications...');
            
            // Wait for Echo to be available
            const checkEcho = () => {
                if (typeof window.Echo !== 'undefined') {
                    console.log('Echo is available, setting up notification listeners...');
                    
                    const userId = {{ Auth::id() }};
                    const channelName = `user.${userId}`;
                    console.log('Subscribing to notification channel:', channelName);
                    
                    const channel = window.Echo.private(channelName);
                    
                    // Test channel subscription
                    channel.subscribed(() => {
                        console.log('Successfully subscribed to notification channel:', channelName);
                    });
                    
                    channel.error((error) => {
                        console.error('Notification channel subscription error:', error);
                    });
                    
                    channel.listen('.notification.sent', (e) => {
                        console.log('New notification received:', e);
                        
                        // Add new notification to the top
                        const newNotification = e.notification;
                        addNewNotification(newNotification);
                        
                        // Update unread count
                        unreadCount++;
                        updateNotificationBadge();
                        
                        // Show toast notification
                        showNotification(newNotification.message, 'info');
                    });
                    
                    // Test if Echo connection is working
                    window.Echo.connector.pusher.connection.bind('connected', function() {
                        console.log('Pusher connected successfully for notifications');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('disconnected', function() {
                        console.log('Pusher disconnected for notifications');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('error', function(error) {
                        console.error('Pusher connection error for notifications:', error);
                    });
                    
                } else {
                    console.log('Echo not available yet, retrying in 500ms...');
                    setTimeout(checkEcho, 500);
                }
            };
            
            // Start checking for Echo
            checkEcho();
        }

        // Add new notification to the list
        function addNewNotification(notification) {
            const mobileList = document.getElementById('notification-list');
            const desktopList = document.getElementById('notification-list-desktop');
            
            const notificationHtml = createNotificationHtml(notification);
            
            // Add to top of both lists
            mobileList.insertAdjacentHTML('afterbegin', notificationHtml);
            desktopList.insertAdjacentHTML('afterbegin', notificationHtml);
            
            // Hide empty states if they were showing
            document.getElementById('no-notifications').classList.add('hidden');
            document.getElementById('no-notifications-desktop').classList.add('hidden');
        }

        // Show notification toast
        function showNotification(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
            
            // Set background color based on type
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            toast.className += ` ${bgColor} text-white`;
            
            toast.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 5000);
        }

        // Toggle notifications dropdown
        function toggleNotifications() {
            const mobileDropdown = document.getElementById('notification-dropdown');
            const desktopDropdown = document.getElementById('notification-dropdown-desktop');
            
            // Toggle mobile dropdown
            if (mobileDropdown.classList.contains('hidden')) {
                mobileDropdown.classList.remove('hidden');
                if (!notificationsLoaded) {
                    loadNotifications();
                }
            } else {
                mobileDropdown.classList.add('hidden');
            }
            
            // Toggle desktop dropdown
            if (desktopDropdown.classList.contains('hidden')) {
                desktopDropdown.classList.remove('hidden');
                if (!notificationsLoaded) {
                    loadNotifications();
                }
            } else {
                desktopDropdown.classList.add('hidden');
            }
        }

        // Close notifications when clicking outside
        document.addEventListener('click', function(event) {
            const mobileDropdown = document.getElementById('notification-dropdown');
            const desktopDropdown = document.getElementById('notification-dropdown-desktop');
            const mobileButton = event.target.closest('#notification-dropdown') ? null : event.target.closest('button[onclick="toggleNotifications()"]');
            const desktopButton = event.target.closest('#notification-dropdown-desktop') ? null : event.target.closest('button[onclick="toggleNotifications()"]');
            
            if (!mobileButton && !desktopButton && !event.target.closest('#notification-dropdown') && !event.target.closest('#notification-dropdown-desktop')) {
                mobileDropdown.classList.add('hidden');
                desktopDropdown.classList.add('hidden');
            }
        });
    </script>
    
    <!-- Custom Scripts Stack -->
    @stack('scripts')

</body>

</html>
