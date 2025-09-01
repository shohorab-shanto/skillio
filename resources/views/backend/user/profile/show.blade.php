@extends('backend.layouts.app')

@section('title', __('trans.profile'))

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.profile') }}</h1>
        </div>
    </div>
@endsection

@section('content')


<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    <!-- Left Column - Edit Profile -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('trans.edit_profile') }}</h2>
        
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Profile Photo Section (Read-only) -->
            <div class="text-center">
                <div class="relative inline-block">
                    @if($user->photo)
                        <img class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg" 
                             src="{{ asset('storage/' . $user->photo) }}" 
                             alt="{{ __('trans.profile_photo') }}" />
                    @else
                        <div class="h-32 w-32 rounded-full bg-purple-100 flex items-center justify-center border-4 border-white shadow-lg">
                            <i class="fa-solid fa-user text-4xl text-purple-600"></i>
                        </div>
                    @endif
                </div>
                
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                </div>
            </div>

            <!-- Location -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.location') }}</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" 
                       maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                       placeholder="{{ __('trans.city_country') }}">
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.phone_number') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                       maxlength="20"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                       placeholder="{{ __('trans.enter_phone_number') }}">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-purple-600 text-white py-2.5 rounded-lg font-medium hover:bg-purple-700 transition-colors duration-200">
                {{ __('trans.update_profile') }}
            </button>
        </form>
    </div>

    <!-- Right Column - Personal Information -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('trans.personal_information') }}</h2>
        </div>
        
        <!-- Display Information (Read-only) -->
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('trans.full_name') }}</label>
                    <p class="text-sm text-gray-900">{{ $user->name ?? 'Anny Leo' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('trans.email') }}</label>
                    <p class="text-sm text-gray-900">{{ $user->email ?? 'annyleo@gmail.com' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('trans.location') }}</label>
                    <p class="text-sm text-gray-900">{{ $user->address ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('trans.phone_number') }}</label>
                    <p class="text-sm text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">{{ __('trans.change_password') }}</h3>
            
            <form id="password-form" method="POST" action="{{ route('user.profile.updatePassword') }}" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.current_password') }} *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="current_password" name="current_password" 
                                   maxlength="255"
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
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.new_password') }} *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" 
                                   maxlength="255"
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                                   placeholder="• • • • • • • • • •">
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        <div class="mt-1">
                            <p class="text-xs text-gray-500">{{ __('trans.password_requirements') }}</p>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.confirm_password') }} *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               maxlength="255"
                               class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                               placeholder="• • • • • • • • • •">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fa-solid fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors duration-200 mt-6">
                    {{ __('trans.update_password') }}
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
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
        let message = '{{ __('trans.password_must_contain') }}';
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
        alert('{{ __('trans.password_must_contain') }}');
        return false;
    }
    
    if (password != confirmPassword) {
        e.preventDefault();
        alert('{{ __('trans.password_confirmation_mismatch') }}');
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
        validationDiv.textContent = '{{ __('trans.enter_valid_phone') }}';
        
        this.parentNode.appendChild(validationDiv);
    }
});
</script>
@endsection
