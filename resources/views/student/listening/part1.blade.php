@extends('layouts.student')

@section('title', $test->title . ' - Listening Part 1')
@section('description', 'IELTS Listening Test - Part 1')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

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
            <div>Part 1: Questions 1-10</div>
        </div>
        <div class="timer" id="timer">30:00</div>
    </div>

    <!-- Navigation Section -->
    <div class="nav-section">
        Listening &gt; Part 1 &gt; Questions 1-10
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Question Panel -->
        <div class="question-panel">
            <!-- Question Instructions -->
            <div class="question-instruction">
                <strong>Questions 1-10</strong><br>
                Complete the notes below. Write <strong>ONE WORD AND/OR A NUMBER</strong> for each answer.<br><br>
                <strong>Phone call about second-hand furniture</strong>
            </div>

            <!-- Section 1: Gap-fill Questions -->
            <h2 style="margin-bottom: 1rem; color: #2c3e50;">Items:</h2>

            <!-- Question 1 -->
            <div class="question-block">
                <div class="text-passage">
                    <strong>Dining table:</strong><br>
                    - shape: <input type="text" class="answer-input" data-question="1" placeholder="1"><br>
                    - medium size<br>
                    - <input type="text" class="answer-input" data-question="2" placeholder="2"> old<br>
                    - price: £25.00
                </div>
            </div>

            <!-- Question 2 -->
            <div class="question-block">
                <div class="text-passage">
                    <strong>Dining chairs:</strong><br>
                    - set of <input type="text" class="answer-input" data-question="3" placeholder="3"> chairs<br>
                    - seats covered in <input type="text" class="answer-input" data-question="4" placeholder="4"> material<br>
                    - in <input type="text" class="answer-input" data-question="5" placeholder="5"> condition<br>
                    - price: £20.00
                </div>
            </div>

            <!-- Question 3 -->
            <div class="question-block">
                <div class="text-passage">
                    <strong>Desk:</strong><br>
                    - length: 1 metre 20<br>
                    - 3 drawers. Top drawer has a <input type="text" class="answer-input" data-question="6" placeholder="6"><br>
                    - price: £<input type="text" class="answer-input" data-question="7" placeholder="7">
                </div>
            </div>

            <!-- Address Section -->
            <div class="question-block">
                <div class="text-passage">
                    <strong>Address:</strong><br>
                    <input type="text" class="answer-input" data-question="8" placeholder="8"> Old Lane, Stonethorpe
                </div>
            </div>

            <!-- Directions Section -->
            <div class="question-block">
                <div class="text-passage">
                    <strong>Directions:</strong><br>
                    Take the Hawcroft road out of Stonethorpe. Go past the secondary school, then turn <input type="text" class="answer-input" data-question="9" placeholder="9"> at the crossroads. House is down this road, opposite the <input type="text" class="answer-input" data-question="10" placeholder="10">.
                </div>
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
                    <source src="{{ asset('audio/listening-part1.mp3') }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>

            <div style="background: white; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                <h4>Instructions</h4>
                <ul style="margin-top: 0.5rem; padding-left: 1.5rem; font-size: 0.9rem;">
                    <li>Listen to the audio carefully</li>
                    <li>Type your answers in the input fields</li>
                    <li>Press Enter to move to next field</li>
                    <li>Answers are auto-saved</li>
                    <li>ONE WORD AND/OR NUMBER per answer</li>
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
                <a href="{{ route('student.tests') }}" class="text-gray-600 hover:text-gray-800 py-2 px-4 border border-gray-300 rounded-lg font-medium flex items-center">
                    <i class="fas fa-list mr-2"></i> All Tests
                </a>
            </div>
            <div class="progress-bar mx-4 flex-grow">
                <div class="progress-fill" style="width: 25%;"></div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('listening.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg font-bold flex items-center">
                    Continue to Part 2 <i class="fas fa-arrow-right ml-2"></i>
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

.text-passage {
    line-height: 1.8;
}

.answer-input {
    display: inline-block;
    width: 120px;
    padding: 5px 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    margin: 0 5px;
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

/* Drag and Drop Styles */
.people-list, .responsibilities-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.person-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.person-label {
    min-width: 120px;
    font-weight: 500;
}

.drop-zone {
    width: 150px;
    height: 40px;
    border: 2px dashed #ddd;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9f9f9;
    transition: all 0.3s;
}

.drop-zone.active {
    border-color: #3498db;
    background-color: #ebf5fb;
}

.draggable-item {
    padding: 10px 15px;
    background-color: #e8f4fd;
    border: 1px solid #b3d7ff;
    border-radius: 5px;
    cursor: move;
    transition: all 0.3s;
}

.draggable-item:hover {
    background-color: #d4e9fc;
}

.placeholder {
    color: #aaa;
}

/* Map Styles */
.map-container {
    margin-bottom: 20px;
}

.map-input {
    font-size: 12px;
    padding: 3px 5px;
    width: 80px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const audioPlayer = document.getElementById('audioPlayer');
    const playBtn = document.getElementById('playBtn');
    const volumeSlider = document.querySelector('.volume-slider');
    const answerInputs = document.querySelectorAll('.answer-input');
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
    
    // Track answered questions
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
        // Log to console for debugging
        console.log(`Saving answer for question ${questionNumber}: ${answer}`);
        
        // Make AJAX call to save the answer
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
    }
});
</script>
@endsection
