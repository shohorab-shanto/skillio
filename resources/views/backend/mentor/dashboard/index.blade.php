@extends('backend.layouts.app')

@section('title', __('trans.mentor_dashboard'))

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('trans.dashboard') }}</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="min-h-full">
    <div class="px-4 sm:px-0">
        <div class="mx-auto space-y-4 sm:space-y-6">
        
        <!-- Top Row - Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Courses Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('assets/images/user_dashboard-1.png') }}" alt="{{ __('trans.total_courses') }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-600 break-words leading-tight">{{ __('trans.total_courses') }}</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ $totalCourses }}</p>
                        </div>
                        </div>
                    </div>
                    <div class="flex space-x-1 justify-between w-full overflow-hidden">
                    @for($i = 1; $i <= 15; $i++)
                            <div class="w-1.5 h-6 sm:w-3 sm:h-8 rounded-xl flex-1 min-w-0 {{ $i <= min(15, $totalCourses) ? 'bg-teal-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Total Users Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('assets/images/user_dashboard-2.png') }}" alt="{{ __('trans.total_user') }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ __('trans.total_user') }}</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ $totalUsers }}</p>
                        </div>
                        </div>
                    </div>
                    <div class="flex space-x-1 justify-between w-full overflow-hidden">
                    @for($i = 1; $i <= 15; $i++)
                            <div class="w-1.5 h-6 sm:w-3 sm:h-8 rounded-xl flex-1 min-w-0 {{ $i <= min(15, $totalUsers / 20) ? 'bg-green-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Mentor Income Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('assets/images/user_dashboard-3.png') }}" alt="{{ __('trans.mentors_income') }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ __('trans.mentors_income') }}</p>
                                <div class="space-y-0.5">
                                    <p class="text-base sm:text-xl font-bold text-gray-900 truncate">${{ number_format($mentorIncomeUSD) }}</p>
                                    <p class="text-base sm:text-xl font-bold text-gray-900 truncate">€{{ number_format($mentorIncomeEUR) }}</p>
                                </div>
                        </div>
                        </div>
                    </div>
                    <div class="flex space-x-1 justify-between w-full overflow-hidden">
                    @php
                        $totalIncome = $mentorIncomeUSD + $mentorIncomeEUR;
                    @endphp
                    @for($i = 1; $i <= 15; $i++)
                            <div class="w-1.5 h-6 sm:w-3 sm:h-8 rounded-xl flex-1 min-w-0 {{ $i <= min(15, $totalIncome / 1000) ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Active Courses Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('assets/images/user_dashboard-4.png') }}" alt="{{ __('trans.active_courses') }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ __('trans.active_courses') }}</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ $activeCourses }}</p>
                        </div>
                        </div>
                    </div>
                    <div class="flex space-x-1 justify-between w-full overflow-hidden">
                    @for($i = 1; $i <= 15; $i++)
                            <div class="w-1.5 h-6 sm:w-3 sm:h-8 rounded-xl flex-1 min-w-0 {{ $i <= min(15, $activeCourses) ? 'bg-orange-600' : 'bg-gray-200' }}"></div>
                    @endfor
                    </div>
            </div>
        </div>

        <!-- Middle Section - Earnings Overview -->
            <div class="w-full overflow-x-hidden">
        @include('components.earnings-chart')
            </div>

        <!-- Bottom Section - Student List -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <h3 class="text-xl font-semibold text-gray-900">{{ __('trans.student_list') }}</h3>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <div class="relative w-full sm:w-auto">
                        <input type="text" 
                               placeholder="{{ __('trans.search_students_services') }}" 
                                   class="w-full sm:w-auto pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               value="{{ request('search') }}">
                        <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                        <div class="relative w-full sm:w-auto">
                            <button id="filterBtn" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center space-x-2">
                            <span>{{ __('trans.filter') }}</span>
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>
                        <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status', 'all') == 'all' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <i class="fa-solid fa-list mr-2"></i>{{ __('trans.all_students') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status') == 'active' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <i class="fa-solid fa-check-circle mr-2 text-green-600"></i>{{ __('trans.active') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status') == 'inactive' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <i class="fa-solid fa-times-circle mr-2 text-red-600"></i>{{ __('trans.inactive') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="w-full max-w-full overflow-x-hidden">
                <!-- Desktop Table View -->
                <div class="hidden lg:block border border-gray-200 rounded-lg w-full">
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 w-1/4">{{ __('trans.student') }}</th>
                                <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 w-1/4">{{ __('trans.service') }}</th>
                                <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 w-1/6">{{ __('trans.date') }}</th>
                                <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 w-1/6">{{ __('trans.status') }}</th>
                                <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 w-1/6">{{ __('trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $enrollment)
                                @php
                                    $student = $enrollment->user;
                                    $service = $enrollment->enrollable;
                                    $durationLeft = $enrollment->duration_left;
                                    $isActive = $enrollment->is_active ?? true;
                                @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-2 w-1/4">
                                        <div class="flex items-center space-x-2">
                                            @if($student->photo)
                                                <img src="{{ asset('storage/' . $student->photo) }}" 
                                                     class="w-6 h-6 rounded-full object-cover flex-shrink-0" 
                                                     alt="{{ $student->name }}">
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-user text-purple-600 text-xs"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $student->name }}</p>
                                                <p class="text-sm text-gray-500 truncate">#{{ $student->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-2 w-1/4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                @if($enrollment->enrollable_type == 'App\Models\Course')
                                                    {{ Str::limit($service->title, 15) }}
                                                @else
                                                    {{ __('trans.session') }}
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                @if($enrollment->enrollable_type == 'App\Models\Course')
                                                    {{ __('trans.course') }}
                                                @else
                                                    {{ __('trans.session') }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-2 px-2 w-1/6">
                                        <p class="text-sm text-gray-900">{{ $enrollment->created_at->format('M d') }}</p>
                                    </td>
                                    <td class="py-2 px-2 w-1/6">
                                        <span class="inline-block px-2 py-1 rounded-full text-sm font-medium
                                            {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $isActive ? __('trans.active') : __('trans.inactive') }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-2 w-1/6">
                                        @php
                                            $currentMentor = auth()->user()->mentor;
                                            $conversation = \App\Models\Conversation::where('mentor_id', $currentMentor->id)
                                                ->where('user_id', $student->id)
                                                ->first();
                                            
                                            if (!$conversation) {
                                                $conversation = \App\Models\Conversation::create([
                                                    'mentor_id' => $currentMentor->id,
                                                    'user_id' => $student->id,
                                                    'last_message_at' => now(),
                                                ]);
                                            }
                                        @endphp
                                        
                                        <a href="{{ route('chat.show', $conversation->unique_code) }}" 
                                           class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                                            <i class="fa-solid fa-comment mr-1"></i>
                                            {{ __('trans.chat') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fa-solid fa-users text-3xl text-gray-300 mb-2"></i>
                                            <p class="text-sm">{{ __('trans.no_students_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Grid View -->
                <div class="lg:hidden space-y-3">
                    @forelse($students as $enrollment)
                        @php
                            $student = $enrollment->user;
                            $service = $enrollment->enrollable;
                            $durationLeft = $enrollment->duration_left;
                            $isActive = $enrollment->is_active ?? true;
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    @if($student->photo)
                                        <img src="{{ asset('storage/' . $student->photo) }}" 
                                             class="w-10 h-10 rounded-full object-cover flex-shrink-0" 
                                             alt="{{ $student->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user text-purple-600"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="text-base font-medium text-gray-900 truncate">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-500">#{{ $student->id }}</p>
                                    </div>
                                </div>
                                <span class="inline-block px-2 py-1 rounded-full text-sm font-medium
                                    {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $isActive ? __('trans.active') : __('trans.inactive') }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">{{ __('trans.service') }}:</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        @if($enrollment->enrollable_type == 'App\Models\Course')
                                            {{ Str::limit($service->title, 20) }}
                                        @else
                                            {{ __('trans.session') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">{{ __('trans.type') }}:</span>
                                    <span class="text-sm text-gray-900">
                                        @if($enrollment->enrollable_type == 'App\Models\Course')
                                            {{ __('trans.course') }}
                                        @else
                                            {{ __('trans.session') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">{{ __('trans.date') }}:</span>
                                    <span class="text-sm text-gray-900">{{ $enrollment->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            <div class="pt-2 border-t border-gray-100">
                                @php
                                    $currentMentor = auth()->user()->mentor;
                                    $conversation = \App\Models\Conversation::where('mentor_id', $currentMentor->id)
                                        ->where('user_id', $student->id)
                                        ->first();
                                    
                                    if (!$conversation) {
                                        $conversation = \App\Models\Conversation::create([
                                            'mentor_id' => $currentMentor->id,
                                            'user_id' => $student->id,
                                            'last_message_at' => now(),
                                        ]);
                                    }
                                @endphp
                                
                                <a href="{{ route('chat.show', $conversation->unique_code) }}" 
                                   class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fa-solid fa-comment mr-2"></i>
                                    {{ __('trans.chat') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-users text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-500">{{ __('trans.no_students_found') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

                <!-- Pagination -->
                @if($students->hasPages())
                    <div class="mt-4 sm:mt-6">
                        <x-custom-pagination :paginator="$students" />
                    </div>
                @endif
                </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    
    if (filterBtn && filterDropdown) {
    filterBtn.addEventListener('click', function() {
        filterDropdown.classList.toggle('hidden');
    });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (filterBtn && filterDropdown && !filterBtn.contains(event.target) && !filterDropdown.contains(event.target)) {
            filterDropdown.classList.add('hidden');
        }
    });
    
    // Handle search form submission
    const searchInput = document.querySelector('input[placeholder="{{ __('trans.search_students_services') }}"]');
    if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if (e.key == 'Enter') {
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = window.location.pathname;
                
                const searchParam = document.createElement('input');
                searchParam.type = 'hidden';
                searchParam.name = 'search';
                searchParam.value = this.value;
                form.appendChild(searchParam);
                
                // Preserve other query parameters
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.forEach((value, key) => {
                    if (key != 'search') {
                        const param = document.createElement('input');
                        param.type = 'hidden';
                        param.name = key;
                        param.value = value;
                        form.appendChild(param);
                    }
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});
</script>
@endsection