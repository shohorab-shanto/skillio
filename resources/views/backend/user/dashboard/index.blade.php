@extends('backend.layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="p-6">

    {{-- middle statistics cards --}}
    <div class="w-full mb-5 mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-y-8 gap-x-5 justify-between">
        {{-- card 1 - Active Courses --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 2.31L18.6 7 12 9.69 5.4 7 12 4.31zM4 8.5l8 4 8-4V16l-8 4-8-4V8.5z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Active Course</span>
            </div>
            <p class="text-4xl font-extrabold mt-3 text-purple-600">{{ $activeCourses }}</p>
            <p class="fa-solid fa-chart-line text-xs text-gray-400"> 
                @if($activeCourses > 0)
                    {{ $activeCourses }} in progress
                @else
                    No active courses
                @endif
            </p>
        </div>

        {{-- card 2 - Completed Courses --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Completed Course</span>
            </div>
            <p class="text-4xl font-extrabold mt-3 text-green-600">{{ $completedCourses }}</p>
            <p class="fa-solid fa-chart-line text-xs text-gray-400"> 
                @if($completedCourses > 0)
                    +{{ $completedCourses }} total completed
                @else
                    No completed courses
                @endif
            </p>
        </div>

        {{-- card 3 - Upcoming Courses --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Upcoming Course</span>
            </div>
            <p class="text-4xl font-extrabold mt-3 text-blue-600">{{ $upcomingCount }}</p>
            <p class="fa-solid fa-chart-line text-xs text-gray-400"> 
                @if($upcomingCount > 0)
                    Next: {{ $upcomingCoursesDisplay->first()?->enrollable?->start_date?->format('M d') ?? 'Soon' }}
                @else
                    No upcoming courses
                @endif
            </p>
        </div>

        {{-- card 4 - Learning Hours --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Learning Hours</span>
            </div>
            <p class="text-4xl font-extrabold mt-3 text-orange-600">{{ $learningHours }}</p>
            <p class="fa-solid fa-chart-line text-xs text-gray-400"> 
                @if($learningHours > 0)
                    +{{ $learningHours }} this month
                @else
                    No learning hours
                @endif
            </p>
        </div>
    </div>

    {{-- under sections --}}
    <div class="w-full mb-5 mx-auto grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-5">
        {{-- left - All Courses --}}
        <div class="w-full bg-white p-5 rounded-2xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <p class="font-semibold text-xl">All Courses</p>
                <a href="{{ route('user.courses') }}" class="font-semibold text-xl text-purple-600 hover:text-purple-800">See All</a>
            </div>
            
            @forelse($currentCourses as $enrollment)
                @php
                    $course = $enrollment->enrollable;
                    $mentor = $course->mentor->user;
                @endphp
                <div class="border p-5 rounded-3xl mb-4 hover:shadow-md transition-shadow">
                    <h1 class="text-xl font-semibold mb-3">{{ $course->title }}</h1>
                    <div class="flex justify-between w-full h-32">
                        <div>
                            <div class="flex items-center mb-2">
                                @if($mentor->photo)
                                    <img src="{{ asset('storage/' . $mentor->photo) }}" class="w-10 h-10 rounded-full object-cover" alt="{{ $mentor->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-purple-600"></i>
                                    </div>
                                @endif
                                <p class="text-sm text-gray-500 ml-2">{{ $mentor->name }}</p>
                            </div>
                            <p class="text-sm text-gray-600">
                                @if($course->start_date && $course->end_date)
                                    Duration: {{ $course->start_date->diffInMonths($course->end_date) }} Months
                                @else
                                    Duration: Ongoing
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="mt-3 mb-1 flex text-sm text-gray-600">
                                <span class="text-yellow-500 text-base mr-1">★</span>
                                <span class="font-semibold">{{ number_format($course->averageRating(), 1) }}</span>
                            </div>
                            <p class="text-sm text-gray-500">
                                @if($course->start_date)
                                    {{ $course->start_date->format('M d, Y') }}
                                @else
                                    Ongoing
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user.courses.show', $course) }}"
                        class="px-6 py-2 border bg-[#6E3FF3] text-white rounded hover:bg-white hover:text-[#6E3FF3] transition-colors duration-300 inline-block">
                        Continue Learning
                    </a>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-book text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Courses</h3>
                    <p class="text-gray-500 text-sm">Start your learning journey by enrolling in courses!</p>
                    <a href="{{ route('courses.index') }}" class="mt-4 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors inline-block">
                        Browse Courses
                    </a>
                </div>
            @endforelse
        </div>

        {{-- right - Upcoming Courses --}}
        <div class="w-full">
            <div class="w-full bg-white p-5 rounded-2xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <p class="font-semibold text-xl">Upcoming Courses</p>
                    <a href="{{ route('user.courses') }}" class="font-semibold text-xl text-purple-600 hover:text-purple-800">See All</a>
                </div>
                
                @forelse($upcomingCoursesDisplay as $enrollment)
                    @php
                        $course = $enrollment->enrollable;
                        $mentor = $course->mentor->user;
                    @endphp
                    <div class="border p-5 rounded-3xl mb-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between w-full">
                            <div>
                                <div class="flex items-center mb-2">
                                    @if($mentor->photo)
                                        <img src="{{ asset('storage/' . $mentor->photo) }}" class="w-10 h-10 rounded-full object-cover" alt="{{ $mentor->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-purple-600"></i>
                                        </div>
                                    @endif
                                    <p class="text-sm text-gray-500 ml-2">{{ $mentor->name }}</p>
                                </div>
                                <p class="text-sm text-gray-600">
                                    @if($course->start_date && $course->end_date)
                                        Duration: {{ $course->start_date->diffInMonths($course->end_date) }} Months
                                    @else
                                        Duration: TBD
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="mt-3 mb-1 text-sm text-gray-600">
                                    @if($course->start_date)
                                        {{ $course->start_date->format('g:i A') }}
                                    @else
                                        TBD
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500">
                                    @if($course->start_date)
                                        {{ $course->start_date->format('M d, Y') }}
                                    @else
                                        Date TBD
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Upcoming Courses</h3>
                        <p class="text-gray-500 text-sm">All your courses are currently active or completed.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection