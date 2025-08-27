@extends('backend.layouts.app')

@section('title', 'Mentor Dashboard')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
        </div>
    </div>
@endsection

@section('content')
<div>
    <div class="mx-auto space-y-6">
        
        <!-- Top Row - Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Courses Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-1.png') }}" alt="Total Courses" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalCourses }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $totalCourses) ? 'bg-teal-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Total Users Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-2.png') }}" alt="Total Users" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total User</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $totalUsers / 20) ? 'bg-green-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Mentor Income Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-3.png') }}" alt="Mentor Income" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Mentors Income</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($mentorIncome) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $mentorIncome / 1000) ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Active Courses Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/user_dashboard-4.png') }}" alt="Active Courses" class="w-6 h-6 object-contain" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Active Courses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $activeCourses }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 15; $i++)
                        <div class="w-5 h-8 rounded-xl {{ $i <= min(15, $activeCourses) ? 'bg-orange-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Middle Section - Earnings Overview -->
        @include('components.earnings-chart')

        <!-- Bottom Section - Student List -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Student List</h3>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search students or services..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               value="{{ request('search') }}">
                        <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <div class="relative">
                        <button id="filterBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center space-x-2">
                            <span>Filter</span>
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>
                        <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status', 'all') === 'all' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <i class="fa-solid fa-list mr-2"></i>All Students
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status') === 'active' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <i class="fa-solid fa-check-circle mr-2 text-green-600"></i>Active
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('status') === 'inactive' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <i class="fa-solid fa-times-circle mr-2 text-red-600"></i>Inactive
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Student Name</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Service</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Enrollment Date</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Duration Left</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Action</th>
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
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" 
                                                 class="w-10 h-10 rounded-full object-cover" 
                                                 alt="{{ $student->name }}">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <i class="fa-solid fa-user text-purple-600"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $student->name }}</p>
                                            <p class="text-sm text-gray-500">Student-{{ $student->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-col">
                                        <p class="text-gray-900 font-medium">
                                            @if($enrollment->enrollable_type === 'App\Models\Course')
                                                {{ $service->title }}
                                            @else
                                                Session
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if($enrollment->enrollable_type === 'App\Models\Course')
                                                Course
                                            @else
                                                Session
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="text-gray-900">{{ $enrollment->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="py-4 px-4">
                                    @if($durationLeft && $durationLeft !== 'No end date' && $durationLeft !== 'No time set')
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                            {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $durationLeft }}
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                                            {{ $durationLeft ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                        {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @php
                                        // Get or create conversation between mentor and student
                                        $currentMentor = auth()->user()->mentor;
                                        
                                        // Debug: Log mentor information
                                        \Log::info('Dashboard mentor info:', [
                                            'current_user_id' => auth()->id(),
                                            'mentor_id' => $currentMentor->id,
                                            'mentor_user_id' => $currentMentor->user_id,
                                            'student_id' => $student->id
                                        ]);
                                        
                                        $conversation = \App\Models\Conversation::where('mentor_id', $currentMentor->id)
                                            ->where('user_id', $student->id)
                                            ->first();
                                        
                                        if (!$conversation) {
                                            // Create new conversation if doesn't exist
                                            $conversation = \App\Models\Conversation::create([
                                                'mentor_id' => $currentMentor->id, // This should be the mentor record ID
                                                'user_id' => $student->id,
                                                'last_message_at' => now(),
                                            ]);
                                            
                                            \Log::info('Created new conversation:', [
                                                'conversation_id' => $conversation->id,
                                                'mentor_id' => $conversation->mentor_id,
                                                'user_id' => $conversation->user_id
                                            ]);
                                        }
                                    @endphp
                                    
                                    <a href="{{ route('chat.show', $conversation->unique_code) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fa-solid fa-comment mr-2"></i>
                                        Chat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-users text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg">No students found</p>
                                        <p class="text-sm">Students will appear here once they enroll in your courses or book sessions</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="mt-6 bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <!-- Pagination Details - Left Aligned -->
                        <div class="text-sm text-gray-700">
                            <p>
                                Showing
                                <span class="font-medium">{{ $students->firstItem() ?? 0 }}</span>
                                to
                                <span class="font-medium">{{ $students->lastItem() ?? 0 }}</span>
                                of
                                <span class="font-medium">{{ $students->total() }}</span>
                                students
                            </p>
                        </div>

                        <!-- Pagination Buttons - Right Aligned -->
                        <div>
                            @include('components.custom-pagination', ['paginator' => $students->appends(request()->query())])
                        </div>
                    </div>
                </div>
            @endif
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
    
    filterBtn.addEventListener('click', function() {
        filterDropdown.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!filterBtn.contains(event.target) && !filterDropdown.contains(event.target)) {
            filterDropdown.classList.add('hidden');
        }
    });
    
    // Handle search form submission
    const searchInput = document.querySelector('input[placeholder="Search students or services..."]');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchValue = this.value;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('search', searchValue);
            window.location.href = currentUrl.toString();
        }
    });
    
    // Enhanced Earnings Line Chart
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: @json(collect($dailyEarnings)->pluck('date')->toArray()),
            datasets: [{
                label: 'Earnings',
                data: @json(collect($dailyEarnings)->pluck('earnings')->toArray()),
                borderColor: '#9333ea',
                backgroundColor: 'rgba(147, 51, 234, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#9333ea',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#9333ea',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    display: false,
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: false,
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 6
                }
            }
        }
    });
    
    // Enhanced Monthly Earnings Bar Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json(collect($monthlyEarnings)->pluck('month')->toArray()),
            datasets: [{
                label: 'Monthly Earnings',
                data: @json(collect($monthlyEarnings)->pluck('earnings')->toArray()),
                backgroundColor: [
                    '#ec4899', // pink
                    '#9333ea', // purple
                    '#14b8a6', // teal
                    '#f97316', // orange
                    '#ec4899', // pink
                    '#9333ea'  // purple
                ],
                borderRadius: 8,
                borderSkipped: false,
                borderWidth: 0,
                hoverBackgroundColor: [
                    '#db2777', // darker pink
                    '#7c3aed', // darker purple
                    '#0d9488', // darker teal
                    '#ea580c', // darker orange
                    '#db2777', // darker pink
                    '#7c3aed'  // darker purple
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#9333ea',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false,
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: false,
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endsection