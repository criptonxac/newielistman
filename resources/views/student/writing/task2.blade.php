@extends('layouts.student')

@section('title', 'Writing Test - Task 2')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Test Categories Navigation -->
    <div class="bg-blue-600 text-white py-4 mb-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('categories.index') }}" class="text-white hover:text-blue-200 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-list-ul mr-1"></i> Kategoriyalar
                    </a>
                    <a href="{{ route('student.tests') }}" class="text-white hover:text-blue-200 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-file-alt mr-1"></i> Barcha Testlar
                    </a>
                    <a href="{{ route('student.results') }}" class="text-white hover:text-blue-200 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-chart-line mr-1"></i> Natijalar
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">IELTS Academic Writing</h1>
                <div class="text-gray-600">Task 2 - Essay</div>
            </div>
            <div class="text-2xl font-bold text-blue-600" id="timer">40:00</div>
        </div>
    </div>

    <!-- Navigation Section -->
    <div class="bg-gray-100 rounded-lg p-4 mb-6 text-sm text-gray-600">
        Writing &gt; Task 2 &gt; Essay
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Task Panel -->
        <div class="bg-white rounded-lg shadow-md p-6 md:w-1/2">
            <div class="overflow-y-auto max-h-[70vh]">
                <h2 class="text-xl font-bold mb-4">Writing Task 2</h2>
                
                <div class="mb-4 text-gray-700">
                    You should spend about <strong>40 minutes</strong> on this task. Write at least <strong>250 words</strong>.
                </div>

                <div class="mb-6">
                    <p class="font-bold mb-4">Some people believe that studying online is more effective than attending classes in person at educational institutions. To what extent do you agree or disagree with this statement?</p>
                    
                    <p class="mb-4">Give reasons for your answer and include any relevant examples from your own knowledge or experience.</p>
                </div>

                <div class="mb-4">
                    <h3 class="font-bold mb-2">Key Requirements:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-gray-700">
                        <li>Write at least 250 words</li>
                        <li>State your position clearly</li>
                        <li>Support your arguments with examples</li>
                        <li>Organize your essay with a clear introduction, body paragraphs, and conclusion</li>
                        <li>Use a range of vocabulary and grammatical structures</li>
                    </ul>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-6">
                    <h3 class="font-bold mb-2">Essay Structure Tips:</h3>
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Introduction:</strong> Paraphrase the question and state your position</p>
                        <p><strong>Body Paragraph 1:</strong> First main point with supporting details</p>
                        <p><strong>Body Paragraph 2:</strong> Second main point with supporting details</p>
                        <p><strong>Body Paragraph 3 (optional):</strong> Counter-argument or third point</p>
                        <p><strong>Conclusion:</strong> Summarize your position and main points</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Writing Panel -->
        <div class="bg-white rounded-lg shadow-md p-6 md:w-1/2">
            <form action="{{ route('writing.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="next_route" value="">
                
                <div class="mb-4">
                    <label for="essay" class="block font-bold mb-2">Your Response:</label>
                    <textarea 
                        id="essay" 
                        name="answers[task2]" 
                        class="w-full h-96 p-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Write your essay here (minimum 250 words)..."
                    ></textarea>
                </div>
                
                <div class="flex justify-between items-center mt-6">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <div id="wordCount">0 words</div>
                            <div>Minimum required: 250 words</div>
                        </div>
                        <a href="{{ route('writing.task1', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="text-blue-600 hover:underline">
                            &larr; Back to Task 1
                        </a>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg font-bold">
                        Complete Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Timer functionality
    document.addEventListener('DOMContentLoaded', function() {
        let timeLeft = 40 * 60; // 40 minutes in seconds
        const timerElement = document.getElementById('timer');
        
        const timer = setInterval(function() {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                alert('Time is up! Submitting your test.');
                document.querySelector('form').submit();
            }
        }, 1000);
        
        // Word count functionality
        const textarea = document.getElementById('essay');
        const wordCountElement = document.getElementById('wordCount');
        
        textarea.addEventListener('input', function() {
            const text = textarea.value;
            const wordCount = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
            wordCountElement.textContent = `${wordCount} words`;
            
            if (wordCount >= 250) {
                wordCountElement.classList.add('text-green-600');
                wordCountElement.classList.remove('text-red-600');
            } else {
                wordCountElement.classList.add('text-red-600');
                wordCountElement.classList.remove('text-green-600');
            }
        });
    });
</script>
@endpush
@endsection
