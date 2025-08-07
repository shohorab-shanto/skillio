@extends('backend.layouts.app')

@section('title', 'My Courses')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Courses</h1>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('mentor.courses.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-plus mr-2"></i>
                Create New Course
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Course Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fa-solid fa-book text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fa-solid fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fa-solid fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rejected</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Enrolled Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_enrolled_students']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('mentor.courses.index') }}" class="flex flex-wrap items-center gap-4">
            
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $request->search }}" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                           placeholder="Search courses by title or description...">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>
                    Filter
                </button>
                
                <a href="{{ route('mentor.courses.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-refresh mr-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Courses Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($courses->count() > 0)
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Your Courses
                        @if($request->status)
                            <span class="text-sm font-normal text-gray-500">({{ ucfirst($request->status) }} courses)</span>
                        @endif
                        @if($request->search)
                            <span class="text-sm font-normal text-gray-500">(searching "{{ $request->search }}")</span>
                        @endif
                    </h2>
                    <div class="text-sm text-gray-500">
                        Showing {{ $courses->firstItem() ?? 0 }}-{{ $courses->lastItem() ?? 0 }} of {{ $courses->total() }} courses
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                            <!-- Course Image -->
                            <div class="aspect-video bg-white relative overflow-hidden p-2">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-purple-50 rounded-lg">
                                        <i class="fa-solid fa-book text-4xl text-purple-300"></i>
                                    </div>
                                @endif
                                
                                <!-- Status Badges -->
                                <div class="absolute top-4 left-4 flex flex-col space-y-2">
                                    @if($course->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-500 text-white shadow-sm">
                                            Online
                                        </span>
                                    @elseif($course->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white shadow-sm">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-500 text-white shadow-sm">
                                            Rejected
                                        </span>
                                    @endif
                                                                 
                                </div>
                            </div>

                            <!-- Course Content -->
                            <div class="p-5">
                                <!-- Rating and Edit Button -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center text-yellow-400">
                                        <i class="fa-solid fa-star text-sm"></i>
                                        <span class="ml-1 text-sm font-semibold text-gray-900">
                                            {{ number_format($course->reviews->avg('rating') ?? 4.8, 1) }}
                                        </span>
                                        <span class="ml-1 text-sm text-gray-500">
                                            ({{ $course->reviews->count() > 0 ? $course->reviews->count() : '120' }} Reviews)
                                        </span>
                                    </div>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('mentor.courses.edit', $course) }}" 
                                       class="w-8 h-8 bg-purple-100 hover:bg-purple-200 text-purple-600 rounded-lg flex items-center justify-center transition-colors"
                                       title="Edit Course">
                                        <i class="fa-solid fa-edit text-sm"></i>
                                    </a>
                                </div>

                                <!-- Course Stats -->
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-users mr-1"></i>
                                        <span>{{ rand(100, 999) }} Students</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        <span>{{ $course->duration_days }} Days</span>
                                    </div>
                                </div>

                                <!-- Course Title -->
                                <button onclick="viewCourseDetails('{{ $course->id }}')" 
                                        class="w-full text-left">
                                    <h3 class="font-semibold text-gray-900 text-base mb-3 line-clamp-2 leading-relaxed hover:text-purple-600 transition-colors cursor-pointer">
                                        {{ $course->title }}
                                    </h3>
                                </button>

                                <!-- Course Category and Price -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fa-solid fa-folder mr-2 text-purple-500"></i>
                                        {{ $course->category->name }}
                                    </div>
                                    
                                    <!-- Course Price -->
                                    <div>
                                        @if($course->discount > 0)
                                            <span class="text-lg font-bold text-gray-900">
                                                ${{ number_format($course->price * (1 - $course->discount / 100), 2) }}
                                            </span>
                                            <span class="text-sm text-gray-400 line-through ml-1">
                                                ${{ number_format($course->price, 2) }}
                                            </span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">
                                                ${{ number_format($course->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="p-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if ($courses->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $courses->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Previous
                                </a>
                            @endif

                            @if ($courses->hasMorePages())
                                <a href="{{ $courses->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Next
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    Next
                                </span>
                            @endif
                        </div>

                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 leading-5">
                                    Showing
                                    <span class="font-medium">{{ $courses->firstItem() ?? 0 }}</span>
                                    to
                                    <span class="font-medium">{{ $courses->lastItem() ?? 0 }}</span>
                                    of
                                    <span class="font-medium">{{ $courses->total() }}</span>
                                    courses
                                </p>
                            </div>

                            <div>
                                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                    {{-- Previous Page Link --}}
                                    @if ($courses->onFirstPage())
                                        <span aria-disabled="true" aria-label="Previous">
                                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </span>
                                        </span>
                                    @else
                                        <a href="{{ $courses->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                                        @if ($page == $courses->currentPage())
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                            </span>
                                        @else
                                            <a href="{{ $courses->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($courses->hasMorePages())
                                        <a href="{{ $courses->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Next">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span aria-disabled="true" aria-label="Next">
                                            <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </span>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-book text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($request->search || $request->status)
                        No courses found
                    @else
                        No courses yet
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if($request->search || $request->status)
                        Try adjusting your search criteria or filters.
                    @else
                        Start creating courses to share your knowledge with students.
                    @endif
                </p>
                
                @if($request->search || $request->status)
                    <a href="{{ route('mentor.courses.index') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        View All Courses
                    </a>
                @else
                    <a href="{{ route('mentor.courses.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Create Your First Course
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
function viewCourseDetails(courseId) {
    // Route to the course show page
    window.location.href = "{{ route('mentor.courses.index') }}/" + courseId;
}
</script>
@endsection
