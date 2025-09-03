@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
        <div class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
            {{ __('trans.showing') }} {{ $paginator->firstItem() }} {{ __('trans.to') }} {{ $paginator->lastItem() }} {{ __('trans.of') }} {{ $paginator->total() }} {{ __('trans.results') }}
        </div>
        <div class="w-full sm:w-auto">
            <nav class="flex items-center justify-center space-x-1 sm:space-x-2 overflow-x-auto">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="flex items-center px-2 sm:px-3 py-1 sm:py-2 text-gray-400 bg-white border border-gray-300 rounded text-xs sm:text-sm cursor-not-allowed whitespace-nowrap">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="hidden sm:inline">{{ __('trans.previous') }}</span>
                        <span class="sm:hidden">Prev</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="flex items-center px-2 sm:px-3 py-1 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded text-xs sm:text-sm hover:bg-gray-50 transition-colors duration-200 whitespace-nowrap">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="hidden sm:inline">{{ __('trans.previous') }}</span>
                        <span class="sm:hidden">Prev</span>
                    </a>
                @endif

                {{-- Pagination Elements - Responsive Display --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    
                    // On mobile, show limited pages
                    $showPages = 3; // Always show 3 pages max for better mobile experience
                    $half = floor($showPages / 2);
                    
                    $startPage = max(1, $currentPage - $half);
                    $endPage = min($lastPage, $currentPage + $half);
                    
                    // Adjust if we're near the beginning or end
                    if ($endPage - $startPage < $showPages - 1) {
                        if ($startPage == 1) {
                            $endPage = min($lastPage, $startPage + $showPages - 1);
                        } else {
                            $startPage = max(1, $endPage - $showPages + 1);
                        }
                    }
                @endphp

                {{-- First page and ellipsis --}}
                @if($startPage > 1)
                    <a href="{{ $paginator->url(1) }}" class="px-2 sm:px-3 py-1 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded text-xs sm:text-sm hover:bg-gray-50 transition-colors duration-200">1</a>
                    @if($startPage > 2)
                        <span class="px-1 text-gray-500 text-xs sm:text-sm">...</span>
                    @endif
                @endif

                {{-- Page numbers --}}
                @for($page = $startPage; $page <= $endPage; $page++)
                    @if ($page == $currentPage)
                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-white bg-purple-600 border border-purple-600 rounded text-xs sm:text-sm font-medium">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="px-2 sm:px-3 py-1 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded text-xs sm:text-sm hover:bg-gray-50 transition-colors duration-200">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Last page and ellipsis --}}
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="px-1 text-gray-500 text-xs sm:text-sm">...</span>
                    @endif
                    <a href="{{ $paginator->url($lastPage) }}" class="px-2 sm:px-3 py-1 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded text-xs sm:text-sm hover:bg-gray-50 transition-colors duration-200">{{ $lastPage }}</a>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="flex items-center px-2 sm:px-3 py-1 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded text-xs sm:text-sm hover:bg-gray-50 transition-colors duration-200 whitespace-nowrap">
                        <span class="hidden sm:inline">{{ __('trans.next') }}</span>
                        <span class="sm:hidden">Next</span>
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <span class="flex items-center px-2 sm:px-3 py-1 sm:py-2 text-gray-400 bg-white border border-gray-300 rounded text-xs sm:text-sm cursor-not-allowed whitespace-nowrap">
                        <span class="hidden sm:inline">{{ __('trans.next') }}</span>
                        <span class="sm:hidden">Next</span>
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
@endif
