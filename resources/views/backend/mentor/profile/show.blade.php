@extends('backend.layouts.app')

@section('title', 'Profile')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
        </div>
    </div>
@endsection

@section('content')


<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    <!-- Left Column - Edit Profile -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Edit Profile</h2>
        
        <form method="POST" action="{{ route('mentor.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Profile Photo Section -->
            <div class="text-center">
                <div class="relative inline-block">
                    @if($mentor->photo)
                        <img class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg" 
                             src="{{ asset('storage/' . $mentor->photo) }}" 
                             alt="Profile Photo" />
                    @else
                        <div class="h-32 w-32 rounded-full bg-gray-100 flex items-center justify-center border-4 border-white shadow-lg">
                            <i class="fa-solid fa-user text-3xl text-gray-400"></i>
                        </div>
                    @endif
                    <label for="photo" class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 bg-white text-gray-600 rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-gray-50 transition-colors shadow-md border border-gray-200">
                        <i class="fa-solid fa-upload text-sm"></i>
                    </label>
                </div>
                <input type="file" id="photo" name="photo" accept="image/*" class="hidden">
                @error('photo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-900">Upload Photo</p>
                    <p class="text-xs text-gray-500">300×300 and max 2 MB</p>
                </div>
                
                <!-- Rating Display -->
                <div class="mt-4">
                    @if($mentor->totalReviews() > 0)
                        <div class="flex items-center justify-center space-x-2 mb-2">
                            <!-- Star Rating Display -->
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $mentor->star_rating['full_stars'])
                                        <i class="fa-solid fa-star text-yellow-400"></i>
                                    @elseif($i == $mentor->star_rating['full_stars'] + 1 && $mentor->star_rating['half_star'])
                                        <i class="fa-solid fa-star-half-stroke text-yellow-400"></i>
                                    @else
                                        <i class="fa-regular fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <a href="{{ route('mentor.reviews.index') }}" class="text-sm font-medium text-gray-900 hover:text-purple-600 transition-colors">
                                {{ $mentor->formatted_average_rating }} ({{ $mentor->totalReviews() }} {{ Str::plural('Review', $mentor->totalReviews()) }})
                            </a>
                        </div>
                        
                        <!-- Additional Rating Stats -->
                        <div class="text-center">
                            @if($mentor->hasExcellentReviews())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fa-solid fa-badge-check mr-1"></i>
                                    Excellent Reviews
                                </span>
                            @endif
                            
                            @if($mentor->five_star_percentage > 70)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-1">
                                    {{ $mentor->five_star_percentage }}% 5-star
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center justify-center space-x-2">
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-regular fa-star text-gray-300"></i>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-500">No reviews yet</span>
                        </div>
                    @endif
                </div>
                
                <!-- Online Status Toggle -->
                <div class="mt-4 flex items-center justify-center space-x-3">
                    <span class="text-sm text-gray-600">Online Status:</span>
                    <div class="flex items-center space-x-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="online-toggle" class="sr-only peer" {{ ($mentor->availability ?? 'available') === 'available' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                        <span id="status-text" class="text-sm font-medium {{ ($mentor->availability ?? 'available') === 'available' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($mentor->availability ?? 'available') === 'available' ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                       placeholder="City,Country">
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                       placeholder="contact number">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Work Experience -->
            <div>
                <label for="work_experience" class="block text-sm font-medium text-gray-700 mb-2">Work Experience</label>
                <input type="text" id="work_experience" name="work_experience" value="{{ old('work_experience', $mentor->work_experience) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                       placeholder="e.g., Designer">
                @error('work_experience')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio/Work Experience -->
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio/Work Experience</label>
                <textarea id="bio" name="bio" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                          placeholder="I'm a motivated learner passionate about personal...">{{ old('bio', $mentor->bio) }}</textarea>
                @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-purple-600 text-white py-2.5 rounded-lg font-medium hover:bg-purple-700 transition-colors duration-200">
                Update Profile
            </button>
        </form>
    </div>

    <!-- Right Column - Personal Information -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Personal Information</h2>
            @if($mentor->verified)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fa-solid fa-badge-check mr-1"></i>
                    Verified Mentor
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fa-solid fa-clock mr-1"></i>
                    Pending Verification
                </span>
            @endif
        </div>
        
        <!-- Display Information (Read-only) -->
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                    <p class="text-sm text-gray-900">{{ $user->name ?? 'Anny Leo' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <p class="text-sm text-gray-900">{{ $user->email ?? 'annyleo@gmail.com' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Location</label>
                    <p class="text-sm text-gray-900">{{ $user->address ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                    <p class="text-sm text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Work Experience</label>
                <p class="text-sm text-gray-900">{{ $mentor->work_experience ?? 'Designer' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Bio/Work Experience</label>
                <p class="text-sm text-gray-900">{{ $mentor->bio ?? "I'm a motivated learner passionate about personal..." }}</p>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Reviews & Ratings</h3>
                @if($mentor->totalReviews() > 0)
                    <a href="{{ route('mentor.reviews.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium transition-colors">
                        {{ $mentor->totalReviews() }} total reviews
                        <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                @else
                    <span class="text-sm text-gray-500">{{ $mentor->totalReviews() }} total reviews</span>
                @endif
            </div>
            
            @if($mentor->totalReviews() > 0)
                <!-- Rating Overview -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900">{{ $mentor->formatted_average_rating }}</div>
                                <div class="flex items-center justify-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $mentor->star_rating['full_stars'])
                                            <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                                        @elseif($i == $mentor->star_rating['full_stars'] + 1 && $mentor->star_rating['half_star'])
                                            <i class="fa-solid fa-star-half-stroke text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="fa-regular fa-star text-gray-300 text-sm"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Overall Rating</div>
                            </div>
                        </div>
                        
                        <!-- Rating Distribution -->
                        <div class="flex-1 ml-6">
                            @php
                                $distribution = $mentor->ratingDistribution();
                                $total = $mentor->totalReviews();
                            @endphp
                            
                            @for($rating = 5; $rating >= 1; $rating--)
                                @php
                                    $count = $distribution[$rating] ?? 0;
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                @endphp
                                <div class="flex items-center mb-1">
                                    <span class="text-xs text-gray-600 w-8">{{ $rating }}★</span>
                                    <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 w-8">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Recent Reviews -->
                @php
                    $recentReviews = $mentor->recentReviews()->get();
                @endphp
                
                @if($recentReviews->count() > 0)
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Recent Reviews</h4>
                        <div class="space-y-4">
                            @foreach($recentReviews as $review)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-purple-600">
                                                    {{ substr($review->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                                                <div class="flex items-center space-x-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                                                        @else
                                                            <i class="fa-regular fa-star text-gray-300 text-xs"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="text-xs text-gray-500 ml-1">{{ $review->rating }}/5</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($mentor->totalReviews() > 5)
                            <div class="text-center mt-4">
                                <a href="{{ route('mentor.reviews.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium transition-colors">
                                    View All {{ $mentor->totalReviews() }} Reviews
                                    <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-star text-2xl text-gray-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No Reviews Yet</h4>
                    <p class="text-sm text-gray-500">Your reviews will appear here once students start rating your mentoring.</p>
                </div>
            @endif
        </div>

        <!-- Change Password Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h3>
            
            <form id="password-form" method="POST" action="{{ route('mentor.profile.updatePassword') }}" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="current_password" name="current_password" 
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                                   placeholder="• • • • • • • • • •">
                            <button type="button" onclick="togglePassword('current_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" 
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                                   placeholder="• • • • • • • • • •">
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        <div class="mt-1">
                            <p class="text-xs text-gray-500">Must contain 1 uppercase letter, 1 number, min. 8 characters</p>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full pl-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                               placeholder="• • • • • • • • • •">
                    </div>
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors duration-200 mt-6">
                    Update Password
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Online status toggle
document.getElementById('online-toggle').addEventListener('change', function() {
    const statusText = document.getElementById('status-text');
    if (this.checked) {
        statusText.textContent = 'Online';
        statusText.className = 'text-sm font-medium text-green-600';
    } else {
        statusText.textContent = 'Offline';
        statusText.className = 'text-sm font-medium text-red-600';
    }
    
    // Update the status in the database
    fetch('{{ route("mentor.profile.updateStatus") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ online: this.checked })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Status updated successfully');
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        // Revert the toggle if there's an error
        this.checked = !this.checked;
        if (this.checked) {
            statusText.textContent = 'Online';
            statusText.className = 'text-sm font-medium text-green-600';
        } else {
            statusText.textContent = 'Offline';
            statusText.className = 'text-sm font-medium text-red-600';
        }
    });
});

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password validation
function validatePassword(password) {
    const hasUppercase = /[A-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    const minLength = password.length >= 8;
    
    return {
        hasUppercase,
        hasNumber,
        minLength,
        isValid: hasUppercase && hasNumber && minLength
    };
}

// Add real-time password validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const validation = validatePassword(password);
    
    // Remove existing validation message
    const existingMessage = document.querySelector('#password-validation');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    if (password.length > 0 && !validation.isValid) {
        let message = 'Password must contain:';
        const requirements = [];
        
        if (!validation.minLength) requirements.push('at least 8 characters');
        if (!validation.hasUppercase) requirements.push('1 uppercase letter');
        if (!validation.hasNumber) requirements.push('1 number');
        
        message += ' ' + requirements.join(', ');
        
        const validationDiv = document.createElement('p');
        validationDiv.id = 'password-validation';
        validationDiv.className = 'mt-1 text-sm text-red-600';
        validationDiv.textContent = message;
        
        this.parentNode.parentNode.appendChild(validationDiv);
    }
});

// Form submission validation
document.getElementById('password-form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const validation = validatePassword(password);
    
    if (!validation.isValid) {
        e.preventDefault();
        alert('Please ensure your password meets all requirements: minimum 8 characters, 1 uppercase letter, and 1 number.');
        return false;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password confirmation does not match.');
        return false;
    }
});

// Phone number validation
document.getElementById('phone').addEventListener('input', function() {
    const phone = this.value;
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    
    // Remove existing validation message
    const existingMessage = document.querySelector('#phone-validation');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    if (phone.length > 0 && !phoneRegex.test(phone)) {
        const validationDiv = document.createElement('p');
        validationDiv.id = 'phone-validation';
        validationDiv.className = 'mt-1 text-sm text-red-600';
        validationDiv.textContent = 'Please enter a valid phone number (e.g., +1234567890)';
        
        this.parentNode.appendChild(validationDiv);
    }
});

// Photo preview
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            this.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const photoContainer = document.querySelector('.relative.inline-block');
            photoContainer.innerHTML = `
                <img class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg" 
                     src="${e.target.result}" 
                     alt="Profile Photo" />
                <label for="photo" class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 bg-white text-gray-600 rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-gray-50 transition-colors shadow-md border border-gray-200">
                    <i class="fa-solid fa-upload text-sm"></i>
                </label>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
