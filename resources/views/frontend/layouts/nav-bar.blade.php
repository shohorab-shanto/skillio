<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm transition-all duration-300">
    <div class="max-w-[1400px] mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-2xl font-bold drop-shadow-sm">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation (Only Large Screens) -->
            <div class="hidden lg:flex items-center space-x-6">
                <!-- About Us Dropdown -->
                <div class="relative group">
                    <button class="nav-item flex items-center gap-1 text-gray-700 hover:text-purple-700 transition-colors duration-300 font-medium drop-shadow-sm focus:outline-none">
                        <span>About Us</span>
                        <svg class="w-3 h-3 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute top-full left-0 mt-2 w-52 bg-white rounded-box shadow-md border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top scale-95 group-hover:scale-100 z-10">
                        <div class="p-2">
                            <a href="/#how-it-works" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">How It Works</a>
                            <a href="/#what-we-offer" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">What We Offer</a>
                            <a href="/#why-we-are-different" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">Why We are Different</a>
                            <a href="/#unleash-your-potential" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">Unleash Your Potential</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('mentors') }}" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">Mentors</a>
                <a href="/courses" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">Courses</a>
                <a href="/#testimonials" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">Testimonials</a>
                <a href="/#faq" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">FAQ</a>
            </div>

            <!-- Search Bar + Language Switcher + Login/Profile Button (Only Large Screens) -->
            <div class="hidden lg:flex items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="search" id="desktop-search" placeholder="Search courses, mentors..." class="navbar-search h-10 w-48 pl-8 pr-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300">
                    
                    <!-- Search Results Dropdown -->
                    <div id="desktop-search-results" class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden max-h-96 overflow-y-auto w-96">
                        <!-- Results will be populated here -->
                    </div>
                </div>
                
                <!-- Language Switcher -->
                <div class="relative inline-block text-left">
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
                                    </span>
                                </button>
                                <button type="submit" name="lang" value="hr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'hr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                    <span class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                        </svg>
                                        <span>Hrvatski</span>
                                    </span>
                                </button>
                                <button type="submit" name="lang" value="sr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'sr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                    <span class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                        </svg>
                                        <span>Српски</span>
                                    </span>
                                </button>
                                <button type="submit" name="lang" value="sl" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'sl' ? 'bg-purple-50 text-purple-700' : '' }}">
                                    <span class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                        </svg>
                                        <span>Slovenščina</span>
                                    </span>
                                </button>
                                <button type="submit" name="lang" value="mk" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 {{ app()->getLocale() === 'mk' ? 'bg-purple-50 text-purple-700' : '' }}">
                                    <span class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                        </svg>
                                        <span>Македонски</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                @auth
                    <!-- User Profile Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 focus:outline-none">
                            <!-- User Avatar -->
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center border-2 border-purple-200">
                                @php
                                    $user = auth()->user();
                                @endphp
                                @if($user->isMentor() && $user->mentor && $user->mentor->photo)
                                    <img src="{{ asset('storage/' . $user->mentor->photo) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-full h-full rounded-full object-cover">
                                @else
                                    <div class="w-full h-full rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <!-- Dropdown Arrow -->
                            <svg class="w-4 h-4 text-gray-600 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top scale-95 group-hover:scale-100 z-50">
                            <div class="py-2">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->isMentor() ? 'Mentor' : 'Student' }}</p>
                                </div>
                                
                                <!-- Dashboard Link -->
                                <a href="{{ auth()->user()->isMentor() ? route('mentor.dashboard') : route('user.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200">
                                    <svg class="mr-2 text-gray-600 w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="7" height="7" rx="1.5" />
                                        <rect x="14" y="3" width="7" height="7" rx="1.5" />
                                        <rect x="14" y="14" width="7" height="7" rx="1.5" />
                                        <rect x="3" y="14" width="7" height="7" rx="1.5" />
                                    </svg>
                                    Dashboard
                                </a>
                                
                                <!-- Logout Button -->
                                <button onclick="showLogoutModal()" 
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
                                    <svg class="mr-2 w-4 h-4 inline-block text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('user.onboarding.login') }}" class="px-6 py-2 border bg-[#6E3FF3] text-white rounded hover:bg-white hover:text-[#6E3FF3] transition-colors duration-300">
                        Login
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button (Mobile + Tablet) -->
            <div class="lg:hidden">
                <button id="mobile-menu-button" class="text-purple-700 hover:text-purple-600 transition-colors duration-300 drop-shadow-sm focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation (Mobile + Tablet) -->
        <div id="mobile-menu" class="lg:hidden hidden">
            <div class="px-6 pb-4 space-y-3 bg-white border-t border-gray-100">
                <!-- Mobile Search -->
                <div class="px-3 py-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="search" id="mobile-search" placeholder="Search courses, mentors..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                        
                        <!-- Mobile Search Results -->
                        <div id="mobile-search-results" class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden max-h-96 overflow-y-auto w-full">
                            <!-- Results will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Mobile Language Switcher -->
                <div class="px-3 py-2">
                    <div class="relative">
                        <form method="POST" action="{{ route('lang.switch') }}">
                            @csrf
                            <button type="button" id="mobile-language-toggle" onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center justify-between w-full px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-globe text-gray-600"></i>
                                    <span class="text-sm font-medium text-gray-700">{{ strtoupper(app()->getLocale()) }}</span>
                                </div>
                                <svg id="mobile-language-arrow" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Mobile Language Menu -->
                            <div id="mobile-language-menu" class="hidden mt-2 border border-gray-200 rounded-lg bg-white shadow-sm">
                                <div class="py-2">
                                    <button type="submit" name="lang" value="en" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ app()->getLocale() === 'en' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            <span>English</span>
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="hr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ app()->getLocale() === 'hr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            <span>Hrvatski</span>
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="sr" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ app()->getLocale() === 'sr' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            <span>Српски</span>
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="sl" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ app()->getLocale() === 'sl' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            <span>Slovenščina</span>
                                        </span>
                                    </button>
                                    <button type="submit" name="lang" value="mk" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ app()->getLocale() === 'mk' ? 'bg-purple-50 text-purple-700' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            <span>Македонски</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Mobile About Us Dropdown -->
                <div class="">
                    <button id="mobile-about-toggle" class="flex flex-row items-center justify-start w-full text-left text-gray-700 hover:text-purple-700 transition-colors duration-300 font-semibold">
                        <span>About Us</span>
                        <svg id="mobile-about-arrow" class="ml-2 w-3 h-3 transition-transform duration-300 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div id="mobile-about-menu" class="hidden mt-2 ml-4 flex flex-col items-start space-y-1">
                        <a href="/#how-it-works" class="block text-sm text-gray-700 hover:text-purple-700 transition-colors duration-200 pl-0 text-left w-full">How It Works</a>
                        <a href="/#what-we-offer" class="block text-sm text-gray-700 hover:text-purple-700 transition-colors duration-200 pl-0 text-left w-full">What We Offer</a>
                        <a href="/#why-we-are-different" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200 pl-0 text-left w-full">Why We are Different</a>
                        <a href="/#unleash-your-potential" class="block text-sm text-gray-700 hover:text-purple-700 transition-colors duration-200 pl-0 text-left w-full">Unleash Your Potential</a>
                    </div>
                </div>

                <a href="{{ route('mentors') }}" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Mentors</a>
                <a href="/courses" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Courses</a>
                <a href="/#testimonials" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Testimonials</a>
                <a href="/#faq" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">FAQ</a>
                
                <!-- Mobile Login/Dashboard Button -->
                <div class="px-3 py-2">
                    @auth
                        <!-- Mobile User Profile Dropdown -->
                        <div class="relative">
                            <button id="mobile-profile-toggle" class="flex items-center justify-between w-full px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center space-x-3">
                                    <!-- User Avatar -->
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center border-2 border-purple-200">
                                        @php
                                            $user = auth()->user();
                                        @endphp
                                        @if($user->isMentor() && $user->mentor && $user->mentor->photo)
                                            <img src="{{ asset('storage/' . $user->mentor->photo) }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="w-full h-full rounded-full object-cover">
                                        @else
                                            <div class="w-full h-full rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                </div>
                                <svg id="mobile-profile-arrow" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Mobile Dropdown Menu -->
                            <div id="mobile-profile-menu" class="hidden mt-2 border border-gray-200 rounded-lg bg-white shadow-sm">
                                <div class="py-2">
                                    <!-- Dashboard Link -->
                                    <a href="{{ auth()->user()->isMentor() ? route('mentor.dashboard') : route('user.dashboard') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <svg class="mr-2 text-gray-600 w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 24 24">
                                            <rect x="3" y="3" width="7" height="7" rx="1.5" />
                                            <rect x="14" y="3" width="7" height="7" rx="1.5" />
                                            <rect x="14" y="14" width="7" height="7" rx="1.5" />
                                            <rect x="3" y="14" width="7" height="7" rx="1.5" />
                                        </svg>
                                        Dashboard
                                    </a>
                                    
                                    <!-- Logout Button -->
                                    <button onclick="showLogoutModal()" 
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <svg class="mr-2 w-4 h-4 inline-block text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('user.onboarding.login') }}" class="block w-full text-center px-4 py-2 border border-purple-700 text-purple-700 rounded hover:bg-purple-700 hover:text-white transition-colors duration-300">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileAboutToggle = document.getElementById('mobile-about-toggle');
    const mobileAboutMenu = document.getElementById('mobile-about-menu');
    const mobileAboutArrow = document.getElementById('mobile-about-arrow');
    const mobileProfileToggle = document.getElementById('mobile-profile-toggle');
    const mobileProfileMenu = document.getElementById('mobile-profile-menu');
    const mobileProfileArrow = document.getElementById('mobile-profile-arrow');
    const mobileLanguageToggle = document.getElementById('mobile-language-toggle');
    const mobileLanguageMenu = document.getElementById('mobile-language-menu');
    const mobileLanguageArrow = document.getElementById('mobile-language-arrow');

    // Toggle mobile menu
    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
    });

    // Toggle mobile about submenu
    mobileAboutToggle.addEventListener('click', function() {
        mobileAboutMenu.classList.toggle('hidden');
        mobileAboutArrow.textContent = mobileAboutMenu.classList.contains('hidden') ? '▼' : '▲';
    });

    // Toggle mobile profile submenu
    if (mobileProfileToggle) {
        mobileProfileToggle.addEventListener('click', function() {
            mobileProfileMenu.classList.toggle('hidden');
            mobileProfileArrow.classList.toggle('rotate-180');
        });
    }

    // Toggle mobile language submenu
    if (mobileLanguageToggle) {
        mobileLanguageToggle.addEventListener('click', function() {
            mobileLanguageMenu.classList.toggle('hidden');
            mobileLanguageArrow.classList.toggle('rotate-180');
        });
    }

    // Close mobile menu when clicking on navigation links
    const mobileNavLinks = document.querySelectorAll('#mobile-about-menu a, #mobile-profile-menu a, #mobile-language-menu button');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
            mobileAboutMenu.classList.add('hidden');
            mobileAboutArrow.textContent = '▼';
            if (mobileProfileMenu) {
                mobileProfileMenu.classList.add('hidden');
                mobileProfileArrow.classList.remove('rotate-180');
            }
            if (mobileLanguageMenu) {
                mobileLanguageMenu.classList.add('hidden');
                mobileLanguageArrow.classList.remove('rotate-180');
            }
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.add('hidden');
            mobileAboutMenu.classList.add('hidden');
            mobileAboutArrow.textContent = '▼';
            if (mobileProfileMenu) {
                mobileProfileMenu.classList.add('hidden');
                mobileProfileArrow.classList.remove('rotate-180');
            }
            if (mobileLanguageMenu) {
                mobileLanguageMenu.classList.add('hidden');
                mobileLanguageArrow.classList.remove('rotate-180');
            }
        }
    });

    // Close mobile menu on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            mobileMenu.classList.add('hidden');
            mobileAboutMenu.classList.add('hidden');
            mobileAboutArrow.textContent = '▼';
            if (mobileProfileMenu) {
                mobileProfileMenu.classList.add('hidden');
                mobileProfileArrow.classList.remove('rotate-180');
            }
            if (mobileLanguageMenu) {
                mobileLanguageMenu.classList.add('hidden');
                mobileLanguageArrow.classList.remove('rotate-180');
            }
        }
    });
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

