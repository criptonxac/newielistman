@extends('layouts.main')

@section('title', 'O\'qituvchi Dashboard - IELTS Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">O'qituvchi Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Salom, {{ auth()->user()->name }}! Talabalar va testlarni boshqaring</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('teacher.tests.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Yangi Test
                    </a>
                    <a href="{{ route('teacher.tests') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-list mr-2"></i>Barcha Testlar
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
                            <i class="fas fa-clipboard-list text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Jami Testlar</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_tests'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-green-500 text-2xl"></i>
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
                            <i class="fas fa-chart-line text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Test Urinishlari</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_attempts'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-star text-yellow-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">O'rtacha Ball</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ number_format($stats['average_score'], 1) }}</dd>
                            </dl>
                        </div>
                    </div>
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
                                <div class="font-medium text-gray-900">{{ $attempt->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $attempt->test->title }}</div>
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
                        <a href="{{ route('teacher.tests.create') }}" class="block w-full bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-plus-circle text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-blue-900">Yangi Test Yaratish</div>
                                    <div class="text-sm text-blue-600">IELTS uchun yangi test yarating</div>
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

                        <a href="{{ route('teacher.analytics') }}" class="block w-full bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-purple-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-purple-900">Analitika va Hisobotlar</div>
                                    <div class="text-sm text-purple-600">Batafsil statistika va tahlillar</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('teacher.settings') }}" class="block w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-cog text-gray-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Sozlamalar</div>
                                    <div class="text-sm text-gray-600">Profil va test sozlamalari</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection