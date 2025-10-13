@extends('admin.layouts.backend')

@section('title', __('trans.sliders_management'))

@section('header')
    {{ __('trans.sliders_management') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600">{{ __('trans.manage_slider_images') }}</p>
        </div>
        <a href="{{ route('admin.sliders.create') }}" 
           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            {{ __('trans.add_new_slider') }}
        </a>
    </div>

    <!-- Sliders List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($sliders->count() > 0)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            {{ __('trans.drag_drop_reorder_sliders') }}
                        </p>
                    </div>
                </div>
            </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="80">
                                    {{ __('trans.order') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('trans.slider_image') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" width="120">
                                    {{ __('trans.status') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" width="150">
                                    {{ __('trans.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="sortableSliders" class="bg-white divide-y divide-gray-200">
                            @foreach($sliders as $slider)
                                <tr data-id="{{ $slider->id }}" data-order="{{ $slider->order }}" class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-grip-vertical handle text-gray-400 cursor-move hover:text-gray-600"></i>
                                            <span class="order-number text-sm font-medium text-gray-900">{{ $slider->order }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($slider->image)
                                            <img src="{{ asset('storage/' . $slider->image) }}" 
                                                 alt="Slider {{ $slider->id }}" 
                                                 class="h-20 w-auto object-cover rounded-lg border-2 border-gray-200">
                                        @else
                                            <span class="text-gray-400 text-sm">No image</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('admin.sliders.toggle-status', $slider) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $slider->status == 'active' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} transition-colors duration-200">
                                                {{ ucfirst($slider->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.sliders.edit', $slider) }}" 
                                               class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.sliders.destroy', $slider) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        @else
            <div class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-images text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.no_sliders_found') }}</h3>
                <p class="text-gray-500 mb-4">{{ __('trans.get_started_first_slider') }}</p>
                <a href="{{ route('admin.sliders.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('trans.create_first_slider') }}
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableTable = document.getElementById('sortableSliders');
    
    if (sortableTable) {
        const sortable = new Sortable(sortableTable, {
            handle: '.handle',
            animation: 150,
            onEnd: function(evt) {
                updateSliderOrder();
            }
        });
    }

    function updateSliderOrder() {
        const rows = document.querySelectorAll('#sortableSliders tr');
        const sliders = [];

        rows.forEach((row, index) => {
            sliders.push({
                id: row.getAttribute('data-id'),
                order: index
            });
            row.querySelector('.order-number').textContent = index;
        });

        // Send AJAX request to update order
        fetch('{{ route("admin.sliders.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sliders: sliders })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showMessage('Order updated successfully', 'success');
            } else {
                showMessage('Failed to update order', 'danger');
            }
        })
        .catch(error => {
            showMessage('An error occurred', 'danger');
            console.error('Error:', error);
        });
    }

    function showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild.nextSibling.nextSibling);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endpush
@endsection

