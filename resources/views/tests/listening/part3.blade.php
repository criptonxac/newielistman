@extends('layouts.student')

@section('title', $test->title . ' - Listening Part 3')
@section('description', 'IELTS Listening Test - Part 3')

@section('content')
<div class="container">
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
            <div>Part 3: Questions 21-30</div>
        </div>
        <div class="timer" id="timer">30:00</div>
    </div>

    <!-- Navigation Section -->
    <div class="nav-section">
        Listening &gt; Part 3 &gt; Questions 21-30
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Question Panel -->
        <div class="question-panel">
            <!-- Question Instructions -->
            <div class="question-instruction">
                <strong>Questions 21-25</strong><br>
                Choose the correct letter, A, B or C.<br><br>
                <strong>UNIVERSITY RESEARCH PROJECT DISCUSSION</strong>
            </div>

            <!-- Multiple Choice Questions -->
            <form action="{{ route('listening.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Dynamic Questions -->
                @php $questionNumber = 21; @endphp
                @foreach($questions as $question)
                    @if($question->question_type == 'fill_blank' || $question->question_type == 'multiple_choice')
                        {!! \App\Services\QuestionRenderer::render($question, $questionNumber++, $userAnswers) !!}
                    @endif
                @endforeach
                
                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Submit Answers
                    </button>
                </div>
            </form>
                        </label>
                        <label class="option">
                            <input type="radio" name="q23" value="C" data-question="23">
                            <span>C. The scope is too broad</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="question-block">
                <div class="question">
                    <p><strong>24. What does Sarah suggest they do to improve their research?</strong></p>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="q24" value="A" data-question="24">
                            <span>A. Focus on a specific geographical area</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="q24" value="B" data-question="24">
                            <span>B. Increase their budget</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="q24" value="C" data-question="24">
                            <span>C. Collaborate with other research teams</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="question-block">
                <div class="question">
                    <p><strong>25. What deadline does Professor Wilson give for the first draft of the research proposal?</strong></p>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="q25" value="A" data-question="25">
                            <span>A. Two weeks</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="q25" value="B" data-question="25">
                            <span>B. One month</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="q25" value="C" data-question="25">
                            <span>C. Six weeks</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Question Instructions for 26-30 -->
            <div class="question-instruction" style="margin-top: 30px;">
                <strong>Questions 26-30</strong><br>
                Complete the table below.<br>
                Write <strong>NO MORE THAN TWO WORDS</strong> for each answer.<br><br>
                <strong>RESEARCH PROJECT TASKS</strong>
            </div>

            <!-- Table Fill Questions -->
            <div class="question-block">
                <table class="task-table">
                    <thead>
                        <tr>
                            <th>Team Member</th>
                            <th>Primary Responsibility</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sarah</td>
                            <td>Prepare <input type="text" class="answer-input" data-question="26" placeholder="26"></td>
                            <td>End of the month</td>
                        </tr>
                        <tr>
                            <td>Michael</td>
                            <td>Contact <input type="text" class="answer-input" data-question="27" placeholder="27"></td>
                            <td>Next Friday</td>
                        </tr>
                        <tr>
                            <td>Professor Wilson</td>
                            <td>Review <input type="text" class="answer-input" data-question="28" placeholder="28"></td>
                            <td>Within 48 hours</td>
                        </tr>
                        <tr>
                            <td>All team</td>
                            <td>Finalize <input type="text" class="answer-input" data-question="29" placeholder="29"></td>
                            <td><input type="text" class="answer-input" data-question="30" placeholder="30"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Audio Panel -->
        <div class="audio-panel">
            <div class="audio-player">
                <h3>Audio Player</h3>
                <div class="audio-controls">
                    <button class="play-btn" id="playBtn">▶</button>
                    <div class="volume-control">
                        <span>🔊</span>
                        <input type="range" class="volume-slider" min="0" max="100" value="70">
                    </div>
                </div>
                <audio id="audioPlayer" controls style="width: 100%; margin-top: 1rem;">
                    <source src="{{ asset('audio/listening-part3.mp3') }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>

            <div style="background: white; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                <h4>Instructions</h4>
                <ul style="margin-top: 0.5rem; padding-left: 1.5rem; font-size: 0.9rem;">
                    <li>Listen to the audio carefully</li>
                    <li>Select one option for questions 21-25</li>
                    <li>Fill in the blanks for questions 26-30</li>
                    <li>Answers are auto-saved</li>
                    <li>NO MORE THAN TWO WORDS per answer for table questions</li>
                </ul>
            </div>

            <div style="background: #e8f4fd; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                <h4>Progress</h4>
                <div id="progressText">0/10 questions answered</div>
                <div style="margin-top: 0.5rem;">
                    <div style="background: #ddd; height: 10px; border-radius: 5px;">
                        <div id="progressBar" style="background: #3498db; height: 100%; width: 0%; border-radius: 5px; transition: width 0.3s;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress and Navigation -->
    <div class="progress-section">
        <div class="flex justify-between items-center w-full py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('listening.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-6 rounded-lg font-medium flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Part 2
                </a>
            </div>
            <div class="progress-bar mx-4 flex-grow">
                <div class="progress-fill" style="width: 75%;"></div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.tests') }}" class="text-gray-600 hover:text-gray-800 py-2 px-4 border border-gray-300 rounded-lg font-medium flex items-center">
                    <i class="fas fa-list mr-2"></i> All Tests
                </a>
                <a href="{{ route('listening.part4', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg font-bold flex items-center">
                    Continue to Part 4 <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Base Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f7fa;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.header h1 {
    margin: 0;
    font-size: 24px;
    color: #2c3e50;
}

