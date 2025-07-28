@extends('layouts.student')

@section('title', 'Reading Test - Part 3')

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
                <div class="text-xl font-bold text-white" id="timer">60:00</div>
            </div>
        </div>
    </div>
    
    <!-- Test header -->
    <div class="bg-gray-100 border-b border-gray-300 py-3">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">IELTS Academic Reading</h1>
                    <div class="text-gray-600 text-sm">Part 3: Questions 27-40</div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('reading.part1', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 1</a>
                    <a href="{{ route('reading.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 2</a>
                    <a href="{{ route('reading.part3', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Part 3</a>
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
                    <h2 class="text-xl font-bold mb-4">Artificial Intelligence and Society</h2>
                    
                    <div class="prose max-w-none">
                        <p><strong>A</strong> Sleep is a naturally recurring state characterized by reduced consciousness and sensory activity. It is distinguished from wakefulness by decreased reactivity to stimuli.</p>

                        <p><strong>B</strong> Sleep mechanisms are partially understood. It may conserve energy, though it only decreases metabolism by 5-10%. Mammals require sleep even during hibernation.</p>

                        <p><strong>C</strong> Sleep is divided into two types: rapid eye movement (REM) and non-rapid eye movement (NREM) sleep. The American Academy of Sleep Medicine divides NREM into three stages: N1, N2, and N3 (delta sleep).</p>
                    </div>
                </div>
            </div>

            <!-- Questions Panel (Right Side) -->
            <div class="questions-panel">
                <form action="{{ route('reading.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf
                    <input type="hidden" name="next_route" value="">
                    
                    <div class="overflow-y-auto questions-container" style="height: calc(100vh - 220px); display: block !important;">
                        <!-- Questions 27-33: Multiple Choice -->
                        <div class="mb-10">
                            <div class="font-bold text-lg mb-4">Questions 27–33</div>
                            <div class="mb-6 text-gray-700">
                                Choose the correct letter, A, B, C or D.
                            </div>

                            
                            <div class="answer-options" id="answer-options">
                                <div class="draggable" draggable="true" data-value="A">A</div>
                                <div class="draggable" draggable="true" data-value="B">B</div>
                                <div class="draggable" draggable="true" data-value="C">C</div>
                                <div class="draggable" draggable="true" data-value="D">D</div>
                            </div>
                            
                            <div class="space-y-2">
                                @php $questionNumber = 27; @endphp
                                @foreach($questions as $question)
                                    {!! \App\Services\QuestionRenderer::render($question, $questionNumber++, $userAnswers) !!}
                                @endforeach
                                </div>
                        </div>
                        
                        <!-- Questions 34-40: Completion -->
                        <div class="mb-10">
                            <div class="font-bold text-lg mb-4">Questions 34–40</div>
                            <div class="mb-6 text-gray-700">
                                Complete the sentences below. Write NO MORE THAN TWO WORDS for each answer.
                            </div>
                            
                            <div class="space-y-2">
                                @php $questionNumber = 34; @endphp
                                @foreach($questions as $question)
                                    @if($questionNumber > 33 && $questionNumber <= 40)
                                        {!! \App\Services\QuestionRenderer::render($question, $questionNumber++, $userAnswers) !!}
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-10">
                            <a href="{{ route('reading.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Oldingi
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Yakunlash <i class="fas fa-check ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Timer functionality
        const timerElement = document.getElementById('timer');
        let timeLeft = 60 * 60; // 60 minutes in seconds
        
        // Only start the timer if it hasn't been started yet
        if (!window.timerInterval) {
            window.timerInterval = setInterval(function() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(window.timerInterval);
                    document.querySelector('form').submit();
                }
                
                timeLeft -= 1;
            }, 1000);
        }
        
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
        
        // Fix for radio buttons and inputs
        document.querySelectorAll('.form-radio').forEach(radio => {
            radio.style.display = 'inline-block';
            radio.style.opacity = '1';
            radio.style.pointerEvents = 'auto';
            radio.style.width = '1rem';
            radio.style.height = '1rem';
        });
        
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.style.display = 'block';
            input.style.opacity = '1';
            input.style.pointerEvents = 'auto';
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
</script>
@endpush
@endsection
