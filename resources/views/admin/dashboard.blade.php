<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IELTS Platform</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fallback CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
<div class="flex bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-slate-900 via-purple-900 to-slate-900 shadow-2xl">
        <div class="p-6 border-b border-purple-800/30">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <span class="ml-3 text-xl font-bold text-white">IELTS Platform</span>
            </div>
        </div>

        <nav class="mt-6">
            <div class="px-4 text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3">Dashboard</div>
            <a href="#" class="flex items-center px-6 py-3 text-white bg-gradient-to-r from-blue-600 to-purple-600 border-r-4 border-blue-400 shadow-lg">
                <i class="fas fa-home mr-3 text-blue-200"></i>
                Home
            </a>

            <div class="px-4 text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3 mt-6">Management</div>
            <a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                <i class="fas fa-users mr-3 text-purple-400"></i>
                Users
            </a>
            <a href="{{ route('test-management.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 transition-all duration-300 {{ request()->routeIs('test-management.*') ? 'text-white bg-gradient-to-r from-blue-600 to-purple-600 border-r-4 border-blue-400 shadow-lg' : '' }}">
                <i class="fas fa-file-alt mr-3 text-purple-400"></i>
                Tests
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">IELTS Mock Test Platform</h1>

                <!-- User Avatar Dropdown -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" onclick="toggleUserMenu()">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Admin' }}&background=6366f1&color=fff"
                             class="w-8 h-8 rounded-full" alt="Avatar">
                        <div class="text-left">
                            <div class="font-medium text-gray-900">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <div class="text-xs text-gray-500">Administrator</div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>
                            Profil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>
                            Sozlamalar
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Chiqish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="p-8">
            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Jami foydalanuvchilar -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">FOYDALANUVCHILAR</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Jami foydalanuvchilar soni</div>
                </div>

                <!-- Talabalar -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-graduate text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">TALABALAR</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_students'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Ro'yxatdan o'tgan talabalar</div>
                </div>

                <!-- Savollar -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-question-circle text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">SAVOLLAR</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_questions'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Jami test savollari</div>
                </div>

                <!-- O'qituvchilar -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-chalkboard-teacher text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">O'QITUVCHILAR</span>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_teachers'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Faol o'qituvchilar soni</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Bugungi o'rtacha ball -->
                <div class="lg:col-span-1 bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Bugungi o'rtacha ball</h3>
                        <div class="text-sm text-gray-500">Bugun topshirilgan testlar bo'yicha talabalar olgan o'rtacha ball</div>
                    </div>

                    <div class="text-center mb-6">
                        <div class="w-32 h-32 bg-gradient-to-r from-blue-400 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <div class="text-white text-center">
                                <div class="text-3xl font-bold">{{ isset($todayAverageScore) ? number_format($todayAverageScore, 1) : '0.0' }}</div>
                                <div class="text-sm">Ball</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="text-sm text-gray-500 mb-2">Bugungi testlar soni</div>
                        <div class="text-2xl font-bold text-gray-800">{{ \App\Models\UserTestAttempt::today()->completed()->count() }}</div>
                    </div>
                </div>

                <!-- Eng yaxshi 10 talaba -->
                <div class="lg:col-span-2 bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Eng yaxshi 10 talaba</h3>
                    <div class="text-sm text-gray-500 mb-6">O'rtacha ball bo'yicha eng yaxshi ko'rsatkichli talabalar</div>

                    @if(isset($topStudents) && $topStudents->count() > 0)
                        <div class="h-64 flex items-end justify-between space-x-2">
                            @foreach(isset($topStudents) ? $topStudents->take(10) : [] as $index => $student)
                                @php
                                    $maxScore = isset($topStudents) ? ($topStudents->max('average_score') ?: 100) : 100;
                                    $height = $student['average_score'] > 0 ? (($student['average_score'] / $maxScore) * 200) : 10;
                                @endphp
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="text-xs font-semibold text-gray-700 mb-1">{{ number_format($student['average_score'], 1) }}</div>
                                    <div
                                        class="w-full bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t transition-all duration-500 hover:from-indigo-600 hover:to-indigo-500 cursor-pointer"
                                        style="height: {{ $height }}px; min-height: 20px;"
                                        title="{{ $student['name'] }} - {{ $student['average_score'] }} ball"
                                    ></div>
                                    <div class="text-xs text-gray-600 mt-2 text-center">
                                        <div class="font-semibold truncate w-full">{{ Str::limit($student['name'], 8) }}</div>
                                        <div class="text-gray-400">{{ $student['total_tests'] }} test</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Legend -->
                        <div class="mt-4 text-center">
                            <div class="text-xs text-gray-500">Ustunlar balandligi o'rtacha ballni ko'rsatadi</div>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>Hozircha test topshirgan talabalar yo'q</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Haftalik Faollik -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Haftalik Faollik</h3>
                    <div class="text-sm text-gray-500 mb-6">Oxirgi 7 kunlik test faolligi va statistikalar</div>

                    <!-- Faollik foizi -->
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold text-indigo-600 mb-2">{{ isset($weeklyActivity) ? $weeklyActivity['activity_percentage'] : '0' }}%</div>
                        <div class="text-sm text-gray-500">Haftalik faollik foizi</div>
                    </div>

                    <!-- Donut Chart -->
                    <div class="w-40 h-40 mx-auto mb-4">
                        <canvas id="weeklyChart"></canvas>
                    </div>

                    <!-- Qo'shimcha ma'lumotlar -->
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-lg font-bold text-gray-800">{{ isset($weeklyActivity) ? $weeklyActivity['total_tests'] : '0' }}</div>
                            <div class="text-xs text-gray-500">Jami testlar</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-800">{{ isset($weeklyActivity) ? $weeklyActivity['avg_daily'] : '0' }}</div>
                            <div class="text-xs text-gray-500">Kunlik o'rtacha</div>
                        </div>
                    </div>
                </div>

                <!-- Talaba Faolligi Kartochalari -->
                <div class="space-y-4">
                    <!-- Faol talabalar -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 flex justify-between items-center">
                        <div>
                            <div class="text-sm text-gray-500">Faol talabalar</div>
                            <div class="text-2xl font-bold text-gray-800">{{ isset($studentActivity) ? $studentActivity['active_students'] : '0' }}</div>
                            <div class="text-xs text-gray-400">Oxirgi 7 kun</div>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-green-600"></i>
                        </div>
                    </div>

                    <!-- Tugallangan testlar -->
                    <div class="bg-blue-600 text-white rounded-lg p-4">
                        <div class="text-sm opacity-80">Tugallangan testlar</div>
                        <div class="text-2xl font-bold">{{ isset($studentActivity) ? $studentActivity['completed_tests'] : '0' }}</div>
                        <div class="text-xs opacity-70">Jami</div>
                    </div>

                    <!-- O'rtacha natija -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-gray-500">O'rtacha natija</div>
                                <div class="text-xl font-bold text-gray-800">{{ isset($studentActivity) ? $studentActivity['average_score'] : '0' }} ball</div>
                            </div>
                            <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                                <i class="fas fa-chart-bar text-blue-600 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Eng yaxshi natija -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-gray-500">Eng yaxshi natija</div>
                                <div class="text-xl font-bold text-gray-800">{{ isset($studentActivity) ? $studentActivity['highest_score'] : '0' }} ball</div>
                            </div>
                            <div class="w-8 h-8 bg-yellow-100 rounded flex items-center justify-center">
                                <i class="fas fa-trophy text-yellow-600 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Talabalar Ballari Grafigi -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Talabalar Ballari</h3>
                    <div class="text-sm text-gray-500 mb-6">Eng yaxshi 10 talabaning o'rtacha ballari</div>
                    <div class="h-64">
                        <canvas id="studentsChart"></canvas>
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

