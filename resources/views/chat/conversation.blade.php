@php
    // Get the other user in the conversation
    $currentUser = auth()->user();
    $currentUserMentor = $currentUser->mentor ?? null; // Get mentor record if user is a mentor
    
    if ($conversation->user_id == $currentUser->id) {
        // Current user is the student, so other user is the mentor
        $otherUser = $conversation->mentor->user;
        $otherUserRole = 'Mentor';
        $otherUserPhoto = $conversation->mentor->photo; // Mentor photo from mentors table
    } else {
        // Current user is the mentor, so other user is the student
        $otherUser = $conversation->user;
        $otherUserRole = 'Student';
        $otherUserPhoto = null; // Students don't have photos
    }
@endphp

<!-- Chat Header -->
<div class="p-4 border-b border-gray-200 bg-white flex-shrink-0">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <!-- Mobile Back Button -->
            <button onclick="goBackToConversations()" class="md:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors mr-2">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            
            <!-- Avatar -->
            <div class="relative">
                @if($otherUserPhoto)
                    <img src="{{ asset('storage/' . $otherUserPhoto) }}" alt="{{ $otherUser->name }}" 
                         class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class="fa-solid fa-user text-purple-600"></i>
                    </div>
                @endif
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            
            <!-- User Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                <p class="text-sm text-gray-500">{{ $otherUserRole }} • Online</p>
            </div>
        </div>
        
        <!-- Chat Actions -->
        <div class="flex items-center space-x-3">
            <button class="p-2 text-gray-600 hover:text-gray-900 transition-colors rounded-lg hover:bg-gray-100" title="Voice Call">
                <i class="fa-solid fa-phone"></i>
            </button>
            <button class="p-2 text-gray-600 hover:text-gray-900 transition-colors rounded-lg hover:bg-gray-100" title="Video Call">
                <i class="fa-solid fa-video"></i>
            </button>
            <button class="p-2 text-gray-600 hover:text-gray-900 transition-colors rounded-lg hover:bg-gray-100" title="More Options">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
        </div>
    </div>
</div>

<!-- Messages Area -->
<div id="messages-container" class="flex-1 overflow-y-auto chat-scroll p-4 space-y-4 bg-gray-50 min-h-0">
    @forelse($messages as $message)
        @php
            $isMyMessage = $message->sender_id == auth()->id();
        @endphp
        
        <div class="flex {{ $isMyMessage ? 'justify-end' : 'justify-start' }}">
            <div class="flex items-start space-x-2 max-w-xs lg:max-w-md">
                @if(!$isMyMessage)
                    <!-- Other user's avatar -->
                    <div class="flex-shrink-0">
                        @if($otherUserPhoto)
                            <img src="{{ asset('storage/' . $otherUserPhoto) }}" alt="{{ $otherUser->name }}" 
                                 class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fa-solid fa-user text-purple-600 text-xs"></i>
                            </div>
                        @endif
                    </div>
                @endif
                
                <div class="flex flex-col {{ $isMyMessage ? 'items-end' : 'items-start' }}">
                    <!-- Message Bubble -->
                    <div class="relative {{ $isMyMessage ? 'bg-purple-600 text-white' : 'bg-white text-gray-900' }} rounded-2xl px-4 py-2 shadow-sm">
                        @if($message->type == 'image')
                            <div class="mb-2">
                                <img src="{{ $message->getFileUrl() }}" alt="Shared image" 
                                     class="max-w-full h-auto rounded-lg cursor-pointer"
                                     onclick="openImageModal('{{ $message->getFileUrl() }}')">
                            </div>
                            @if($message->content)
                                <p class="text-sm">{{ $message->content }}</p>
                            @endif
                        @elseif($message->type == 'file')
                            <div class="flex items-center space-x-2 p-2 {{ $isMyMessage ? 'bg-purple-700' : 'bg-gray-100' }} rounded-lg">
                                <i class="fa-solid fa-file text-lg"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs {{ $isMyMessage ? 'text-purple-200' : 'text-gray-500' }}">
                                        {{ $message->file_size ? number_format($message->file_size / 1024, 1) . ' KB' : 'File' }}
                                    </p>
                                </div>
                                <a href="{{ $message->getFileUrl() }}" download="{{ $message->file_name ?? basename($message->file_path) }}" 
                                   class="text-sm font-medium hover:underline">
                                    Download
                                </a>
                            </div>
                            @if($message->content)
                                <p class="text-sm mt-2">{{ $message->content }}</p>
                            @endif
                        @else
                            <p class="text-sm">{{ $message->content }}</p>
                        @endif
                    </div>
                    
                    <!-- Message Time -->
                    <div class="flex items-center mt-1 space-x-1">
                        <span class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</span>
                        @if($isMyMessage)
                            <span class="text-xs text-gray-500">
                                @if($message->read_at)
                                    <i class="fa-solid fa-check-double text-blue-500"></i>
                                @else
                                    <i class="fa-solid fa-check"></i>
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
                
                @if($isMyMessage)
                    <!-- My avatar -->
                    <div class="flex-shrink-0">
                        @if($currentUserMentor && $currentUserMentor->photo)
                            <img src="{{ asset('storage/' . $currentUserMentor->photo) }}" alt="{{ auth()->user()->name }}" 
                                 class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fa-solid fa-user text-purple-600 text-xs"></i>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="flex items-center justify-center h-full">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-comment-dots text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Start the conversation</h3>
                <p class="text-gray-500 text-sm">Send a message to begin chatting with {{ $otherUser->name }}</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Message Input -->
