@extends('admin.layouts.backend')

@section('title', __('trans.transactions_management'))

@section('header')
    {{ __('trans.transactions_management') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Transactions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.total_transactions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['total_transactions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-credit-card text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.total_revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($summary['total_gross_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-dollar-sign text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Admin Commission -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.admin_commission') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($summary['total_admin_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-percentage text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Completed Transactions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.completed') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['completed_transactions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check text-xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="{{ __('trans.transaction_id_customer_description') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Transaction Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.status') }}</label>
                    <select name="transaction_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">{{ __('trans.all_status') }}</option>
                        <option value="pending" {{ request('transaction_status') == 'pending' ? 'selected' : '' }}>{{ __('trans.pending') }}</option>
                        <option value="processing" {{ request('transaction_status') == 'processing' ? 'selected' : '' }}>{{ __('trans.processing') }}</option>
                        <option value="completed" {{ request('transaction_status') == 'completed' ? 'selected' : '' }}>{{ __('trans.completed') }}</option>
                        <option value="failed" {{ request('transaction_status') == 'failed' ? 'selected' : '' }}>{{ __('trans.failed') }}</option>
                        <option value="cancelled" {{ request('transaction_status') == 'cancelled' ? 'selected' : '' }}>{{ __('trans.cancelled') }}</option>
                        <option value="refunded" {{ request('transaction_status') == 'refunded' ? 'selected' : '' }}>{{ __('trans.refunded') }}</option>
                    </select>
                </div>

                <!-- Transaction Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.type') }}</label>
                    <select name="transaction_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">{{ __('trans.all_types') }}</option>
                        <option value="payment" {{ request('transaction_type') == 'payment' ? 'selected' : '' }}>{{ __('trans.payment') }}</option>
                        <option value="refund" {{ request('transaction_type') == 'refund' ? 'selected' : '' }}>{{ __('trans.refund') }}</option>
                        <option value="partial_refund" {{ request('transaction_type') == 'partial_refund' ? 'selected' : '' }}>{{ __('trans.partial_refund') }}</option>
                    </select>
                </div>

                <!-- Currency -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.currency') }}</label>
                    <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">{{ __('trans.all_currencies') }}</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ request('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Amount Min -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.min_amount') }}</label>
                    <input type="number" name="amount_min" value="{{ request('amount_min') }}" 
                           placeholder="0" min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Amount Max -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.max_amount') }}</label>
                    <input type="number" name="amount_max" value="{{ request('amount_max') }}" 
                           placeholder="1000" min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.from_date') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-filter mr-2"></i>{{ __('trans.apply_filters') }}
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i class="fa-solid fa-times mr-2"></i>{{ __('trans.clear_filters') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.transaction') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.customer') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.amount_details') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.payment_method') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('trans.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $transaction->transaction_id }}</div>
                                <div class="text-gray-500">{{ $transaction->transaction_type }}</div>
                                @if($transaction->stripe_payment_intent_id)
                                    <div class="text-xs text-gray-400">{{ __('trans.stripe') }} {{ Str::limit($transaction->stripe_payment_intent_id, 20) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->enrollments->count() > 0)
                                @php $enrollment = $transaction->enrollments->first() @endphp
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $enrollment->user->name ?? __('trans.na') }}</div>
                                    <div class="text-gray-500">{{ $enrollment->user->email ?? __('trans.na') }}</div>
                                    @if($transaction->stripe_customer_id)
                                        <div class="text-xs text-gray-400">ID: {{ Str::limit($transaction->stripe_customer_id, 20) }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">{{ __('trans.no_enrollment_data') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">${{ number_format($transaction->gross_amount, 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ __('trans.admin') }} ${{ number_format($transaction->admin_amount, 2) }} | 
                                    {{ __('trans.mentor') }} ${{ number_format($transaction->mentor_amount, 2) }}
                                </div>
                                @if($transaction->stripe_fee > 0)
                                    <div class="text-xs text-gray-400">{{ __('trans.fee') }} ${{ number_format($transaction->stripe_fee, 2) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($transaction->transaction_status)
                                @case('completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fa-solid fa-check mr-1"></i>
                                        {{ __('trans.completed') }}
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        {{ __('trans.pending') }}
                                    </span>
                                    @break
                                @case('processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-spinner mr-1"></i>
                                        {{ __('trans.processing') }}
                                    </span>
                                    @break
                                @case('failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fa-solid fa-times mr-1"></i>
                                        {{ __('trans.failed') }}
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fa-solid fa-ban mr-1"></i>
                                        {{ __('trans.cancelled') }}
                                    </span>
                                    @break
                                @case('refunded')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fa-solid fa-undo mr-1"></i>
                                        {{ __('trans.refunded') }}
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($transaction->payment_method_type)
                                    <div class="font-medium">{{ ucfirst($transaction->payment_method_type) }}</div>
                                    @if($transaction->payment_method_last4)
                                        <div class="text-gray-500">**** {{ $transaction->payment_method_last4 }}</div>
                                    @endif
                                    @if($transaction->payment_method_brand)
                                        <div class="text-xs text-gray-400">{{ ucfirst($transaction->payment_method_brand) }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">{{ __('trans.na') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.transactions.show', $transaction->id) }}" 
                               class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                <i class="fa-solid fa-eye mr-1"></i>
                                {{ __('trans.details') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-credit-card text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg font-medium">{{ __('trans.no_transactions_found') }}</p>
                                <p class="text-sm">{{ __('trans.try_adjusting_filters') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Pagination Details - Left Aligned -->
                <div class="text-sm text-gray-700">
                    <p>
                        {{ __('trans.showing') }}
                        <span class="font-medium">{{ $transactions->firstItem() ?? 0 }}</span>
                        {{ __('trans.to') }}
                        <span class="font-medium">{{ $transactions->lastItem() ?? 0 }}</span>
                        {{ __('trans.of') }}
                        <span class="font-medium">{{ $transactions->total() }}</span>
                        {{ __('trans.transactions') }}
                    </p>
                </div>

                <!-- Pagination Buttons - Right Aligned -->
                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        {{-- Previous Page Link --}}
                        @if ($transactions->onFirstPage())
                            <span aria-disabled="true" aria-label="Previous">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            </span>
                        @else
                            <a href="{{ $transactions->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                            @if ($page == $transactions->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $transactions->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($transactions->hasMorePages())
                            <a href="{{ $transactions->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-200" aria-label="Next">
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
