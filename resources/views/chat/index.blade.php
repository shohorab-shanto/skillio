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
    <!-- Mobile Back Button (only visible on mobile when in conversation) -->
    <div id="mobile-back-btn" class="lg:hidden fixed top-4 left-4 z-50 hidden">
        <button onclick="showConversationList()" class="p-2 bg-white rounded-full shadow-lg border border-gray-200 hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-arrow-left text-gray-600"></i>
        </button>
    </div>

    <!-- Left Sidebar - Conversations -->
    <div id="conversation-sidebar" class="w-full lg:w-1/3 border-r border-gray-200 flex flex-col lg:flex">
        <!-- Sidebar Header -->
        <div class="p-3 sm:p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">{{ __('trans.messages') }}</h2>
                <button onclick="openNewConversationModal()" class="p-2 text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="{{ __('trans.start_new_conversation') }}">
                    <i class="fa-solid fa-plus text-sm sm:text-base"></i>
                </button>
            </div>
            <div class="flex space-x-1 sm:space-x-2 mb-3 sm:mb-4">
                <button id="active-tab" class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium text-white bg-purple-600 rounded-lg transition-colors">
                    {{ __('trans.active') }}
                </button>
                <button id="archive-tab" class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    {{ __('trans.archive') }}
                </button>
            </div>
            
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                </div>
                <input type="text" id="search-conversations" placeholder="{{ __('trans.search') }}" 
                       class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            @php
                // Filter out conversations without enrollment_id first
                $validConversations = $list_conversations->filter(function($conversation) {
                    return $conversation->enrollment_id !== null;
                });
                
                // Separate conversations into active and archived
                $activeConversations = $validConversations->filter(function($conversation) {
                    return $conversation->isConversationActive();
                });
                
                $archivedConversations = $validConversations->filter(function($conversation) {
                    return !$conversation->isConversationActive();
                });
                
                // Debug information
                \Log::info('Chat Debug', [
                    'total_conversations' => $list_conversations->count(),
                    'valid_conversations' => $validConversations->count(),
                    'active_count' => $activeConversations->count(),
                    'archived_count' => $archivedConversations->count(),
                    'archived_ids' => $archivedConversations->pluck('id')->toArray(),
                    'route_code' => request()->route('code'),
                    'conversation_ids' => $list_conversations->pluck('id')->toArray()
                ]);
            @endphp
            
            <!-- Active Conversations -->
            <div id="active-conversations" class="conversation-section">
                @if($activeConversations->count() > 0)
                    @foreach($activeConversations as $list_conversation)
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
                    <div class="conversation-item p-3 sm:p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors {{ request()->route('code') == $list_conversation->unique_code ? 'bg-purple-50 border-r-4 border-r-purple-600' : '' }}"
                         onclick="loadConversation('{{ $list_conversation->unique_code }}')">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                @if($otherUser->photo)
                                    <img src="{{ asset('storage/' . $otherUser->photo) }}" alt="{{ $otherUser->name }}" 
                                         class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-purple-600 text-sm sm:text-base"></i>
                                    </div>
                                @endif
                                <!-- Online Status -->
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full border-2 border-white"></div>
                            </div>
                            
                            <!-- Conversation Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm sm:text-sm font-semibold text-gray-900 truncate">{{ $otherUser->name }}</p>
                                    @if($lastMessage)
                                        <span class="text-xs text-gray-500 flex-shrink-0 ml-2">{{ $lastMessage->created_at->format('H:i') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs sm:text-sm text-gray-600 truncate">
                                        @if($lastMessage)
                                            @if($lastMessage->type == 'image')
                                                <i class="fa-solid fa-image mr-1"></i> {{ __('trans.image') }}
                                            @elseif($lastMessage->type == 'file')
                                                <i class="fa-solid fa-file mr-1"></i> {{ __('trans.file') }}
                                            @else
                                                {{ Str::limit($lastMessage->content, 25) }}
                                            @endif
                                        @else
                                            {{ __('trans.no_messages_yet') }}
                                        @endif
                                    </p>
                                    @if($unreadCount > 0)
                                        <span class="bg-purple-600 text-white text-xs rounded-full px-1.5 sm:px-2 py-0.5 sm:py-1 min-w-[18px] sm:min-w-[20px] text-center flex-shrink-0 ml-2">{{ $unreadCount }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ $otherUserRole }}</p>
                                
                                @if($list_conversation->enrollment)
                                    @php
                                        $validityPeriod = $list_conversation->getValidityPeriod();
                                    @endphp
                                    @if($validityPeriod)
                                        <div class="text-xs text-gray-400 mt-1 hidden sm:block">
                                            @if($validityPeriod['type'] == 'session')
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                {{ __('trans.session') }}: {{ $validityPeriod['start']->format('M d, H:i') }} - {{ $validityPeriod['end']->format('M d, H:i') }}
                                            @else
                                                <i class="fa-solid fa-graduation-cap mr-1"></i>
                                                {{ __('trans.course') }}: {{ $list_conversation->enrollment->enrollable->title }}
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="flex flex-col items-center justify-center p-6 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fa-solid fa-comments text-xl text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">{{ __('trans.no_active_conversations') }}</h3>
                    <p class="text-gray-500 text-xs">{{ __('trans.all_conversations_archived') }}</p>
                </div>
            @endif
            </div>
            
            <!-- Archived Conversations -->
            <div id="archived-conversations" class="conversation-section hidden">
                <!-- Debug: Valid conversations = {{ $validConversations->count() }}, Archived count = {{ $archivedConversations->count() }} -->
                @if($archivedConversations->count() > 0)
                    @foreach($archivedConversations as $list_conversation)
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
                        <div class="conversation-item p-3 sm:p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors opacity-60 {{ request()->route('code') == $list_conversation->unique_code ? 'bg-purple-50 border-r-4 border-r-purple-600' : '' }}"
                             onclick="loadConversation('{{ $list_conversation->unique_code }}')">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <!-- Avatar -->
                                <div class="relative flex-shrink-0">
                                    @if($otherUser->photo)
                                        <img src="{{ asset('storage/' . $otherUser->photo) }}" alt="{{ $otherUser->name }}" 
                                             class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-purple-600 text-sm sm:text-base"></i>
                                        </div>
                                    @endif
                                    <!-- Archived Status -->
                                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-gray-400 rounded-full border-2 border-white"></div>
                                </div>
                                
                                <!-- Conversation Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm sm:text-sm font-semibold text-gray-900 truncate">{{ $otherUser->name }}</p>
                                        @if($lastMessage)
                                            <span class="text-xs text-gray-500 flex-shrink-0 ml-2">{{ $lastMessage->created_at->format('H:i') }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs sm:text-sm text-gray-600 truncate">
                                            @if($lastMessage)
                                                @if($lastMessage->type == 'image')
                                                    <i class="fa-solid fa-image mr-1"></i> {{ __('trans.image') }}
                                                @elseif($lastMessage->type == 'file')
                                                    <i class="fa-solid fa-file mr-1"></i> {{ __('trans.file') }}
                                                @else
                                                    {{ Str::limit($lastMessage->content, 25) }}
                                                @endif
                                            @else
                                                {{ __('trans.no_messages_yet') }}
                                            @endif
                                        </p>
                                        @if($unreadCount > 0)
                                            <span class="bg-gray-400 text-white text-xs rounded-full px-1.5 sm:px-2 py-0.5 sm:py-1 min-w-[18px] sm:min-w-[20px] text-center flex-shrink-0 ml-2">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $otherUserRole }}</p>
                                    
                                    @if($list_conversation->enrollment)
                                        @php
                                            $validityPeriod = $list_conversation->getValidityPeriod();
                                        @endphp
                                        @if($validityPeriod)
                                            <div class="text-xs text-gray-400 mt-1 hidden sm:block">
                                                <i class="fa-solid fa-archive mr-1"></i>
                                                @if($validityPeriod['type'] == 'session')
                                                    {{ __('trans.expired_session') }}
                                                @else
                                                    {{ __('trans.expired_course') }} - {{ $list_conversation->enrollment->enrollable->title }}
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center p-6 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fa-solid fa-archive text-xl text-gray-400"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">{{ __('trans.no_archived_conversations') }}</h3>
                        <p class="text-gray-500 text-xs">{{ __('trans.all_conversations_active') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Side - Chat Area -->
    <div id="chat-area" class="flex-1 flex flex-col hidden lg:flex">
        @if(isset($error))
            <!-- Error Message -->
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('trans.access_denied') }}</h3>
                    <p class="text-gray-500 mb-4">{{ $error }}</p>
                    @if($errorType == 'no_permission')
                        <div class="text-sm text-gray-600">
                            <p>{{ __('trans.conversation_belongs_others') }}</p>
                            <ul class="mt-2 space-y-1">
                                <li>• {{ __('trans.student_participant') }}</li>
                                <li>• {{ __('trans.mentor_participant') }}</li>
                            </ul>
                        </div>
                    @endif
                    <button onclick="window.location.href='/chat'" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        {{ __('trans.back_to_conversations') }}
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
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('trans.welcome_to_chat') }}</h3>
                    <p class="text-gray-500">{{ __('trans.select_conversation_start') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- New Conversation Modal -->
<div id="new-conversation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('trans.start_new_conversation_title') }}</h3>
                <button onclick="closeNewConversationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <form id="new-conversation-form">
                @csrf
                <div class="mb-4">
                    <label for="user-search" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ auth()->user()->mentor ? __('trans.search_students') : __('trans.search_mentors') }}
                    </label>
                    <div class="relative">
                        <input type="text" id="user-search" placeholder="{{ __('trans.type_name_email') }}" 
                               class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <div id="user-results" class="mb-4 max-h-32 sm:max-h-48 overflow-y-auto hidden">
                    <!-- Search results will be populated here -->
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeNewConversationModal()" 
                            class="w-full sm:w-auto px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        {{ __('trans.cancel') }}
                    </button>
                    <button type="button" id="start-conversation-btn" disabled
                            class="w-full sm:w-auto px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm">
                        {{ __('trans.start_conversation') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedUserId = null;

function loadConversation(conversationCode) {
    // On mobile, show chat area and hide conversation list
    if (window.innerWidth < 1024) {
        showChatArea();
    }
    window.location.href = `/chat/${conversationCode}`;
}

// Mobile navigation functions
function showConversationList() {
    document.getElementById('conversation-sidebar').classList.remove('hidden');
    document.getElementById('chat-area').classList.add('hidden');
    document.getElementById('mobile-back-btn').classList.add('hidden');
}

function showChatArea() {
    document.getElementById('conversation-sidebar').classList.add('hidden');
    document.getElementById('chat-area').classList.remove('hidden');
    document.getElementById('mobile-back-btn').classList.remove('hidden');
}

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        // Desktop view - show both sidebar and chat area
        document.getElementById('conversation-sidebar').classList.remove('hidden');
        document.getElementById('chat-area').classList.remove('hidden');
        document.getElementById('mobile-back-btn').classList.add('hidden');
    } else {
        // Mobile view - show conversation list by default if no conversation is selected
        if (!window.location.pathname.includes('/chat/') || window.location.pathname === '/chat') {
            showConversationList();
        }
    }
});

