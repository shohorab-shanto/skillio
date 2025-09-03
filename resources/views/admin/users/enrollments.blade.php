@extends('admin.layouts.backend')

@section('title', __('trans.user_enrollment_history'))

@section('header')
    {{ __('trans.user_enrollment_history') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('trans.back_to_users') }}
            </a>
            <span class="text-sm text-gray-500">
                {{ __('trans.total') }} <span class="font-semibold">{{ $enrollments->total() }}</span> {{ __('trans.enrollments') }}
            </span>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center">
                <span class="text-2xl font-semibold text-purple-700">
                    {{ substr($user->name, 0, 1) }}
                </span>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                <p class="text-sm text-gray-500">{{ __('trans.user_id') }} {{ $user->id }}</p>
            </div>
        </div>
    </div>

    <!-- Enrollments Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.enrollment_type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.title_details') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.mentor') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.payment_amount') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.enrollment_date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.status') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($enrollment->enrollable_type == 'App\Models\Course')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fa-solid fa-book mr-1"></i>
                                    {{ __('trans.course') }}
                                </span>
                            @elseif($enrollment->enrollable_type == 'App\Models\SessionBooking')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    {{ __('trans.session') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ class_basename($enrollment->enrollable_type) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($enrollment->enrollable)
                                @if($enrollment->enrollable_type == 'App\Models\Course')
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $enrollment->enrollable->title ?? __('trans.untitled_course') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($enrollment->enrollable->duration_days)
                                            {{ __('trans.duration') }} {{ $enrollment->enrollable->duration_days }} {{ Str::plural(__('trans.day'), $enrollment->enrollable->duration_days) }}
                                        @else
                                            {{ __('trans.duration') }} {{ __('trans.na') }}
                                        @endif
                                    </div>
                                @elseif($enrollment->enrollable_type == 'App\Models\SessionBooking')
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ __('trans.session_with') }} {{ $enrollment->enrollable->mentor->user->name ?? __('trans.unknown_mentor') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($enrollment->enrollable->date && $enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                            {{ $enrollment->enrollable->date->format('M d, Y') }} at 
                                            {{ \Carbon\Carbon::parse($enrollment->enrollable->start_time)->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($enrollment->enrollable->end_time)->format('g:i A') }}
                                        @else
                                            {{ __('trans.time') }} {{ __('trans.na') }}
                                        @endif
                                    </div>
                                @endif
                            @else
                                <span class="text-gray-400">{{ __('trans.na') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($enrollment->enrollable && $enrollment->enrollable->mentor)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-purple-700">
                                                {{ substr($enrollment->enrollable->mentor->user->name ?? 'M', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $enrollment->enrollable->mentor->user->name ?? __('trans.unknown_mentor') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">{{ __('trans.na') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($enrollment->payment_transaction_id)
                                @php
                                    $payment = \App\Models\PaymentTransaction::find($enrollment->payment_transaction_id);
                                @endphp
                                @if($payment)
                                    <div class="text-sm font-medium text-gray-900">
                                        ${{ number_format($payment->gross_amount, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($payment->transaction_status == 'completed')
                                            <span class="text-green-600">{{ __('trans.paid') }}</span>
                                        @elseif($payment->transaction_status == 'pending')
                                            <span class="text-yellow-600">{{ __('trans.pending') }}</span>
                                        @else
                                            <span class="text-red-600">{{ ucfirst($payment->transaction_status) }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">{{ __('trans.payment_record_not_found') }}</span>
                                @endif
                            @else
                                <div class="text-sm font-medium text-gray-900">
                                    ${{ number_format($enrollment->amount, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($enrollment->payment_status == 'paid')
                                        <span class="text-green-600">{{ __('trans.paid') }}</span>
                                    @elseif($enrollment->payment_status == 'pending')
                                        <span class="text-yellow-600">{{ __('trans.pending') }}</span>
                                    @else
                                        <span class="text-red-600">{{ ucfirst($enrollment->payment_status) }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $enrollment->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($enrollment->enrollment_status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        {{ __('trans.pending') }}
                                    </span>
                                    @break
                                @case('active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fa-solid fa-play mr-1"></i>
                                        {{ __('trans.active') }}
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-check-circle mr-1"></i>
                                        {{ __('trans.completed') }}
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fa-solid fa-times-circle mr-1"></i>
                                        {{ __('trans.cancelled') }}
                                    </span>
                                    @break
                                @case('refunded')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fa-solid fa-undo mr-1"></i>
                                        {{ __('trans.refunded') }}
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fa-solid fa-question mr-1"></i>
                                        {{ ucfirst($enrollment->enrollment_status ?? __('trans.unknown')) }}
                                    </span>
                            @endswitch
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-graduation-cap text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg font-medium">{{ __('trans.no_enrollments_found') }}</p>
                                <p class="text-sm">{{ __('trans.user_no_enrollments_yet') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($enrollments->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Pagination Details - Left Aligned -->
                <div class="text-sm text-gray-700">
                    <p>
                        {{ __('trans.showing') }}
                        <span class="font-medium">{{ $enrollments->firstItem() ?? 0 }}</span>
                        {{ __('trans.to') }}
                        <span class="font-medium">{{ $enrollments->lastItem() ?? 0 }}</span>
                        {{ __('trans.of') }}
                        <span class="font-medium">{{ $enrollments->total() }}</span>
                        {{ __('trans.enrollments') }}
                    </p>
                </div>

                <!-- Pagination Buttons - Right Aligned -->
                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        {{-- Previous Page Link --}}
                        @if ($enrollments->onFirstPage())
                            <span aria-disabled="true" aria-label="Previous">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            </span>
                        @else
                            <a href="{{ $enrollments->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($enrollments->getUrlRange(1, $enrollments->lastPage()) as $page => $url)
                            @if ($page == $enrollments->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $enrollments->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($enrollments->hasMorePages())
                            <a href="{{ $enrollments->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-200" aria-label="Next">
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
