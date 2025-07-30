@extends('layouts.student')

@section('title', 'Reading Test - Complete')

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
                    <div class="text-gray-600 text-sm">Test Complete</div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('reading.part1', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 1</a>
                    <a href="{{ route('reading.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 2</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="flex justify-center">
            <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl w-full">
                <div class="text-center">
                    <div class="text-green-500 mb-4">
                        <i class="fas fa-check-circle text-6xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">Test Complete!</h2>
                    <p class="text-gray-600 mb-6">You have completed the IELTS Reading test. Your answers have been submitted successfully.</p>
                    
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-bold text-lg mb-2">Test Summary</h3>
                        <div class="flex justify-between mb-2">
                            <span>Test:</span>
                            <span class="font-medium">{{ $test->title }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Parts Completed:</span>
                            <span class="font-medium">2 of 2</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Questions:</span>
                            <span class="font-medium">26 questions</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('student.results') }}" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-chart-line mr-2"></i> View Results
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                            <i class="fas fa-home mr-2"></i> Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Test complete page loaded');
        // Clear timer data
        localStorage.removeItem('readingTestRemainingTime');
        
        // Show completion message
        function startTimer() {
            console.log('Timer ishga tushirilmoqda...');
            const timerElement = document.getElementById('timer');
            if (!timerElement) {
                console.error('Timer elementi topilmadi!');
                return;
            }
            
            let totalSeconds = parseInt(timerElement.getAttribute('data-time-seconds')) || 3600;
            console.log('Jami vaqt (sekund):', totalSeconds);
            let minutes, seconds;
            
            // Only start the timer if it hasn't been started yet
            if (!window.timerInterval) {
                window.timerInterval = setInterval(function() {
                    if (totalSeconds <= 0) {
                        clearInterval(window.timerInterval);
                        console.log('Vaqt tugadi, forma yuborilmoqda...');
                        // Formani topish va yuborish
                        const form = document.querySelector('form');
                        if (form) {
                            form.submit();
                        } else {
                            console.error('Forma topilmadi!');
                            // Natijalar sahifasiga yo'naltirish
                            window.location.href = '/student/results';
                        }
                        return;
                    }
                    
                    minutes = Math.floor(totalSeconds / 60);
                    seconds = totalSeconds % 60;
                    
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    totalSeconds--;
                }, 1000);
            }
        }
        
        // Sahifa yuklanganda timerni ishga tushirish
        startTimer();
        
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
@endsection
