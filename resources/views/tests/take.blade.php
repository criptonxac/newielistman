@extends('layouts.main')

@section('title', $test->title . ' - Test')
@section('description', 'IELTS ' . $test->category->name . ' testini topshiring.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Test Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $test->title }}</h1>
                <p class="text-gray-600">{{ $test->category->name }}</p>
            </div>
            
            @if($test->time_limit)
            <div class="text-right">
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-clock mr-2"></i>
                    <span id="timer">{{ $test->time_limit }}:00</span>
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

    <!-- Questions Container -->
    <div class="bg-white rounded-xl shadow-lg p-8">
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
                        @php $options = json_decode($question->options, true) ?? [] @endphp
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
        <div class="flex justify-between mt-8">
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
let currentQuestion = 1;
const totalQuestions = {{ $test->questions->count() }};
const testId = {{ $test->id }};
const attemptId = {{ $attempt->id }};

// Timer functionality
@if($test->time_limit)
let timeRemaining = {{ $test->time_limit }} * 60; // seconds
const timer = setInterval(function() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById('timer').textContent = 
        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    
    if (timeRemaining <= 0) {
        clearInterval(timer);
        finishTest();
    }
    timeRemaining--;
}, 1000);
@endif

// Navigation functions
function showQuestion(questionNum) {
    document.querySelectorAll('.question-container').forEach(container => {
        container.classList.add('hidden');
    });
    
    document.querySelector(`[data-question="${questionNum}"]`).classList.remove('hidden');
    
    document.getElementById('current-question').textContent = questionNum;
    
    const progressPercent = (questionNum / totalQuestions) * 100;
    document.getElementById('progress-bar').style.width = progressPercent + '%';
    
    // Button visibility
    document.getElementById('prev-btn').classList.toggle('hidden', questionNum === 1);
    document.getElementById('next-btn').classList.toggle('hidden', questionNum === totalQuestions);
    document.getElementById('finish-btn').classList.toggle('hidden', questionNum !== totalQuestions);
}

// Event listeners
document.getElementById('prev-btn').addEventListener('click', function() {
    if (currentQuestion > 1) {
        currentQuestion--;
        showQuestion(currentQuestion);
    }
});

document.getElementById('next-btn').addEventListener('click', function() {
    saveCurrentAnswer();
    if (currentQuestion < totalQuestions) {
        currentQuestion++;
        showQuestion(currentQuestion);
    }
});

document.getElementById('finish-btn').addEventListener('click', function() {
    saveCurrentAnswer();
    finishTest();
});

// Save answer function
function saveCurrentAnswer() {
    const questionContainer = document.querySelector(`[data-question="${currentQuestion}"]`);
    const inputs = questionContainer.querySelectorAll('input[type="radio"]:checked, input[type="text"], textarea');
    
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            const questionId = input.name.replace('question_', '');
            
            fetch(`{{ route('tests.submit-answer', ['test' => $test->slug, 'attempt' => $attempt->id]) }}`, {
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

// Finish test function  
function finishTest() {
    fetch(`{{ route('tests.complete', ['test' => $test->slug, 'attempt' => $attempt->id]) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => {
        window.location.href = `{{ route('tests.results', ['test' => $test->slug, 'attempt' => $attempt->id]) }}`;
    });
}

// Auto-save on input change
document.addEventListener('change', function(e) {
    if (e.target.name && e.target.name.startsWith('question_')) {
        saveCurrentAnswer();
    }
});
</script>
@endsection