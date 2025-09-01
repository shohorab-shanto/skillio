@extends('backend.layouts.app')

@section('title', __('trans.chat'))

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.chat') }}</h1>
        </div>
    </div>
@endsection

@section('content')
<!-- Flash Messages -->
@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <div class="flex items-center">
            <i class="fa-solid fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <div class="flex items-center">
            <i class="fa-solid fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

<div class="flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="height: calc(100vh - 7rem); min-height: 500px;">
    <!-- Left Sidebar - Conversations -->
    <div class="w-1/3 border-r border-gray-200 flex flex-col">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('trans.messages') }}</h2>
                <button onclick="openNewConversationModal()" class="p-2 text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="{{ __('trans.start_new_conversation') }}">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
            <div class="flex space-x-2 mb-4">
                <button id="active-tab" class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded-lg transition-colors">
                    {{ __('trans.active') }}
                </button>
                <button id="archive-tab" class="px-3 py-1 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    {{ __('trans.archive') }}
                </button>
            </div>
            
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="search-conversations" placeholder="{{ __('trans.search') }}" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            @if($list_conversations->count() > 0)
                @foreach($list_conversations as $list_conversation)
                    @php
                        // Get the other user in the conversation
                        $currentUser = auth()->user();
                        $currentUserMentor = $currentUser->mentor ?? null; // Get mentor record if user is a mentor
                        
                        if ($list_conversation->user_id == $currentUser->id) {
                            // Current user is the student, so other user is the mentor
                            $otherUser = $list_conversation->mentor->user;
                            $otherUserRole = __('trans.mentor');
                        } else {
                            // Current user is the mentor, so other user is the student
                            $otherUser = $list_conversation->user;
                            $otherUserRole = __('trans.student');
                        }
                        
                        $unreadCount = $list_conversation->unreadMessagesCount(auth()->id());
                        $lastMessage = $list_conversation->latestMessage;
                    @endphp
                    <div class="conversation-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors {{ request()->route('code') == $list_conversation->unique_code ? 'bg-purple-50 border-r-4 border-r-purple-600' : '' }}"
                         onclick="loadConversation('{{ $list_conversation->unique_code }}')">
                        <div class="flex items-center space-x-3">
                            <!-- Avatar -->
                            <div class="relative">
                                @if($otherUser->photo)
                                    <img src="{{ asset('storage/' . $otherUser->photo) }}" alt="{{ $otherUser->name }}" 
                                         class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-purple-600"></i>
                                    </div>
                                @endif
                                <!-- Online Status -->
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                            </div>
                            
                            <!-- Conversation Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $otherUser->name }}</p>
                                    @if($lastMessage)
                                        <span class="text-xs text-gray-500">{{ $lastMessage->created_at->format('H:i') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate">
                                        @if($lastMessage)
                                            @if($lastMessage->type == 'image')
                                                <i class="fa-solid fa-image mr-1"></i> Image
                                            @elseif($lastMessage->type == 'file')
                                                <i class="fa-solid fa-file mr-1"></i> File
                                            @else
                                                {{ Str::limit($lastMessage->content, 30) }}
                                            @endif
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                    @if($unreadCount > 0)
                                        <span class="bg-purple-600 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">{{ $unreadCount }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ $otherUserRole }}</p>
                                
                                @if($list_conversation->enrollment)
                                    @php
                                        $validityPeriod = $list_conversation->getValidityPeriod();
                                    @endphp
                                    @if($validityPeriod)
                                        <div class="text-xs text-gray-400 mt-1">
                                            @if($validityPeriod['type'] == 'session')
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                Session: {{ $validityPeriod['start']->format('M d, H:i') }} - {{ $validityPeriod['end']->format('M d, H:i') }}
                                            @else
                                                <i class="fa-solid fa-graduation-cap mr-1"></i>
                                                Course: {{ $validityPeriod['start']->format('M d') }} - {{ $validityPeriod['end']->format('M d') }}
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="flex flex-col items-center justify-center h-full p-6 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-comments text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                    <p class="text-gray-500 text-sm">Start a conversation with a mentor or student to begin chatting.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Side - Chat Area -->
    <div class="flex-1 flex flex-col">
        @if(isset($error))
            <!-- Error Message -->
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Access Denied</h3>
                    <p class="text-gray-500 mb-4">{{ $error }}</p>
                    @if($errorType == 'no_permission')
                        <div class="text-sm text-gray-600">
                            <p>This conversation belongs to other users. You can only access conversations where you are either:</p>
                            <ul class="mt-2 space-y-1">
                                <li>• The student participant</li>
                                <li>• The mentor participant</li>
                            </ul>
                        </div>
                    @endif
                    <button onclick="window.location.href='/chat'" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Back to Conversations
                    </button>
                </div>
            </div>
        @elseif(request()->route('code'))
            @include('chat.conversation')
        @else
            <!-- Welcome Screen -->
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-comments text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Chat</h3>
                    <p class="text-gray-500">Select a conversation to start messaging</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- New Conversation Modal -->
<div id="new-conversation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Start New Conversation</h3>
                <button onclick="closeNewConversationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <form id="new-conversation-form">
                @csrf
                <div class="mb-4">
                    <label for="user-search" class="block text-sm font-medium text-gray-700 mb-2">
                        Search {{ auth()->user()->mentor ? 'Students' : 'Mentors' }}
                    </label>
                    <div class="relative">
                        <input type="text" id="user-search" placeholder="Type name or email..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div id="user-results" class="mb-4 max-h-48 overflow-y-auto hidden">
                    <!-- Search results will be populated here -->
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeNewConversationModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" id="start-conversation-btn" disabled
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Start Conversation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedUserId = null;

function loadConversation(conversationCode) {
    window.location.href = `/chat/${conversationCode}`;
}

// New Conversation Modal Functions
function openNewConversationModal() {
    document.getElementById('new-conversation-modal').classList.remove('hidden');
    document.getElementById('user-search').focus();
}

function closeNewConversationModal() {
    document.getElementById('new-conversation-modal').classList.add('hidden');
    document.getElementById('user-search').value = '';
    document.getElementById('user-results').classList.add('hidden');
    document.getElementById('user-results').innerHTML = '';
    selectedUserId = null;
    document.getElementById('start-conversation-btn').disabled = true;
}

// User Search Functionality
let searchTimeout;
document.getElementById('user-search').addEventListener('input', function() {
    const searchTerm = this.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (searchTerm.length < 2) {
        document.getElementById('user-results').classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchUsers(searchTerm);
    }, 300);
});

