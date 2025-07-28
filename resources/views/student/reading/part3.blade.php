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
                    <button type="button" class="tab-btn active px-4 py-2 bg-blue-600 text-white rounded-md" data-tab="passage">O'qish matni</button>
                    <button type="button" class="tab-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-md" data-tab="questions">Savollar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-4">
        <div class="tab-content">
            <!-- Passage Panel -->
            <div id="passage-tab" class="tab-pane active">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="overflow-y-auto questions-container" style="height: auto !important; min-height: 500px; max-height: none !important; display: block !important;">
                        <h2 class="text-xl font-bold mb-4">The Importance of Sleep</h2>
                        
                        <div class="prose max-w-none">
                            <p><strong>A</strong> Sleep is a naturally recurring state characterized by reduced or absent consciousness, relatively suspended sensory activity, and inactivity of nearly all voluntary muscles. It is distinguished from quiet wakefulness by a decreased ability to react to stimuli, and it is more easily reversible than hibernation or coma. Sleep is a heightened anabolic state, accentuating the growth and rejuvenation of the immune, nervous, skeletal and muscular systems. It is observed in all mammals, all birds, and many reptiles, amphibians, and fish.</p>

                            <p><strong>B</strong> The purposes and mechanisms of sleep are only partially clear and are the subject of intense research. Sleep is often thought to help conserve energy, though this theory is not fully adequate as it only decreases metabolism by about 5–10%. Additionally, it is observed that mammals require sleep even during the hypometabolic state of hibernation, in which circumstance it is actually a net loss of energy as the animal returns from hypothermia to euthermia in order to sleep.</p>

                            <p><strong>C</strong> In mammals and birds, sleep is divided into two broad types: rapid eye movement (REM) and non-rapid eye movement (NREM or non-REM) sleep. Each type has a distinct set of associated physiological, neurological, and psychological features. The American Academy of Sleep Medicine (AASM) divides NREM into three stages: N1, N2, and N3, the last of which is also called delta sleep or slow-wave sleep (SWS).</p>

                            <p><strong>D</strong> During sleep, especially REM sleep, humans tend to experience dreams. These are elusive and mostly unpredictable first-person experiences which seem realistic while in progress, despite their frequently bizarre qualities. Dreams can seamlessly incorporate elements within a person's mind that would not normally go together. They can include apparent sensations of all types, especially vision and movement.</p>

                            <p><strong>E</strong> The most pronounced physiological changes in sleep occur in the brain. The brain uses significantly less energy during sleep than it does when awake, especially during non-REM sleep. In areas with reduced activity, the brain restores its supply of adenosine triphosphate (ATP), the molecule used for short-term storage and transport of energy. During slow-wave sleep, humans secrete growth hormone. The secretion of prolactin is increased and its effects are augmented.</p>

                            <p><strong>F</strong> The traditional view is that sleep gives the body and brain time to restore and repair themselves. In recent years, these claims have gained support from empirical evidence collected in human and animal studies. The most striking of these is that animals deprived of sleep die within a few weeks. Sleep deprivation affects the immune system in detrimental ways. Sleep deprivation makes the body susceptible to many different diseases. The mechanism for this is unknown but study after study has shown its effects.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Panel -->
            <div id="questions-tab" class="tab-pane hidden">
                <form action="{{ route('reading.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf
                    <input type="hidden" name="next_route" value="">
                    
                    <div class="overflow-y-auto questions-container" style="height: auto !important; min-height: 500px; max-height: none !important; display: block !important;">
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
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">27.</span>
                                        According to the text, sleep is:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">27</div>
                                        <div class="drop-zone" data-question="27">
                                            <input type="hidden" name="answers[27]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> a state that only mammals experience<br>
                                        <span class="font-medium">B.</span> characterized by complete muscle inactivity<br>
                                        <span class="font-medium">C.</span> a state that promotes growth and rejuvenation<br>
                                        <span class="font-medium">D.</span> more difficult to reverse than coma
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">28.</span>
                                        The text suggests that the energy conservation theory of sleep:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">28</div>
                                        <div class="drop-zone" data-question="28">
                                            <input type="hidden" name="answers[28]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> is fully supported by scientific evidence<br>
                                        <span class="font-medium">B.</span> does not fully explain the purpose of sleep<br>
                                        <span class="font-medium">C.</span> has been disproven by recent research<br>
                                        <span class="font-medium">D.</span> explains why animals hibernate
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">29.</span>
                                        The restorative theory of sleep suggests that sleep primarily functions to:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">29</div>
                                        <div class="drop-zone" data-question="29">
                                            <input type="hidden" name="answers[29]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> conserve energy during periods of inactivity<br>
                                        <span class="font-medium">B.</span> repair and rejuvenate the body's systems<br>
                                        <span class="font-medium">C.</span> protect animals from predators at night<br>
                                        <span class="font-medium">D.</span> process information gathered during waking hours
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">30.</span>
                                        According to the text, REM sleep is characterized by:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">30</div>
                                        <div class="drop-zone" data-question="30">
                                            <input type="hidden" name="answers[30]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> increased muscle tone and activity<br>
                                        <span class="font-medium">B.</span> decreased brain activity compared to waking state<br>
                                        <span class="font-medium">C.</span> vivid dreams and rapid eye movements<br>
                                        <span class="font-medium">D.</span> slow brain waves and deep relaxation
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">31.</span>
                                        The brain consolidation theory suggests that sleep helps to:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">31</div>
                                        <div class="drop-zone" data-question="31">
                                            <input type="hidden" name="answers[31]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> strengthen neural connections formed during learning<br>
                                        <span class="font-medium">B.</span> reduce the need for oxygen in the brain<br>
                                        <span class="font-medium">C.</span> eliminate toxins that accumulate during waking hours<br>
                                        <span class="font-medium">D.</span> decrease overall brain activity to save energy
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">32.</span>
                                        Sleep deprivation studies have shown that:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">32</div>
                                        <div class="drop-zone" data-question="32">
                                            <input type="hidden" name="answers[32]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> humans can adapt to not sleeping at all<br>
                                        <span class="font-medium">B.</span> cognitive functions are severely impaired without sleep<br>
                                        <span class="font-medium">C.</span> physical health is unaffected by lack of sleep<br>
                                        <span class="font-medium">D.</span> sleep is primarily important for emotional regulation only
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">33.</span>
                                        The circadian rhythm is primarily regulated by:
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">33</div>
                                        <div class="drop-zone" data-question="33">
                                            <input type="hidden" name="answers[33]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-4">
                                        <span class="font-medium">A.</span> exposure to light and darkness<br>
                                        <span class="font-medium">B.</span> the amount of physical activity during the day<br>
                                        <span class="font-medium">C.</span> food consumption patterns<br>
                                        <span class="font-medium">D.</span> ambient temperature fluctuations
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Questions 34-40: Completion -->
                        <div class="mb-10">
                            <div class="font-bold text-lg mb-4">Questions 34–40</div>
                            <div class="mb-6 text-gray-700">
                                Complete the sentences below. Write NO MORE THAN TWO WORDS for each answer.
                            </div>
                            
                            <div class="space-y-2">
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">34.</span>
                                        During sleep, the brain's supply of ______________ is restored in areas with reduced activity.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">34</div>
                                        <div class="drop-zone" data-question="34">
                                            <input type="hidden" name="answers[34]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">35.</span>
                                        The secretion of ______________ increases during slow-wave sleep.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">35</div>
                                        <div class="drop-zone" data-question="35">
                                            <input type="hidden" name="answers[35]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">36.</span>
                                        The ______________ stage of sleep is when most dreaming occurs.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">36</div>
                                        <div class="drop-zone" data-question="36">
                                            <input type="hidden" name="answers[36]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">37.</span>
                                        Sleep disorders can lead to increased risk of ______________ and other health problems.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">37</div>
                                        <div class="drop-zone" data-question="37">
                                            <input type="hidden" name="answers[37]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">38.</span>
                                        The ______________ is the part of the brain that regulates sleep-wake cycles.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">38</div>
                                        <div class="drop-zone" data-question="38">
                                            <input type="hidden" name="answers[38]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">39.</span>
                                        Studies show that ______________ before bedtime can disrupt sleep patterns.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">39</div>
                                        <div class="drop-zone" data-question="39">
                                            <input type="hidden" name="answers[39]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">40.</span>
                                        The average adult needs approximately ______________ hours of sleep per night.
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">40</div>
                                        <div class="drop-zone" data-question="40">
                                            <input type="hidden" name="answers[40]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
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
