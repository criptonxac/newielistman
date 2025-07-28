@extends('layouts.student')

@section('title', 'Barcha Testlar - IELTS Platform')
@section('description', 'IELTS familiarisation testlari va mashqlar platformasi')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl lg:text-4xl font-bold mb-3">
                Bepul IELTS mock test kompyuterda
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                IELTS Familiarisation test yoki IELTS mock test sifatida test imtihoni bo'yicha savollar berib, kompyuteringizda haqiqiy test tajribasini taqdiim etadi.
            </p>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Test Kategoriyalari</h2>
            <p class="mt-2 text-gray-600">Quyidagi test kategoriyalaridan birini tanlang</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                <!-- Category Header -->
                @php
                    $headerClass = 'bg-blue-600';
                    $iconClass = 'fas fa-headphones';
                    
                    if (strpos(strtolower($category->name), 'reading') !== false) {
                        $headerClass = 'bg-red-600';
                        $iconClass = 'fas fa-book-open';
                    } elseif (strpos(strtolower($category->name), 'writing') !== false) {
                        $headerClass = 'bg-orange-500';
                        $iconClass = 'fas fa-pen-fancy';
                    }
                @endphp
                
                <div class="{{ $headerClass }} px-6 py-4 text-white">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="{{ $iconClass }} text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">{{ $category->name }}</h3>
                            <p class="text-sm text-white text-opacity-80">{{ $category->tests->count() }} ta test</p>
                        </div>
                    </div>
                </div>
                
                <!-- Category Content -->
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-6">{{ $category->description ?: 'IELTS ' . $category->name . ' testlari to\'plamidan foydalaning.' }}</p>
                    
                    @if(strpos(strtolower($category->name), 'listening') !== false)
                        @if($category->tests->count() > 0)
                            <a href="{{ route('listening.start', ['test' => $category->tests->first()->slug]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-play-circle mr-2"></i> Testni boshlash
                            </a>
                        @else
                            <button disabled class="block w-full bg-gray-400 text-white text-center py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                <i class="fas fa-exclamation-circle mr-2"></i> Test mavjud emas
                            </button>
                        @endif
                    @elseif(strpos(strtolower($category->name), 'reading') !== false)
                        @if($category->tests->count() > 0)
                            <a href="{{ route('reading.start', ['test' => $category->tests->first()->slug]) }}" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-play-circle mr-2"></i> Testni boshlash
                            </a>
                        @else
                            <button disabled class="block w-full bg-gray-400 text-white text-center py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                <i class="fas fa-exclamation-circle mr-2"></i> Test mavjud emas
                            </button>
                        @endif
                    @elseif(strpos(strtolower($category->name), 'writing') !== false)
                        @if($category->tests->count() > 0)
                            <a href="{{ route('writing.start', ['test' => $category->tests->first()->slug]) }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-play-circle mr-2"></i> Testni boshlash
                            </a>
                        @else
                            <button disabled class="block w-full bg-gray-400 text-white text-center py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                <i class="fas fa-exclamation-circle mr-2"></i> Test mavjud emas
                            </button>
                        @endif
                    @else
                        <a href="#" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors">
                            <i class="fas fa-play-circle mr-2"></i> Testni boshlash
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection