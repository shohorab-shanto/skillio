@extends('admin.layouts.backend')

@section('title', __('trans.course_details'))

@section('header')
    {{ __('trans.course_details') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.course_information') }}</h1>
            <p class="text-gray-600 mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('trans.back_to_courses') }}
            </a>
            
            <!-- Course Action Buttons -->
            @if($course->status == 'pending')
                <button onclick="approveCourse({{ $course->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i class="fa-solid fa-check mr-2"></i>
                    {{ __('trans.approve_course') }}
                </button>
                
                <button onclick="showRejectModal({{ $course->id }})" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <i class="fa-solid fa-times mr-2"></i>
                    {{ __('trans.reject_course') }}
                </button>
            @elseif($course->status == 'approved')
                <button onclick="toggleCourseStatus({{ $course->id }})" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fa-solid fa-pause mr-2"></i>
                    {{ __('trans.pause_course') }}
                </button>
            @elseif($course->status == 'rejected')
                <button onclick="approveCourse({{ $course->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i class="fa-solid fa-check mr-2"></i>
                    {{ __('trans.approve_course') }}
                </button>
            @endif

            <!-- Featured Course Toggle Button -->
            @if($course->status == 'approved')
                <button onclick="toggleFeatured({{ $course->id }})" 
                        class="px-4 py-2 {{ $course->isFeatured() ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white rounded-lg transition-colors duration-200">
                    <i class="fa-solid {{ $course->isFeatured() ? 'fa-star' : 'fa-star-half-stroke' }} mr-2"></i>
                    {{ $course->isFeatured() ? __('trans.remove_featured') : __('trans.make_featured') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Course Header Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Course Thumbnail -->
            <div class="flex-shrink-0">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                         alt="{{ $course->title }}" 
                         class="w-48 h-32 rounded-lg object-cover">
                @else
                    <div class="w-48 h-32 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fa-solid fa-book text-4xl text-purple-600"></i>
                    </div>
                @endif
            </div>

            <!-- Course Basic Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h2>
                    @switch($course->status)
                        @case('approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fa-solid fa-check mr-1"></i>
                                {{ __('trans.approved') }}
                            </span>
                            @break
                        @case('pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fa-solid fa-clock mr-1"></i>
                                {{ __('trans.pending') }}
                            </span>
                            @break
                        @case('rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fa-solid fa-times mr-1"></i>
                                {{ __('trans.rejected') }}
                            </span>
                            @break
                    @endswitch

                    @if($course->isFeatured())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fa-solid fa-star mr-1"></i>
                            {{ __('trans.featured') }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        @php
                            $currencySymbol = $course->currency == 'EUR' ? '€' : '$';
                        @endphp
                        <p class="text-gray-600"><strong>{{ __('trans.price') }}:</strong> {{ $currencySymbol }}{{ number_format($course->price, 2) }}</p>
                        @if($course->discount > 0)
                            <p class="text-gray-600"><strong>{{ __('trans.discount') }}:</strong> {{ number_format($course->discount, 0) }}%</p>
                            <p class="text-gray-600"><strong>{{ __('trans.final_price') }}:</strong> {{ $currencySymbol }}{{ number_format($course->price - ($course->price * $course->discount / 100), 2) }}</p>
                        @endif
                        <p class="text-gray-600"><strong>{{ __('trans.duration') }}:</strong> {{ $course->duration_days }} {{ Str::plural(__('trans.day'), $course->duration_days) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600"><strong>{{ __('trans.category') }}:</strong> {{ $course->category->name ?? __('trans.na') }}</p>
                        <p class="text-gray-600"><strong>{{ __('trans.sub_categories') }}:</strong> 
                            @if($course->subCategories->count() > 0)
                                {{ $course->subCategories->pluck('name')->implode(', ') }}
                            @else
                                {{ __('trans.na') }}
                            @endif
                        </p>
                        <p class="text-gray-600"><strong>{{ __('trans.created') }}:</strong> {{ $course->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                @if($course->rejection_reason)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="text-sm font-medium text-red-800 mb-2">{{ __('trans.rejection_reason') }}</h4>
                    <p class="text-sm text-red-700">{{ $course->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Course Description -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('trans.description') }}</h3>
            <div class="text-gray-700 leading-relaxed">{!! nl2br(e($course->description)) !!}</div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Students -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.total_students') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->enrolledStudentsCount() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-users text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.average_rating') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->averageRating() ? number_format($course->averageRating(), 1) : '0' }}</p>
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
                    <p class="text-sm text-gray-600">{{ __('trans.total_reviews') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->totalReviews() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-comments text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Income -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('trans.total_income') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($course->totalIncome(), 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-dollar-sign text-xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mentor Information -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('trans.mentor_information') }}</h3>
        @if($course->mentor && $course->mentor->user)
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
                @if($course->mentor->photo)
                    <img src="{{ asset('storage/' . $course->mentor->photo) }}" 
                         alt="{{ $course->mentor->user->name }}" 
                         class="w-16 h-16 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-semibold text-blue-700">
                            {{ substr($course->mentor->user->name, 0, 1) }}
                        </span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-medium text-gray-900">{{ $course->mentor->user->name }}</h4>
                <p class="text-gray-600">{{ $course->mentor->user->email }}</p>
                @if($course->mentor->work_experience)
                    <p class="text-sm text-gray-500">{{ $course->mentor->work_experience }} {{ __('trans.years_experience') }}</p>
                @endif
                @if($course->mentor->bio)
                    <p class="text-sm text-gray-600 mt-2">{{ $course->mentor->bio }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($course->mentor->verified)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fa-solid fa-check-circle mr-1"></i>
                        {{ __('trans.verified') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fa-solid fa-clock mr-1"></i>
                        {{ __('trans.unverified') }}
                    </span>
                @endif
            </div>
        </div>
        @else
        <p class="text-gray-500">{{ __('trans.mentor_information_not_available') }}</p>
        @endif
    </div>

    <!-- Course Reviews -->
    @if($course->reviews->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('trans.course_reviews') }}</h3>
        <div class="space-y-4">
            @foreach($course->reviews->take(5) as $review)
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
        @if($course->reviews->count() > 5)
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-500">{{ __('trans.showing_reviews') }} {{ $course->reviews->count() }} {{ __('trans.reviews') }}</p>
        </div>
        @endif
    </div>
    @endif


</div>

<!-- Reject Course Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="reject-modal-content">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fa-solid fa-times text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
                {{ __('trans.reject_course') }}
            </h3>
            <p class="text-gray-600 text-center mb-6">
                {{ __('trans.provide_rejection_reason') }}
            </p>
            
            <form id="reject-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.rejection_reason') }}</label>
                    <textarea id="rejection-reason" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="{{ __('trans.enter_rejection_reason') }}"></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="cancelReject()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                        {{ __('trans.cancel') }}
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors duration-200">
                        <i class="fa-solid fa-times mr-2"></i>
                        {{ __('trans.reject_course_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveCourse(courseId) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = '{{ __('trans.approving') }}';
    
    fetch(`/admin/courses/${courseId}/approve`, {
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
        showNotification('{{ __('trans.an_error_occurred_approving') }}', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

function showRejectModal(courseId) {
    const modal = document.getElementById('reject-modal');
    const modalContent = document.getElementById('reject-modal-content');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function cancelReject() {
    const modal = document.getElementById('reject-modal');
    const modalContent = document.getElementById('reject-modal-content');
    
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
    
    document.getElementById('rejection-reason').value = '';
}

function toggleFeatured(courseId) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = '{{ __('trans.updating') }}';
    
    fetch(`/admin/courses/${courseId}/toggle-featured`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Reload page to show updated featured status
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ __('trans.an_error_occurred_updating_featured') }}', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

function toggleCourseStatus(courseId) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading
    button.disabled = true;
    button.textContent = '{{ __('trans.updating') }}';
    
    fetch(`/admin/courses/${courseId}/toggle-status`, {
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
        showNotification('{{ __('trans.an_error_occurred_updating_status') }}', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

// Handle reject form submission
document.getElementById('reject-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const rejectionReason = document.getElementById('rejection-reason').value.trim();
    
    if (!rejectionReason) {
        showNotification('{{ __('trans.please_provide_rejection_reason') }}', 'error');
        return;
    }
    
    const courseId = {{ $course->id }};
    
    fetch(`/admin/courses/${courseId}/reject`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rejection_reason: rejectionReason
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            cancelReject();
            // Reload page to show updated status
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ __('trans.an_error_occurred_rejecting') }}', 'error');
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
document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        cancelReject();
    }
});
</script>
@endpush
