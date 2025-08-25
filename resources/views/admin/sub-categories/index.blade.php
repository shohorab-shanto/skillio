@extends('admin.layouts.backend')

@section('title', 'Sub-Categories Management')

@section('header')
    Sub-Categories Management
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-end items-center gap-3">
        <a href="{{ route('admin.sub-categories.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back to Sub-Categories
        </a>
    </div>

    <!-- Sub-Categories Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sub-Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Parent Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usage Stats
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subCategories as $subCategory)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($subCategory->image)
                                        <img src="{{ asset('storage/' . $subCategory->image) }}" 
                                             alt="{{ $subCategory->name }}" 
                                             class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-tag text-lg text-blue-600"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $subCategory->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($subCategory->description, 60) ?: 'No description' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fa-solid fa-tags text-sm text-purple-600"></i>
                                    </div>
                                    <span class="font-medium">{{ $subCategory->category->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-blue-600">{{ $subCategory->courses_count }}</div>
                                        <div class="text-xs text-gray-500">Courses</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-green-600">{{ $subCategory->session_bookings_count }}</div>
                                        <div class="text-xs text-gray-500">Sessions</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subCategory->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.sub-categories.edit', $subCategory->id) }}" 
                                   class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                    <i class="fa-solid fa-edit mr-1"></i>
                                    Edit
                                </a>
                                
                                <button onclick="deleteSubCategory({{ $subCategory->id }}, '{{ $subCategory->name }}')" 
                                        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                    <i class="fa-solid fa-trash mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-tag text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg font-medium">No sub-categories found</p>
                                <p class="text-sm">Create your first sub-category to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($subCategories->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Pagination Details - Left Aligned -->
                <div class="text-sm text-gray-700">
                    <p>
                        Showing
                        <span class="font-medium">{{ $subCategories->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-medium">{{ $subCategories->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-medium">{{ $subCategories->total() }}</span>
                        sub-categories
                    </p>
                </div>

                <!-- Pagination Buttons - Right Aligned -->
                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        {{-- Previous Page Link --}}
                        @if ($subCategories->onFirstPage())
                            <span aria-disabled="true" aria-label="Previous">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            </span>
                        @else
                            <a href="{{ $subCategories->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($subCategories->getUrlRange(1, $subCategories->lastPage()) as $page => $url)
                            @if ($page == $subCategories->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($subCategories->hasMorePages())
                            <a href="{{ $subCategories->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-200" aria-label="Next">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @else
                            <span aria-disabled="true" aria-label="Next">
                                <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                            @endif
                        </span>
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
                <i class="fa-solid fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
                Delete Sub-Category
            </h3>
            <p class="text-gray-600 text-center mb-6">
                Are you sure you want to delete "<span id="sub-category-name" class="font-semibold"></span>"?
                <br><br>
                <span class="text-sm text-red-600">This action cannot be undone.</span>
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
@endsection

@push('scripts')
<script>
let subCategoryToDelete = null;

function deleteSubCategory(subCategoryId, subCategoryName) {
    subCategoryToDelete = subCategoryId;
    document.getElementById('sub-category-name').textContent = subCategoryName;
    
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
    
    subCategoryToDelete = null;
}

function confirmDelete() {
    if (subCategoryToDelete) {
        fetch(`/admin/sub-categories/${subCategoryToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                cancelDelete();
                // Reload page to show updated list
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
                cancelDelete();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the sub-category', 'error');
            cancelDelete();
        });
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[200] px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
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
    if (e.target === this) {
        cancelDelete();
    }
});
</script>
@endpush