// Haftalik Faollik Area Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
const weeklyChart = new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach(isset($weeklyStats) ? $weeklyStats : [] as $stat)
                '{{ $stat["date"] }}',
            @endforeach
        ],
        datasets: [{
            label: 'Kunlik testlar',
            data: [
                @foreach(isset($weeklyStats) ? $weeklyStats : [] as $stat)
                    {{ $stat['count'] }},
                @endforeach
            ],
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            borderColor: '#6366f1',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6366f1',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 12,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#6366f1',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        return 'Testlar soni: ' + context.parsed.y + ' ta';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#9ca3af',
                    font: {
                        size: 11
                    }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(156, 163, 175, 0.1)'
                },
                ticks: {
                    color: '#9ca3af',
                    font: {
                        size: 11
                    },
                    callback: function(value) {
                        return Math.floor(value) === value ? value : '';
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Talabalar Ballari Chart
@if(isset($topStudents) && $topStudents->count() > 0)
const studentsCtx = document.getElementById('studentsChart').getContext('2d');
const studentsChart = new Chart(studentsCtx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($topStudents->take(10) as $student)
                '{{ Str::limit($student["name"], 10) }}',
            @endforeach
        ],
        datasets: [{
            label: 'O\'rtacha Ball',
            data: [
                @foreach($topStudents->take(10) as $student)
                    {{ $student['average_score'] }},
                @endforeach
            ],
            backgroundColor: [
                '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#f59e0b',
                '#10b981', '#06b6d4', '#84cc16', '#f97316', '#ef4444'
            ],
            borderRadius: 6,
            barThickness: 30
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    title: function(context) {
                        const studentIndex = context[0].dataIndex;
                        const students = [
                            @foreach($topStudents->take(10) as $student)
                                '{{ $student["name"] }}',
                            @endforeach
                        ];
                        return students[studentIndex];
                    },
                    label: function(context) {
                        return 'O\'rtacha ball: ' + context.parsed.y + ' ball';
                    },
                    afterLabel: function(context) {
                        const studentIndex = context.dataIndex;
                        const testCounts = [
                            @foreach($topStudents->take(10) as $student)
                                {{ $student['total_tests'] }},
                            @endforeach
                        ];
                        return 'Jami testlar: ' + testCounts[studentIndex] + ' ta';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    color: '#9ca3af',
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                grid: { color: '#f3f4f6' },
                ticks: {
                    color: '#9ca3af',
                    callback: function(value) {
                        return value + ' ball';
                    }
                },
                beginAtZero: true
            }
        }
    }
});

// User Avatar Dropdown Menu
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');

    if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
    }
});
</script>
</body>
</html>
@endif
