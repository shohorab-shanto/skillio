@extends('admin.layouts.backend')

@section('title', 'Mentor Details')

@section('header')
    Mentor Details
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-end items-center gap-4 mb-4">
        <a href="{{ route('admin.mentors.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back to Mentors
        </a>
    </div>

    <!-- Mentor Profile Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Profile Photo -->
            <div class="flex-shrink-0">
                @if($mentor->photo)
                    <img src="{{ asset('storage/' . $mentor->photo) }}" 
                         alt="{{ $user->name }}" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-purple-100">
                @else
                    <div class="w-32 h-32 rounded-full bg-purple-100 flex items-center justify-center border-4 border-purple-200">
                        <span class="text-4xl font-semibold text-purple-700">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Basic Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                    @if($mentor->verified)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fa-solid fa-check-circle mr-1"></i>
                            Verified
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fa-solid fa-clock mr-1"></i>
                            Unverified
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="text-gray-600"><strong>User ID:</strong> {{ $user->id }}</p>
                        <p class="text-gray-600"><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600"><strong>Work Experience:</strong> {{ $mentor->work_experience ?? 'N/A' }} years</p>
                        <p class="text-gray-600"><strong>Type:</strong> {{ ucfirst($mentor->type ?? 'Standard') }}</p>
                        <p class="text-gray-600"><strong>Availability:</strong> 
                            @if($mentor->availability == 'available')
                                <span class="text-green-600 font-medium">Available</span>
                            @else
                                <span class="text-red-600 font-medium">Unavailable</span>
                            @endif
                        </p>
                        <p class="text-gray-600"><strong>Stripe Account:</strong> 
                            @if($mentor->hasStripeConnectAccount())
                                <span class="text-green-600">Connected</span>
                            @else
                                <span class="text-red-600">Not Connected</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio Section -->
        @if($mentor->bio)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Biography</h3>
            <p class="text-gray-700 leading-relaxed">{{ $mentor->bio }}</p>
        </div>
        @endif

        <!-- Certifications Section -->
        @if($mentor->certifications && is_array($mentor->certifications) && count($mentor->certifications) > 0)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Certifications</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($mentor->certifications as $certification)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fa-solid fa-certificate mr-1"></i>
                        {{ $certification }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Courses -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $mentor->totalCourses() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Sessions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Sessions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $mentor->sessionBookings()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Average Rating</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $mentor->getFormattedAverageRatingAttribute() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-star text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Reviews -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $mentor->totalReviews() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-comments text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.mentors.courses', $user->id) }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fa-solid fa-book mr-2"></i>
                View Courses
            </a>
            
            <a href="{{ route('admin.mentors.sessions', $user->id) }}" 
               class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fa-solid fa-clock mr-2"></i>
                View Sessions
            </a>
            
            <button onclick="toggleVerification({{ $user->id }}, {{ $mentor->verified ? 'true' : 'false' }})" 
                    class="px-6 py-3 rounded-lg transition-colors duration-200 {{ $mentor->verified ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white">
                <i class="fa-solid {{ $mentor->verified ? 'fa-times-circle' : 'fa-check-circle' }} mr-2"></i>
                {{ $mentor->verified ? 'Unverify' : 'Verify' }}
            </button>
            
            <button onclick="showAccountDetailsModal()" 
                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <i class="fa-solid fa-credit-card mr-2"></i>
                Stripe Connect
            </button>
            
            <button onclick="showAvailabilityModal({{ $user->id }}, '{{ $mentor->availability }}')" 
                    class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                <i class="fa-solid fa-edit mr-2"></i>
                Update Availability
            </button>
        </div>
    </div>

    <!-- Recent Reviews -->
    @if($mentor->recentReviews()->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Reviews</h3>
        <div class="space-y-4">
            @foreach($mentor->recentReviews()->get() as $review)
            <div class="border-l-4 border-purple-500 pl-4 py-2">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">{{ $review->user->name }}</span>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                                @else
                                    <i class="fa-regular fa-star text-gray-300 text-sm"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-gray-700 text-sm">{{ $review->comment }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Reviews</h3>
        <div class="text-center py-8">
            <i class="fa-solid fa-comments text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No reviews yet</p>
            <p class="text-sm text-gray-400">This mentor hasn't received any reviews</p>
        </div>
    </div>
    @endif
</div>

<!-- Availability Update Modal -->
<div id="availability-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="availability-modal-content">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-orange-100 rounded-full mb-4">
                <i class="fa-solid fa-clock text-2xl text-orange-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
                Toggle Availability
            </h3>
            <p class="text-gray-600 text-center mb-6">
                Switch between available and unavailable status.
            </p>
            
            <form id="availability-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Set Status To</label>
                    <select id="availability-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="cancelAvailabilityUpdate()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition-colors duration-200">
                        <i class="fa-solid fa-update mr-2"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Account Details Modal -->
<div id="account-details-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="account-details-modal-content">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-purple-100 rounded-full mb-4">
                <i class="fa-solid fa-user-cog text-2xl text-purple-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
                Stripe Connect Account
            </h3>
            <p class="text-gray-600 text-center mb-6">
                Manage mentor's Stripe Connect account for payment processing.
            </p>
            
            <form id="account-details-form" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                


                <!-- Stripe Connect Information -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Stripe Connect Account</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Connect Account ID</label>
                            <input type="text" name="stripe_connect_account_id" value="{{ $mentor->stripe_connect_account_id ?? '' }}" 
                                   placeholder="acct_..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Stripe Connect account ID for receiving payments</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Status</label>
                            <select name="connect_account_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="pending" {{ ($mentor->connect_account_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ ($mentor->connect_account_status ?? 'pending') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="rejected" {{ ($mentor->connect_account_status ?? 'pending') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="restricted" {{ ($mentor->connect_account_status ?? 'pending') == 'restricted' ? 'selected' : '' }}>Restricted</option>
                            </select>
                        </div>
                    </div>
                </div>




                
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="cancelAccountDetailsUpdate()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition-colors duration-200">
                        <i class="fa-solid fa-credit-card mr-2"></i>
                        Update Stripe Connect
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAccountDetailsModal() {
    const modal = document.getElementById('account-details-modal');
    const modalContent = document.getElementById('account-details-modal-content');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function cancelAccountDetailsUpdate() {
    const modal = document.getElementById('account-details-modal');
    const modalContent = document.getElementById('account-details-modal-content');
    
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function toggleVerification(mentorId, currentStatus) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = 'Updating...';
    
    fetch(`/admin/mentors/${mentorId}/toggle-verification`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated status
            window.location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating verification status', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

function showAvailabilityModal(mentorId, currentAvailability) {
    // Set current availability in select
    document.getElementById('availability-select').value = currentAvailability;
    
    const modal = document.getElementById('availability-modal');
    const modalContent = document.getElementById('availability-modal-content');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function cancelAvailabilityUpdate() {
    const modal = document.getElementById('availability-modal');
    const modalContent = document.getElementById('availability-modal-content');
    
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Handle availability form submission
document.getElementById('availability-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newAvailability = document.getElementById('availability-select').value;
    const mentorId = {{ $user->id }};
    
    fetch(`/admin/mentors/${mentorId}/update-availability`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            availability: newAvailability
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            cancelAvailabilityUpdate();
            // Reload page to show updated availability
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating availability', 'error');
    });
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[200] px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type == 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('availability-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        cancelAvailabilityUpdate();
    }
});

// Handle account details form submission
document.getElementById('account-details-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Disable button and show loading
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Updating...';
    
    // Collect form data
    const formData = new FormData(form);
    
    fetch(`/admin/mentors/${formData.get('user_id')}/update-account-details`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Account details updated successfully!', 'success');
            cancelAccountDetailsUpdate();
            
            // Reload page after a short delay to reflect changes
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to update account details. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable button and restore original text
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});

// Close account details modal when clicking outside
document.getElementById('account-details-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        cancelAccountDetailsUpdate();
    }
});
</script>
@endpush
