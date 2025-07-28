@extends('layouts.student')

@section('title', 'Talaba Dashboard - IELTS Platform')
@section('page_title', 'Dashboard')

@section('content')

    <!-- Progress Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-check text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Yakunlangan Testlar</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['completed_tests'] }}</dd>
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
                                <dd class="text-3xl font-bold text-gray-900">{{ number_format($stats['average_score'], 1) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-trophy text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Eng Yuqori Ball</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['highest_score'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Sarflangan Vaqt</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_time'] }}s</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Available Tests -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-list-ul mr-2 text-blue-500"></i>Mavjud Testlar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($test_categories as $category)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-3">
                                    <div class="w-12 h-12 bg-{{ $category->color ?? 'blue' }}-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-{{ $category->icon ?? 'file-alt' }} text-{{ $category->color ?? 'blue' }}-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $category->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $category->tests_count }} ta test</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">{{ $category->description }}</p>
                                <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                    Testlarni Ko'rish
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities and Progress -->
            <div class="space-y-6">
                <!-- Recent Test Results -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-history mr-2 text-green-500"></i>So'nggi Natijalar
                        </h3>
                        <div class="space-y-3">
                            @forelse($recent_attempts as $attempt)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm">{{ $attempt->test->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $attempt->completed_at?->format('M d, H:i') }}</div>
                                    </div>
                                    <div class="text-right">
                                        @if($attempt->completed_at)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                            @if($attempt->score >= 80) bg-green-100 text-green-800
                                            @elseif($attempt->score >= 60) bg-yellow-100 text-yellow-800  
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $attempt->score }}%
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Jarayonda
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4 text-sm">Hali test topshirmagansiz</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('student.results') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                Barcha natijalarni ko'rish →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Study Tips -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Tayyorgarlik Maslahatlari
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="font-medium text-blue-900 text-sm">📚 Har kuni mashq qiling</div>
                                <div class="text-xs text-blue-700 mt-1">Doimiy mashq - muvaffaqiyat kaliti</div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="font-medium text-green-900 text-sm">⏰ Vaqtni nazorat qiling</div>
                                <div class="text-xs text-green-700 mt-1">Real imtihon kabi vaqt chegarasida ishlang</div>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                                <div class="font-medium text-purple-900 text-sm">📈 Taraqqiyotni kuzating</div>
                                <div class="text-xs text-purple-700 mt-1">Natijalaringizni tahlil qiling va yaxshilang</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection