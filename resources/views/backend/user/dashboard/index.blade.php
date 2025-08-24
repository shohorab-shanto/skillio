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
<div>

    {{-- middle statistics cards --}}
    <div class="w-full mb-5 mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-y-8 gap-x-5 justify-between">
        {{-- card 1 - Active Courses --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center p-5 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('assets/images/user_dashboard-1.png') }}" alt="Active Courses" class="w-5 h-5 object-contain" />
                </div>
                <span class="text-sm text-gray-500">Active Course</span>
            </div>
            <p class="text-2xl font-extrabold mt-3 text-gray-700">{{ $activeCourses }}</p>
            <span class="flex items-center space-x-2 text-sm text-gray-800">
                <i class="fa-solid fa-chart-line"></i>
                <span>
                    @if($activeCourses > 0)
                        {{ $activeCourses }} in progress
                    @else
                        No active courses
                    @endif
                </span>
            </span>
        </div>

        {{-- card 2 - Completed Courses --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center p-5 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('assets/images/user_dashboard-2.png') }}" alt="Completed Courses" class="w-5 h-5 object-contain" />
                </div>
                <span class="text-sm text-gray-500">Completed Course</span>
            </div>
            <p class="text-2xl font-extrabold mt-3 text-gray-700">{{ $completedCourses }}</p>
            <span class="flex items-center space-x-2 text-sm text-gray-800">
                <i class="fa-solid fa-check-circle"></i>
                <span>
                    @if($completedCourses > 0)
                        {{ $completedCourses }} completed
                    @else
                        No courses
                    @endif
                </span>
            </span>
        </div>

        {{-- card 3 - Upcoming Sessions --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center p-5 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('assets/images/user_dashboard-3.png') }}" alt="Upcoming Sessions" class="w-5 h-5 object-contain" />
                </div>
                <span class="text-sm text-gray-500">Upcoming Sessions</span>
            </div>
            <p class="text-2xl font-extrabold mt-3 text-gray-700">{{ $upcomingCount }}</p>
            <span class="flex items-center space-x-2 text-sm text-gray-800">
                <i class="fa-solid fa-calendar-alt"></i>
                <span>
                    @if($upcomingCount > 0)
                        Next: {{ $upcomingSessionsDisplay->first()?->enrollable?->date?->format('M d') ?? 'Soon' }}
                    @else
                        No sessions
                    @endif
                </span>
            </span>
        </div>

        {{-- card 4 - Learning Hours --}}
        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center p-5 shadow-lg">
            <div class="flex gap-2 items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('assets/images/user_dashboard-4.png') }}" alt="Learning Hours" class="w-5 h-5 object-contain" />
                </div>
                <span class="text-sm text-gray-500">Learning Hours</span>
            </div>
            <p class="text-2xl font-extrabold mt-3 text-gray-700">{{ $learningHours }}</p>
            <span class="flex items-center space-x-2 text-sm text-gray-800">
                <i class="fa-solid fa-chart-line"></i>
                <span>
                    @if($learningHours > 0)
                        +{{ $learningHours }} this month
                    @else
                        No hours
                    @endif
                </span>
            </span>
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
                                    @php
                                        $diff = $course->start_date->diff($course->end_date);
                                        $months = $diff->m + ($diff->y * 12);
                                        $days = $diff->d;
                                    @endphp
                                    Duration: 
                                    @if($months > 0)
                                        {{ $months }} Month{{ $months > 1 ? 's' : '' }}
                                    @endif
                                    @if($months > 0 && $days > 0)
                                        ,
                                    @endif
                                    @if($days > 0)
                                        {{ $days }} Day{{ $days > 1 ? 's' : '' }}
                                    @endif
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
                    <a href="{{ route('user.courses.show', $enrollment) }}"
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

        {{-- right - Upcoming Sessions --}}
        <div class="w-full">
            <div class="w-full bg-white p-5 rounded-2xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <p class="font-semibold text-xl">Upcoming Sessions</p>
                    <a href="{{ route('user.sessions') }}" class="font-semibold text-xl text-purple-600 hover:text-purple-800">See All</a>
                </div>
                
                @forelse($upcomingSessionsDisplay as $enrollment)
                    @php
                        $session = $enrollment->enrollable;
                        $mentor = $session->mentor->user;
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
                                    @if($session->subCategories && $session->subCategories->count() > 0)
                                        {{ $session->subCategories->pluck('name')->implode(', ') }}
                                    @else
                                        Session
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="mt-3 mb-1 text-sm text-gray-600">
                                    @if($session->start_time)
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                    @else
                                        TBD
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500">
                                    @if($session->date)
                                        {{ $session->date->format('M d, Y') }}
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
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Upcoming Sessions</h3>
                        <p class="text-gray-500 text-sm">No sessions scheduled at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection