@extends('backend.layouts.app')

@section('title', 'My Time Slots')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Time Slots</h1>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('mentor.time-slots.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-plus mr-2"></i>
                Create Time Slot
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('mentor.time-slots.index') }}" class="flex flex-wrap items-center gap-4">
            
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ $request->date_from }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ $request->date_to }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 cursor-pointer hover:border-gray-400 transition-colors appearance-none">
                    <option value="">All Status</option>
                    <option value="active" {{ $request->status == 'active' ? 'selected' : '' }}>Available</option>
                    <option value="booked" {{ $request->status == 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-col">
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-filter mr-2"></i>
                        Filter
                    </button>
                    
                    <a href="{{ route('mentor.time-slots.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-refresh mr-2"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Time Slots Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($timeSlots->count() > 0)
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Your Time Slots
                        @if($request->status)
                            <span class="text-sm font-normal text-gray-500">({{ ucfirst($request->status) }} slots)</span>
                        @endif
                        @if($request->date_from || $request->date_to)
                            <span class="text-sm font-normal text-gray-500">
                                ({{ $request->date_from ? \Carbon\Carbon::parse($request->date_from)->format('M d') : 'All' }} - {{ $request->date_to ? \Carbon\Carbon::parse($request->date_to)->format('M d, Y') : 'All' }})
                            </span>
                        @endif
                    </h2>
                    <div class="text-sm text-gray-500">
                        Showing {{ $timeSlots->firstItem() ?? 0 }}-{{ $timeSlots->lastItem() ?? 0 }} of {{ $timeSlots->total() }} slots
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($timeSlots as $timeSlot)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                        <!-- Header with Day and Edit -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <h3 class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($timeSlot->date)->format('l') }}
                                </h3>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($timeSlot->status == 'booked' && $timeSlot->user_id)
                                    <button onclick="showUserDetails({{ $timeSlot->user_id }}, '{{ $timeSlot->user->name }}', '{{ $timeSlot->user->email }}', '{{ $timeSlot->user->phone ?? 'N/A' }}', '{{ $timeSlot->user->address ?? 'N/A' }}', '{{ $timeSlot->user->created_at->format('M d, Y') }}')"
                                            class="text-blue-500 hover:text-blue-700 transition-colors"
                                            title="View Student Details">
                                        <i class="fa-solid fa-user text-sm"></i>
                                    </button>
                                @endif
                                @if($timeSlot->status == 'active')
                                    <a href="{{ route('mentor.time-slots.edit', $timeSlot) }}" 
                                       class="text-gray-400 hover:text-purple-600 transition-colors"
                                       title="Edit Time Slot">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div class="mb-4">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">
                                {{ \Carbon\Carbon::parse($timeSlot->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($timeSlot->end_time)->format('g:i A') }}
                            </h4>
                        </div>

                        <!-- Categories/Subjects -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                <!-- Main Category -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $timeSlot->category->name }}
                                </span>
                                
                                <!-- Sub Categories -->
                                @foreach($timeSlot->subCategories as $subCategory)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $subCategory->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Date and Status -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>{{ \Carbon\Carbon::parse($timeSlot->date)->format('j F Y') }}</span>
                            
                            @if($timeSlot->status == 'booked')
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500 text-white">
                                        <i class="fa-solid fa-user mr-1"></i>
                                        Booked
                                    </span>
                                </div>
                            @elseif($timeSlot->status == 'active')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                    Available
                                </span>
                            @elseif($timeSlot->status == 'completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                    Completed
                                </span>
                            @elseif($timeSlot->status == 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                    Cancelled
                                </span>
                            @endif
                        </div>

                        <!-- Fee and Type -->
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <i class="fa-solid fa-dollar-sign mr-1"></i>
                                {{ number_format($timeSlot->fee, 2) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <i class="fa-solid fa-{{ $timeSlot->mentor->type == 'online' ? 'video' : 'location-dot' }} mr-1"></i>
                                {{ ucfirst($timeSlot->mentor->type) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>

            <!-- Pagination -->
            @if($timeSlots->hasPages())
                <div class="p-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if ($timeSlots->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $timeSlots->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Previous
                                </a>
                            @endif

                            @if ($timeSlots->hasMorePages())
                                <a href="{{ $timeSlots->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
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
                                    <span class="font-medium">{{ $timeSlots->firstItem() ?? 0 }}</span>
                                    to
                                    <span class="font-medium">{{ $timeSlots->lastItem() ?? 0 }}</span>
                                    of
                                    <span class="font-medium">{{ $timeSlots->total() }}</span>
                                    time slots
                                </p>
                            </div>

                            <div>
                                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                    {{-- Previous Page Link --}}
                                    @if ($timeSlots->onFirstPage())
                                        <span aria-disabled="true" aria-label="Previous">
                                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </span>
                                        </span>
                                    @else
                                        <a href="{{ $timeSlots->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($timeSlots->getUrlRange(1, $timeSlots->lastPage()) as $page => $url)
                                        @if ($page == $timeSlots->currentPage())
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                            </span>
                                        @else
                                            <a href="{{ $timeSlots->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($timeSlots->hasMorePages())
                                        <a href="{{ $timeSlots->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Next">
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
                    <i class="fa-solid fa-clock text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($request->date_from || $request->date_to || $request->status)
                        No time slots found
                    @else
                        No time slots yet
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if($request->date_from || $request->date_to || $request->status)
                        Try adjusting your date range or filters.
                    @else
                        Start creating time slots for students to book sessions with you.
                    @endif
                </p>
                
                @if($request->date_from || $request->date_to || $request->status)
                    <a href="{{ route('mentor.time-slots.index') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        View All Time Slots
                    </a>
                @else
                    <a href="{{ route('mentor.time-slots.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Create Your First Time Slot
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- User Details Modal -->
<div id="userDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fa-solid fa-user text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Student Details</h3>
                    <p class="text-sm text-gray-500">Booked student information</p>
                </div>
            </div>
            <button onclick="closeUserDetailsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        
        <div class="space-y-3">
            <div class="text-gray-700">
                <span class="font-medium">Name:</span> <span id="userName"></span>
            </div>
        </div>
        
        
        <!-- Chat Button -->
        <div class="mb-4 mt-4">
            <button onclick="chatWithUser()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                <i class="fa-solid fa-message"></i>
                <span>Chat with Student</span>
            </button>
        </div>
    </div>
</div>

<script>
function showUserDetails(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('userDetailsModal').classList.remove('hidden');
}

function closeUserDetailsModal() {
    document.getElementById('userDetailsModal').classList.add('hidden');
}

function chatWithUser() {
    // TODO: Add route link later
    alert('Chat functionality will be implemented here');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('userDetailsModal');
    if (event.target == modal) {
        closeUserDetailsModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key == 'Escape') {
        closeUserDetailsModal();
    }
});
</script>
@endsection
