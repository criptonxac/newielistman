@extends('layouts.main')

@section('title', $category->name . ' - IELTS Platform')
@section('description', $category->description ?? $category->name . ' bo\'yicha IELTS familiarisation testlari.')

@section('content')
<!-- Category Header -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center lg:justify-start">
            <div class="bg-white bg-opacity-20 w-20 h-20 rounded-xl flex items-center justify-center mr-6">
                @if($category->icon)
                    <i class="{{ $category->icon }} text-white text-3xl"></i>
                @else
                    <i class="fas fa-clipboard-list text-white text-3xl"></i>
                @endif
            </div>
            <div class="text-center lg:text-left">
                <h1 class="text-4xl lg:text-5xl font-bold mb-2">{{ $category->name }}</h1>
                <p class="text-xl text-blue-100">
                    {{ $category->activeTests->count() }} ta test mavjud
                    @if($category->duration_minutes)
                        • {{ $category->duration_minutes }} daqiqa
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Category Description -->
@if($category->description)
<div class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-8">
            <div class="flex items-start">
                <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $category->name }} haqida</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $category->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tests Grid -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($category->activeTests->count() > 0)
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Mavjud testlar</h2>
                <p class="text-gray-600">
                    {{ $category->name }} bo'yicha familiarisation testlarni tanlang va darhol boshlang.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($category->activeTests as $test)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Test Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-1">{{ $test->title }}</h3>
                                    <p class="text-indigo-100">{{ $test->type }} test</p>
                                </div>
                                <div class="bg-white bg-opacity-20 w-12 h-12 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-play text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Test Info -->
                        <div class="p-6">
                            @if($test->description)
                                <p class="text-gray-600 mb-6">{{ $test->description }}</p>
                            @endif

                            <!-- Test Details -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-question-circle text-blue-500 mr-2"></i>
                                        <div>
                                            <p class="text-sm text-gray-500">Savollar soni</p>
                                            <p class="font-semibold text-gray-900">{{ $test->total_questions }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-green-500 mr-2"></i>
                                        <div>
                                            <p class="text-sm text-gray-500">Vaqt</p>
                                            <p class="font-semibold text-gray-900">
                                                @if($test->is_timed && $test->duration_minutes)
                                                    {{ $test->duration_minutes }} daqiqa
                                                @else
                                                    Cheklanmagan
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Test Features -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3">Test xususiyatlari:</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        <span class="text-gray-600 text-sm">Bepul familiarisation test</span>
                                    </div>
                                    @if(!$test->is_timed)
                                        <div class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            <span class="text-gray-600 text-sm">Vaqt chegarasi yo'q</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        <span class="text-gray-600 text-sm">Darhol javoblarni tekshirish</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        <span class="text-gray-600 text-sm">Haqiqiy test muhiti</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Test Instructions -->
                            @if($test->instructions && count($test->instructions) > 0)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                        Ko'rsatmalar:
                                    </h4>
                                    <ul class="space-y-1">
                                        @foreach($test->instructions as $instruction)
                                            <li class="text-sm text-gray-700">• {{ $instruction }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('student.tests.show', $test) }}" 
                                   class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 rounded-lg font-medium text-center transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-eye mr-2"></i>
                                    Batafsil ko'rish
                                </a>
                                
                                @if($category->name === 'Listening')
                                <form action="{{ route('listening.start', $test) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white py-3 px-6 rounded-lg font-medium transition-all duration-200">
                                        <i class="fas fa-play mr-2"></i>
                                        Darhol boshlash
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('tests.start', $test) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white py-3 px-6 rounded-lg font-medium transition-all duration-200">
                                        <i class="fas fa-play mr-2"></i>
                                        Darhol boshlash
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>

                        <!-- Test Footer -->
                        <div class="bg-gray-50 px-6 py-4">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-laptop mr-1"></i>
                                    Kompyuterda test
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
                    <i class="fas fa-clipboard text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Hech qanday test topilmadi</h3>
                <p class="text-gray-600 mb-6">{{ $category->name }} kategoriyasida hozircha testlar mavjud emas.</p>
                <a href="{{ route('categories.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Boshqa kategoriyalarga qaytish
                </a>
            </div>
        @endif
    </div>
</div>
@endsection