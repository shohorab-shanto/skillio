<section id="mentors" class="max-w-[1400px] mx-auto mt-20 px-6">
    <x-section-header 
        title="Find Your Perfect Mentor"
        subtitle="Connecting with mentors who have many years of knowledge and experience in their fields."
        class="mb-8"
    />

    <div
        class="max-w-[1200px] w-full mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6 justify-items-center pt-5">
        
        @forelse($topMentors as $mentor)
        <div class="bg-white rounded-xl shadow-md p-6 w-full max-w-sm">
            <div class="flex items-center space-x-4">
                @if($mentor->photo)
                    <img class="w-14 h-14 rounded-full object-cover" 
                         src="{{ asset('storage/' . $mentor->photo) }}" 
                         alt="{{ $mentor->user->name }}" />
                @else
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($mentor->user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $mentor->user->name }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ $mentor->work_experience ? Str::limit($mentor->work_experience, 30) : 'Professional Mentor' }}
                    </p>
                </div>
            </div>

            <div class="mt-3 flex items-center text-sm text-gray-600">
                <span class="text-yellow-500 text-base mr-1">★</span>
                <span class="font-semibold">{{ number_format($mentor->reviews_avg_rating ?? 0, 1) }}</span>
                <span class="ml-1 text-gray-400">({{ $mentor->total_reviews ?? 0 }} reviews)</span>
            </div>

            <p class="mt-4 text-gray-700 text-sm">
                {{ $mentor->bio ? Str::limit($mentor->bio, 100) : 'Experienced mentor with expertise in various fields.' }}
            </p>

            <div class="flex flex-wrap gap-2 mt-4">
                @if($mentor->sessionBookings->isNotEmpty())
                    @php
                        $categories = $mentor->sessionBookings->pluck('category.name')->unique()->take(3);
                        $subCategories = $mentor->sessionBookings->pluck('subCategories.*.name')->flatten()->unique()->take(2);
                        $tags = $categories->merge($subCategories)->take(4);
                    @endphp
                    
                    @foreach($tags as $tag)
                        @if($tag)
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ $tag }}</span>
                        @endif
                    @endforeach
                @else
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Professional</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Expert</span>
                @endif
            </div>

            <div class="mt-5 flex justify-between items-center">
                <div class="text-xl font-bold text-gray-900">
                    ${{ $mentor->lowest_session_rate ?? 'N/A' }}<span class="text-sm font-normal text-gray-500">/hour</span>
                </div>
                <a href="{{ route('mentor.sessions', $mentor->id) }}" 
                   class="bg-violet-600 hover:bg-violet-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                    Book session
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-500">
                <i class="fa-solid fa-user-tie text-4xl mb-4"></i>
                <p class="text-lg font-medium">No mentors available at the moment</p>
                <p class="text-sm">Please check back later for available mentors</p>
            </div>
        </div>
        @endforelse
    </div>
    <div class="flex justify-center mt-8">
        <a href="{{ route('mentors') }}" class="border-2 px-5 py-3 border-violet-600 rounded-lg">
            View All Mentors
        </a>
    </div>

</section>
