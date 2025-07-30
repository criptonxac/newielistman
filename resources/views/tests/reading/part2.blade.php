@extends('layouts.student')

@section('title', 'Reading Test - Part 2')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ielts-custom.css') }}">
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Top navigation bar -->
    <div class="bg-blue-600 text-white py-3">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6">
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
                <div class="text-xl font-bold text-white" id="timer" data-time-seconds="{{ $test->time_limit * 60 }}">{{ sprintf('%02d:%02d', $test->time_limit, 0) }}</div>
            </div>
        </div>
    </div>
    
    <!-- Test header -->
    <div class="bg-gray-100 border-b border-gray-300 py-3">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">IELTS Academic Reading</h1>
                    <div class="text-gray-600 text-sm">Part 2: Questions 14-26</div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('reading.part1', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 1</a>
                    <a href="{{ route('reading.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Part 2</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
            <!-- Passage Panel (Left Side) -->
            <div class="bg-white rounded-lg shadow-md p-4 passage-container">
                <div class="overflow-y-auto" style="height: calc(100vh - 220px);">
                    <h2 class="text-xl font-bold mb-4">{{ $test->title }}</h2>
                    
                    <div class="prose max-w-none">
                        {!! $test->description !!}
                    </div>
                </div>
            </div>

            <!-- Questions Panel (Right Side) -->
            <div class="questions-panel">
                <form action="{{ route('reading.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf
                    <input type="hidden" name="next_route" value="">
                    
                    <div class="overflow-y-auto questions-container" style="height: calc(100vh - 220px); display: block !important;">
                        <!-- Questions 14-26 -->
                        <div class="mb-10">
                            <div class="font-bold text-lg mb-4">Questions 14–26</div>
                            <div class="mb-6 text-gray-700">
                                Answer the questions according to the instructions.
                            </div>
                            
                            <div class="space-y-2">
                                @php $questionNumber = 14; @endphp
                                @foreach($questions as $question)
                                    {!! \App\Services\QuestionRenderer::render($question, $questionNumber++, $userAnswers) !!}
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-10">
                            <a href="{{ route('reading.part1', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Oldingi
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Keyingi <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Part 2 page loaded');
        // Timer functionality
        function startTimer() {
            console.log('Timer ishga tushirilmoqda...');
            const timerElement = document.getElementById('timer');
            if (!timerElement) {
                console.error('Timer elementi topilmadi!');
                return;
            }
            
            // Get time from data attribute
            const totalSeconds = parseInt(timerElement.dataset.timeSeconds) || 3600; // Default to 60 minutes
            console.log('Jami vaqt (sekund):', totalSeconds);
            let minutes, seconds;
            let remainingSeconds = totalSeconds;
            
            // Only start the timer if it hasn't been started yet
            if (!window.timerInterval) {
                window.timerInterval = setInterval(function() {
                    if (remainingSeconds <= 0) {
                        clearInterval(window.timerInterval);
                        console.log('Vaqt tugadi!');
                        
                        // Submit the form when time expires
                        const form = document.querySelector('form');
                        if (form) {
                            console.log('Forma avtomatik topshirilmoqda...');
                            form.submit();
                        }
                        return;
                    }
                    
                    minutes = Math.floor(remainingSeconds / 60);
                    seconds = remainingSeconds % 60;
                    
                    // Change color based on time remaining
                    if (minutes < 10) {
                        timerElement.style.color = '#e74c3c'; // Red when less than 10 minutes
                    } else if (minutes < 20) {
                        timerElement.style.color = '#f39c12'; // Orange when less than 20 minutes
                    }
                    
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    remainingSeconds--;
                    
                    // Save remaining time to localStorage to persist between page navigations
                    localStorage.setItem('readingTestRemainingTime', remainingSeconds);
                }, 1000);
            }
        }
        
        // Check if there's a saved timer value in localStorage
        const savedTime = localStorage.getItem('readingTestRemainingTime');
        if (savedTime) {
            const timerElement = document.getElementById('timer');
            if (timerElement) {
                timerElement.dataset.timeSeconds = savedTime;
                console.log('Saved time found:', savedTime);
            }
        }
        
        // Start the timer
        startTimer();
        
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Toggle active class on buttons
                tabButtons.forEach(btn => btn.classList.remove('active', 'bg-blue-600', 'text-white'));
                tabButtons.forEach(btn => btn.classList.add('bg-gray-200', 'text-gray-700'));
                button.classList.add('active', 'bg-blue-600', 'text-white');
                button.classList.remove('bg-gray-200', 'text-gray-700');
                
                // Toggle active class on panes
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('active');
                });
                
                document.getElementById(`${tabId}-tab`).classList.remove('hidden');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });
        
        // Fix for select elements
        document.querySelectorAll('select').forEach(select => {
            select.style.display = 'block';
            select.style.opacity = '1';
            select.style.pointerEvents = 'auto';
        });
        
        // Drag and Drop functionality
        const draggables = document.querySelectorAll('.draggable');
        const dropZones = document.querySelectorAll('.drop-zone');
        
        // Initialize drag events for draggable elements
        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging');
            });
            
            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging');
            });
        });
        
        // Initialize drop events for drop zones
        dropZones.forEach(dropZone => {
            dropZone.addEventListener('dragover', e => {
                e.preventDefault();
                dropZone.classList.add('drag-over');
            });
            
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('drag-over');
            });
            
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                const dragging = document.querySelector('.dragging');
                if (dragging) {
                    // Clear existing content except the hidden input
                    const hiddenInput = dropZone.querySelector('input[type="hidden"]');
                    dropZone.innerHTML = '';
                    dropZone.appendChild(hiddenInput);
                    
                    // Create a clone of the dragged element
                    const clone = dragging.cloneNode(true);
                    clone.classList.remove('dragging');
                    clone.setAttribute('draggable', 'false');
                    
                    // Add a remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.className = 'absolute top-1 right-1 text-gray-500 hover:text-red-500';
                    removeBtn.addEventListener('click', () => {
                        dropZone.innerHTML = '';
                        dropZone.appendChild(hiddenInput);
                        hiddenInput.value = '';
                        dropZone.classList.remove('has-item');
                        const placeholder = document.createElement('div');
                        placeholder.className = 'placeholder';
                        placeholder.textContent = 'Drop your answer here';
                        dropZone.appendChild(placeholder);
                    });
                    
                    // Update the hidden input value
                    hiddenInput.value = dragging.getAttribute('data-value');
                    
                    // Add the clone to the drop zone
                    dropZone.appendChild(clone);
                    dropZone.appendChild(removeBtn);
                    dropZone.classList.add('has-item');
                    dropZone.classList.remove('drag-over');
                }
            });
        });
    });
        
        // Load the highlighting functionality from reading-test.js
        if (typeof initializeHighlighting === 'function') {
            initializeHighlighting();
        } else {
            console.error('Highlighting functionality not available');
            // Load the script if not already loaded
            if (!document.querySelector('script[src="/js/reading-test.js"]')) {
                const script = document.createElement('script');
                script.src = '/js/reading-test.js';
                document.head.appendChild(script);
                script.onload = function() {
                    if (typeof initializeHighlighting === 'function') {
                        initializeHighlighting();
                    }
                };
            }
        }
    });
</script>
@endsection
