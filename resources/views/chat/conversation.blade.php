@php
    // Debug: Verify the conversation object being used
    \Log::info('Conversation view - conversation object:', [
        'conversation_id' => $conversation->id ?? 'null',
        'conversation_unique_code' => $conversation->unique_code ?? 'null',
        'mentor_id' => $conversation->mentor_id ?? 'null',
        'user_id' => $conversation->user_id ?? 'null'
    ]);
    
    // Get the other user in the conversation - ONLY from the current conversation object
    $currentUser = auth()->user();
    
    // CRITICAL: Only get mentor and student info from the CURRENT conversation object
    $currentConversationMentor = $conversation->mentor;
    $currentConversationStudent = $conversation->user;
    
    // Verify the mentor and student data is from the correct conversation
    \Log::info('Current conversation mentor/student data:', [
        'conversation_id' => $conversation->id,
        'mentor_id' => $currentConversationMentor->id ?? 'null',
        'mentor_user_id' => $currentConversationMentor->user_id ?? 'null',
        'mentor_user_name' => $currentConversationMentor->user->name ?? 'null',
        'student_id' => $currentConversationStudent->id ?? 'null',
        'student_name' => $currentConversationStudent->name ?? 'null'
    ]);
    
    // Determine the current user's role and who they're talking to
    if ($conversation->user_id == $currentUser->id) {
        // Current user is the student, so other user is the mentor
        $otherUser = $currentConversationMentor->user;
        $otherUserRole = 'Mentor';
        $otherUserPhoto = $currentConversationMentor->photo;
        $currentUserRole = 'Student';
    } else {
        // Current user is the mentor, so other user is the student
        $otherUser = $currentConversationStudent;
        $otherUserRole = 'Student';
        $otherUserPhoto = null;
        $currentUserRole = 'Mentor';
    }
    
    // Final verification of what will be displayed
    \Log::info('Final display data:', [
        'conversation_id' => $conversation->id,
        'other_user_name' => $otherUser->name ?? 'null',
        'other_user_role' => $otherUserRole,
        'current_user_role' => $currentUserRole,
        'mentor_name_from_conversation' => $currentConversationMentor->user->name ?? 'null'
    ]);
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
        
        <!-- Chat End Time Info -->
        @if(isset($chatStatus) && $chatStatus['can_chat'])
            <div class="text-right">
                @if($chatStatus['type'] === 'session')
                    <p class="text-sm text-gray-600">Session ends at {{ \Carbon\Carbon::parse($chatStatus['session_end_time'])->format('H:i') }}</p>
                @elseif($chatStatus['type'] === 'course')
                    <p class="text-sm text-gray-600">Course ends {{ \Carbon\Carbon::parse($chatStatus['course_end_date'])->format('M d, Y') }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Chat Status Display -->
@if(isset($chatStatus) && !$chatStatus['can_chat'])
    <div class="bg-amber-100 border border-amber-300 text-amber-800 px-4 py-3 mx-4 mt-4 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm8.707-7.293a1 1 0 00-1.414-1.414L11 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong class="font-medium">{{ $chatStatus['reason'] }}</strong>
                <p class="text-sm mt-1">{{ $chatStatus['details'] }}</p>
            </div>
        </div>
    </div>
@endif

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
    @if(isset($chatStatus) && $chatStatus['can_chat'])
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
    @else
        <div class="text-center py-6">
            <div class="text-gray-500 mb-2">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">{{ $chatStatus['reason'] ?? 'Chat is not available' }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $chatStatus['details'] ?? 'You cannot send messages at this time' }}</p>
        </div>
    @endif
    
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
    
    // Check if chat is active
    @if(isset($chatStatus) && !$chatStatus['can_chat'])
        alert('{{ $chatStatus['reason'] }}: {{ $chatStatus['details'] }}');
        return;
    @endif
    
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
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (response.status === 302) {
            console.error('Received 302 redirect - likely authentication issue');
            alert('Session expired. Please refresh the page and try again.');
            window.location.reload();
            return;
        }
        
        if (response.ok) {
            const result = await response.json();
            console.log('Success response:', result);
            
            // Clear form
            messageInput.value = '';
            messageInput.style.height = 'auto';
            selectedFile = null;
            document.getElementById('file-input').value = '';
            document.getElementById('image-input').value = '';
            document.getElementById('file-preview').classList.add('hidden');
            
            // Add the message to chat immediately (optimistic update)
            if (result.message) {
                addMessageToChat({
                    ...result.message,
                    sender: {
                        id: {{ auth()->id() }},
                        name: '{{ auth()->user()->name }}'
                    },
                    formatted_time: new Date().toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        hour12: false 
                    }),
                    file_url: result.message.file_path ? `/storage/${result.message.file_path}` : null
                });
                scrollToBottom();
            }
        } else {
            const errorText = await response.text();
            console.error('Error response:', response.status, errorText);
            alert(`Failed to send message (${response.status}). Please try again.`);
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
    
    // Set up real-time messaging with Laravel Echo
    setupRealtimeMessaging();
});

