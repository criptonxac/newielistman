@extends('layouts.student')

@section('title', 'IELTS Reading Test')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Test Header -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">{{ $test->title }}</h1>
            <div class="text-gray-600 text-sm">IELTS Academic Reading Test</div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="timer-container">
                <span class="text-lg font-mono" id="timer" data-time-seconds="{{ $test->time_limit * 60 }}">60:00</span>
            </div>
            <div class="flex space-x-2">
                <button id="prevPartBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hidden" onclick="previousPart()">
                    <i class="fas fa-arrow-left mr-2"></i> Oldingi
                </button>
                <button id="nextPartBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md" onclick="nextPart()">
                    Keyingi <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Test Navigation -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <div class="flex space-x-4">
            <button id="part1-btn" class="part-button px-4 py-2 bg-blue-600 text-white rounded-md" data-part="1" onclick="showPart(1)">Part 1</button>
            <button id="part2-btn" class="part-button px-4 py-2 bg-gray-200 text-gray-700 rounded-md" data-part="2" onclick="showPart(2)">Part 2</button>
            <button id="part3-btn" class="part-button px-4 py-2 bg-gray-200 text-gray-700 rounded-md" data-part="3" onclick="showPart(3)">Part 3</button>
        </div>
    </div>

    <!-- Test Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Passage Panel (Left Side) -->
        <div class="bg-white rounded-lg shadow-md p-4 passage-container">
            <div class="overflow-y-auto" style="height: calc(100vh - 300px);">
                <!-- Part 1 Content -->
                <div id="part1-content" class="test-content active">
                    <h2 class="text-xl font-bold mb-4">{{ isset($passages[0]) ? $passages[0]->title : 'Reading Passage 1' }}</h2>
                    <div class="prose max-w-none reading-passage">
                        {!! isset($passages[0]) ? $passages[0]->content : '<p>Passage content not available</p>' !!}
                    </div>
                </div>
                
                <!-- Part 2 Content -->
                <div id="part2-content" class="test-content hidden">
                    <h2 class="text-xl font-bold mb-4">{{ isset($passages[1]) ? $passages[1]->title : 'Reading Passage 2' }}</h2>
                    <div class="prose max-w-none reading-passage">
                        {!! isset($passages[1]) ? $passages[1]->content : '<p>Passage content not available</p>' !!}
                    </div>
                </div>
                
                <!-- Part 3 Content -->
                <div id="part3-content" class="test-content hidden">
                    <h2 class="text-xl font-bold mb-4">{{ isset($passages[2]) ? $passages[2]->title : 'Reading Passage 3' }}</h2>
                    <div class="prose max-w-none reading-passage">
                        {!! isset($passages[2]) ? $passages[2]->content : '<p>Passage content not available</p>' !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Panel (Right Side) -->
        <div class="bg-white rounded-lg shadow-md p-4 questions-panel">
            <form id="reading-test-form" action="{{ route('reading.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST">
                @csrf
                <div class="overflow-y-auto questions-container" style="height: calc(100vh - 300px);">
                    <!-- Part 1 Questions -->
                    <div id="part1-questions" class="questions-content active">
                        <div class="font-bold text-lg mb-4">Questions 1-13</div>
                        <div class="space-y-6">
                            @foreach($part1Questions as $question)
                                <div class="question-item mb-4">
                                    {!! $questionRenderer->render($question, $question->question_number, $userAnswers) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Part 2 Questions -->
                    <div id="part2-questions" class="questions-content hidden">
                        <div class="font-bold text-lg mb-4">Questions 14-26</div>
                        <div class="space-y-6">
                            @foreach($part2Questions as $question)
                                <div class="question-item mb-4">
                                    {!! $questionRenderer->render($question, $question->question_number, $userAnswers) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Part 3 Questions -->
                    <div id="part3-questions" class="questions-content hidden">
                        <div class="font-bold text-lg mb-4">Questions 27-40</div>
                        <div class="space-y-6">
                            @foreach($part3Questions as $question)
                                <div class="question-item mb-4">
                                    {!! $questionRenderer->render($question, $question->question_number, $userAnswers) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button type="button" id="prevBtn" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors hidden">
                        <i class="fas fa-arrow-left mr-2"></i> Oldingi
                    </button>
                    <button type="button" id="nextBtn" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Keyingi <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="finishBtn" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors hidden">
                        Yakunlash <i class="fas fa-check ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="bg-white shadow-md rounded-lg p-4 mt-6">
        <div class="flex justify-between items-center mb-2">
            <span id="progressText">0/40 questions answered</span>
            <span id="timeRemaining" class="font-mono">60:00</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
        </div>
    </div>
</div>

<!-- Context Menu for Highlighting -->
<div id="context-menu" class="context-menu hidden">
    <div class="context-menu-item" data-action="highlight">
        <i class="fas fa-highlighter"></i> Highlight
    </div>
    <div class="context-menu-item" data-action="note">
        <i class="fas fa-sticky-note"></i> Add Note
    </div>
    <div class="context-menu-item" data-action="clear">
        <i class="fas fa-eraser"></i> Clear
    </div>
    <div class="context-menu-item" data-action="clearAll">
        <i class="fas fa-trash"></i> Clear All
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/reading-test.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Reading test page loaded');
        
        // Initialize highlighting
        if (typeof initializeHighlighting === 'function') {
            initializeHighlighting();
        }
        
        // Start timer
        if (typeof startTimer === 'function') {
            startTimer();
        }
        
        // Initialize input handling
        if (typeof initializeInputHandling === 'function') {
            initializeInputHandling();
        }
        
        // Initialize radio handling
        if (typeof initializeRadioHandling === 'function') {
            initializeRadioHandling();
        }
        
        // Initialize drag and drop functionality
        if (typeof initializeDragAndDrop === 'function') {
            initializeDragAndDrop();
        }
    });
</script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/ielts-custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/reading-test.css') }}">
@endsection