<div class="p-4 border-t border-gray-200 bg-white flex-shrink-0">
    <form id="message-form" class="flex items-end space-x-3" enctype="multipart/form-data">
        @csrf
        <!-- File Upload -->
        <div class="flex space-x-2">
            <label for="file-input" class="cursor-pointer p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" title="Attach File">
                <i class="fa-solid fa-paperclip"></i>
            </label>
            <input type="file" id="file-input" name="file" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt">
            
            <label for="image-input" class="cursor-pointer p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" title="Send Image">
                <i class="fa-solid fa-image"></i>
            </label>
            <input type="file" id="image-input" name="image" class="hidden" accept="image/*">
        </div>
        
        <!-- Message Input -->
        <div class="flex-1 relative">
            <textarea id="message-input" name="content" rows="1" 
                      placeholder="Type your message..." 
                      class="w-full resize-none border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                      style="min-height: 40px; max-height: 120px;"></textarea>
        </div>
        
        <!-- Send Button -->
        <button type="submit" id="send-button" 
                class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
    
    <!-- File Preview -->
    <div id="file-preview" class="hidden mt-3 p-3 bg-gray-100 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i id="file-icon" class="fa-solid fa-file text-lg text-gray-600"></i>
                <div>
                    <p id="file-name" class="text-sm font-medium text-gray-900"></p>
                    <p id="file-size" class="text-xs text-gray-500"></p>
                </div>
            </div>
            <button type="button" id="remove-file" class="text-red-600 hover:text-red-800">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="image-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="max-w-4xl max-h-full p-4">
        <img id="modal-image" src="" alt="Image" class="max-w-full max-h-full object-contain">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>

<script>
let selectedFile = null;

// Auto-resize textarea
document.getElementById('message-input').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Handle Enter key
document.getElementById('message-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('message-form').dispatchEvent(new Event('submit'));
    }
});

// File input handlers
document.getElementById('file-input').addEventListener('change', handleFileSelect);
document.getElementById('image-input').addEventListener('change', handleFileSelect);

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        selectedFile = file;
        showFilePreview(file);
    }
}

function showFilePreview(file) {
    const preview = document.getElementById('file-preview');
    const icon = document.getElementById('file-icon');
    const name = document.getElementById('file-name');
    const size = document.getElementById('file-size');
    
    // Set icon based on file type
    if (file.type.startsWith('image/')) {
        icon.className = 'fa-solid fa-image text-lg text-green-600';
    } else {
        icon.className = 'fa-solid fa-file text-lg text-gray-600';
    }
    
    name.textContent = file.name;
    size.textContent = (file.size / 1024).toFixed(1) + ' KB';
    preview.classList.remove('hidden');
}

// Remove file
document.getElementById('remove-file').addEventListener('click', function() {
    selectedFile = null;
    document.getElementById('file-input').value = '';
    document.getElementById('image-input').value = '';
    document.getElementById('file-preview').classList.add('hidden');
});

// Form submission
document.getElementById('message-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const content = messageInput.value.trim();
    
    if (!content && !selectedFile) return;
    
    // Disable form
    sendButton.disabled = true;
    messageInput.disabled = true;
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    if (content) {
        formData.append('content', content);
    }
    
    if (selectedFile) {
        formData.append('file', selectedFile);
    }
    
    try {
        const response = await fetch(`/chat/{{ $conversation->unique_code }}/send`, {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            // Clear form
            messageInput.value = '';
            messageInput.style.height = 'auto';
            selectedFile = null;
            document.getElementById('file-input').value = '';
            document.getElementById('image-input').value = '';
            document.getElementById('file-preview').classList.add('hidden');
            
            // Reload messages (in a real app, you'd use WebSockets)
            location.reload();
        } else {
            alert('Failed to send message. Please try again.');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    }
    
    // Re-enable form
    sendButton.disabled = false;
    messageInput.disabled = false;
    messageInput.focus();
});

// Image modal functions
function openImageModal(src) {
    document.getElementById('modal-image').src = src;
    document.getElementById('image-modal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
}

// Scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
});

// Mobile navigation
function goBackToConversations() {
    window.location.href = '/chat';
}

// Mark messages as read (you can implement this with an API call)
// This would typically be done via WebSocket or periodic AJAX calls
</script>
