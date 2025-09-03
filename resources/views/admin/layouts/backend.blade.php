<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('trans.admin_dashboard')) | Skillio</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">

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
                    {{ __('trans.confirm_logout') }}
                </h3>
                <p class="text-gray-600 text-center mb-6">
                    {{ __('trans.logout_confirmation_message') }}
                </p>
                <div class="flex space-x-3">
                    <button onclick="cancelLogout()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                        {{ __('trans.cancel') }}
                    </button>
                    <button onclick="confirmLogout()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors duration-200">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i>
                        {{ __('trans.logout') }}
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
                        <p class="user-role">{{ __('trans.administrator') }}</p>
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
                        <h1 class="text-xl font-bold text-gray-900">@yield('header', __('trans.dashboard'))</h1>
                    </div>
                </div>
                
                <!-- Right side header content -->
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <div class="relative inline-block text-left">
                        <form method="POST" action="{{ route('lang.switch') }}">
                            @csrf
                            <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center space-x-2 px-3 py-2 text-gray-700 hover:text-purple-700 transition-colors duration-200 focus:outline-none border border-gray-300 rounded-lg hover:border-purple-500">
                                @if(app()->getLocale() == 'en')
                                    <span class="text-sm">🇺🇸</span>
                                @elseif(app()->getLocale() == 'hr')
                                    <span class="text-sm">🇭🇷</span>
                                @elseif(app()->getLocale() == 'sr')
                                    <span class="text-sm">🇷🇸</span>
                                @elseif(app()->getLocale() == 'sl')
                                    <span class="text-sm">🇸🇮</span>
                                @elseif(app()->getLocale() == 'mk')
                                    <span class="text-sm">🇲🇰</span>
                                @else
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke-width="2" />
                                        <path stroke-width="2" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20" />
                                    </svg>
                                @endif
                                <span class="text-sm font-medium">{{ strtoupper(app()->getLocale()) }}</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                    <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="absolute top-full right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                                <div class="py-2">
                                    <button type="submit" name="lang" value="en" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() == 'en' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <span class="text-sm">🇺🇸</span>
                                            <span>{{ __('trans.english') }}</span>
                                            @if(app()->getLocale() == 'en')
                                                <svg class="w-4 h-4 text-purple-600 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="hr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() == 'hr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <span class="text-sm">🇭🇷</span>
                                            <span>{{ __('trans.hrvatski') }}</span>
                                            @if(app()->getLocale() == 'hr')
                                                <svg class="w-4 h-4 text-purple-600 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="sr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() == 'sr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <span class="text-sm">🇷🇸</span>
                                            <span>{{ __('trans.srpski') }}</span>
                                            @if(app()->getLocale() == 'sr')
                                                <svg class="w-4 h-4 text-purple-600 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="sl" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() == 'sl' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <span class="text-sm">🇸🇮</span>
                                            <span>{{ __('trans.slovenscina') }}</span>
                                            @if(app()->getLocale() == 'sl')
                                                <svg class="w-4 h-4 text-purple-600 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="mk" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() == 'mk' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <span class="text-sm">🇲🇰</span>
                                            <span>{{ __('trans.makedonski') }}</span>
                                            @if(app()->getLocale() == 'mk')
                                                <svg class="w-4 h-4 text-purple-600 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Admin Notifications -->
                    @include('admin.components.notification-dropdown')
                    
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
