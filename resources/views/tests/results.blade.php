@extends('layouts.main')

@section('title', 'Test Natijalari - ' . $test->title)
@section('description', 'IELTS ' . $test->category->name . ' test natijalari.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Results Header -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8 text-center">
        <div class="mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Test Yakunlandi!</h1>
            <p class="text-gray-600">{{ $test->title }}</p>
        </div>

        <!-- To'g'ri/noto'g'ri javoblar statistikasini ko'rsatmaslik -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-8">
            <div class="bg-blue-50 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">Test yakunlandi</div>
                <div class="text-blue-800 font-medium">Javoblaringiz saqlandi</div>
            </div>
        </div>

        <div class="flex justify-center space-x-4">
            <a href="{{ route('tests.show', $test) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                <i class="fas fa-redo mr-2"></i>
                Qayta Urinish
            </a>
            <a href="{{ route('categories.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg">
                <i class="fas fa-list mr-2"></i>
                Boshqa Testlar
            </a>
        </div>
    </div>

    <!-- Detailed Results -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Batafsil Natijalar</h2>
        
        @foreach($test->questions as $index => $question)
        @php
            $userAnswer = $attempt->userAnswers->where('test_question_id', $question->id)->first();
            $isCorrect = $userAnswer && $userAnswer->is_correct;
        @endphp
        
        <div class="border border-gray-200 rounded-lg p-6 mb-4">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium mr-3">
                            Savol {{ $index + 1 }}
                        </span>
                        <!-- To'g'ri/Noto'g'ri belgilarini ko'rsatmaslik -->
                        <!-- <div class="flex items-center">
                            @if($isCorrect)
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-green-600 font-medium">To'g'ri</span>
                            @else
                                <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                <span class="text-red-600 font-medium">Noto'g'ri</span>
                            @endif
                        </div> -->
                    </div>
                    
                    <div class="text-gray-900 mb-4">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Sizning Javobingiz:</h4>
                    <div class="bg-gray-50 rounded-lg p-3">
                        @if($userAnswer)
                            @if($question->question_type === 'multiple_choice')
                                @php 
                                    $options = json_decode($question->options, true) ?? [];
                                    $userChoice = $options[$userAnswer->user_answer] ?? $userAnswer->user_answer;
                                @endphp
                                <span class="text-gray-700">{{ $userChoice }}</span>
                            @else
                                <span class="text-gray-700">{{ $userAnswer->user_answer }}</span>
                            @endif
                        @else
                            <span class="text-gray-500 italic">Javob berilmagan</span>
                        @endif
                    </div>
                </div>
                
               
            </div>

            @if($question->explanation)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tushuntirish:
                </h4>
                <p class="text-blue-800">{{ $question->explanation }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Performance Summary -->
    <div class="bg-white rounded-xl shadow-lg p-8 mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Natijalar Tahlili</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Ma'lumotlari</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Boshlangan vaqt:</span>
                        <span class="font-medium">{{ $attempt->started_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tugallangan vaqt:</span>
                        <span class="font-medium">{{ $attempt->completed_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sarflangan vaqt:</span>
                        <span class="font-medium">
                            {{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jami savollar:</span>
                        <span class="font-medium">{{ $test->questions->count() }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tavsiyalar</h3>
                <div class="space-y-3">
                    @if($attempt->score >= 80)
                        <div class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-2 mt-1"></i>
                            <span class="text-gray-700">Ajoyib natija! Siz bu mavzuni yaxshi bilasiz.</span>
                        </div>
                    @elseif($attempt->score >= 60)
                        <div class="flex items-start">
                            <i class="fas fa-thumbs-up text-blue-500 mr-2 mt-1"></i>
                            <span class="text-gray-700">Yaxshi natija. Ba'zi mavzularni takrorlash tavsiya etiladi.</span>
                        </div>
                    @else
                        <div class="flex items-start">
                            <i class="fas fa-book text-red-500 mr-2 mt-1"></i>
                            <span class="text-gray-700">Bu mavzuni chuqurroq o'rganish tavsiya etiladi.</span>
                        </div>
                    @endif
                    
                    <div class="flex items-start">
                        <i class="fas fa-redo text-green-500 mr-2 mt-1"></i>
                        <span class="text-gray-700">Testni qayta topshirib ko'ring va natijalaringizni taqqoslang.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection