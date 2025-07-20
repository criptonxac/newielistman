@extends('layouts.main')

@section('title', 'Admin Dashboard - IELTS Platform')

@section('content')
<div class="flex bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-lg"></i>
                </div>
                <span class="ml-3 text-xl font-bold text-gray-800">DashboardKit</span>
            </div>
        </div>
        
        <nav class="mt-6">
            <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dashboard</div>
            <a href="#" class="flex items-center px-6 py-3 text-indigo-600 bg-indigo-50 border-r-2 border-indigo-600">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            
            <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 mt-6">Elements</div>
            <a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-users mr-3"></i>
                Foydalanuvchilar
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-palette mr-3"></i>
                Color
            </a>
            <a href="{{ route('admin.tests') }}" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-file-alt mr-3"></i>
                Testlar
            </a>
            
            <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 mt-6">Components</div>
            <a href="#" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-th mr-3"></i>
                Typography
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-cog mr-3"></i>
                Settings
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex justify-between items-center px-8 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Level</h1>
                <div class="flex items-center space-x-4">
                    <i class="fas fa-search text-gray-400"></i>
                    <div class="flex items-center">
                        <img src="https://ui-avatars.com/api/?name=Joseph+William&background=6366f1&color=fff" 
                             class="w-8 h-8 rounded-full mr-2" alt="Avatar">
                        <span class="text-sm text-gray-600">Joseph William</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-8">
            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Customers -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">CUSTOMERS</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] ?? 1000 }}</div>
                </div>

                <!-- Revenue -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-dollar-sign text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">REVENUE</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">1252</div>
                </div>

                <!-- Growth -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-cart text-yellow-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">GROWTH</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_tests'] ?? 500 }}</div>
                </div>

                <!-- Sales Report -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="text-sm text-gray-500 mb-2">Department wise monthly sales report</div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xl font-bold">$21,356.46</span>
                        <span class="text-gray-500">$1935.6</span>
                    </div>
                    <div class="text-xs text-gray-400">Total Sales / Average</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Left Chart -->
                <div class="lg:col-span-1 bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="mb-6">
                        <div class="text-3xl font-bold text-gray-800">53.94%</div>
                        <div class="text-sm text-gray-500 mt-1">Number of conversions divided by the total visitors</div>
                    </div>
                    <div class="h-32 bg-gradient-to-r from-purple-400 to-indigo-600 rounded-lg mb-6 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-white text-center">
                                <div class="text-lg font-bold">16</div>
                                <div class="text-xs">Apr</div>
                            </div>
                            <div class="text-white text-center mx-8">
                                <div class="text-lg font-bold">18</div>
                                <div class="text-xs">May</div>
                            </div>
                            <div class="text-white text-center">
                                <div class="text-lg font-bold">19</div>
                                <div class="text-xs">Jun</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-2xl font-bold text-gray-800">1432</div>
                        <div class="text-sm text-gray-500">Number of conversions divided by the total visitors</div>
                    </div>
                    <div class="flex justify-between text-center">
                        <div>
                            <div class="font-bold">130</div>
                            <div class="text-xs text-gray-500">May</div>
                        </div>
                        <div>
                            <div class="font-bold">251</div>
                            <div class="text-xs text-gray-500">June</div>
                        </div>
                        <div>
                            <div class="font-bold">235</div>
                            <div class="text-xs text-gray-500">July</div>
                        </div>
                    </div>
                </div>

                <!-- Right Chart Area -->
                <div class="lg:col-span-2 bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <canvas id="mainChart" class="w-full h-64"></canvas>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Customer Satisfaction -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Satisfaction</h3>
                    <div class="text-sm text-gray-500 mb-6">An effort to maintain high customer satisfaction with internal and external.</div>
                    <div class="w-40 h-40 mx-auto mb-4">
                        <canvas id="satisfactionChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Cards -->
                <div class="space-y-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 flex justify-between items-center">
                        <div>
                            <div class="text-sm text-gray-500">Sales/Result</div>
                            <div class="text-2xl font-bold text-gray-800">$1,783</div>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-600 text-white rounded-lg p-4">
                        <div class="text-sm opacity-80">Total Orders</div>
                        <div class="text-2xl font-bold">15,830</div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-gray-500">Avg.Price</div>
                                <div class="text-xl font-bold text-gray-800">$6,780</div>
                            </div>
                            <div class="w-8 h-8 bg-purple-100 rounded flex items-center justify-center">
                                <i class="fas fa-arrow-up text-purple-600 text-xs"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-gray-500">Product Sold</div>
                                <div class="text-xl font-bold text-gray-800">6,784</div>
                            </div>
                            <div class="w-8 h-8 bg-yellow-100 rounded flex items-center justify-center">
                                <i class="fas fa-tag text-yellow-600 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Feeds</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm text-gray-800">You have 3 pending tasks.</div>
                                <div class="text-xs text-gray-500">Just Now</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm text-gray-800">New order received</div>
                                <div class="text-xs text-gray-500">30 min ago</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm text-gray-800">You have 3 pending tasks.</div>
                                <div class="text-xs text-gray-500">Just Now</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm text-gray-800">You have 4 tasks Done.</div>
                                <div class="text-xs text-gray-500">30 min ago</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <div class="text-sm text-gray-800">You have 3 pending tasks.</div>
                                <div class="text-xs text-gray-500">Just Now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Main Chart
const mainCtx = document.getElementById('mainChart').getContext('2d');
const mainChart = new Chart(mainCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            data: [65, 45, 70, 80, 60, 75, 85, 70, 90, 95, 85, 80],
            backgroundColor: '#6366f1',
            borderRadius: 4,
            barThickness: 20
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { 
                grid: { display: false },
                ticks: { color: '#9ca3af' }
            },
            y: { 
                grid: { color: '#f3f4f6' },
                ticks: { color: '#9ca3af' }
            }
        }
    }
});

// Satisfaction Pie Chart
const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
const satisfactionChart = new Chart(satisfactionCtx, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [75, 15, 10],
            backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
@endsection