@extends('admin.layouts.backend')

@section('title', 'Create Mentor')

@section('header')
    Create Mentor
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-end items-center gap-3">
        <a href="{{ route('admin.mentors.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back to Mentors
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               placeholder="Enter mentor's full name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               placeholder="Enter mentor's email address"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   placeholder="Enter password (min 8 characters)"
                                   class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Must contain 1 uppercase letter, 1 number, min. 8 characters</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   placeholder="Confirm password"
                                   class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="+1 (555) 123-4567">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Mentor Profile -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mentor Profile</h3>
                
                <!-- Profile Photo -->
                <div class="mb-6">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                        Profile Photo
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div id="photo-preview" class="h-20 w-20 rounded-full bg-gray-100 flex items-center justify-center border-2 border-gray-300">
                                <i class="fa-solid fa-user text-2xl text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('photo') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">300×300 recommended, max 2MB (JPEG, PNG, JPG, GIF)</p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Work Experience -->
                    <div>
                        <label for="work_experience" class="block text-sm font-medium text-gray-700 mb-2">
                            Work Experience
                        </label>
                        <input type="text" id="work_experience" name="work_experience" value="{{ old('work_experience') }}"
                               placeholder="e.g., 5 years in software development"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('work_experience') border-red-500 @enderror">
                        @error('work_experience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div class="mt-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Bio/Work Experience
                    </label>
                    <textarea id="bio" name="bio" rows="4"
                              placeholder="Enter mentor's bio and background"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('bio') border-red-500 @enderror">{{ old('bio') }}</textarea>
                    <div class="flex justify-between items-center mt-1">
                        <div>
                            @error('bio')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <span id="bio-counter" class="text-xs text-gray-500">0/1000</span>
                    </div>
                </div>
            </div>

            <!-- Mentor Settings -->
            <div class="pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mentor Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                    <!-- Availability -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Availability *</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="availability" 
                                       value="available" 
                                       {{ old('availability', 'available') == 'available' ? 'checked' : '' }}
                                       class="mr-2 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Available</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="availability" 
                                       value="unavailable" 
                                       {{ old('availability', 'available') == 'unavailable' ? 'checked' : '' }}
                                       class="mr-2 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Unavailable</span>
                            </label>
                        </div>
                        @error('availability')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Verification Status -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="verified" 
                                   value="1"
                                   {{ old('verified') ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-700">Verified Mentor</span>
                        </label>
                        @error('verified')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                                    <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mentor Type *</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="type" 
                                   value="online" 
                                   {{ old('type', 'online') == 'online' ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500"
                                   onchange="toggleLocationField()">
                            <span class="text-sm text-gray-700">Online</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="type" 
                                   value="in-person" 
                                   {{ old('type', 'online') == 'in-person' ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500"
                                   onchange="toggleLocationField()">
                            <span class="text-sm text-gray-700">In-Person</span>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location (conditional) -->
                <div id="location-field" style="display: none;">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="address" 
                           name="address" 
                           value="{{ old('address') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('address') border-red-500 @enderror"
                           placeholder="City, Country">
                    <p class="text-sm text-gray-500 mt-1">Required for in-person mentors</p>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.mentors.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Create Mentor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type == 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function previewPhoto(input) {
    const file = input.files[0];
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            input.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-20 w-20 rounded-full object-cover">`;
        };
        reader.readAsDataURL(file);
    }
}

// Character counter for bio field
document.getElementById('bio').addEventListener('input', function() {
    const bioCounter = document.getElementById('bio-counter');
    const currentLength = this.value.length;
    const maxLength = 1000;
    
    bioCounter.textContent = `${currentLength}/${maxLength}`;
    
    // Change color based on usage
    if (currentLength > maxLength * 0.9) {
        bioCounter.className = 'text-xs text-red-500';
    } else if (currentLength > maxLength * 0.7) {
        bioCounter.className = 'text-xs text-yellow-500';
    } else {
        bioCounter.className = 'text-xs text-gray-500';
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

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    const availability = document.getElementById('availability').value;
    const type = document.getElementById('type').value;
    
    if (!name) {
        e.preventDefault();
        alert('Please enter mentor name.');
        document.getElementById('name').focus();
        return;
    }
    
    if (!email) {
        e.preventDefault();
        alert('Please enter mentor email.');
        document.getElementById('email').focus();
        return;
    }
    
    if (!password) {
        e.preventDefault();
        alert('Please enter password.');
        document.getElementById('password').focus();
        return;
    }
    
    if (password !== passwordConfirmation) {
        e.preventDefault();
        alert('Passwords do not match.');
        document.getElementById('password_confirmation').focus();
        return;
    }
    
    if (!availability) {
        e.preventDefault();
        alert('Please select availability.');
        document.getElementById('availability').focus();
        return;
    }
    
    if (!type) {
        e.preventDefault();
        alert('Please select mentor type.');
        document.getElementById('type').focus();
        return;
    }

    // Additional validation for in-person mentors
    if (type === 'in-person' && !document.getElementById('address').value.trim()) {
        e.preventDefault();
        alert('Location is required for in-person mentors.');
        document.getElementById('address').focus();
        return;
    }
});

// Initialize character counter on page load
document.addEventListener('DOMContentLoaded', function() {
    const bioField = document.getElementById('bio');
    if (bioField) {
        const event = new Event('input', { bubbles: true });
        bioField.dispatchEvent(event);
    }
});

// Toggle location field based on mentor type
function toggleLocationField() {
    const onlineRadio = document.querySelector('input[name="type"][value="online"]');
    const inPersonRadio = document.querySelector('input[name="type"][value="in-person"]');
    const locationField = document.getElementById('location-field');
    const addressInput = document.getElementById('address');
    
    if (inPersonRadio.checked) {
        locationField.style.display = 'block';
        addressInput.required = true;
    } else {
        locationField.style.display = 'none';
        addressInput.required = false;
        addressInput.value = ''; // Clear the field when hidden
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleLocationField();
});
</script>
@endpush
