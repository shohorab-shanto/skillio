@extends('admin.layouts.backend')

@section('title', __('trans.transaction_details'))

@section('header')
    {{ __('trans.transaction_details') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Back Button -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            {{ __('trans.back_to_transactions') }}
        </a>
    </div>

    <!-- Transaction Overview Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $transaction->transaction_id }}</h2>
                <p class="text-gray-600">{{ $transaction->description ?? __('trans.no_description_provided') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @switch($transaction->transaction_status)
                    @case('completed')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fa-solid fa-check mr-2"></i>
                            {{ __('trans.completed') }}
                        </span>
                        @break
                    @case('pending')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fa-solid fa-clock mr-2"></i>
                            {{ __('trans.pending') }}
                        </span>
                        @break
                    @case('processing')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fa-solid fa-spinner mr-2"></i>
                            {{ __('trans.processing') }}
                        </span>
                        @break
                    @case('failed')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fa-solid fa-times mr-2"></i>
                            {{ __('trans.failed') }}
                        </span>
                        @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fa-solid fa-ban mr-2"></i>
                            {{ __('trans.cancelled') }}
                        </span>
                        @break
                    @case('refunded')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                            <i class="fa-solid fa-undo mr-2"></i>
                            {{ __('trans.refunded') }}
                        </span>
                        @break
                @endswitch
                
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fa-solid fa-tag mr-2"></i>
                    {{ ucfirst($transaction->transaction_type) }}
                </span>
            </div>
        </div>

        <!-- Transaction Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('trans.basic_information') }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.transaction_id') }}</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $transaction->transaction_id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.created_date') }}</label>
                        <p class="text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.updated_date') }}</label>
                        <p class="text-sm text-gray-900">{{ $transaction->updated_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.currency') }}</label>
                        <p class="text-sm text-gray-900">{{ $transaction->currency }}</p>
                    </div>
                </div>
            </div>

            <!-- Amount Breakdown -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('trans.amount_breakdown') }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.gross_amount') }}</label>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($transaction->gross_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.stripe_fee') }}</label>
                        <p class="text-sm text-gray-900">${{ number_format($transaction->stripe_fee, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.net_amount') }}</label>
                        <p class="text-sm text-gray-900">${{ number_format($transaction->net_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.admin_commission_20') }}</label>
                        <p class="text-sm text-purple-600 font-semibold">${{ number_format($transaction->admin_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.mentor_amount_80') }}</label>
                        <p class="text-sm text-green-600 font-semibold">${{ number_format($transaction->mentor_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Stripe Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('trans.stripe_details') }}</h3>
                <div class="space-y-3">
                    @if($transaction->stripe_payment_intent_id)
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.payment_intent_id') }}</label>
                        <p class="text-xs text-gray-900 font-mono break-all">{{ $transaction->stripe_payment_intent_id }}</p>
                    </div>
                    @endif
                    @if($transaction->stripe_customer_id)
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.customer_id') }}</label>
                        <p class="text-xs text-gray-900 font-mono break-all">{{ $transaction->stripe_customer_id }}</p>
                    </div>
                    @endif
                    @if($transaction->stripe_charge_id)
                    <div>
                        <label class="text-sm font-medium text-gray-500">{{ __('trans.charge_id') }}</label>
                        <p class="text-xs text-gray-900 font-mono break-all">{{ $transaction->stripe_charge_id }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Enrollments -->
    @if($transaction->enrollments->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('trans.related_enrollments') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('trans.user') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('trans.enrollment_type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('trans.title') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('trans.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('trans.enrolled_date') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transaction->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $enrollment->user->name ?? __('trans.na') }}</div>
                                <div class="text-gray-500">{{ $enrollment->user->email ?? __('trans.na') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $enrollment->enrollable_type == 'App\Models\Course' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $enrollment->enrollable_type == 'App\Models\Course' ? __('trans.course') : __('trans.session') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($enrollment->enrollable_type == 'App\Models\Course')
                                    {{ $enrollment->enrollable->title ?? __('trans.na') }}
                                @else
                                    {{ $enrollment->enrollable->title ?? __('trans.na') }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($enrollment->enrollment_status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ __('trans.pending') }}
                                    </span>
                                    @break
                                @case('active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ __('trans.active') }}
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('trans.completed') }}
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ __('trans.cancelled') }}
                                    </span>
                                    @break
                                @case('refunded')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ __('trans.refunded') }}
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($enrollment->enrollment_status) }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $enrollment->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
