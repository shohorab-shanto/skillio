@extends('admin.layouts.backend')

@section('title', 'Transaction Details')

@section('header')
    Transaction Details
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Back Button -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back to Transactions
        </a>
    </div>

    <!-- Transaction Overview Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $transaction->transaction_id }}</h2>
                <p class="text-gray-600">{{ $transaction->description ?? 'No description provided' }}</p>
            </div>
            <div class="flex items-center gap-3">
                @switch($transaction->transaction_status)
                    @case('completed')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fa-solid fa-check mr-2"></i>
                            Completed
                        </span>
                        @break
                    @case('pending')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fa-solid fa-clock mr-2"></i>
                            Pending
                        </span>
                        @break
                    @case('processing')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fa-solid fa-spinner mr-2"></i>
                            Processing
                        </span>
                        @break
                    @case('failed')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fa-solid fa-times mr-2"></i>
                            Failed
                        </span>
                        @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fa-solid fa-ban mr-2"></i>
                            Cancelled
                        </span>
                        @break
                    @case('refunded')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                            <i class="fa-solid fa-undo mr-2"></i>
                            Refunded
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
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Transaction ID</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $transaction->transaction_id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created Date</label>
                        <p class="text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Updated Date</label>
                        <p class="text-sm text-gray-900">{{ $transaction->updated_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Currency</label>
                        <p class="text-sm text-gray-900">{{ $transaction->currency }}</p>
                    </div>
                </div>
            </div>

            <!-- Amount Breakdown -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Amount Breakdown</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Gross Amount</label>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($transaction->gross_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Stripe Fee</label>
                        <p class="text-sm text-gray-900">${{ number_format($transaction->stripe_fee, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Net Amount</label>
                        <p class="text-sm text-gray-900">${{ number_format($transaction->net_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Admin Commission (20%)</label>
                        <p class="text-sm text-purple-600 font-semibold">${{ number_format($transaction->admin_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Mentor Amount (80%)</label>
                        <p class="text-sm text-green-600 font-semibold">${{ number_format($transaction->mentor_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Stripe Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Stripe Details</h3>
                <div class="space-y-3">
                    @if($transaction->stripe_payment_intent_id)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Payment Intent ID</label>
                        <p class="text-xs text-gray-900 font-mono break-all">{{ $transaction->stripe_payment_intent_id }}</p>
                    </div>
                    @endif
                    @if($transaction->stripe_customer_id)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Customer ID</label>
                        <p class="text-xs text-gray-900 font-mono break-all">{{ $transaction->stripe_customer_id }}</p>
                    </div>
                    @endif
                    @if($transaction->stripe_charge_id)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Charge ID</label>
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
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Enrollments</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transaction->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                <div class="text-gray-500">{{ $enrollment->user->email ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $enrollment->enrollable_type === 'App\Models\Course' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $enrollment->enrollable_type === 'App\Models\Course' ? 'Course' : 'Session' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($enrollment->enrollable_type === 'App\Models\Course')
                                    {{ $enrollment->enrollable->title ?? 'N/A' }}
                                @else
                                    {{ $enrollment->enrollable->title ?? 'N/A' }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($enrollment->enrollment_status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                    @break
                                @case('active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Completed
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                    @break
                                @case('refunded')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Refunded
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
