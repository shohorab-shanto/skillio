<!-- Earnings Chart Component -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Card - Earnings Line Chart (2/3 width) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xl font-semibold text-gray-900">{{ __('trans.earnings') }}</h3>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm rounded-lg hover:bg-gray-100" data-period="1D">1D</button>
                <button class="px-3 py-1 text-sm rounded-lg hover:bg-gray-100" data-period="7D">7D</button>
                <button class="px-3 py-1 text-sm rounded-lg bg-purple-600 text-white" data-period="1M">1M</button>
                <button class="px-3 py-1 text-sm rounded-lg hover:bg-gray-100" data-period="1YR">1YR</button>
                <button class="px-3 py-1 text-sm rounded-lg hover:bg-gray-100" data-period="ALL">All</button>
            </div>
        </div>
        
        <div class="mb-2">
            <p class="text-xl font-bold text-gray-900" id="earningsAmount">${{ number_format($currentMonthEarnings) }}</p>
            <p class="text-xs text-gray-500" id="earningsDate">{{ Carbon\Carbon::now()->format('M d, Y') }}</p>
        </div>
        
        <!-- Improved Earnings Chart with increased height -->
        <div class="relative h-56 bg-gradient-to-b from-purple-50 to-white rounded-lg p-4">
            <canvas id="earningsChart" width="400" height="224"></canvas>
            
            <!-- Chart Legend -->
            <div class="absolute bottom-2 left-4 flex items-center space-x-2">
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                    <span class="text-xs text-gray-600">{{ __('trans.earnings') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Card - Monthly Earnings Bar Chart (1/3 width) -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-8">{{ __('trans.monthly_earnings') }}</h3>
        
        <!-- Improved Monthly Earnings Chart with increased height -->
        <div class="relative h-56 bg-gradient-to-b from-blue-50 to-white rounded-lg p-4">
            <canvas id="monthlyChart" width="400" height="224"></canvas>
            
            <!-- Chart Legend -->
            <div class="absolute bottom-2 left-4 flex items-center space-x-2">
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-gradient-to-t from-purple-500 to-pink-400 rounded"></div>
                    <span class="text-xs text-gray-600">{{ __('trans.monthly') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let earningsChart, monthlyChart;
    
    // Chart data for different time periods
    const chartData = {
        '1D': {
            labels: @json(collect($dailyEarnings)->take(1)->pluck('date')->toArray()),
            data: @json(collect($dailyEarnings)->take(1)->pluck('earnings')->toArray()),
            total: @json(collect($dailyEarnings)->take(1)->sum('earnings')),
            date: '{{ Carbon\Carbon::now()->format("M d, Y") }}'
        },
        '7D': {
            labels: @json(collect($dailyEarnings)->take(7)->pluck('date')->toArray()),
            data: @json(collect($dailyEarnings)->take(7)->pluck('earnings')->toArray()),
            total: @json(collect($dailyEarnings)->take(7)->sum('earnings')),
            date: '{{ Carbon\Carbon::now()->subDays(6)->format("M d") }} - {{ Carbon\Carbon::now()->format("M d, Y") }}'
        },
        '1M': {
            labels: @json(collect($dailyEarnings)->pluck('date')->toArray()),
            data: @json(collect($dailyEarnings)->pluck('earnings')->toArray()),
            total: @json($currentMonthEarnings),
            date: '{{ Carbon\Carbon::now()->format("M d, Y") }}'
        },
        '1YR': {
            labels: @json(collect($monthlyEarnings)->pluck('month')->toArray()),
            data: @json(collect($monthlyEarnings)->pluck('earnings')->toArray()),
            total: @json(collect($monthlyEarnings)->sum('earnings')),
            date: '{{ Carbon\Carbon::now()->subMonths(5)->format("M Y") }} - {{ Carbon\Carbon::now()->format("M Y") }}'
        },
        'ALL': {
            labels: @json(collect($monthlyEarnings)->pluck('month')->toArray()),
            data: @json(collect($monthlyEarnings)->pluck('earnings')->toArray()),
            total: @json(collect($monthlyEarnings)->sum('earnings')),
            date: '{{ __('trans.all_time') }}'
        }
    };
    
    // Initialize charts
    function initCharts() {
        // Enhanced Earnings Line Chart
        const earningsCtx = document.getElementById('earningsChart').getContext('2d');
        earningsChart = new Chart(earningsCtx, {
            type: 'line',
            data: {
                labels: chartData['1M'].labels,
                datasets: [{
                    label: 'Earnings',
                    data: chartData['1M'].data,
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
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
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
        monthlyChart = new Chart(monthlyCtx, {
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
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            },
                            maxTicksLimit: 5
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Update chart data based on selected time period
    function updateChart(period) {
        const data = chartData[period];
        
        // Update earnings amount and date
        document.getElementById('earningsAmount').textContent = '$' + data.total.toLocaleString();
        document.getElementById('earningsDate').textContent = data.date;
        
        // Update chart data
        earningsChart.data.labels = data.labels;
        earningsChart.data.datasets[0].data = data.data;
        earningsChart.update();
        
        // Update button states
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.classList.remove('bg-purple-600', 'text-white');
            btn.classList.add('hover:bg-gray-100');
        });
        
        event.target.classList.remove('hover:bg-gray-100');
        event.target.classList.add('bg-purple-600', 'text-white');
    }
    
    // Initialize charts
    initCharts();
    
    // Add click event listeners to time period buttons
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.addEventListener('click', function() {
            updateChart(this.dataset.period);
        });
    });
});
</script>