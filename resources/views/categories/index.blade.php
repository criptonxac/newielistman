@extends('layouts.main')

@section('title', 'Test Kategoriyalari - IELTS Platform')
@section('description', 'Barcha IELTS test kategoriyalari: Listening, Academic Reading, Academic Writing va boshqa testlar.')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                IELTS Test Kategoriyalari
            </h1>
            <p class="text-xl text-indigo-100 max-w-3xl mx-auto">
                Turli xil IELTS test kategoriyalarini tanlang va o'zingizni sinab ko'ring. 
                Har bir kategoriya bo'yicha bepul familiarisation testlari mavjud.
            </p>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($categories as $category)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                        <!-- Category Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                            <div class="flex items-center">
                                <div class="bg-white bg-opacity-20 w-16 h-16 rounded-lg flex items-center justify-center mr-4">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} text-white text-2xl"></i>
                                    @else
                                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">{{ $category->name }}</h2>
                                    <p class="text-blue-100">
                                        {{ $category->active_tests_count }} ta test mavjud
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Category Content -->
                        <div class="p-6">
                            @if($category->description)
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    {{ $category->description }}
                                </p>
                            @endif

                            <!-- Category Info -->
                            <div class="space-y-3 mb-6">
                                @if($category->duration_minutes)
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-clock w-5 text-center mr-3 text-blue-500"></i>
                                        <span>{{ $category->duration_minutes }} daqiqa</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-tasks w-5 text-center mr-3 text-green-500"></i>
                                    <span>{{ $category->active_tests_count }} ta familiarisation test</span>
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-laptop w-5 text-center mr-3 text-purple-500"></i>
                                    <span>Kompyuterda test olish</span>
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-download w-5 text-center mr-3 text-orange-500"></i>
                                    <span>Javoblar varaqasi mavjud</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-3">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 rounded-lg font-medium text-center transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-eye mr-2"></i>
                                    Testlarni ko'rish
                                </a>
                                
                                @if($category->activeTests->count() > 0)
                                    <a href="{{ route('tests.show', $category->activeTests->first()) }}" 
                                       class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white py-3 px-6 rounded-lg font-medium text-center transition-all duration-200">
                                        <i class="fas fa-play mr-2"></i>
                                        Darhol boshlash
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Category Footer -->
                        <div class="bg-gray-50 px-6 py-4">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-user-graduate mr-1"></i>
                                    Professional tayyorgarlik
                                </span>
                                <span>
                                    <i class="fas fa-certificate mr-1"></i>
                                    Rasmiy namuna
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Hech qanday kategoriya topilmadi</h3>
                <p class="text-gray-600 mb-6">Hozircha test kategoriyalari mavjud emas. Iltimos, keyinroq qayta urinib ko'ring.</p>
                <a href="{{ route('home') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Bosh sahifaga qaytish
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Additional Info Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 lg:p-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        IELTS testlari haqida qo'shimcha ma'lumot
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Familiarisation testlar sizga haqiqiy IELTS imtihoni muhitini his qilish imkonini beradi. 
                        Bu testlar vaqt chegarasi bo'lmagan holda o'tkaziladi va siz test ekranlari bilan tanishib, 
                        turli qismlar orasida harakatlanishingiz mumkin.
                    </p>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Bepul familiarisation testlari</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Vaqt chegarasi yo'q</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Haqiqiy test muhiti</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Darhol javoblarni tekshirish</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-white rounded-xl p-8 shadow-lg">
                        <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lightbulb text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Foydali maslahat</h3>
                        <p class="text-gray-600 mb-6">
                            Test tugagach, sahifaning pastki qismida javoblaringizni tekshirishingiz mumkin.
                        </p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Haqiqiy IELTS testdan farqli ravishda, bu testlar ball bermaydi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection