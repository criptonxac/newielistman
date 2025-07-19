
@extends('layouts.main')

@section('title', 'Admin Dashboard - IELTS Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Tizim statistikasi va boshqaruv</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-user-plus mr-2"></i>Yangi foydalanuvchi
                    </a>
                    <a href="{{ route('admin.export.results') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-file-excel mr-2"></i>Excel export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Jami foydalanuvchilar</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-graduation-cap text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Talabalar</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">O'qituvchilar</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_teachers'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list text-orange-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Jami testlar</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_tests'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Weekly Attempts Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Haftalik test urinishlari</h3>
                <canvas id="weeklyChart" height="200"></canvas>
            </div>

            <!-- Category Distribution Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategoriya bo'yicha taqsimot</h3>
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.users') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-500 text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Foydalanuvchilar</h3>
                        <p class="text-gray-600">Foydalanuvchilarni boshqarish</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.tests') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-clipboard-list text-green-500 text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Testlar</h3>
                        <p class="text-gray-600">Testlarni boshqarish</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.statistics') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar text-purple-500 text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Statistika</h3>
                        <p class="text-gray-600">Batafsil hisobotlar</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Weekly Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: @json($weeklyStats->pluck('date')),
        datasets: [{
            label: 'Test urinishlari',
            data: @json($weeklyStats->pluck('count')),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: @json($categoryStats->pluck('name')),
        datasets: [{
            data: @json($categoryStats->pluck('count')),
            backgroundColor: [
                '#3B82F6',
                '#10B981',
                '#8B5CF6',
                '#F59E0B',
                '#EF4444'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endsection