.timer {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
    padding: 8px 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
    border: 1px solid #ddd;
}

/* Navigation Section */
.nav-section {
    background-color: #f8f9fa;
    padding: 10px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    color: #6c757d;
    font-size: 14px;
}

/* Main Content Layout */
.main-content {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.question-panel {
    flex: 3;
    background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.audio-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Question Styles */
.question-instruction {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #3498db;
}

.question-block {
    margin-bottom: 25px;
}

.question {
    margin-bottom: 20px;
}

.options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
}

.option {
    display: flex;
    align-items: flex-start;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.option:hover {
    background-color: #f5f5f5;
}

.option input {
    margin-right: 10px;
    margin-top: 3px;
}

/* Table Styles */
.task-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.task-table th, .task-table td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
}

.task-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.task-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.answer-input {
    display: inline-block;
    width: 120px;
    padding: 5px 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.answer-input:focus {
    border-color: #3498db;
    outline: none;
}

/* Audio Player Styles */
.audio-player {
    background-color: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.audio-player h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #2c3e50;
}

.audio-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.play-btn {
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}

.play-btn:hover {
    background-color: #2980b9;
}

.volume-control {
    display: flex;
    align-items: center;
    gap: 8px;
}

.volume-slider {
    width: 80px;
}

/* Progress and Navigation */
.progress-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}

.progress-bar {
    flex: 1;
    height: 8px;
    background-color: #e0e0e0;
    border-radius: 4px;
    margin: 0 15px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background-color: #3498db;
    transition: width 0.3s ease;
}

.nav-btn {
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.primary {
    background-color: #3498db;
    color: white;
}

.primary:hover {
    background-color: #2980b9;
}

.secondary {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #ddd;
}

.secondary:hover {
    background-color: #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const audioPlayer = document.getElementById('audioPlayer');
    const playBtn = document.getElementById('playBtn');
    const volumeSlider = document.querySelector('.volume-slider');
    const answerInputs = document.querySelectorAll('.answer-input');
    const radioInputs = document.querySelectorAll('input[type="radio"]');
    let answeredCount = 0;
    const totalQuestions = 10;
    
    // Timer functionality
    let timeRemaining = 30 * 60; // 30 minutes in seconds
    const timerElement = document.getElementById('timer');
    
    const timer = setInterval(function() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        timerElement.textContent = 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
        
        if (timeRemaining <= 0) {
            clearInterval(timer);
            alert('Time is up!');
            // Redirect to results or next part
        }
        
        timeRemaining--;
    }, 1000);
    
    // Audio player controls
    playBtn.addEventListener('click', function() {
        if (audioPlayer.paused) {
            audioPlayer.play();
            playBtn.textContent = '⏸';
        } else {
            audioPlayer.pause();
            playBtn.textContent = '▶';
        }
    });
    
    volumeSlider.addEventListener('input', function() {
        audioPlayer.volume = this.value / 100;
    });
    
    // Track answered questions - text inputs
    answerInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value.trim() !== '' && !this.dataset.counted) {
                answeredCount++;
                this.dataset.counted = 'true';
                updateProgress();
                saveAnswer(this.dataset.question, this.value);
            } else if (this.value.trim() === '' && this.dataset.counted) {
                answeredCount--;
                delete this.dataset.counted;
                updateProgress();
            }
        });
    });
    
    // Track answered questions - radio buttons
    radioInputs.forEach(input => {
        input.addEventListener('change', function() {
            const questionName = this.name;
            const questionNumber = this.dataset.question;
            
            if (!document.querySelector(`input[name="${questionName}"]`).dataset.counted) {
                answeredCount++;
                document.querySelectorAll(`input[name="${questionName}"]`).forEach(radio => {
                    radio.dataset.counted = 'true';
                });
                updateProgress();
                saveAnswer(questionNumber, this.value);
            }
        });
    });
    
    // Update progress display
    function updateProgress() {
        const progressText = document.getElementById('progressText');
        const progressBar = document.getElementById('progressBar');
        const percentage = (answeredCount / totalQuestions) * 100;
        
        progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
        progressBar.style.width = percentage + '%';
    }
    
    // Save answer to server
    function saveAnswer(questionNumber, answer) {
        // Here you would typically make an AJAX call to save the answer
        console.log(`Saving answer for question ${questionNumber}: ${answer}`);
        
        // Example AJAX call (uncomment and modify as needed)
        /*
        fetch('{{ route("listening.save-answer", ["test" => $test->slug, "attempt" => $attempt->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                question_number: questionNumber,
                answer: answer
            })
        })
        .then(response => response.json())
        .then(data => console.log('Success:', data))
        .catch(error => console.error('Error:', error));
        */
    }
});
</script>
@endsection
