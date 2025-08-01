@extends('layouts.main')

@section('title', $test->title . ' - Test')
@section('description', 'IELTS ' . $test->category->name . ' testini topshiring.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Timer Container -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $test->title }}</h1>
            <div class="flex items-center mt-2">
                <span class="text-gray-600">Savol</span>
                <span id="current-question" class="mx-1 font-semibold">1</span>
                <span class="text-gray-600">/ {{ $test->questions->count() }}</span>
            </div>
        </div>
            
            @if($test->time_limit)
            <div class="text-right">
                <div class="flex flex-col gap-2">
                    <div id="timer-container" class="bg-red-100 text-red-800 px-4 py-2 rounded-lg">
                        <i class="fas fa-clock mr-2"></i>
                        <span id="timer">{{ sprintf('%02d:%02d', $test->time_limit, 0) }}</span>
                    </div>
                    <div id="timer-status" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm">
                        Test boshlanishini kutmoqda
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Jarayon</span>
            <span class="text-sm font-medium text-gray-700">
                <span id="current-question">1</span> / {{ $test->questions->count() }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full" style="width: {{ (1 / $test->questions->count()) * 100 }}%"></div>
        </div>
    </div>

    <!-- Test Intro Section -->
    <div id="test-intro" class="bg-white rounded-xl shadow-lg p-8 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Test haqida ma'lumot</h2>
        
        <div class="mb-6">
            <p class="mb-2"><strong>Test turi:</strong> {{ $test->category->name }}</p>
            <p class="mb-2"><strong>Savollar soni:</strong> {{ $test->questions->count() }}</p>
            <p class="mb-2"><strong>Vaqt:</strong> {{ $test->time_limit }} daqiqa</p>
        </div>
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Ko'rsatmalar:</h3>
            <ul class="list-disc pl-5 space-y-1">
                @if(strtolower($test->category->name) === 'listening')
                <li>Test boshlanganda avtomatik ravishda vaqt hisoblagichi ishga tushadi.</li>
                <li>Audio faylni diqqat bilan tinglang.</li>
                <li>Savollarni ketma-ketlikda bajaring.</li>
                @elseif(strtolower($test->category->name) === 'reading')
                <li>Test boshlanganda avtomatik ravishda vaqt hisoblagichi ishga tushadi.</li>
                <li>Matnni diqqat bilan o'qing.</li>
                <li>Savollarni ketma-ketlikda bajaring.</li>
                @elseif(strtolower($test->category->name) === 'writing')
                <li>Test boshlanganda avtomatik ravishda vaqt hisoblagichi ishga tushadi.</li>
                <li>Insho yozish uchun berilgan mavzuni diqqat bilan o'qing.</li>
                <li>Kamida 150 so'z yozing.</li>
                @else
                <li>"Testni boshlash" tugmasini bosganingizda vaqt hisoblagichi ishga tushadi.</li>
                <li>Savollarni ketma-ketlikda bajaring.</li>
                @endif
                <li>Testni yakunlash uchun "Testni yakunlash" tugmasini bosing.</li>
            </ul>
        </div>
        
        <button id="start-test-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
            <i class="fas fa-play mr-2"></i>
            Testni boshlash
        </button>
    </div>

    <!-- Questions Container -->
    <div id="test-questions" class="bg-white rounded-xl shadow-lg p-8 {{ strtolower($test->category->name) === 'listening' || strtolower($test->category->name) === 'reading' || strtolower($test->category->name) === 'writing' ? '' : 'hidden' }}">
        @foreach($test->questions as $index => $question)
        <div class="question-container {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index + 1 }}">
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-3">
                        Savol {{ $index + 1 }}
                    </span>
                    @if($question->points)
                    <span class="text-gray-500 text-sm">{{ $question->points }} ball</span>
                    @endif
                </div>
                
                <div class="question-text text-lg text-gray-900 mb-6">
                    {!! nl2br(e($question->question_text)) !!}
                </div>

                @if($question->question_type === 'multiple_choice')
                    <div class="space-y-3">
                        @php $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []) @endphp
                        @foreach($options as $key => $option)
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="question_{{ $question->id }}" value="{{ $key }}" 
                                   class="mr-3 text-blue-600 focus:ring-blue-500"
                                   {{ (isset($answers[$question->id]) && $answers[$question->id]->user_answer === $key) ? 'checked' : '' }}>
                            <span class="text-gray-900">{{ $option }}</span>
                        </label>
                        @endforeach
                    </div>

                @elseif($question->question_type === 'fill_blank')
                    <div class="space-y-4">
                        <input type="text" name="question_{{ $question->id }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Javobingizni kiriting..."
                               value="{{ isset($answers[$question->id]) ? $answers[$question->id]->user_answer : '' }}">
                    </div>

                @elseif($question->question_type === 'true_false')
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="question_{{ $question->id }}" value="true" 
                                   class="mr-3 text-blue-600 focus:ring-blue-500"
                                   {{ (isset($answers[$question->id]) && $answers[$question->id]->user_answer === 'true') ? 'checked' : '' }}>
                            <span class="text-gray-900">To'g'ri</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="question_{{ $question->id }}" value="false" 
                                   class="mr-3 text-blue-600 focus:ring-blue-500"
                                   {{ (isset($answers[$question->id]) && $answers[$question->id]->user_answer === 'false') ? 'checked' : '' }}>
                            <span class="text-gray-900">Noto'g'ri</span>
                        </label>
                    </div>

                @elseif($question->question_type === 'essay')
                    <div class="space-y-4">
                        <textarea name="question_{{ $question->id }}" rows="8" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Javobingizni yozing...">{{ isset($answers[$question->id]) ? $answers[$question->id]->user_answer : '' }}</textarea>
                        <p class="text-sm text-gray-500">Minimal 150 so'z yozing.</p>
                    </div>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Navigation Buttons -->
        <div id="test-navigation" class="flex justify-between mt-8 {{ strtolower($test->category->name) === 'listening' || strtolower($test->category->name) === 'reading' || strtolower($test->category->name) === 'writing' ? '' : 'hidden' }}">
            <button id="prev-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-lg hidden">
                <i class="fas fa-arrow-left mr-2"></i>
                Oldingi
            </button>
            
            <div class="flex space-x-4">
                <button id="next-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Keyingi
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
                
                <button id="finish-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg hidden">
                    <i class="fas fa-check mr-2"></i>
                    Testni Yakunlash
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Asosiy o'zgaruvchilar
let currentQuestion = 1;
const totalQuestions = {{ $test->questions->count() }};
const testId = {{ $test->id }};
const attemptId = {{ $attempt->id }};

@if($test->time_limit)
// Timer o'zgaruvchilari
let timeRemaining = {{ $test->time_limit }} * 60; // sekundlarda
let timer;

// Test kategoriyasini aniqlash
const testCategory = "{{ strtolower($test->category->name ?? '') }}";
console.log('Test kategoriyasi:', testCategory);

// Timer funksiyasi - eng sodda usulda
function startTimer() {
    // Timer statusini yangilash
    document.getElementById('timer-status').textContent = 'Vaqt ketmoqda';
    document.getElementById('timer-status').classList.remove('bg-gray-100', 'text-gray-800');
    document.getElementById('timer-status').classList.add('bg-red-100', 'text-red-800');
    
    // Timer intervali
    timer = setInterval(function() {
        // Vaqtni kamaytirish
        timeRemaining--;
        
        // Daqiqa va sekundlarni hisoblash
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        // Timer matnini yangilash
        document.getElementById('timer').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        
        // Konsol logga yozish
        console.log('Timer:', minutes + ':' + seconds);
        
        // Vaqt tugaganda
        if (timeRemaining <= 0) {
            clearInterval(timer);
            finishTest();
        }
    }, 1000);
}

// Reading, Listening va Writing test turlari uchun avtomatik timer boshlash
if (testCategory === 'reading' || testCategory === 'listening' || testCategory === 'writing') {
    // Sahifa yuklanganda timer boshlash
    window.onload = function() {
        console.log('Sahifa yuklandi, timer boshlanmoqda...');
        startTimer();
    };
} else {
    // Boshqa test turlari uchun tugma bosilganda timer boshlash
    document.getElementById('start-test-btn').onclick = function() {
        startTimer();
        document.getElementById('test-intro').classList.add('hidden');
        document.getElementById('test-questions').classList.remove('hidden');
        document.getElementById('test-navigation').classList.remove('hidden');
    };
}
@endif

// Savollar navigatsiyasi
function showQuestion(questionNum) {
    // Barcha savollarni yashirish
    document.querySelectorAll('.question-container').forEach(function(container) {
        container.classList.add('hidden');
    });
    
    // Kerakli savolni ko'rsatish
    document.querySelector(`[data-question="${questionNum}"]`).classList.remove('hidden');
    
    // Joriy savol raqamini yangilash
    document.getElementById('current-question').textContent = questionNum;
    
    // Progress bar yangilash
    const progressPercent = (questionNum / totalQuestions) * 100;
    document.getElementById('progress-bar').style.width = progressPercent + '%';
    
    // Tugmalar ko'rinishini yangilash
    document.getElementById('prev-btn').classList.toggle('hidden', questionNum === 1);
    document.getElementById('next-btn').classList.toggle('hidden', questionNum === totalQuestions);
    document.getElementById('finish-btn').classList.toggle('hidden', questionNum !== totalQuestions);
}

// Javobni saqlash
function saveCurrentAnswer() {
    const questionContainer = document.querySelector(`[data-question="${currentQuestion}"]`);
    const inputs = questionContainer.querySelectorAll('input[type="radio"]:checked, input[type="text"], textarea');
    
    inputs.forEach(function(input) {
        if (input.value.trim() !== '') {
            const questionId = input.name.replace('question_', '');
            
            fetch(`{{ route('tests.save-answer', ['test' => $test->slug, 'attempt' => $attempt->id]) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer: input.value
                })
            });
        }
    });
}

// Testni yakunlash
function finishTest() {
    fetch(`{{ route('tests.submit', ['test' => $test->slug, 'attempt' => $attempt->id]) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(function() {
        window.location.href = `{{ route('tests.result', ['test' => $test->slug, 'attempt' => $attempt->id]) }}`;
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('Event listenerlar qoshilmoqda...');
    
    // Oldingi savol tugmasi
    const prevBtn = document.getElementById('prev-btn');
    if (prevBtn) {
        prevBtn.onclick = function() {
            if (currentQuestion > 1) {
                saveCurrentAnswer();
                currentQuestion--;
                showQuestion(currentQuestion);
            }
        };
    }

    // Keyingi savol tugmasi
    const nextBtn = document.getElementById('next-btn');
    if (nextBtn) {
        nextBtn.onclick = function() {
            if (currentQuestion < totalQuestions) {
                saveCurrentAnswer();
                currentQuestion++;
                showQuestion(currentQuestion);
            }
        };
    }

    // Testni yakunlash tugmasi
    const finishBtn = document.getElementById('finish-btn');
    if (finishBtn) {
        finishBtn.onclick = function() {
            saveCurrentAnswer();
            finishTest();
        };
    }

    // Javoblar ozgarishini kuzatish
    document.addEventListener('change', function(e) {
        if (e.target.name && e.target.name.startsWith('question_')) {
            saveCurrentAnswer();
        }
    });
});
</script>
@endsection