// Initialize mobile view on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth < 1024) {
        // Mobile view
        if (window.location.pathname.includes('/chat/') && window.location.pathname !== '/chat') {
            // We're in a conversation, show chat area
            showChatArea();
        } else {
            // We're on the main chat page, show conversation list
            showConversationList();
        }
    }
});

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
        resultsContainer.innerHTML = '<p class="text-gray-500 text-sm p-3">{{ __('trans.no_users_found') }}</p>';
        resultsContainer.classList.remove('hidden');
        return;
    }
    
    const html = users.map(user => `
        <div class="flex items-center p-2 sm:p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 user-result" 
             onclick="selectUser(${user.id}, '${user.name}')">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-purple-100 flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0">
                ${user.photo ? 
                    `<img src="/storage/${user.photo}" alt="${user.name}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover">` :
                    `<i class="fa-solid fa-user text-purple-600 text-sm"></i>`
                }
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-900 text-sm sm:text-base truncate">${user.name}</p>
                <p class="text-xs sm:text-sm text-gray-500 truncate">${user.email}</p>
                <p class="text-xs text-gray-400">${user.role == 'mentor' ? '{{ __('trans.mentor') }}' : '{{ __('trans.student') }}'}</p>
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
    this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>{{ __('trans.starting') }}';
    
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
            alert(error.message || '{{ __('trans.failed_start_conversation') }}');
        }
    } catch (error) {
        console.error('Error starting conversation:', error);
        alert('{{ __('trans.failed_start_conversation') }}');
    }
    
    this.disabled = false;
    this.innerHTML = '{{ __('trans.start_conversation') }}';
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
    activateTab('active');
});

document.getElementById('archive-tab').addEventListener('click', function() {
    activateTab('archive');
});

// Function to activate a specific tab
function activateTab(tabName) {
    if (tabName === 'active') {
        document.getElementById('active-tab').classList.add('text-white', 'bg-purple-600');
        document.getElementById('active-tab').classList.remove('text-gray-600');
        document.getElementById('archive-tab').classList.remove('text-white', 'bg-purple-600');
        document.getElementById('archive-tab').classList.add('text-gray-600');
        
        // Show active conversations, hide archived
        document.getElementById('active-conversations').classList.remove('hidden');
        document.getElementById('archived-conversations').classList.add('hidden');
    } else {
        document.getElementById('archive-tab').classList.add('text-white', 'bg-purple-600');
        document.getElementById('archive-tab').classList.remove('text-gray-600');
        document.getElementById('active-tab').classList.remove('text-white', 'bg-purple-600');
        document.getElementById('active-tab').classList.add('text-gray-600');
        
        // Show archived conversations, hide active
        document.getElementById('archived-conversations').classList.remove('hidden');
        document.getElementById('active-conversations').classList.add('hidden');
    }
}

// Auto-activate correct tab based on current conversation
@if(isset($currentConversationInfo))
    @php
        // Debug log
        \Log::info('Auto-tab activation debug', [
            'conversation_id' => $currentConversationInfo['id'],
            'conversation_code' => $currentConversationInfo['unique_code'],
            'is_active' => $currentConversationInfo['is_active'] ? 'yes' : 'no'
        ]);
    @endphp
    
    @if($currentConversationInfo['is_active'])
        // Current conversation is active, activate active tab
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Auto-activating active tab for conversation: {{ $currentConversationInfo['unique_code'] }}');
            activateTab('active');
        });
    @else
        // Current conversation is archived, activate archive tab
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Auto-activating archive tab for conversation: {{ $currentConversationInfo['unique_code'] }}');
            activateTab('archive');
        });
    @endif
@endif

// Close modal on outside click
document.getElementById('new-conversation-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        closeNewConversationModal();
    }
});
</script>
@endsection
