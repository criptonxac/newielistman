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
                    <div class="text-gray-600 text-sm">Part 2: Questions 14-26</div>
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
                        <h2 class="text-xl font-bold mb-4">Climate Change and Global Initiatives</h2>
                        
                        <div class="prose max-w-none">
                            <p>Climate change is one of the most pressing challenges of our time, with far-reaching implications for the environment, economies, and societies worldwide. The scientific consensus is clear: human activities, particularly the burning of fossil fuels and deforestation, are driving unprecedented changes in our climate system. These changes manifest in rising global temperatures, shifting precipitation patterns, more frequent and intense extreme weather events, and rising sea levels.</p>

                            <p>The Intergovernmental Panel on Climate Change (IPCC), established by the United Nations in 1988, has been instrumental in assessing the scientific basis of climate change, its impacts, and potential response strategies. Through its comprehensive assessment reports, the IPCC has provided policymakers with the most authoritative scientific information about climate change. The panel's findings have consistently emphasized the urgency of reducing greenhouse gas emissions to limit global warming to well below 2°C above pre-industrial levels, as stipulated in the Paris Agreement.</p>

                            <p>The Paris Agreement, adopted in 2015, represents a landmark in global climate governance. It brings together nations in a common cause to undertake ambitious efforts to combat climate change and adapt to its effects. The agreement operates on a five-year cycle of increasingly ambitious climate action carried out by countries. By 2020, countries were expected to submit their plans for climate action known as nationally determined contributions (NDCs).</p>

                            <p>Beyond international agreements, various initiatives are underway to address climate change at different scales. These include the development and deployment of renewable energy technologies, energy efficiency measures, sustainable land management practices, and carbon pricing mechanisms. Cities and local governments are also taking action through urban planning, transportation policies, and building standards designed to reduce emissions and enhance resilience.</p>

                            <p>The private sector plays a crucial role in climate action as well. Many companies are setting science-based targets for reducing their emissions, investing in clean energy, and developing innovative solutions for a low-carbon future. Financial institutions are increasingly considering climate risks in their investment decisions and developing green financial products to support the transition to a sustainable economy.</p>

                            <p>Despite these efforts, significant challenges remain. Current pledges under the Paris Agreement are insufficient to limit global warming to 1.5°C. The transition to a low-carbon economy requires substantial investments, technological innovation, and policy support. Moreover, ensuring a just transition that addresses the needs of vulnerable communities and workers in carbon-intensive industries is essential for the success of climate action.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Panel -->
            <div id="questions-tab" class="tab-pane hidden">
                <form action="{{ route('reading.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf
                    <input type="hidden" name="next_route" value="reading.part3">
                
                    <div class="overflow-y-auto questions-container" style="height: auto !important; min-height: 500px; max-height: none !important; display: block !important;">
                        <!-- Questions 14-20: Matching Headings -->
                        <div class="mb-10">
                            <div class="font-bold text-lg mb-4">Questions 14–20</div>
                            <div class="mb-6 text-gray-700">
                                Match each statement with the correct paragraph (A-F). Select the correct letter A-F for questions 14-20.
                            </div>
                            
                            <div class="answer-options" id="answer-options">
                                <div class="draggable" draggable="true" data-value="A">A</div>
                                <div class="draggable" draggable="true" data-value="B">B</div>
                                <div class="draggable" draggable="true" data-value="C">C</div>
                                <div class="draggable" draggable="true" data-value="D">D</div>
                                <div class="draggable" draggable="true" data-value="E">E</div>
                                <div class="draggable" draggable="true" data-value="F">F</div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">14.</span>
                                        Description of the main organization that provides scientific information about climate change
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">14</div>
                                        <div class="drop-zone" data-question="14">
                                            <input type="hidden" name="answers[14]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">15.</span>
                                        Reference to a significant international agreement on climate change
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">15</div>
                                        <div class="drop-zone" data-question="15">
                                            <input type="hidden" name="answers[15]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">16.</span>
                                        Mention of how businesses are contributing to climate action
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">16</div>
                                        <div class="drop-zone" data-question="16">
                                            <input type="hidden" name="answers[16]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">17.</span>
                                        Explanation of the scientific evidence for global warming
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">17</div>
                                        <div class="drop-zone" data-question="17">
                                            <input type="hidden" name="answers[17]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">18.</span>
                                        Discussion of renewable energy solutions
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">18</div>
                                        <div class="drop-zone" data-question="18">
                                            <input type="hidden" name="answers[18]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">19.</span>
                                        Reference to the impact of climate change on biodiversity
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">19</div>
                                        <div class="drop-zone" data-question="19">
                                            <input type="hidden" name="answers[19]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="mb-4">
                                        <span class="font-bold mr-2 text-lg">20.</span>
                                        Description of extreme weather events linked to climate change
                                    </div>
                                    <div class="answer-item">
                                        <div class="answer-number">20</div>
                                        <div class="drop-zone" data-question="20">
                                            <input type="hidden" name="answers[20]" value="">
                                            <div class="placeholder">Drop your answer here</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Questions 21-26: True/False/Not Given -->
                                <div class="mb-10 mt-10">
                                    <div class="font-bold text-lg mb-4">Questions 21–26</div>
                                    <div class="mb-6 text-gray-700">
                                        Choose TRUE if the statement agrees with the information given in the text, choose FALSE if the statement contradicts the information, or choose NOT GIVEN if there is no information on this.
                                    </div>
                                    <div class="answer-options mt-4 mb-6">
                                        <div class="draggable" draggable="true" data-value="TRUE">TRUE</div>
                                        <div class="draggable" draggable="true" data-value="FALSE">FALSE</div>
                                        <div class="draggable" draggable="true" data-value="NOT GIVEN">NOT GIVEN</div>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                            <div class="mb-4">
                                                <span class="font-bold mr-2 text-lg">21.</span>
                                                Climate change is primarily caused by human activities.
                                            </div>
                                            <div class="answer-item">
                                                <div class="answer-number">21</div>
                                                <div class="drop-zone" data-question="21">
                                                    <input type="hidden" name="answers[21]" value="">
                                                    <div class="placeholder">Drop your answer here</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                            <div class="mb-4">
                                                <span class="font-bold mr-2 text-lg">22.</span>
                                                The Paris Agreement was signed by all countries in the world.
                                            </div>
                                            <div class="answer-item">
                                                <div class="answer-number">22</div>
                                                <div class="drop-zone" data-question="22">
                                                    <input type="hidden" name="answers[22]" value="">
                                                    <div class="placeholder">Drop your answer here</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                            <div class="mb-4">
                                                <span class="font-bold mr-2 text-lg">23.</span>
                                                Renewable energy is now cheaper than fossil fuels in most countries.
                                            </div>
                                            <div class="answer-item">
                                                <div class="answer-number">23</div>
                                                <div class="drop-zone" data-question="23">
                                                    <input type="hidden" name="answers[23]" value="">
                                                    <div class="placeholder">Drop your answer here</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                            <div class="mb-4">
                                                <span class="font-bold mr-2 text-lg">24.</span>
                                                The IPCC was established in the 1990s.
                                            </div>
                                            <div class="answer-item">
                                                <div class="answer-number">24</div>
                                                <div class="drop-zone" data-question="24">
                                                    <input type="hidden" name="answers[24]" value="">
                                                    <div class="placeholder">Drop your answer here</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                            <div class="mb-4">
                                                <span class="font-bold mr-2 text-lg">25.</span>
                                                Sea levels are expected to rise by several meters by 2100.
                                            </div>
                                            <div class="answer-item">
                                                <div class="answer-number">25</div>
                                                <div class="drop-zone" data-question="25">
                                                    <input type="hidden" name="answers[25]" value="">
                                                    <div class="placeholder">Drop your answer here</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                                            <div class="mb-4">
                                                <span class="font-bold mr-2 text-lg">26.</span>
                                                Individual actions have no significant impact on climate change.
                                            </div>
                                            <div class="answer-item">
                                                <div class="answer-number">26</div>
                                                <div class="drop-zone" data-question="26">
                                                    <input type="hidden" name="answers[26]" value="">
                                                    <div class="placeholder">Drop your answer here</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
</script>
@endpush
@endsection
