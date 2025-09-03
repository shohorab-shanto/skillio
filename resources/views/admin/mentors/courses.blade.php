@extends('admin.layouts.backend')

@section('title', __('trans.mentor_courses'))

@section('header')
    {{ __('trans.mentor_courses') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.mentor_courses') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('trans.all_courses_created_by') }} {{ $user->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.mentors.show', $user->id) }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('trans.back_to_mentor') }}
            </a>
            <span class="text-sm text-gray-500">
                {{ __('trans.total') }} <span class="font-semibold">{{ $courses->total() }}</span> {{ __('trans.courses') }}
            </span>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.course') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.category') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.price') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.duration') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.created') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                             alt="{{ $course->title }}" 
                                             class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center">
                                            <i class="fa-solid fa-book text-lg text-purple-600"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($course->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $course->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">{{ __('trans.no_category') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($course->status)
                                @case('approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fa-solid fa-check mr-1"></i>
                                        {{ __('trans.approved') }}
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        {{ __('trans.pending') }}
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fa-solid fa-times mr-1"></i>
                                        {{ __('trans.rejected') }}
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($course->status) }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($course->discount > 0)
                                    <div class="line-through text-gray-400">${{ number_format($course->price, 2) }}</div>
                                    <div class="text-green-600 font-medium">${{ number_format($course->price - $course->discount, 2) }}</div>
                                    <div class="text-xs text-gray-500">-{{ number_format(($course->discount / $course->price) * 100, 0) }}%</div>
                                @else
                                    <div class="font-medium">${{ number_format($course->price, 2) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($course->duration_days)
                                <div class="text-sm text-gray-900">{{ $course->duration_days }} {{ Str::plural(__('trans.day'), $course->duration_days) }}</div>
                            @else
                                <span class="text-gray-400">{{ __('trans.na') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $course->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-book text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg font-medium">{{ __('trans.no_courses_found') }}</p>
                                <p class="text-sm">{{ __('trans.mentor_hasnt_created_courses') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($courses->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Pagination Details - Left Aligned -->
                <div class="text-sm text-gray-700">
                    <p>
                        {{ __('trans.showing') }}
                        <span class="font-medium">{{ $courses->firstItem() ?? 0 }}</span>
                        {{ __('trans.to') }}
                        <span class="font-medium">{{ $courses->lastItem() ?? 0 }}</span>
                        {{ __('trans.of') }}
                        <span class="font-medium">{{ $courses->total() }}</span>
                        {{ __('trans.courses') }}
                    </p>
                </div>

                <!-- Pagination Buttons - Right Aligned -->
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
                            <a href="{{ $courses->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-200" aria-label="Next">
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