// Real-time messaging setup
function setupRealtimeMessaging() {
    console.log('Setting up real-time messaging...');
    
    if (typeof window.Echo !== 'undefined') {
        console.log('Echo is available, setting up listeners...');
        
        const channelName = 'conversation.{{ $conversation->unique_code }}';
        console.log('Subscribing to channel:', channelName);
        
        // Use ONLY private channel (remove public channel to avoid duplicates)
        const channel = window.Echo.private(channelName);
        
        // Test channel subscription
        channel.subscribed(() => {
            console.log('Successfully subscribed to channel:', channelName);
        });
        
        channel.error((error) => {
            console.error('Channel subscription error:', error);
        });
        
        // Listen for the message event (use ONLY one listener)
        channel.listen('.message.sent', (e) => {
            console.log('New message received via Echo:', e);
            if (e.message && e.message.sender.id !== {{ auth()->id() }}) {
                // Only add message if it's NOT from current user (avoid optimistic duplicate)
                addMessageToChat(e.message);
                scrollToBottom();
            } else {
                console.log('Ignoring own message to avoid duplicate');
            }
        });
            
        // Test if Echo connection is working
        window.Echo.connector.pusher.connection.bind('connected', function() {
            console.log('Pusher connected successfully');
        });
        
        window.Echo.connector.pusher.connection.bind('disconnected', function() {
            console.log('Pusher disconnected');
        });
        
        window.Echo.connector.pusher.connection.bind('error', function(error) {
            console.error('Pusher connection error:', error);
        });
        
        // Log relevant pusher events for debugging
        window.Echo.connector.pusher.bind_global(function(eventName, data) {
            if (eventName !== 'pusher:pong' && eventName !== 'pusher:ping') {
                console.log('Pusher event received:', eventName, data);
            }
        });
        
    } else {
        console.error('Laravel Echo is not available');
        console.log('Available window properties:', Object.keys(window));
    }
}

// Add new message to chat
function addMessageToChat(messageData) {
    const messagesContainer = document.getElementById('messages-container');
    const currentUserId = {{ auth()->id() }};
    const isMyMessage = messageData.sender.id === currentUserId;
    
    // Check if message already exists (prevent duplicates)
    const existingMessage = messagesContainer.querySelector(`[data-message-id="${messageData.id}"]`);
    if (existingMessage) {
        console.log('Message already exists, skipping duplicate:', messageData.id);
        return;
    }
    
    // Get other user info for avatar
    @php
        $currentUser = auth()->user();
        $currentUserMentor = $currentUser->mentor ?? null;
        
        if ($conversation->user_id == $currentUser->id) {
            $otherUser = $conversation->mentor->user;
            $otherUserPhoto = $conversation->mentor->photo;
        } else {
            $otherUser = $conversation->user;
            $otherUserPhoto = null;
        }
    @endphp
    
    const messageHtml = createMessageHtml(messageData, isMyMessage);
    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
}

