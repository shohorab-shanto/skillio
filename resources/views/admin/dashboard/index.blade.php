@extends('admin.layouts.backend')

@section('title', 'Admin Dashboard')

@section('header')
    Dashboard
@endsection

@section('content')
<div>
    <div class="mx-auto space-y-6">
        
        <!-- Top Row - Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-1.png') }}" alt="Total Users" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $totalUsers / 10) ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Total Mentors Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-2.png') }}" alt="Total Mentors" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Mentors</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalMentors }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $totalMentors / 10) ? 'bg-green-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-3.png') }}" alt="Total Courses" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalCourses }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $totalCourses / 10) ? 'bg-yellow-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Admin Commission Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-4.png') }}" alt="Admin Commission" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Admin Commission</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($adminCommission) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $adminCommission / 100) ? 'bg-purple-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Middle Section - Platform Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Users (10)</h3>
                    <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View All</a>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($recentUsers as $user)
                        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-purple-700">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fa-solid fa-users text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No users found</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Courses -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Courses (10)</h3>
                    <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View All</a>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($recentCourses as $course)
                        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-book text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">{{ $course->mentor->user->name ?? 'Unknown Mentor' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $course->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fa-solid fa-book text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No courses found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>


    </div>
</div>
@endsection
