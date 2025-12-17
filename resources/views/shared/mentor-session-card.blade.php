<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
    <!-- Header with Day and Time -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
            <h3 class="font-semibold text-gray-900">
                {{ \Carbon\Carbon::parse($session->date)->format('l') }}
            </h3>
        </div>
        <div class="flex items-center space-x-2">
            <!-- Session Status -->
            @if($session->status == 'booked')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500 text-white">
                    <i class="fa-solid fa-user mr-1"></i>
                    {{ __('trans.booked') }}
                </span>
            @elseif($session->status == 'available')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                    {{ __('trans.available') }}
                </span>
            @elseif($session->status == 'completed')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                    {{ __('trans.completed') }}
                </span>
            @elseif($session->status == 'cancelled')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                    {{ __('trans.cancelled') }}
                </span>
            @endif
        </div>
    </div>

    <!-- Time Range -->
    <div class="mb-4">
        <h4 class="text-xl font-bold text-gray-900 mb-2">
            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
            {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
        </h4>
    </div>

    <!-- Categories/Subjects -->
    <div class="mb-4">
        <div class="flex flex-wrap gap-2">
            <!-- Main Category -->
            @if($session->category)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    {{ $session->category->name }}
                </span>
            @endif
            
            <!-- Sub Categories -->
            @if($session->subCategories && count($session->subCategories) > 0)
                @foreach($session->subCategories as $subCategory)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        {{ $subCategory->name }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Date and Session Info -->
    <div class="mb-4">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
            <span>{{ \Carbon\Carbon::parse($session->date)->format('j F Y') }}</span>
            <span class="text-purple-600 font-medium">
                @php
                    $startTime = \Carbon\Carbon::parse($session->start_time);
                    $endTime = \Carbon\Carbon::parse($session->end_time);
                    $totalMinutes = $startTime->diffInMinutes($endTime);
                    $hours = intval($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                @endphp
                @if($hours > 0 && $minutes > 0)
                    {{ $hours }}h {{ $minutes }}m {{ __('trans.session') }}
                @elseif($hours > 0)
                    {{ $hours }} {{ __('trans.hour_session') }}
                @else
                    {{ $minutes }} {{ __('trans.minute_session') }}
                @endif
            </span>
        </div>
    </div>

    <!-- Fee and Type -->
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-gray-600">
            @php
                $currencyCode = strtoupper($session->currency ?? 'USD');
                $currencySymbol = $currencyCode === 'EUR' ? '€' : '$';
            @endphp
            <span class="mr-1">{{ $currencySymbol }}</span>{{ number_format($session->fee ?? 0, 2) }}
        </div>
        <div class="text-sm text-gray-600">
            @if(($session->type ?? 'online') == 'online')
                <!-- FontAwesome Video Icon -->
                <i class="fas fa-video mr-1" style="display: inline-block !important;"></i>
                <!-- Fallback SVG Video Icon -->
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
            @else
                <!-- FontAwesome Location Icon -->
                <i class="fas fa-map-marker-alt mr-1" style="display: inline-block !important;"></i>
                <!-- Fallback SVG Location Icon -->
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            @endif
            {{ ucfirst($session->type ?? 'online') }}
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end">
        <!-- Book Now Button - Bottom Right Corner -->
        @if($session->status == 'active')
            <a href="{{ route('checkout.session', $session->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                <!-- FontAwesome Calendar Icon -->
                <i class="fas fa-calendar-plus mr-2" style="display: inline-block !important;"></i>
                <!-- Fallback SVG Calendar Icon -->
                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                {{ __('trans.book_now') }}
            </a>
        @else
            <span class="text-sm text-gray-400 font-medium">{{ __('trans.not_available') }}</span>
        @endif
    </div>
</div>

<script>
// Check if FontAwesome is loaded and show fallback SVG if needed
document.addEventListener('DOMContentLoaded', function() {
    // Check if FontAwesome is available
    if (typeof FontAwesome == 'undefined' || !document.querySelector('.fas')) {
        // Show SVG fallbacks
        document.querySelectorAll('svg[style*="display: none"]').forEach(svg => {
            svg.style.display = 'inline';
        });
        // Hide FontAwesome icons
        document.querySelectorAll('.fas').forEach(icon => {
            icon.style.display = 'none';
        });
    }
    
    // Additional check for calendar icon specifically
    setTimeout(function() {
        const calendarIcon = document.querySelector('.fa-calendar-plus');
        if (calendarIcon && calendarIcon.offsetWidth == 0) {
            // Calendar icon is not visible, show SVG fallback
            const calendarSvg = calendarIcon.parentElement.querySelector('svg');
            if (calendarSvg) {
                calendarSvg.style.display = 'inline';
                calendarIcon.style.display = 'none';
            }
        }
    }, 100);
});
</script>