// Close logout modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('logout-modal');
    const modalContent = document.getElementById('logout-modal-content');
    
    if (event.target === modal) {
        cancelLogout();
    }
});

// Close logout modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('logout-modal');
        if (!modal.classList.contains('hidden')) {
            cancelLogout();
        }
    }
});

// Search functionality
let searchTimeout;
const searchInputs = ['desktop-search', 'mobile-search'];
const searchResults = ['desktop-search-results', 'mobile-search-results'];

searchInputs.forEach((inputId, index) => {
    const input = document.getElementById(inputId);
    const results = document.getElementById(searchResults[index]);
    
    if (input && results) {
        // Handle input changes
        input.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Hide results if query is too short
            if (query.length < 2) {
                results.classList.add('hidden');
                return;
            }
            
            // Set timeout for search (debouncing)
            searchTimeout = setTimeout(() => {
                performSearch(query, results);
            }, 300);
        });
        
        // Handle focus
        input.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                results.classList.remove('hidden');
            }
        });
        
        // Handle click outside to close results
        document.addEventListener('click', function(event) {
            if (!input.contains(event.target) && !results.contains(event.target)) {
                results.classList.add('hidden');
            }
        });
    }
});

// Perform search function
function performSearch(query, resultsContainer) {
    fetch(`/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data.results, resultsContainer);
            resultsContainer.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Search error:', error);
            resultsContainer.classList.add('hidden');
        });
}

// Display search results
function displaySearchResults(results, container) {
    if (!results || results.length === 0) {
        container.innerHTML = `
            <div class="p-4 text-center text-gray-500">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p>No results found</p>
            </div>
        `;
        return;
    }
    
    // Helper function to truncate text
    function truncateText(text, maxLength = 200) {
        if (!text || text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
    
    // Helper function to safely get nested properties
    function safeGet(obj, path, defaultValue = 'N/A') {
        try {
            return path.split('.').reduce((current, key) => current && current[key], obj) || defaultValue;
        } catch (e) {
            return defaultValue;
        }
    }
    
    const resultsHTML = results.map((result, index) => {
        if (result.type === 'course') {
            return `
                <a href="${safeGet(result, 'url', '#')}" class="block p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors duration-200">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 19 7.5 19s3.332-.477 4.5-1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.747 0-3.332-.523-4.5-1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Course
                                </span>
                                <h3 class="text-sm font-semibold text-gray-900 leading-tight">${truncateText(safeGet(result, 'title', 'Untitled Course'), 60)}</h3>
                            </div>
                            <p class="text-xs text-gray-600 mb-2 leading-relaxed">${truncateText(safeGet(result, 'category', 'N/A') + (safeGet(result, 'sub_categories') ? ' • ' + safeGet(result, 'sub_categories') : ''), 80)}</p>
                            <p class="text-xs text-gray-500 mb-2">by ${truncateText(safeGet(result, 'mentor', 'Unknown Mentor'), 40)}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <span class="text-xs text-gray-600 font-medium">${safeGet(result, 'rating') ? Number(safeGet(result, 'rating')).toFixed(1) : 'N/A'}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">(${safeGet(result, 'reviews_count', 0)} reviews)</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">$${safeGet(result, 'price', '0')}</span>
                            </div>
                        </div>
                    </div>
                </a>
            `;
        } else if (result.type === 'mentor') {
            return `
                <a href="${safeGet(result, 'url', '#')}" class="block p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors duration-200">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Mentor
                                </span>
                                <h3 class="text-sm font-semibold text-gray-900 leading-tight">${truncateText(safeGet(result, 'title', 'Unknown Mentor'), 60)}</h3>
                            </div>
                            <p class="text-xs text-gray-600 mb-2 leading-relaxed">${truncateText(safeGet(result, 'category', 'N/A') + (safeGet(result, 'sub_categories') ? ' • ' + safeGet(result, 'sub_categories') : ''), 80)}</p>
                            <p class="text-xs text-gray-500 mb-2">${safeGet(result, 'experience', 'N/A')}</p>
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">${safeGet(result, 'rating') ? Number(safeGet(result, 'rating')).toFixed(1) : 'N/A'}</span>
                                </div>
                                <span class="text-xs text-gray-500">(${safeGet(result, 'reviews_count', 0)} reviews)</span>
                            </div>
                        </div>
                    </div>
                </a>
            `;
        } else {
            return ''; // Skip unknown types
        }
    }).join('');
    
    container.innerHTML = resultsHTML;
}
</script>
