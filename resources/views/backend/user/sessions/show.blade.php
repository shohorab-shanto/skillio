@extends('backend.layouts.app')

@section('title', __('trans.session_details'))

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('user.sessions') }}" 
               class="inline-flex items-center text-purple-600 hover:text-purple-700 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('trans.back_to_sessions') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.session_details') }}</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Session Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($enrollment->enrollment_status == 'active') bg-green-100 text-green-800
                        @elseif($enrollment->enrollment_status == 'completed') bg-blue-100 text-blue-800
                        @elseif($enrollment->enrollment_status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($enrollment->enrollment_status) }}
                    </span>
                    
                    @if($enrollment->enrollable->date)
                        <span class="text-lg font-semibold text-gray-900">
                            {{ $enrollment->enrollable->date->format('l, M d, Y') }}
                        </span>
                    @endif
                </div>
                
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-600">
                        ${{ number_format($enrollment->amount, 2) }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ ucfirst($enrollment->payment_status) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Mentor Information -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-6">
                    <!-- Mentor Photo -->
                    <div class="flex-shrink-0">
                        @php
                            $mentorPhoto = $enrollment->enrollable->mentor->photo 
                                ? (Str::startsWith($enrollment->enrollable->mentor->photo, ['http://', 'https://', '/storage/']) 
                                    ? $enrollment->enrollable->mentor->photo 
                                    : Storage::url($enrollment->enrollable->mentor->photo))
                                : asset('assets/images/user-avatar.png');
                        @endphp
                        <img src="{{ $mentorPhoto }}" 
                             alt="{{ $enrollment->enrollable->mentor->user->name }}"
                             class="w-20 h-20 rounded-full object-cover border-3 border-white shadow-lg">
                    </div>

                    <!-- Mentor Details -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h2 class="text-xl font-bold text-gray-900">
                                {{ $enrollment->enrollable->mentor->user->name }}
                            </h2>
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-500">{{ __('trans.your_mentor') }}</span>
                        </div>
                        @if($enrollment->enrollable->mentor->bio)
                            <p class="text-gray-600 mb-4 leading-relaxed">{{ $enrollment->enrollable->mentor->bio }}</p>
                        @endif
                        
                        <!-- Categories -->
                        @if($enrollment->enrollable->subCategories && count($enrollment->enrollable->subCategories) > 0)
                            <div class="flex flex-wrap gap-2">
                                @if($enrollment->enrollable->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $enrollment->enrollable->category->name }}
                                    </span>
                                @endif
                                @foreach($enrollment->enrollable->subCategories as $subCategory)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $subCategory->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Session Information -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                        {{ __('trans.session_information') }}
                    </h3>
                        <div class="space-y-4">
                            @if($enrollment->enrollable->date)
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('trans.date') }}</p>
                                        <p class="font-medium">{{ $enrollment->enrollable->date->format('l, M d, Y') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                <div class="flex items-center">
                                    <i class="fas fa-clock w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('trans.time') }}</p>
                                        <p class="font-medium">
                                            {{ $enrollment->enrollable->start_time->format('H:i A') }} - 
                                            {{ $enrollment->enrollable->end_time->format('H:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                @php
                                    $duration = $enrollment->enrollable->start_time->diffInMinutes($enrollment->enrollable->end_time);
                                @endphp
                                <div class="flex items-center">
                                    <i class="fas fa-hourglass-half w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('trans.duration') }}</p>
                                        <p class="font-medium">{{ $duration }} {{ __('trans.minutes') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->enrollable->subCategories && count($enrollment->enrollable->subCategories) > 0)
                                <div class="flex items-start">
                                    <i class="fas fa-tags w-5 text-purple-600 mr-3 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500 mb-2">{{ __('trans.topics') }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($enrollment->enrollable->subCategories as $subCategory)
                                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                                    {{ $subCategory->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- Right Column -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                        {{ __('trans.payment_information') }}
                    </h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign w-5 text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('trans.amount_paid') }}</p>
                                    <p class="font-medium text-green-600 text-lg">${{ number_format($enrollment->amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-credit-card w-5 text-blue-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('trans.payment_method') }}</p>
                                    <p class="font-medium">{{ ucfirst($enrollment->payment_method ?? 'Card') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-check-circle w-5 text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('trans.payment_status') }}</p>
                                    <p class="font-medium text-green-600">{{ ucfirst($enrollment->payment_status) }}</p>
                                </div>
                            </div>

                            @if($enrollment->enrolled_at)
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-plus w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('trans.booked_on') }}</p>
                                        <p class="font-medium">{{ $enrollment->enrolled_at->format('M d, Y H:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->paymentTransaction)
                                <div class="flex items-center">
                                    <i class="fas fa-receipt w-5 text-gray-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('trans.transaction_id') }}</p>
                                        <p class="font-medium text-xs">{{ $enrollment->paymentTransaction->transaction_id }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            <!-- Action Buttons -->
            <div class="bg-gray-50 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-purple-600 mr-2"></i>
                    {{ __('trans.quick_actions') }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($enrollment->enrollment_status == 'active' && $enrollment->enrollable->has_not_started)
                        <button onclick="openSwitchSessionModal()" class="inline-flex items-center justify-center px-4 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors duration-200 shadow-sm">
                            <i class="fas fa-clock mr-2"></i>
                            {{ __('trans.switch_time') }}
                        </button>
                    @endif

                    <a href="{{ $conversation ? route('chat.show', $conversation->unique_code) : route('chat.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-comment mr-2"></i>
                        {{ __('trans.message_mentor') }}
                    </a>
                </div>
            </div>
            </div>
        </div>

        <!-- Switch Session Modal -->
        <div id="switchSessionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('trans.switch_session_time') }}</h3>
                        <button onclick="closeSwitchSessionModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <!-- Loading State -->
                    <div id="switchSessionLoading" class="text-center py-8">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-purple-500 hover:bg-purple-400 transition ease-in-out duration-150 cursor-not-allowed">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('trans.loading_available_sessions') }}
                        </div>
                    </div>

                    <!-- Available Sessions List -->
                    <div id="availableSessionsList" class="hidden">
                        <p class="text-sm text-gray-600 mb-4">{{ __('trans.select_new_session_time') }}</p>
                        <div id="sessionsContainer" class="space-y-3 max-h-64 overflow-y-auto">
                            <!-- Available sessions will be loaded here -->
                        </div>
                    </div>

                    <!-- No Sessions Available -->
                    <div id="noSessionsAvailable" class="hidden text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">{{ __('trans.no_available_sessions_found') }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ __('trans.try_checking_back_later') }}</p>
                    </div>

                    <!-- Error State -->
                    <div id="switchSessionError" class="hidden text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                        <p class="text-red-600">{{ __('trans.error_loading_sessions') }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ __('trans.please_try_again') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Form for Session -->
        @php
            $existingReview = auth()->user()->reviews()->where('mentor_id', $enrollment->enrollable->mentor->user_id)->first();
        @endphp
        @include('components.review-form', [
            'type' => 'mentor',
            'item' => $enrollment->enrollable->mentor,
            'existingReview' => $existingReview
        ])
    </div>
</div>

<script>
// Switch Session Modal Functions
function openSwitchSessionModal() {
    document.getElementById('switchSessionModal').classList.remove('hidden');
    loadAvailableSessions();
}

function closeSwitchSessionModal() {
    document.getElementById('switchSessionModal').classList.add('hidden');
    resetModalState();
}

function resetModalState() {
    document.getElementById('switchSessionLoading').classList.remove('hidden');
    document.getElementById('availableSessionsList').classList.add('hidden');
    document.getElementById('noSessionsAvailable').classList.add('hidden');
    document.getElementById('switchSessionError').classList.add('hidden');
}

async function loadAvailableSessions() {
    try {
        const response = await fetch(`/user/sessions/{{ $enrollment->id }}/available-slots`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            displayAvailableSessions(data.sessions);
        } else {
            throw new Error('Failed to load sessions');
        }
    } catch (error) {
        console.error('Error loading available sessions:', error);
        showErrorState();
    }
}

function displayAvailableSessions(sessions) {
    const loading = document.getElementById('switchSessionLoading');
    const list = document.getElementById('availableSessionsList');
    const noSessions = document.getElementById('noSessionsAvailable');
    const container = document.getElementById('sessionsContainer');

    loading.classList.add('hidden');

    if (sessions.length == 0) {
        noSessions.classList.remove('hidden');
        return;
    }

    // Generate session options
    const sessionsHtml = sessions.map(session => `
        <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors cursor-pointer"
             onclick="selectNewSession(${session.id})">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium text-gray-900">${session.date}</div>
                    <div class="text-sm text-gray-600">${session.time_slot}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-green-600">$${session.fee}</div>
                    <div class="text-xs text-gray-500">{{ __('trans.available') }}</div>
                </div>
            </div>
        </div>
    `).join('');

    container.innerHTML = sessionsHtml;
    list.classList.remove('hidden');
}

function showErrorState() {
    document.getElementById('switchSessionLoading').classList.add('hidden');
    document.getElementById('switchSessionError').classList.remove('hidden');
}

async function selectNewSession(newSessionId) {
    if (!confirm('{{ __("trans.are_you_sure_switch_session") }}')) {
        return;
    }

    try {
        const response = await fetch(`/user/sessions/{{ $enrollment->id }}/switch`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                new_session_id: newSessionId
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // Show success message and reload page
                alert('{{ __("trans.session_time_switched_successfully") }}');
                window.location.reload();
            } else {
                alert('{{ __("trans.failed_to_switch_session") }} ' + data.message);
            }
        } else {
            throw new Error('Failed to switch session');
        }
    } catch (error) {
        console.error('Error switching session:', error);
        alert('{{ __("trans.error_switching_session") }}');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('switchSessionModal');
    if (event.target == modal) {
        closeSwitchSessionModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key == 'Escape') {
        closeSwitchSessionModal();
    }
});
</script>
@endsection
