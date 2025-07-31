@extends('layouts.main')

@section('title', $test->title . ' - IELTS Platform')
@section('description', $test->description ?: 'IELTS ' . $test->category->name . ' testi.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Test Header -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium mr-3">
                        {{ $test->category->name }}
                    </div>
                    <span class="text-gray-500 text-sm">
                        {{ $test->questions->count() }} ta savol
                    </span>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $test->title }}</h1>
                
                @if($test->description)
                    <p class="text-gray-600 mb-6">{{ $test->description }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        <span class="text-gray-700">
                            @if($test->time_limit)
                                {{ $test->time_limit }} daqiqa
                            @else
                                Vaqt chegarasi yo'q
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        <span class="text-gray-700">{{ $test->questions->count() }} ta savol</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        <span class="text-gray-700">
                            @if($test->difficulty)
                                {{ ucfirst($test->difficulty) }} daraja
                            @else
                                Familiarisation
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Test Ko'rsatmalari
            </h3>
            <ul class="text-blue-800 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    Bu familiarisation test bo'lib, haqiqiy IELTS test muhitini simulyatsiya qiladi
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    Testni boshlash uchun "Testni Boshlash" tugmasini bosing
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    Har bir savol uchun eng to'g'ri javobni tanlang
                </li>
                @if($test->time_limit)
                <li class="flex items-start">
                    <i class="fas fa-clock text-blue-600 mr-2 mt-1"></i>
                    Testni {{ $test->time_limit }} daqiqada yakunlash kerak
                </li>
                @endif
            </ul>
        </div>

        <!-- Start Button -->
        <div class="text-center">
            <form action="{{ route('tests.start', $test) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition-colors duration-200">
                    <i class="fas fa-play mr-2"></i>
                    Testni Boshlash
                </button>
            </form>
        </div>
    </div>

    <!-- Test Categories -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Boshqa Testlar</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @if($test->category)
                @foreach($test->category->tests->where('id', '!=', $test->id) as $relatedTest)
                <a href="{{ route('student.tests.show', $relatedTest) }}" class="block bg-gray-50 hover:bg-gray-100 rounded-lg p-4 transition-colors">
                    <h4 class="font-semibold text-gray-900 mb-2">{{ $relatedTest->title }}</h4>
                    <p class="text-sm text-gray-600">{{ $relatedTest->questions->count() }} ta savol</p>
                </a>
                @endforeach
            @else
                <p class="text-gray-500">Boshqa testlar mavjud emas</p>
            @endif
        </div>
    </div>
</div>
@endsection