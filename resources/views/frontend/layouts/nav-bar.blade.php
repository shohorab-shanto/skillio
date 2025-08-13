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
                        <span class="transition-transform duration-300 group-hover:rotate-180">▼</span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute top-full left-0 mt-2 w-52 bg-white rounded-box shadow-md border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top scale-95 group-hover:scale-100 z-10">
                        <div class="p-2">
                            <a href="#how-it-works" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">How It Works</a>
                            <a href="#what-we-offer" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">What We Offer</a>
                            <a href="#why-we-are-different" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">Why We are Different</a>
                            <a href="#unleash-your-potential" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-purple-700 transition-colors duration-200 rounded">Unleash Your Potential</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('mentors') }}" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">Mentors</a>
                <a href="#courses" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">Courses</a>
                <a href="#testimonials" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">Testimonials</a>
                <a href="#faq" class="nav-item text-gray-700 hover:text-purple-700 transition-colors duration-300 drop-shadow-sm">FAQ</a>
            </div>

            <!-- Search Bar + Login Button (Only Large Screens) -->
            <div class="hidden lg:flex items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="search" placeholder="Search" class="navbar-search h-10 w-48 pl-8 pr-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300">
                </div>
                
                <a href="{{ route('login') }}" class="px-6 py-2 border bg-[#6E3FF3] text-white rounded hover:bg-white hover:text-[#6E3FF3] transition-colors duration-300">
                    Login
                </a>
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
                        <input type="search" placeholder="Search" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                    </div>
                </div>

                <!-- Mobile About Us Dropdown -->
                <div class="px-3 py-2">
                    <button id="mobile-about-toggle" class="flex items-center justify-between w-full text-left text-gray-700 hover:text-purple-700 transition-colors duration-300 font-semibold">
                        <span>About Us</span>
                        <span id="mobile-about-arrow" class="transition-transform duration-300">▼</span>
                    </button>
                    <div id="mobile-about-menu" class="hidden mt-2 ml-4 space-y-1">
                        <a href="#how-it-works" class="block text-sm text-gray-700 hover:text-purple-700 transition-colors duration-200">How It Works</a>
                        <a href="#what-we-offer" class="block text-sm text-gray-700 hover:text-purple-700 transition-colors duration-200">What We Offer</a>
                        <a href="#why-we-are-different" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Why We are Different</a>
                        <a href="#unleash-your-potential" class="block text-sm text-gray-700 hover:text-purple-700 transition-colors duration-200">Unleash Your Potential</a>
                    </div>
                </div>

                <a href="{{ route('mentors') }}" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Mentors</a>
                <a href="#courses" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Courses</a>
                <a href="#testimonials" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">Testimonials</a>
                <a href="#faq" class="block text-gray-700 hover:text-purple-700 transition-colors duration-200">FAQ</a>
                
                <!-- Mobile Login Button -->
                <div class="px-3 py-2">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 border border-purple-700 text-purple-700 rounded hover:bg-purple-700 hover:text-white transition-colors duration-300">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileAboutToggle = document.getElementById('mobile-about-toggle');
    const mobileAboutMenu = document.getElementById('mobile-about-menu');
    const mobileAboutArrow = document.getElementById('mobile-about-arrow');

    // Toggle mobile menu
    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
    });

    // Toggle mobile about submenu
    mobileAboutToggle.addEventListener('click', function() {
        mobileAboutMenu.classList.toggle('hidden');
        mobileAboutArrow.textContent = mobileAboutMenu.classList.contains('hidden') ? '▼' : '▲';
    });

    // Close mobile menu when clicking on navigation links
    const mobileNavLinks = document.querySelectorAll('#mobile-about-menu a');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
            mobileAboutMenu.classList.add('hidden');
            mobileAboutArrow.textContent = '▼';
        });
    });

            // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                mobileAboutMenu.classList.add('hidden');
                mobileAboutArrow.textContent = '▼';
            }
        });

                    // Close mobile menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                mobileMenu.classList.add('hidden');
                mobileAboutMenu.classList.add('hidden');
                mobileAboutArrow.textContent = '▼';
            }
        });
    });
</script>
