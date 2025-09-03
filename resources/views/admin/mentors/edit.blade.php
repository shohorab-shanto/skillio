@extends('admin.layouts.backend')

@section('title', 'Edit Mentor')

@section('header')
    Edit Mentor
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-end items-center gap-3">
        <a href="{{ route('admin.mentors.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Mentors
        </a>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('admin.mentors.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Personal Information Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $user->phone ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                           placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location', $user->address ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('location') border-red-500 @enderror"
                           placeholder="City, Country">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Profile Photo Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Photo</h2>
            
            <div class="flex items-center space-x-6">
                <!-- Current Photo -->
                <div class="flex-shrink-0">
                    @if($mentor && $mentor->photo)
                        <img src="{{ Storage::url($mentor->photo) }}" 
                             alt="Current Profile Photo" 
                             class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Upload New Photo -->
                <div class="flex-1">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Upload New Photo</label>
                    <input type="file" 
                           id="photo" 
                           name="photo" 
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('photo') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Max 2MB, JPEG/PNG/JPG/GIF formats</p>
                    @error('photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Professional Information Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h2>
            
            <div class="space-y-6">
                <!-- Work Experience -->
                <div>
                    <label for="work_experience" class="block text-sm font-medium text-gray-700 mb-2">Work Experience</label>
                    <input type="text" 
                           id="work_experience" 
                           name="work_experience" 
                           value="{{ old('work_experience', $mentor->work_experience ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('work_experience') border-red-500 @enderror"
                           placeholder="e.g., Software Engineer, Marketing Manager">
                    @error('work_experience')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio/Work Experience</label>
                    <textarea id="bio" 
                              name="bio" 
                              rows="4"
                              maxlength="1000"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('bio') border-red-500 @enderror"
                              placeholder="Tell us about your professional background, expertise, and experience...">{{ old('bio', $mentor->bio ?? '') }}</textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm text-gray-500">Describe your professional background and expertise</p>
                        <span id="bio-counter" class="text-sm text-gray-400">0/1000</span>
                    </div>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Account Settings Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Settings</h2>
            
            <div class="space-y-6">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="Leave blank to keep current password">
                    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password_confirmation') border-red-500 @enderror"
                           placeholder="Confirm new password">
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Availability *</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="availability" 
                                   value="available" 
                                   {{ old('availability', $mentor->availability ?? 'available') == 'available' ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-700">Available</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="availability" 
                                   value="unavailable" 
                                   {{ old('availability', $mentor->availability ?? 'available') == 'unavailable' ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-700">Unavailable</span>
                        </label>
                    </div>
                    @error('availability')
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
                                   {{ old('type', $mentor->type ?? 'online') == 'online' ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-700">Online</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="type" 
                                   value="in-person" 
                                   {{ old('type', $mentor->type ?? 'online') == 'in-person' ? 'checked' : '' }}
                                   class="mr-2 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-700">In-Person</span>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Verification Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="verified" 
                               value="1"
                               {{ old('verified', $mentor->verified ?? false) ? 'checked' : '' }}
                               class="mr-2 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Verified Mentor</span>
                    </label>
                    @error('verified')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.mentors.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Update Mentor
            </button>
        </div>
    </form>
</div>

<script>
// Bio character counter
document.addEventListener('DOMContentLoaded', function() {
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');
    
    function updateCounter() {
        const length = bioTextarea.value.length;
        bioCounter.textContent = length + '/1000';
        
        if (length > 1000) {
            bioCounter.classList.add('text-red-500');
            bioCounter.classList.remove('text-gray-400');
        } else {
            bioCounter.classList.remove('text-red-500');
            bioCounter.classList.add('text-gray-400');
        }
    }
    
    bioTextarea.addEventListener('input', updateCounter);
    updateCounter(); // Initial count
});
</script>
@endsection
