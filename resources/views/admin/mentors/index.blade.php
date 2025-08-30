@extends('admin.layouts.backend')

@section('title', 'Mentors Management')

@section('header')
    Mentors Management
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('admin.mentors.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or email..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Verification Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Verification</label>
                    <select name="verification_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="verified" {{ request('verification_status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="unverified" {{ request('verification_status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                    </select>
                </div>

                <!-- Availability Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                    <select name="availability" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Availability</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="busy" {{ request('availability') == 'busy' ? 'selected' : '' }}>Busy</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.mentors.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i class="fa-solid fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Mentors Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mentor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Verification
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Availability
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rating
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mentors as $mentor)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($mentor->mentor && $mentor->mentor->photo)
                                        <img src="{{ asset('storage/' . $mentor->mentor->photo) }}" 
                                             alt="{{ $mentor->name }}" 
                                             class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-lg font-semibold text-purple-700">
                                                {{ substr($mentor->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $mentor->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $mentor->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mentor->email }}</div>
                            @if($mentor->mentor && $mentor->mentor->work_experience)
                                <div class="text-sm text-gray-500">{{ $mentor->mentor->work_experience }} years exp.</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($mentor->mentor && $mentor->mentor->verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fa-solid fa-check-circle mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    Unverified
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($mentor->mentor)
                                @switch($mentor->mentor->availability)
                                    @case('available')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fa-solid fa-check mr-1"></i>
                                            Available
                                        </span>
                                        @break
                                    @case('unavailable')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fa-solid fa-times mr-1"></i>
                                            Unavailable
                                        </span>
                                        @break
                                    @case('busy')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fa-solid fa-clock mr-1"></i>
                                            Busy
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fa-solid fa-question mr-1"></i>
                                            Unknown
                                        </span>
                                @endswitch
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($mentor->mentor)
                                <div class="text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="font-medium">{{ $mentor->mentor->getFormattedAverageRatingAttribute() }}</span>
                                        <span class="text-yellow-400 ml-1">⭐</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.mentors.show', $mentor->id) }}" 
                               class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                <i class="fa-solid fa-eye mr-1"></i>
                                Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-chalkboard-teacher text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg font-medium">No mentors found</p>
                                <p class="text-sm">Try adjusting your filters or search terms</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($mentors->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Pagination Details - Left Aligned -->
                <div class="text-sm text-gray-700">
                    <p>
                        Showing
                        <span class="font-medium">{{ $mentors->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-medium">{{ $mentors->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-medium">{{ $mentors->total() }}</span>
                        mentors
                    </p>
                </div>

                <!-- Pagination Buttons - Right Aligned -->
                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        {{-- Previous Page Link --}}
                        @if ($mentors->onFirstPage())
                            <span aria-disabled="true" aria-label="Previous">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            </span>
                        @else
                            <a href="{{ $mentors->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($mentors->getUrlRange(1, $mentors->lastPage()) as $page => $url)
                            @if ($page == $mentors->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $mentors->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($mentors->hasMorePages())
                            <a href="{{ $mentors->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-200" aria-label="Next">
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
        @endif
    </div>
</div>


@endsection

@push('scripts')
<script>
// No JavaScript needed for the simplified index page
</script>
@endpush
