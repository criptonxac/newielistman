@extends('layouts.main')

@section('title', 'IELTS Tayyorgarlik Platformasi - Bosh sahifa')
@section('description', 'IELTS imtihoniga tayyorgarlik uchun eng yaxshi platforma. Familiarisation testlar, namuna testlar va professional materialler.')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="text-center">
            <h1 class="text-4xl lg:text-6xl font-bold mb-6">
                IELTS Familiarisation Test
            </h1>
            <p class="text-xl lg:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Haqiqiy IELTS imtihoni muhitida bepul familiarisation testlarini topib olasiz. 
                Kompyuterda IELTS testini yaqin kunlarda topshirmoqchimisiz?
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('categories.index') }}" class="bg-white text-blue-600 hover:bg-blue-50 px-8 py-4 rounded-lg font-semibold text-lg transition-colors shadow-lg">
                    <i class="fas fa-play mr-2"></i>
                    Testni boshlash
                </a>
                <a href="#features" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    Batafsil ma'lumot
                </a>
            </div>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" class="w-full h-20 fill-white">
            <path d="M0,64L1440,32L1440,120L0,120Z"></path>
        </svg>
    </div>
</div>

<!-- Statistics Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tasks text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $totalTests }}</h3>
                <p class="text-gray-600">Mavjud testlar</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $totalAttempts }}</h3>
                <p class="text-gray-600">Test urinishlari</p>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trophy text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $completedTests }}</h3>
                <p class="text-gray-600">Tugallangan testlar</p>
            </div>
        </div>
    </div>
</div>

<!-- Featured Tests Section -->
<div id="features" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Bepul IELTS mock test kompyuterda
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                IELTS Familiarisation test yoki IELTS mock test sizga test imtihoni bo'yicha savollar berib, 
                kompyuteringizda haqiqiy test tajribasini taqdim etadi.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach($testCategories as $category)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow h-full">
                    <div class="p-6 h-full flex flex-col">
                        <div class="flex items-center mb-4">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} text-white text-xl"></i>
                                @else
                                    <i class="fas fa-headphones text-white text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    @if($category->duration_minutes)
                                        {{ $category->duration_minutes }} daqiqa
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <p class="text-gray-600 mb-6">{{ $category->description }}</p>
                        
                        @if($category->activeTests->count() > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($category->activeTests->take(2) as $test)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $test->title }}</h4>
                                            <p class="text-sm text-gray-500">{{ $test->total_questions }} savol</p>
                                        </div>
                                        <a href="{{ route('tests.show', $test) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Boshlash
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="mt-auto pt-4">
                            <a href="{{ route('categories.show', $category) }}" 
                               class="block w-full text-center bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 rounded-lg font-medium transition-all">
                                Barcha testlarni ko'rish
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Test Guide Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                    IELTS kompyuterda qanday ishlaydi?
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    IELTS Familiarisation test sizga IELTS on Computer Listening, Reading va Writing testlariga qisqa qo'llanma taqdim etadi.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                            <i class="fas fa-headphones text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Listening Test</h3>
                            <p class="text-gray-600">4 ta yozuv asosida 40 ta savolga javob bering</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-green-100 w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                            <i class="fas fa-book-open text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Academic Reading</h3>
                            <p class="text-gray-600">3 ta uzun matnni o'qing va jami 40 ta savolga javob bering</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-purple-100 w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                            <i class="fas fa-pen text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Academic Writing</h3>
                            <p class="text-gray-600">Ikkita writing vazifasini bajaring</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-8">
                <div class="text-center">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-play text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Qisqa qo'llanma videosi</h3>
                    <p class="text-gray-600 mb-6">IELTS on Computer Listening, Reading va Writing testlariga qisqa qo'llanma.</p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-play mr-2"></i>
                        Videoni tomosha qilish
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sample Tests Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Yangi namuna testlar mavjud
            </h2>
            <p class="text-xl text-gray-600">
                IELTS kompyuterda uchun yangi namuna testlari
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredTests as $test)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 p-6">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $test->title }}</h3>
                        <p class="text-blue-100">{{ $test->category->name }}</p>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">{{ $test->description }}</p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                @if($test->duration_minutes)
                                    {{ $test->duration_minutes }} daqiqa
                                @else
                                    Vaqt chegarasi yo'q
                                @endif
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-question-circle mr-1"></i>
                                {{ $test->total_questions }} savol
                            </span>
                        </div>
                        <a href="{{ route('tests.show', $test) }}" 
                           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition-colors">
                            Testni boshlash
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Download Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Javoblar varaqalarini yuklab olish
            </h2>
            <p class="text-xl text-gray-600">
                Familiarisation test uchun javoblar varaqalari
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="#" class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6 hover:shadow-lg transition-all group">
                <div class="text-center">
                    <div class="bg-green-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-download text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Academic Listening javoblari</h3>
                    <p class="text-sm text-gray-600">PDF fayl yuklab olish</p>
                </div>
            </a>

            <a href="#" class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 hover:shadow-lg transition-all group">
                <div class="text-center">
                    <div class="bg-blue-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-download text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Academic Reading javoblari</h3>
                    <p class="text-sm text-gray-600">PDF fayl yuklab olish</p>
                </div>
            </a>

            <a href="#" class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-6 hover:shadow-lg transition-all group">
                <div class="text-center">
                    <div class="bg-purple-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-download text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Writing namuna javoblari</h3>
                    <p class="text-sm text-gray-600">PDF fayl yuklab olish</p>
                </div>
            </a>

            <a href="#" class="bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 rounded-xl p-6 hover:shadow-lg transition-all group">
                <div class="text-center">
                    <div class="bg-orange-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-download text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">General Training Reading</h3>
                    <p class="text-sm text-gray-600">PDF fayl yuklab olish</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection