@extends('admin.layouts.backend')

@section('title', 'Users Management')

@section('header')
    Users Management
@endsection

@section('content')
<div class="mx-auto space-y-6">

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or email..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>



                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i class="fa-solid fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Joined Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-purple-700">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <!-- Delete Button - Commented out for now -->
                                {{-- <button onclick="deleteUser({{ $user->id }})" 
                                        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                    Delete
                                </button> --}}
                                
                                <!-- View Enrollment History Button -->
                                <a href="{{ route('admin.users.enrollments', $user->id) }}" 
                                   class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                    <i class="fa-solid fa-history mr-1"></i>
                                    Enrollments
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-users text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg font-medium">No users found</p>
                                <p class="text-sm">Try adjusting your filters or search terms</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Pagination Details - Left Aligned -->
                <div class="text-sm text-gray-700">
                    <p>
                        Showing
                        <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-medium">{{ $users->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-medium">{{ $users->total() }}</span>
                        users
                    </p>
                </div>

                <!-- Pagination Buttons - Right Aligned -->
                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <span aria-disabled="true" aria-label="Previous">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            </span>
                        @else
                            <a href="{{ $users->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $users->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <a href="{{ $users->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Next">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @else
                            <span aria-disabled="true" aria-label="Next">
                                <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="delete-modal-content">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fa-solid fa-trash text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
                Delete User
            </h3>
            <p class="text-gray-600 text-center mb-6">
                Are you sure you want to delete this user? This action cannot be undone.
            </p>
            <div class="flex space-x-3">
                <button onclick="cancelDelete()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </button>
                <button onclick="confirmDelete()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors duration-200">
                    <i class="fa-solid fa-trash mr-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
let userToDelete = null;



function deleteUser(userId) {
    userToDelete = userId;
    showDeleteModal();
}

function showDeleteModal() {
    const modal = document.getElementById('delete-modal');
    const modalContent = document.getElementById('delete-modal-content');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function cancelDelete() {
    const modal = document.getElementById('delete-modal');
    const modalContent = document.getElementById('delete-modal-content');
    
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
    
    userToDelete = null;
}

function confirmDelete() {
    if (userToDelete) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/users/${userToDelete}`;
        form.submit();
    }
}

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
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        cancelDelete();
    }
});
</script>
@endpush
