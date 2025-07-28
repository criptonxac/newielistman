@extends('layouts.student')

@section('title', 'Writing Test Completed')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <!-- Success Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-yellow-500 px-6 py-8 text-white">
                <div class="flex items-center justify-center">
                    <div class="bg-white rounded-full p-3 mr-4">
                        <svg class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold">Test yakunlandi!</h2>
                        <p class="text-orange-100">Sizning IELTS Academic Writing testingiz muvaffaqiyatli yakunlandi.</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-8">
                <div class="mb-8 text-center">
                    <div class="text-gray-600 mb-2">Test yakunlangan vaqt</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $attempt->completed_at->format('d.m.Y H:i') }}</div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-gray-600 mb-1">Test nomi</div>
                        <div class="font-bold text-gray-800">{{ $test->title }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-gray-600 mb-1">Kategoriya</div>
                        <div class="font-bold text-gray-800">{{ $test->category->name }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-gray-600 mb-1">Vazifalar</div>
                        <div class="font-bold text-gray-800">Task 1 va Task 2</div>
                    </div>
                </div>
                
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-orange-800 mb-4">Muhim ma'lumot</h3>
                    <p class="text-orange-700 mb-3">
                        Sizning yozma ishlaringiz tekshirish uchun yuborildi. Natijalar tez orada e'lon qilinadi.
                    </p>
                    <p class="text-orange-700">
                        IELTS Writing testida quyidagi to'rtta mezon bo'yicha baholanasiz:
                    </p>
                    <ul class="list-disc pl-5 mt-2 text-orange-700">
                        <li>Task Achievement / Response (vazifani bajarish)</li>
                        <li>Coherence and Cohesion (fikrlarning bog'liqligi va izchilligi)</li>
                        <li>Lexical Resource (lug'at boyligi)</li>
                        <li>Grammatical Range and Accuracy (grammatik xilma-xillik va aniqlik)</li>
                    </ul>
                </div>
                
                <div class="flex justify-center">
                    <a href="{{ route('student.dashboard') }}" class="bg-orange-500 hover:bg-orange-600 text-white py-3 px-8 rounded-lg font-bold transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Bosh sahifaga qaytish
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
