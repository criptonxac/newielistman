@extends('layouts.teacher')

@section('title', 'O\'qituvchi Dashboard - IELTS Platform')

@section('page_title', 'O\'qituvchi Dashboard')

@section('content')

            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Jami testlar -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-file-alt text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-blue-600 font-medium bg-blue-50 px-2 py-1 rounded-full">+12%</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_tests'] }}</div>
                    <div class="text-xs text-gray-400">Jami testlar soni</div>
                </div>

                <!-- Talabalar -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded-full">+8%</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_students'] }}</div>
                    <div class="text-xs text-gray-400">Ro'yxatdan o'tgan talabalar</div>
                </div>

                <!-- Test urinishlari -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-purple-600 font-medium bg-purple-50 px-2 py-1 rounded-full">+15%</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_attempts'] }}</div>
                    <div class="text-xs text-gray-400">Jami test urinishlari</div>
                </div>

                <!-- O'rtacha ball -->
                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-trophy text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-orange-600 font-medium bg-orange-50 px-2 py-1 rounded-full">+5%</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['average_score'], 1) }}</div>
                    <div class="text-xs text-gray-400">O'rtacha ball</div>
                </div>
            </div>
        </div>

        <!-- Recent Activities and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Test Attempts -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>So'nggi Test Urinishlari
                    </h3>
                    <div class="space-y-3">
                        @forelse($recent_attempts as $attempt)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900">{{ $attempt->user ? $attempt->user->name : 'Noma\'lum foydalanuvchi' }}</div>
                                <div class="text-sm text-gray-500">{{ $attempt->test ? $attempt->test->title : 'Noma\'lum test' }}</div>
                                <div class="text-xs text-gray-400">{{ $attempt->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-right">
                                @if($attempt->completed_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $attempt->score }}%
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Jarayonda
                                </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">Hali test urinishlari yo'q</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>Tezkor Amallar
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('categories.index') }}" class="block w-full bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-list text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-blue-900">Testlarni Ko'rish</div>
                                    <div class="text-sm text-blue-600">Barcha mavjud testlarni ko'ring</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('teacher.students') }}" class="block w-full bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-users text-green-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-green-900">Talabalarni Boshqarish</div>
                                    <div class="text-sm text-green-600">Talabalar va ularning natijalarini ko'ring</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('teacher.results') }}" class="block w-full bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-purple-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-purple-900">Test Natijalari</div>
                                    <div class="text-sm text-purple-600">Talabalar natijalarini ko'ring</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('teacher.dashboard') }}" class="block w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-home text-gray-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Bosh sahifa</div>
                                    <div class="text-sm text-gray-600">Dashboard sahifasiga qaytish</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
@endsection