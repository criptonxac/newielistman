@extends('layouts.student')

@section('title', 'Writing Test - Task 1')

@section('content')
<div class="container mx-auto px-4 py-8">
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
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">IELTS Academic Writing</h1>
                <div class="text-gray-600">Task 1 - Chart Description</div>
            </div>
            <div class="text-2xl font-bold text-blue-600" id="timer">20:00</div>
        </div>
    </div>

    <!-- Navigation Section -->
    <div class="bg-gray-100 rounded-lg p-4 mb-6 text-sm text-gray-600">
        Writing &gt; Task 1 &gt; Chart Analysis
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Task Panel -->
        <div class="bg-white rounded-lg shadow-md p-6 md:w-1/2">
            <div class="overflow-y-auto max-h-[70vh]">
                <h2 class="text-xl font-bold mb-4">Writing Task 1</h2>
                
                <div class="mb-4 text-gray-700">
                    You should spend about <strong>20 minutes</strong> on this task. Write at least <strong>150 words</strong>.
                </div>

                <div class="mb-4 text-sm text-gray-600">
                    <strong>Recommended time allocation:</strong> 20 minutes for Task 1, 40 minutes for Task 2
                </div>

                <div class="mb-6">
                    <p class="font-bold mb-4">The chart below shows the number of adults participating in different major sports in one area, in 1997 and 2017.</p>
                    
                    <p class="font-bold mb-4">Summarise the information by selecting and reporting the main features, and make comparisons where relevant.</p>
                </div>

                <!-- Chart -->
                <div class="border border-gray-300 rounded-lg p-4 mb-6">
                    <div class="text-center font-bold mb-4">Adults participating in different major sports (1997 vs 2017)</div>
                    <div class="flex justify-center">
                        <svg width="500" height="350" viewBox="0 0 500 350" class="chart-image">
                            <!-- Chart background -->
                            <rect x="50" y="50" width="400" height="250" fill="#f8f9fa" stroke="#dee2e6" />
                            
                            <!-- Y-axis -->
                            <line x1="50" y1="50" x2="50" y2="300" stroke="#212529" stroke-width="2" />
                            <!-- X-axis -->
                            <line x1="50" y1="300" x2="450" y2="300" stroke="#212529" stroke-width="2" />
                            
                            <!-- Y-axis labels -->
                            <text x="45" y="300" text-anchor="end" font-size="12">0</text>
                            <text x="45" y="250" text-anchor="end" font-size="12">10</text>
                            <text x="45" y="200" text-anchor="end" font-size="12">20</text>
                            <text x="45" y="150" text-anchor="end" font-size="12">30</text>
                            <text x="45" y="100" text-anchor="end" font-size="12">40</text>
                            <text x="45" y="50" text-anchor="end" font-size="12">50</text>
                            
                            <!-- Y-axis title -->
                            <text x="20" y="175" text-anchor="middle" font-size="12" transform="rotate(-90, 20, 175)">Number of participants (thousands)</text>
                            
                            <!-- X-axis categories -->
                            <text x="100" y="320" text-anchor="middle" font-size="12">Football</text>
                            <text x="200" y="320" text-anchor="middle" font-size="12">Swimming</text>
                            <text x="300" y="320" text-anchor="middle" font-size="12">Tennis</text>
                            <text x="400" y="320" text-anchor="middle" font-size="12">Cycling</text>
                            
                            <!-- Data for 1997 -->
                            <rect x="80" y="150" width="20" height="150" fill="#4dabf7" />
                            <rect x="180" y="200" width="20" height="100" fill="#4dabf7" />
                            <rect x="280" y="220" width="20" height="80" fill="#4dabf7" />
                            <rect x="380" y="250" width="20" height="50" fill="#4dabf7" />
                            
                            <!-- Data for 2017 -->
                            <rect x="100" y="100" width="20" height="200" fill="#ff6b6b" />
                            <rect x="200" y="120" width="20" height="180" fill="#ff6b6b" />
                            <rect x="300" y="180" width="20" height="120" fill="#ff6b6b" />
                            <rect x="400" y="120" width="20" height="180" fill="#ff6b6b" />
                            
                            <!-- Legend -->
                            <rect x="350" y="30" width="15" height="15" fill="#4dabf7" />
                            <text x="370" y="42" font-size="12">1997</text>
                            <rect x="410" y="30" width="15" height="15" fill="#ff6b6b" />
                            <text x="430" y="42" font-size="12">2017</text>
                        </svg>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="font-bold mb-2">Key Requirements:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-gray-700">
                        <li>Write at least 150 words</li>
                        <li>Summarise the main features of the chart</li>
                        <li>Make comparisons between data points</li>
                        <li>Organize your response coherently</li>
                        <li>Use appropriate language to describe trends</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Writing Panel -->
        <div class="bg-white rounded-lg shadow-md p-6 md:w-1/2">
            <form action="{{ route('writing.submit-answers', ['test' => $test->slug, 'attempt' => $attempt->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="next_route" value="writing.task2">
                
                <div class="mb-4">
                    <label for="essay" class="block font-bold mb-2">Your Response:</label>
                    <textarea 
                        id="essay" 
                        name="answers[task1]" 
                        class="w-full h-96 p-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Write your response here (minimum 150 words)..."
                    ></textarea>
                </div>
                
                <div class="flex justify-between items-center mt-6">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('student.tests') }}" class="text-gray-600 hover:text-gray-800 py-2 px-4 border border-gray-300 rounded-lg font-medium flex items-center">
                            <i class="fas fa-list mr-2"></i> All Tests
                        </a>
                        <div class="text-sm text-gray-600">
                            <div id="wordCount">0 words</div>
                            <div>Minimum required: 150 words</div>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg font-bold flex items-center">
                        Continue to Task 2 <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Timer functionality
    document.addEventListener('DOMContentLoaded', function() {
        let timeLeft = 20 * 60; // 20 minutes in seconds
        const timerElement = document.getElementById('timer');
        
        const timer = setInterval(function() {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                alert('Time is up for Task 1! Moving to Task 2.');
                document.querySelector('form').submit();
            }
        }, 1000);
        
        // Word count functionality
        const textarea = document.getElementById('essay');
        const wordCountElement = document.getElementById('wordCount');
        
        textarea.addEventListener('input', function() {
            const text = textarea.value;
            const wordCount = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
            wordCountElement.textContent = `${wordCount} words`;
            
            if (wordCount >= 150) {
                wordCountElement.classList.add('text-green-600');
                wordCountElement.classList.remove('text-red-600');
            } else {
                wordCountElement.classList.add('text-red-600');
                wordCountElement.classList.remove('text-green-600');
            }
        });
    });
</script>
@endpush
@endsection
