@extends('layouts.student')

@section('title', 'Reading Test - Part 1')

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
        <div class="container-fluid px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">IELTS Academic Reading</h1>
                    <div class="text-gray-600 text-sm">Part 1: Questions 1-13</div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('reading.part1', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Part 1</a>
                    <a href="{{ route('reading.part2', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 2</a>
                    <a href="{{ route('reading.part3', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Part 3</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
            <!-- Passage Panel (Left Side) -->
            <div class=" bg-white rounded-lg shadow-md p-4 passage-container">
                <div class="overflow-y-auto" style="height: calc(100vh - 220px);">
                    <h2 class="text-xl font-bold mb-4">The Importance of Sleep</h2>
                    
                    <div class="prose max-w-none">
                            <p>Sleep is a naturally recurring state characterized by reduced or absent consciousness, relatively suspended sensory activity, and inactivity of nearly all voluntary muscles. It is distinguished from quiet wakefulness by a decreased ability to react to stimuli, and it is more easily reversible than hibernation or coma. Sleep is a heightened anabolic state, accentuating the growth and rejuvenation of the immune, nervous, skeletal, and muscular systems; it is observed in all birds, and many reptiles, amphibians, and fish.</p>

                            <p>The purposes and mechanisms of sleep are only partially clear and are the subject of intense research. Sleep is often thought to help conserve energy, though this theory is not fully adequate as it only decreases metabolism by about 5–10%. Additionally, it is observed that the brain is still active during the hypothalamic-driven state of mammalian sleep, whether it actually requires more energy to stay awake or to fall asleep in order to sleep.</p>

                            <p>In mammals and birds, sleep is divided into two broad types: rapid eye movement (REM) and non-rapid eye movement (NREM) sleep. Each type has a distinct set of associated physiological, neurological, and psychological features. The American Academy of Sleep Medicine (AASM) divides NREM into three stages: N1, N2, and N3, the last of which is also called delta sleep or slow-wave sleep.</p>

                            <p>During sleep, especially REM sleep, people tend to experience dreams: images and sensations that are experienced while sleeping and seem real while happening. These are studied and described as dream imagery. People may not remember the dreams that they have during sleep. This lack of memory may be explained by the changes in brain activity that occur during different stages of sleep.</p>

                            <p>The most pronounced physiological changes in sleep occur in the brain. The brain uses significantly less energy during sleep than it does when awake, especially during NREM sleep. In areas with reduced activity, the brain restores its supply of adenosine triphosphate (ATP), the molecule used for short-term storage and transport of energy. During slow-wave sleep, humans secrete growth hormone. The secretion of prolactin is increased and its effects are augmented.</p>

                            <p>In recent years, these claims have gained support from empirical evidence collected in human and animal studies. The most striking of these is that animals deprived of sleep die within a few weeks. Sleep deprivation affects the immune system in detrimental ways. Sleep deprivation makes the body susceptible to many different diseases. The mechanism for this is unknown but it is known that sleep deprivation decreases the immune system's ability to respond to invaders.</p>
                        
                    </div>
                </div>
            </div>

            <!-- Questions Panel (Right Side) -->
            <div class=" questions-panel">
                <form action="{{ route('reading.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf
                    <input type="hidden" name="next_route" value="reading.part2">
                    
                    <div class="overflow-y-auto questions-container" style="height: calc(100vh - 220px); display: block !important;">
                        <!-- Questions 1-13 -->
                        <div class="mb-10">
                            <div class="font-bold text-lg mb-4">Questions 1–13</div>
                            <div class="mb-6 text-gray-700">
                                Answer the questions according to the instructions.
                            </div>
                            </div>
                            <div class="space-y-2">
                                @php $questionNumber = 1; @endphp
                                @foreach($questions as $question)
                                    {!! \App\Services\QuestionRenderer::render($question, $questionNumber++, $userAnswers) !!}
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-10">
                            <a href="{{ route('student.tests') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Bekor qilish
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
    
    <!-- Remove tab switching JavaScript -->
    @push('scripts')
    <script>
    // Timer functionality
    function startTimer() {
        const timerElement = document.getElementById('timer');
        if (!timerElement) return;
        
        let totalSeconds = parseInt(timerElement.getAttribute('data-time-seconds')) || 3600;
        let minutes, seconds;
        
        const timerInterval = setInterval(function() {
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                document.getElementById('reading-form').submit();
                return;
            }
            
            minutes = Math.floor(totalSeconds / 60);
            seconds = totalSeconds % 60;
            
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            totalSeconds--;
        }, 1000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
    });
    
    window.addEventListener('load', function() {
        startTimer();
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
                    placeholder.textContent = 'Drop answer here';
                    dropZone.appendChild(placeholder);
                });
                
                // Set the value of the hidden input
                hiddenInput.value = dragging.getAttribute('data-value');
                
                // Append the clone and remove button
                dropZone.appendChild(clone);
                dropZone.appendChild(removeBtn);
                dropZone.classList.add('has-item');
                dropZone.classList.remove('drag-over');
            }
        });
    });
</script>
@endpush
</div>

@push('scripts')
<script>
    // Timer functionality
    let timerStarted = false;
    
    function startTimer() {
        if (timerStarted) return;
        timerStarted = true;
        
        let timeLeft = 60 * 60; // 60 minutes in seconds
        const timerDisplay = document.getElementById('timer');
        
        const timer = setInterval(function() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.querySelector('form').submit();
            }
            
            timeLeft -= 1;
        }, 1000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
    });
    
    window.addEventListener('load', function() {
        startTimer();
    });
        
        // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            tabButtons.forEach(btn => btn.classList.remove('active', 'bg-blue-600', 'text-white'));
            tabButtons.forEach(btn => btn.classList.add('bg-gray-200', 'text-gray-700'));
            
            button.classList.remove('bg-gray-200', 'text-gray-700');
            button.classList.add('active', 'bg-blue-600', 'text-white');
            
            tabPanes.forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('active');
            });
            const activeTab = document.getElementById(`${tabId}-tab`);
            activeTab.classList.remove('hidden');
            activeTab.classList.add('active');
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
</script>
@endpush
@endsection