// Create message HTML
function createMessageHtml(messageData, isMyMessage) {
    const otherUserPhoto = @json($otherUserPhoto ?? null);
    const currentUserMentorPhoto = @json($currentUserMentor->photo ?? null);
    
    let avatarHtml = '';
    let myAvatarHtml = '';
    
    if (!isMyMessage) {
        if (otherUserPhoto) {
            avatarHtml = `<img src="{{ asset('storage/') }}/${otherUserPhoto}" alt="${messageData.sender.name}" class="w-8 h-8 rounded-full object-cover">`;
        } else {
            avatarHtml = `<div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                <i class="fa-solid fa-user text-purple-600 text-xs"></i>
            </div>`;
        }
    }
    
    if (isMyMessage) {
        if (currentUserMentorPhoto) {
            myAvatarHtml = `<img src="{{ asset('storage/') }}/${currentUserMentorPhoto}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">`;
        } else {
            myAvatarHtml = `<div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                <i class="fa-solid fa-user text-purple-600 text-xs"></i>
            </div>`;
        }
    }
    
    let messageContentHtml = '';
    
    if (messageData.type === 'image') {
        messageContentHtml = `
            <div class="mb-2">
                <img src="${messageData.file_url}" alt="Shared image" 
                     class="max-w-full h-auto rounded-lg cursor-pointer"
                     onclick="openImageModal('${messageData.file_url}')">
            </div>
            ${messageData.content ? `<p class="text-sm">${messageData.content}</p>` : ''}
        `;
    } else if (messageData.type === 'file') {
        messageContentHtml = `
            <div class="flex items-center space-x-2 p-2 ${isMyMessage ? 'bg-purple-700' : 'bg-gray-100'} rounded-lg">
                <i class="fa-solid fa-file text-lg"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-xs ${isMyMessage ? 'text-purple-200' : 'text-gray-500'}">
                        ${messageData.file_size ? Math.round(messageData.file_size / 1024) + ' KB' : 'File'}
                    </p>
                </div>
                <a href="${messageData.file_url}" download="${messageData.file_name}" 
                   class="text-sm font-medium hover:underline">
                    Download
                </a>
            </div>
            ${messageData.content ? `<p class="text-sm mt-2">${messageData.content}</p>` : ''}
        `;
    } else {
        messageContentHtml = `<p class="text-sm">${messageData.content}</p>`;
    }
    
    return `
        <div class="flex ${isMyMessage ? 'justify-end' : 'justify-start'}" data-message-id="${messageData.id}">
            <div class="flex items-start space-x-2 max-w-xs lg:max-w-md">
                ${!isMyMessage ? `<div class="flex-shrink-0">${avatarHtml}</div>` : ''}
                
                <div class="flex flex-col ${isMyMessage ? 'items-end' : 'items-start'}">
                    <div class="relative ${isMyMessage ? 'bg-purple-600 text-white' : 'bg-white text-gray-900'} rounded-2xl px-4 py-2 shadow-sm">
                        ${messageContentHtml}
                    </div>
                    
                    <div class="flex items-center mt-1 space-x-1">
                        <span class="text-xs text-gray-500">${messageData.formatted_time}</span>
                        ${isMyMessage ? '<span class="text-xs text-gray-500"><i class="fa-solid fa-check"></i></span>' : ''}
                    </div>
                </div>
                
                ${isMyMessage ? `<div class="flex-shrink-0">${myAvatarHtml}</div>` : ''}
            </div>
        </div>
    `;
}

// Scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// Mobile navigation
function goBackToConversations() {
    window.location.href = '/chat';
}

// Mark messages as read (you can implement this with an API call)
// This would typically be done via WebSocket or periodic AJAX calls
</script>
