{{-- resources/views/tests/listening/listening-test.blade.php da qo'shish kerak --}}

@extends('layouts.student')

@section('title', $test->title . ' - IELTS Listening Test')
@section('description', 'IELTS Listening Test - All Parts')

{{-- CSS Files --}}
@push('styles')
   
@endpush

{{-- JavaScript Files --}}
@push('scripts')
  
@endpush

@section('content')
<link rel="stylesheet" href="{{ asset('css/listening-test.css') }}">
<link rel="stylesheet" href="{{ asset('css/enhanced-dragdrop.css') }}">


<div class="container listening-test-container">
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
    <div class="header">
        <div>
            <h1>{{ $test->title }}</h1>
            <div>IELTS Listening Test</div>
        </div>
        <div class="timer" id="timer" data-time-seconds="{{ $test->duration_minutes * 60 }}">{{ $test->duration_minutes }}:00</div>
    </div>

    <!-- Parts Navigation -->
    <div class="parts-nav">
        <button id="part1-btn" class="part-btn active" onclick="showPart(1)">Part 1</button>
        <button id="part2-btn" class="part-btn" onclick="showPart(2)">Part 2</button>
        <button id="part3-btn" class="part-btn" onclick="showPart(3)">Part 3</button>
        <button id="part4-btn" class="part-btn" onclick="showPart(4)">Part 4</button>
    </div>

    <!-- Main Content -->
    <form action="{{ route('listening.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" id="testForm">
        @csrf
        <div class="main-content">
            <!-- Audio Player (Common for all parts) -->
            <div class="audio-panel">
                <div class="audio-player">
                    <h3>Audio Player</h3>
                    <div class="audio-controls">
                        <button type="button" class="play-btn" id="playBtn">▶</button>
                        <div class="volume-control">
                            <span>🔊</span>
                            <input type="range" class="volume-slider" min="0" max="100" value="70">
                        </div>
                    </div>
                    
                    <!-- Audio Loading Indicator -->
                    <div id="audioLoadingIndicator" style="display: none; margin: 10px 0; flex-direction: column; align-items: center;">
                        <div style="width: 100%; background-color: #e0e0e0; border-radius: 5px; overflow: hidden; margin-bottom: 5px;">
                            <div id="audioLoadingProgress" style="background-color: #3498db; height: 20px; color: white; text-align: center; line-height: 20px; font-size: 12px; transition: width 0.3s;">0%</div>
                        </div>
                        <div style="font-size: 12px; color: #666;">Audio yuklanmoqda...</div>
                    </div>
                    
                    <audio id="audioPlayer" controls style="width: 100%; margin-top: 1rem;">
                        <source src="{{ $audioFiles['part1'] }}" type="audio/mpeg" id="audioSource">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                
                <div style="background: white; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <h4>Instructions</h4>
                    <ul class="text-sm">
                        <li>Listen carefully to the audio</li>
                        <li>You can only play the audio ONCE</li>
                        <li>Answer all questions</li>
                        <li>Press Enter to move to next field</li>
                        <li>Answers are auto-saved</li>
                    </ul>
                </div>
                
                <div style="background: #e8f4fd; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <h4>Progress</h4>
                    <div id="progressText">0/40 questions answered</div>
                    <div style="margin-top: 0.5rem;">
                        <div style="background: #e0e0e0; height: 10px; border-radius: 5px;">
                            <div id="progressBar" style="background: #3498db; height: 100%; width: 0%; border-radius: 5px; transition: width 0.3s;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Part 1 Questions -->
            <div id="part1-content" class="part-content active">
                <div class="question-panel">
                    <div class="question-instruction">
                        <strong>Questions 1-10</strong><br>
                        Complete the notes below. Write <strong>ONE WORD AND/OR A NUMBER</strong> for each answer.<br><br>
                        <strong>Section 1: Questions 1-10</strong>
                    </div>
                    
                    <!-- Dynamic Questions for Part 1 -->
                    @php $questionNumber = 1; @endphp
                    @foreach($part1Questions as $question)
                        {!! $questionRenderer->render($question, $questionNumber++, $userAnswers) !!}
                    @endforeach
                </div>
            </div>
            
            <!-- Part 2 Questions -->
            <div id="part2-content" class="part-content">
                <div class="question-panel">
                    <div class="question-instruction">
                        <strong>Questions 11-20</strong><br>
                        Complete the form below. Write <strong>NO MORE THAN THREE WORDS AND/OR A NUMBER</strong> for each answer.<br><br>
                        <strong>Section 2: Questions 11-20</strong>
                    </div>
                    
                    <!-- Dynamic Questions for Part 2 -->
                    @php $questionNumber = 11; @endphp
                    @foreach($part2Questions as $question)
                        {!! $questionRenderer->render($question, $questionNumber++, $userAnswers) !!}
                    @endforeach
                </div>
            </div>
            
            <!-- Part 3 Questions -->
            <div id="part3-content" class="part-content">
                <div class="question-panel">
                    <div class="question-instruction">
                        <strong>Questions 21-30</strong><br>
                        Choose the correct letter, A, B or C.<br><br>
                        <strong>Section 3: Questions 21-30</strong>
                    </div>
                    
                    <!-- Dynamic Questions for Part 3 -->
                    @php $questionNumber = 21; @endphp
                    @foreach($part3Questions as $question)
                        {!! $questionRenderer->render($question, $questionNumber++, $userAnswers) !!}
                    @endforeach
                </div>
            </div>
            
            <!-- Part 4 Questions -->
            <div id="part4-content" class="part-content">
                <div class="question-panel">
                    <div class="question-instruction">
                        <strong>Questions 31-40</strong><br>
                        Complete the sentences below. Write <strong>NO MORE THAN TWO WORDS</strong> for each answer.<br><br>
                        <strong>Section 4: Questions 31-40</strong>
                    </div>
                    
                    <!-- Dynamic Questions for Part 4 -->
                    @php $questionNumber = 31; @endphp
                    @foreach($part4Questions as $question)
                        {!! $questionRenderer->render($question, $questionNumber++, $userAnswers) !!}
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
            <button type="button" id="prevBtn" onclick="navigatePart(-1)" class="nav-btn">
                <i class="fas fa-arrow-left mr-2"></i> Oldingi
            </button>
            <button type="button" id="nextBtn" onclick="navigatePart(1)" class="nav-btn">
                Keyingi <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit" class="submit-btn">
                Testni yakunlash <i class="fas fa-check-circle ml-2"></i>
            </button>
        </div>
    </form>
</div>
<script src="{{ asset('js/listening-test.js') }}"></script>
<script src="{{ asset('js/enhanced-dragdrop.js') }}"></script>

<script>
    // Pass Laravel variables to JavaScript
    window.audioFiles = {
        part1: "{{ $audioFiles['part1'] }}",
        part2: "{{ $audioFiles['part2'] }}",
        part3: "{{ $audioFiles['part3'] }}",
        part4: "{{ $audioFiles['part4'] }}"
    };
    
    window.routes = {
        saveAnswer: "{{ route('listening.save-answer', ['test' => $test->slug, 'attempt' => $attempt->id]) }}"
    };
    
    window.csrf_token = "{{ csrf_token() }}";

    // Enhanced Drag Drop Event Listeners
    document.addEventListener('enhancedDragDropAnswer', function(e) {
        console.log('Drag & Drop Answer:', e.detail);
        // Additional handling if needed
    });

    document.addEventListener('enhancedDragDropProgress', function(e) {
        console.log('Drag & Drop Progress:', e.detail);
        // Update global progress if needed
        updateProgress();
    });

    document.addEventListener('enhancedDragDropCheck', function(e) {
        console.log('Drag & Drop Check Results:', e.detail);
        // Handle check results
    });
</script>

@endsection