<div class="bg-white rounded-xl shadow-md p-6 w-full max-w-sm hover:shadow-lg transition-shadow duration-300">
    <!-- Mentor Photo and Name -->
    <div class="flex items-center space-x-4 mb-4">
        @php
            $photoPath = $mentor['photo']
                ? (Str::startsWith($mentor['photo'], ['http://', 'https://', '/storage/']) 
                    ? $mentor['photo'] 
                    : Storage::url($mentor['photo']))
                : asset('assets/images/user-avatar.png');
        @endphp
        <img
            class="w-16 h-16 rounded-full object-cover"
            src="{{ $photoPath }}"
            alt="{{ $mentor['name'] }}"
            loading="lazy"
        />
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $mentor['name'] }}</h2>
            @if($mentor['top_category'])
                <p class="text-sm text-purple-600 font-medium">{{ $mentor['top_category'] }}</p>
            @endif
        </div>
    </div>

    <!-- Rating and Reviews -->
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <div class="flex text-yellow-500 mr-2">
            @for($i = 0; $i < $mentor['star_rating']['full_stars']; $i++)
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
            @endfor
            @if($mentor['star_rating']['half_star'])
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
            @endif
            @for($i = 0; $i < $mentor['star_rating']['empty_stars']; $i++)
                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
            @endfor
        </div>
        <span class="font-semibold text-gray-900">{{ $mentor['formatted_rating'] }}</span>
        <span class="ml-1 text-gray-500">({{ $mentor['total_reviews'] }} reviews)</span>
    </div>

    <!-- Bio -->
    <p class="text-gray-700 text-sm mb-4 leading-relaxed">
        {{ $mentor['bio'] ?: 'Experienced professional ready to guide you on your learning journey.' }}
    </p>

    <!-- Sub-categories of the top category -->
    @if($mentor['top_category_sub_categories'] && count($mentor['top_category_sub_categories']) > 0)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($mentor['top_category_sub_categories'] as $subCategory)
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">{{ $subCategory }}</span>
            @endforeach
        </div>
    @endif

    <!-- Book Session Button -->
    <div class="mt-4">
        <a href="{{ route('mentor.sessions', $mentor['id']) }}" class="block w-full bg-violet-600 hover:bg-violet-700 text-white px-5 py-3 rounded-lg text-sm font-semibold transition-colors duration-200 text-center transition-colors duration-200">
            Book Session
        </a>
    </div>
</div>