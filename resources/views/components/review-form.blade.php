@props(['type', 'item', 'existingReview' => null])

<div x-data="reviewForm" class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ $existingReview ? __('trans.edit_your_review') : __('trans.write_a_review') }}
        </h3>
        @if($existingReview)
            <span class="text-sm text-gray-500">{{ __('trans.last_updated') }} {{ $existingReview->updated_at->format('M d, Y') }}</span>
        @endif
    </div>

    <form @submit.prevent="submitReview()" class="space-y-6">
        @csrf
        <!-- Rating -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('trans.your_rating') }}</label>
            <div class="flex items-center space-x-2">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" 
                            @click="rating = {{ $i }}"
                            :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                            class="w-10 h-10 hover:scale-110 transition-transform">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
                <span class="ml-3 text-sm text-gray-600">
                    <span x-text="rating === 0 ? '{{ __('trans.select_rating') }}' : rating + ' {{ __('trans.star') }}' + (rating > 1 ? '{{ __('trans.stars') }}' : '')"></span>
                </span>
            </div>
        </div>

        <!-- Comment -->
        <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.your_review') }}</label>
            <textarea 
                id="comment"
                x-model="comment"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                placeholder="{{ $type === 'course' ? __('trans.share_experience_course') : __('trans.share_experience_mentor') }}"></textarea>
        </div>

        <!-- Error Message -->
        <div x-show="error" x-text="error" class="text-sm text-red-600 bg-red-50 p-4 rounded-lg border border-red-200"></div>

        <!-- Success Message -->
        <div x-show="success" x-text="success" class="text-sm text-green-600 bg-green-50 p-4 rounded-lg border border-green-200"></div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            @if($existingReview)
                <!-- Delete Button -->
                <button type="button" 
                        onclick="openDeleteModal()"
                        class="px-6 py-3 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    {{ __('trans.delete_review') }}
                </button>
            @endif
            
            <button type="submit" 
                    :disabled="submitting"
                    class="px-6 py-3 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span x-show="!submitting">
                    {{ $existingReview ? __('trans.update_review') : __('trans.submit_review') }}
                </span>
                <span x-show="submitting">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $existingReview ? __('trans.updating') : __('trans.submitting') }}
                </span>
            </button>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
@if($existingReview)
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    
    <!-- Modal panel -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            
            <!-- Modal content -->
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                        {{ __('trans.delete_review_modal_title') }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            {{ $type === 'course' ? __('trans.delete_review_confirmation') : __('trans.delete_review_confirmation_mentor') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Modal actions -->
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <!-- Laravel Delete Form -->
                <form action="{{ route('reviews.destroy', $existingReview->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                        {{ __('trans.delete_review') }}
                    </button>
                </form>
                
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                    {{ __('trans.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
// Regular JavaScript for modal control
function openDeleteModal() {
    console.log('Opening delete modal');
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeDeleteModal() {
    console.log('Closing delete modal');
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeDeleteModal();
            }
        });
    }
});

document.addEventListener('alpine:init', () => {
    Alpine.data('reviewForm', () => ({
        rating: {{ $existingReview ? $existingReview->rating : 0 }},
        comment: '{{ $existingReview ? $existingReview->comment : "" }}',

        submitting: false,
        error: '',
        success: '',

        async submitReview() {
            if (this.rating === 0) {
                this.error = '{{ __('trans.please_select_rating') }}';
                return;
            }

            if (!this.comment.trim()) {
                this.error = '{{ __('trans.please_write_review_comment') }}';
                return;
            }

            this.submitting = true;
            this.error = '';

            try {
                // Get CSRF token - try multiple methods
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                // Fallback: try to get from input field
                if (!csrfToken) {
                    csrfToken = document.querySelector('input[name="_token"]')?.value;
                }
                
                // Fallback: try to get from cookie
                if (!csrfToken) {
                    csrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
                }
                
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Please refresh the page and try again.');
                }

                const formData = {
                    rating: this.rating,
                    comment: this.comment,
                    @if($type === 'course')
                    course_id: {{ $item->id }}
                    @else
                    mentor_id: {{ $item->id }}
                    @endif
                };

                const url = '{{ $existingReview ? route("reviews.update", $existingReview->id) : route("reviews.store") }}';
                const method = '{{ $existingReview ? "PUT" : "POST" }}';

                console.log('Submitting review:', { url, method, formData, csrfToken });

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error text:', errorText);
                    
                    try {
                        const errorResult = JSON.parse(errorText);
                        this.error = errorResult.message || `HTTP ${response.status}: ${response.statusText}`;
                    } catch (parseError) {
                        this.error = `HTTP ${response.status}: ${response.statusText}`;
                    }
                    return;
                }

                const result = await response.json();
                console.log('Response result:', result);

                this.success = result.message;
                setTimeout(() => {
                    // Refresh the page to show updated review
                    window.location.reload();
                }, 1500);

            } catch (error) {
                console.error('Review submission error:', error);
                this.error = 'Network error. Please check your connection and try again. Error: ' + error.message;
            } finally {
                this.submitting = false;
            }
        }
    }));
});
</script>
