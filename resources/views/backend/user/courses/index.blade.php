@extends('backend.layouts.app')

@section('title', 'My Courses')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Courses</h1>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Courses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
                </div>
            </div>
        </div>

        <!-- Active Courses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-play-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_courses'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Courses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_courses'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('user.courses') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Courses</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by course title or description..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Category Filter -->
            <div class="w-full md:w-48">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="category" 
                        name="category" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-40">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" 
                        name="status" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="w-full md:w-auto">
                <button type="submit" 
                        class="w-full md:w-auto bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>
                    Filter
                </button>
            </div>

            <!-- Clear Filters -->
            @if(request()->hasAny(['search', 'category', 'status']))
                <div class="w-full md:w-auto">
                    <a href="{{ route('user.courses') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Courses Grid -->
    @if($enrollments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($enrollments as $enrollment)
                @php
                    $course = $enrollment->enrollable;
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Course Image -->
                    <div class="relative">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-purple-600 via-fuchsia-500 to-pink-500 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 2.31L18.6 7 12 9.69 5.4 7 12 4.31zM4 8.5l8 4 8-4V16l-8 4-8-4V8.5z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            @if($enrollment->enrollment_status === 'active')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fa-solid fa-play-circle mr-1"></i>
                                    Active
                                </span>
                            @elseif($enrollment->enrollment_status === 'completed')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fa-solid fa-check-circle mr-1"></i>
                                    Completed
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="p-6">
                        <!-- Course Title -->
                        <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                        
                        <!-- Course Meta -->
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                            <span class="flex items-center">
                                <i class="fa-solid fa-tag mr-1"></i>
                                {{ $course->category->name }}
                            </span>
                            @if($course->duration_days)
                                <span class="flex items-center">
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    {{ $course->duration_days }} days
                                </span>
                            @endif
                        </div>

                        <!-- Mentor Info -->
                        <div class="flex items-center mb-4">
                            @php
                                $mentorPhotoPath = $course->mentor->photo
                                    ? (Str::startsWith($course->mentor->photo, ['http://', 'https://', '/storage/']) 
                                        ? $course->mentor->photo 
                                        : Storage::url($course->mentor->photo))
                                    : asset('assets/images/user-avatar.png');
                            @endphp
                            <img src="{{ $mentorPhotoPath }}" 
                                 alt="{{ $course->mentor->name }}" 
                                 class="w-8 h-8 rounded-full object-cover mr-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $course->mentor->name }}</p>
                                <p class="text-xs text-gray-500">Instructor</p>
                            </div>
                        </div>

                        <!-- Enrollment Date -->
                        <div class="text-sm text-gray-500 mb-4">
                            <i class="fa-solid fa-calendar mr-1"></i>
                            Enrolled: {{ $enrollment->created_at->format('M d, Y') }}
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <a href="{{ $enrollment->conversation ? route('chat.show', $enrollment->conversation->unique_code) : route('chat.index') }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                <i class="fa-solid fa-comment mr-2"></i>
                                Chat
                            </a>
                            <a href="{{ route('user.courses.show', $enrollment) }}" 
                               class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                <i class="fa-solid fa-eye mr-2"></i>
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            <x-custom-pagination :paginator="$enrollments->appends(request()->query())" />
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-graduation-cap text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Courses Found</h3>
            <p class="text-gray-500 mb-6">
                @if(request()->hasAny(['search', 'category', 'status']))
                    No courses match your current filters. Try adjusting your search criteria.
                @else
                    You haven't enrolled in any courses yet. Start learning today!
                @endif
            </p>
            <div class="flex justify-center space-x-4">
                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('user.courses') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-times mr-2"></i>
                        Clear Filters
                    </a>
                @endif
                <a href="{{ route('courses') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fa-solid fa-search mr-2"></i>
                    Browse Courses
                </a>
            </div>
        </div>
    @endif
@endsection
