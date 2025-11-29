@extends('backend.layouts.app')

@section('title', __('trans.create_time_slot'))

@push('styles')
<style>
/* Force remove all dropdown arrows across all browsers */
select.custom-dropdown {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: none !important;
    background: white !important;
}

/* Extra specificity for stubborn browsers */
select.custom-dropdown::-ms-expand {
    display: none;
}

/* Firefox specific */
select.custom-dropdown:-moz-focusring {
    color: transparent;
    text-shadow: 0 0 0 #000;
}

/* Calendar Modal Styles */
#calendar-modal {
    backdrop-filter: blur(4px);
}

.calendar-container {
    max-width: 100%;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.375rem;
}

.calendar-day-header {
    text-align: center;
    font-weight: 600;
    color: #6b7280;
    font-size: 0.75rem;
    padding: 0.375rem;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    font-weight: 500;
    font-size: 0.8125rem;
}

.calendar-day:hover:not(.disabled):not(.other-month) {
    border-color: #a855f7;
    background-color: #faf5ff;
    transform: scale(1.05);
}

.calendar-day.selected {
    background-color: #9333ea;
    border-color: #9333ea;
    color: white;
    font-weight: 600;
}

.calendar-day.today {
    border-color: #9333ea;
    font-weight: 700;
}

.calendar-day.disabled {
    background-color: #f3f4f6;
    color: #d1d5db;
    cursor: not-allowed;
}