async function searchUsers(searchTerm) {
    try {
        const response = await fetch(`/api/users/search?q=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        if (response.ok) {
            const users = await response.json();
            displaySearchResults(users);
        }
    } catch (error) {
        console.error('Error searching users:', error);
    }
}

function displaySearchResults(users) {
    const resultsContainer = document.getElementById('user-results');
    
    if (users.length == 0) {
        resultsContainer.innerHTML = '<p class="text-gray-500 text-sm p-3">No users found</p>';
        resultsContainer.classList.remove('hidden');
        return;
    }
    
    const html = users.map(user => `
        <div class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 user-result" 
             onclick="selectUser(${user.id}, '${user.name}')">
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                ${user.photo ? 
                    `<img src="/storage/${user.photo}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover">` :
                    `<i class="fa-solid fa-user text-purple-600"></i>`
                }
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-900">${user.name}</p>
                <p class="text-sm text-gray-500">${user.email}</p>
                <p class="text-xs text-gray-400">${user.role == 'mentor' ? 'Mentor' : 'Student'}</p>
            </div>
        </div>
    `).join('');
    
    resultsContainer.innerHTML = html;
    resultsContainer.classList.remove('hidden');
}

function selectUser(userId, userName) {
    selectedUserId = userId;
    document.getElementById('user-search').value = userName;
    document.getElementById('user-results').classList.add('hidden');
    document.getElementById('start-conversation-btn').disabled = false;
}

// Start Conversation
document.getElementById('start-conversation-btn').addEventListener('click', async function() {
    if (!selectedUserId) return;
    
    this.disabled = true;
    this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Starting...';
    
    try {
        const response = await fetch('/chat/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                other_user_id: selectedUserId
            })
        });
        
        if (response.ok) {
            const result = await response.json();
            window.location.href = `/chat/${result.conversation_code}`;
        } else {
            const error = await response.json();
            alert(error.message || 'Failed to start conversation');
        }
    } catch (error) {
        console.error('Error starting conversation:', error);
        alert('Failed to start conversation. Please try again.');
    }
    
    this.disabled = false;
    this.innerHTML = 'Start Conversation';
});

// Search functionality
document.getElementById('search-conversations').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const conversationItems = document.querySelectorAll('.conversation-item');
    
    conversationItems.forEach(item => {
        const userName = item.querySelector('.font-semibold').textContent.toLowerCase();
        const lastMessage = item.querySelector('.text-gray-600').textContent.toLowerCase();
        
        if (userName.includes(searchTerm) || lastMessage.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Tab switching
document.getElementById('active-tab').addEventListener('click', function() {
    this.classList.add('text-white', 'bg-purple-600');
    this.classList.remove('text-gray-600');
    document.getElementById('archive-tab').classList.remove('text-white', 'bg-purple-600');
    document.getElementById('archive-tab').classList.add('text-gray-600');
    // TODO: Filter conversations by status
});

document.getElementById('archive-tab').addEventListener('click', function() {
    this.classList.add('text-white', 'bg-purple-600');
    this.classList.remove('text-gray-600');
    document.getElementById('active-tab').classList.remove('text-white', 'bg-purple-600');
    document.getElementById('active-tab').classList.add('text-gray-600');
    // TODO: Filter conversations by status
});

// Close modal on outside click
document.getElementById('new-conversation-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        closeNewConversationModal();
    }
});
</script>
@endsection