.calendar-day.other-month {
    color: #d1d5db;
}
</style>
@endpush
@section('pageTitle', __('trans.create_new_time_slot'))
@section('backUrl', route('mentor.dashboard'))

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mentor.time-slots.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.create_new_time_slot') }}</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Main Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('mentor.time-slots.store') }}" class="space-y-8">
            @csrf
            <!-- Date and Price Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.date') }} *</label>
                    <div class="relative">
                        <input type="text" 
                               id="date-input"
                               readonly
                               onclick="openCalendarModal()"
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent cursor-pointer bg-white"
                               placeholder="{{ __('trans.select_dates') }}"
                               value="">
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <i class="fa-solid fa-calendar-days text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500" id="selected-count-display">0 dates selected</p>
                    
                    <div id="selected-dates-container" class="hidden"></div>
                    
                    @error('dates')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.currency') }} *</label>
                    <select id="currency" name="currency" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                            required onchange="updateCurrencySymbol()">
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.price') }} *</label>
                <div class="relative max-w-xs">
                    <span id="currency-symbol" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" 
                           name="fee"
                           step="0.01"
                           min="0"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="0.00" 
                           value="{{ old('fee') }}" required>
                </div>
                @error('fee')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.category') }} *</label>
                    <div class="relative">
                        <select name="category_id" 
                                id="category_id"
                                class="custom-dropdown w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 cursor-pointer hover:border-gray-400 transition-colors"
                                required onchange="updateSubCategories()">
                            <option value="">{{ __('trans.select_a_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('trans.sub_category') }} *</label>
                    <div class="relative">
                        <select name="sub_category_id" 
                                id="sub_category_id"
                                class="custom-dropdown w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 cursor-pointer hover:border-gray-400 transition-colors"
                                required>
                            <option value="">{{ __('trans.select_a_sub_category') }}</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('sub_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Multiple Time Slots -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">{{ __('trans.time_slots') }} *</label>
                    <button type="button" 
                            onclick="addTimeSlot()"
                            class="text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center space-x-1">
                        <i class="fa-solid fa-plus"></i>
                        <span>{{ __('trans.add_time_slot') }}</span>
                    </button>
                </div>
                
                <div id="time-slots-container" class="space-y-3">
                    <!-- Initial time slot -->
                    <div class="time-slot-row flex items-center gap-3">
                        <div class="flex-1">
                            <input type="time" 
                                   name="time_slots[0][start_time]"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="{{ __('trans.start_time') }}" required>
                        </div>
                        <span class="text-gray-400">-</span>
                        <div class="flex-1">
                            <input type="time" 
                                   name="time_slots[0][end_time]"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="{{ __('trans.end_time') }}" required>
                        </div>
                        <button type="button" 
                                onclick="removeTimeSlot(this)"
                                class="text-red-500 hover:text-red-700 p-2 opacity-0 pointer-events-none"
                                disabled>
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                @error('time_slots')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                <button type="submit" 
                        class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ __('trans.create_time_slot_button') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Calendar Modal -->
<div id="calendar-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" onclick="event.stopPropagation()">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('trans.select_dates') }}</h3>
                <button type="button" onclick="closeCalendarModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ __('trans.click_dates_to_select_multiple') }}</p>
        </div>
        
        <div class="p-4">
            <div class="calendar-container">
                <div class="calendar-header mb-3">
                    <button type="button" onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <h3 class="text-sm font-semibold" id="current-month"></h3>
                    <button type="button" onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
                
                <div class="calendar-grid" id="calendar-grid">
                    <!-- Calendar will be generated by JavaScript -->
                </div>
            </div>
            
            <!-- Selected Dates Summary -->
            <div class="mt-4 p-3 bg-purple-50 rounded-lg" id="modal-selected-summary">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-check text-purple-600 text-sm"></i>
                        <span class="text-sm font-medium text-gray-900" id="modal-selected-count">0 dates selected</span>
                    </div>
                    <button type="button" onclick="clearAllDates()" class="text-xs text-red-600 hover:text-red-700 font-medium">
                        {{ __('trans.clear_all') }}
                    </button>
                </div>
                <div id="modal-selected-dates-list" class="flex flex-wrap gap-1.5">
                    <!-- Selected dates will appear here -->
                </div>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-200 flex justify-end space-x-2">
            <button type="button" onclick="closeCalendarModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                {{ __('trans.cancel') }}
            </button>
            <button type="button" onclick="confirmDateSelection()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors">
                {{ __('trans.confirm_selection') }}
            </button>
        </div>
    </div>
</div>

<script>
// Categories data for JavaScript
const categoriesData = @json($categories);
let currentMonth = new Date();
let selectedDates = new Set();
let timeSlotCounter = 1;

function updateSubCategories() {
    const categorySelect = document.getElementById('category_id');
    const subCategorySelect = document.getElementById('sub_category_id');
    const selectedCategoryId = categorySelect.value;
    
    // Clear existing options
    subCategorySelect.innerHTML = '<option value="">{{ __('trans.select_a_sub_category') }}</option>';
    
    if (selectedCategoryId) {
        const selectedCategory = categoriesData.find(cat => cat.id == selectedCategoryId);
        if (selectedCategory && selectedCategory.sub_categories && selectedCategory.sub_categories.length > 0) {
            selectedCategory.sub_categories.forEach(subCat => {
                const option = document.createElement('option');
                option.value = subCat.id;
                option.textContent = subCat.name;
                subCategorySelect.appendChild(option);
            });
        }
    }
}

// Calendar Functions
function renderCalendar() {
    const grid = document.getElementById('calendar-grid');
    const monthTitle = document.getElementById('current-month');
    
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();
    
    monthTitle.textContent = currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    
    // Clear grid
    grid.innerHTML = '';
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-day-header';
        header.textContent = day;
        grid.appendChild(header);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Add empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'calendar-day other-month';
        grid.appendChild(emptyDay);
    }
    
    // Add days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        // Format date as YYYY-MM-DD in local timezone
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.textContent = day;
        dayCell.dataset.date = dateStr;
        
        // Check if date is in the past
        if (date < today) {
            dayCell.classList.add('disabled');
        } else {
            dayCell.onclick = function() { toggleDate(this); };
        }
        
        // Check if today
        if (date.getTime() === today.getTime()) {
            dayCell.classList.add('today');
        }
        
        // Check if selected
        if (selectedDates.has(dateStr)) {
            dayCell.classList.add('selected');
        }
        
        grid.appendChild(dayCell);
    }
    
    updateSelectedCount();
}

function toggleDate(element) {
    if (element.classList.contains('disabled')) return;
    
    const dateStr = element.dataset.date;
    
    if (selectedDates.has(dateStr)) {
        selectedDates.delete(dateStr);
        element.classList.remove('selected');
    } else {
        selectedDates.add(dateStr);
        element.classList.add('selected');
    }
    
    updateSelectedCount();
    updateHiddenInputs();
}

function updateSelectedCount() {
    const count = selectedDates.size;
    
    // Update modal count
    const modalCount = document.getElementById('modal-selected-count');
    if (modalCount) {
        modalCount.textContent = `${count} date${count !== 1 ? 's' : ''} selected`;
    }
    
    // Update modal selected dates list
    const modalList = document.getElementById('modal-selected-dates-list');
    if (modalList) {
        modalList.innerHTML = '';
        if (count > 0) {
            const sortedDates = Array.from(selectedDates).sort();
            sortedDates.forEach(dateStr => {
                const date = new Date(dateStr);
                const formatted = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center px-2 py-0.5 bg-white border border-purple-200 text-purple-700 rounded-full text-xs';
                badge.innerHTML = `
                    ${formatted}
                    <button type="button" onclick="removeDateFromSelection('${dateStr}')" class="ml-1 text-purple-500 hover:text-purple-700">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                `;
                modalList.appendChild(badge);
            });
        }
    }
    
    // Update main form input field
    const dateInput = document.getElementById('date-input');
    const countDisplay = document.getElementById('selected-count-display');
    
    if (count === 0) {
        dateInput.value = '';
        countDisplay.textContent = '0 dates selected';
    } else {
        const sortedDates = Array.from(selectedDates).sort();
        if (count === 1) {
            dateInput.value = new Date(sortedDates[0]).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        } else if (count <= 3) {
            dateInput.value = sortedDates.map(d => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })).join(', ');
        } else {
            const first = new Date(sortedDates[0]).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const last = new Date(sortedDates[sortedDates.length - 1]).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            dateInput.value = `${first} - ${last} (${count} dates)`;
        }
        countDisplay.textContent = `${count} date${count !== 1 ? 's' : ''} selected`;
    }
}

function updateHiddenInputs() {
    const container = document.getElementById('selected-dates-container');
    container.innerHTML = '';
    
    selectedDates.forEach(date => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'dates[]';
        input.value = date;
        container.appendChild(input);
    });
}

function previousMonth() {
    currentMonth.setMonth(currentMonth.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentMonth.setMonth(currentMonth.getMonth() + 1);
    renderCalendar();
}

// Time Slot Functions
function addTimeSlot() {
    const container = document.getElementById('time-slots-container');
    const newRow = document.createElement('div');
    newRow.className = 'time-slot-row flex items-center gap-3';
    newRow.innerHTML = `
        <div class="flex-1">
            <input type="time" 
                   name="time_slots[${timeSlotCounter}][start_time]"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="{{ __('trans.start_time') }}" required>
        </div>
        <span class="text-gray-400">-</span>
        <div class="flex-1">
            <input type="time" 
                   name="time_slots[${timeSlotCounter}][end_time]"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="{{ __('trans.end_time') }}" required>
        </div>
        <button type="button" 
                onclick="removeTimeSlot(this)"
                class="text-red-500 hover:text-red-700 p-2">
            <i class="fa-solid fa-trash"></i>
        </button>
    `;
    container.appendChild(newRow);
    timeSlotCounter++;
    updateRemoveButtons();
}

function removeTimeSlot(button) {
    const row = button.closest('.time-slot-row');
    row.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.time-slot-row');
    rows.forEach((row, index) => {
        const removeBtn = row.querySelector('button[onclick*="removeTimeSlot"]');
        if (rows.length === 1) {
            removeBtn.classList.add('opacity-0', 'pointer-events-none');
            removeBtn.disabled = true;
        } else {
            removeBtn.classList.remove('opacity-0', 'pointer-events-none');
            removeBtn.disabled = false;
        }
    });
}

// Modal Functions
function openCalendarModal() {
    const modal = document.getElementById('calendar-modal');
    modal.classList.remove('hidden');
    renderCalendar();
}

function closeCalendarModal() {
    const modal = document.getElementById('calendar-modal');
    modal.classList.add('hidden');
}

function confirmDateSelection() {
    if (selectedDates.size === 0) {
        alert('{{ __('trans.please_select_at_least_one_date') }}');
        return;
    }
    updateHiddenInputs();
    closeCalendarModal();
}

function clearAllDates() {
    selectedDates.clear();
    renderCalendar();
    updateSelectedCount();
    updateHiddenInputs();
}

function removeDateFromSelection(dateStr) {
    selectedDates.delete(dateStr);
    renderCalendar();
    updateSelectedCount();
    updateHiddenInputs();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('calendar-modal');
    if (event.target === modal) {
        closeCalendarModal();
    }
});

// Currency Symbol Update
function updateCurrencySymbol() {
    const currency = document.getElementById('currency').value;
    const symbol = document.getElementById('currency-symbol');
    
    if (currency === 'EUR') {
        symbol.textContent = '€';
    } else {
        symbol.textContent = '$';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
    updateSelectedCount();
    updateCurrencySymbol();
});
</script>
@endsection
