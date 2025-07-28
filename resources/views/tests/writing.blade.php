<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IELTS Writing Test</title>
    <link rel="stylesheet" href="{{ asset('css/writing.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>IELTS Academic Writing</h1>
                <div>Complete Test - Tasks 1 & 2</div>
            </div>
            <div class="timer" id="timer">60:00</div>
        </div>

        <!-- Navigation Section -->
        <div class="nav-section">
            Writing &gt; Complete Test &gt; Tasks 1 & 2
        </div>

        <!-- Task Tabs -->
        <div class="task-tabs">
            <button class="task-tab active" data-task="1">
                Task 1 - Chart Description (20 min)
            </button>
            <button class="task-tab" data-task="2">
                Task 2 - Essay Writing (40 min)
            </button>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Task Panel -->
            <div class="task-panel">
                
                <!-- Task 1 Content -->
                <div class="task-content" data-task="1">
                    <div class="task-container">
                        <h2 class="task-title">Writing Task 1</h2>
                        
                        <div class="task-instruction">
                            You should spend about <strong>20 minutes</strong> on this task. Write at least <strong>150 words</strong>.
                        </div>

                        <div class="task-description">
                            <p><strong>The chart below shows the number of adults participating in different major sports in one area, in 1997 and 2017.</strong></p>
                            <p><strong>Summarise the information by selecting and reporting the main features, and make comparisons where relevant.</strong></p>
                        </div>

                        <!-- Chart -->
                        <div class="chart-container">
                            <div class="chart-title">Adults participating in different major sports (1997 vs 2017)</div>
                            <svg width="500" height="350" viewBox="0 0 500 350" class="chart-image">
                                <!-- Chart background -->
                                <rect width="500" height="350" fill="#f8f9fa" stroke="#e0e0e0"/>
                                
                                <!-- Chart title -->
                                <text x="250" y="25" text-anchor="middle" font-size="14" font-weight="bold">Number of Adults Participating in Major Sports</text>
                                
                                <!-- Y-axis -->
                                <line x1="60" y1="50" x2="60" y2="300" stroke="#333" stroke-width="2"/>
                                <text x="30" y="55" font-size="12">3000</text>
                                <text x="30" y="100" font-size="12">2500</text>
                                <text x="30" y="145" font-size="12">2000</text>
                                <text x="30" y="190" font-size="12">1500</text>
                                <text x="30" y="235" font-size="12">1000</text>
                                <text x="30" y="280" font-size="12">500</text>
                                <text x="45" y="305" font-size="12">0</text>
                                
                                <!-- X-axis -->
                                <line x1="60" y1="300" x2="450" y2="300" stroke="#333" stroke-width="2"/>
                                
                                <!-- Grid lines -->
                                <line x1="60" y1="50" x2="450" y2="50" stroke="#ddd" stroke-width="1"/>
                                <line x1="60" y1="95" x2="450" y2="95" stroke="#ddd" stroke-width="1"/>
                                <line x1="60" y1="140" x2="450" y2="140" stroke="#ddd" stroke-width="1"/>
                                <line x1="60" y1="185" x2="450" y2="185" stroke="#ddd" stroke-width="1"/>
                                <line x1="60" y1="230" x2="450" y2="230" stroke="#ddd" stroke-width="1"/>
                                <line x1="60" y1="275" x2="450" y2="275" stroke="#ddd" stroke-width="1"/>
                                
                                <!-- Tennis bars -->
                                <rect x="80" y="120" width="25" height="180" fill="#3498db"/>
                                <rect x="110" y="95" width="25" height="205" fill="#e74c3c"/>
                                <text x="92" y="315" font-size="10" text-anchor="middle">Tennis</text>
                                
                                <!-- Basketball bars -->
                                <rect x="160" y="185" width="25" height="115" fill="#3498db"/>
                                <rect x="190" y="140" width="25" height="160" fill="#e74c3c"/>
                                <text x="172" y="315" font-size="10" text-anchor="middle">Basketball</text>
                                
                                <!-- Badminton bars -->
                                <rect x="240" y="230" width="25" height="70" fill="#3498db"/>
                                <rect x="270" y="185" width="25" height="115" fill="#e74c3c"/>
                                <text x="252" y="315" font-size="10" text-anchor="middle">Badminton</text>
                                
                                <!-- Rugby bars -->
                                <rect x="320" y="275" width="25" height="25" fill="#3498db"/>
                                <rect x="350" y="230" width="25" height="70" fill="#e74c3c"/>
                                <text x="332" y="315" font-size="10" text-anchor="middle">Rugby</text>
                                
                                <!-- Legend -->
                                <rect x="70" y="330" width="15" height="15" fill="#3498db"/>
                                <text x="90" y="342" font-size="12">1997</text>
                                <rect x="140" y="330" width="15" height="15" fill="#e74c3c"/>
                                <text x="160" y="342" font-size="12">2017</text>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Task 2 Content -->
                <div class="task-content" data-task="2" style="display: none;">
                    <div class="task-container">
                        <h2 class="task-title">Writing Task 2</h2>
                        
                        <div class="task-instruction">
                            You should spend about <strong>40 minutes</strong> on this task. Write at least <strong>250 words</strong>.
                        </div>

                        <div class="task-description">
                            <h3 style="color: #2c3e50; margin-bottom: 1rem;">Essay Question:</h3>
                            
                            <div style="background: #f8f9fa; border-left: 4px solid #3498db; padding: 1.5rem; margin: 1rem 0; font-size: 1.1rem; line-height: 1.6;">
                                <p><strong>Some people believe that the internet has brought people closer together by enabling them to communicate with others around the world. Others argue that the internet has actually made people more isolated and less connected to their local communities.</strong></p>
                                
                                <p style="margin-top: 1rem;"><strong>Discuss both views and give your own opinion.</strong></p>
                            </div>

                            <p><strong>Give reasons for your answer and include any relevant examples from your own knowledge or experience.</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Writing Panel -->
            <div class="writing-panel">
                <!-- Task 1 Writing Area -->
                <div class="writing-area" data-task="1">
                    <h3>Task 1 Response</h3>
                    
                    <div class="word-count-info">
                        <span>Target: 150+ words</span>
                        <span class="word-count task1">0 words</span>
                        <div class="auto-save-indicator">
                            <span class="save-dot"></span>
                            <span>Auto-saved</span>
                        </div>
                    </div>

                    <div class="writing-tools">
                        <button class="tool-btn" data-action="bold">B</button>
                        <button class="tool-btn" data-action="italic">I</button>
                        <button class="tool-btn" data-action="underline">U</button>
                        <button class="tool-btn" data-action="copy">Copy</button>
                        <button class="tool-btn" data-action="paste">Paste</button>
                        <button class="tool-btn" data-action="clear">Clear</button>
                    </div>

                    <textarea 
                        class="writing-textarea task1" 
                        placeholder="Write your Task 1 response here..."
                    ></textarea>
                </div>

                <!-- Task 2 Writing Area -->
                <div class="writing-area" data-task="2" style="display: none;">
                    <h3>Task 2 Response</h3>
                    
                    <div class="word-count-info">
                        <span>Target: 250+ words</span>
                        <span class="word-count task2">0 words</span>
                        <div class="auto-save-indicator">
                            <span class="save-dot"></span>
                            <span>Auto-saved</span>
                        </div>
                    </div>

                    <div class="writing-tools">
                        <button class="tool-btn" data-action="bold">B</button>
                        <button class="tool-btn" data-action="italic">I</button>
                        <button class="tool-btn" data-action="underline">U</button>
                        <button class="tool-btn" data-action="copy">Copy</button>
                        <button class="tool-btn" data-action="paste">Paste</button>
                        <button class="tool-btn" data-action="clear">Clear</button>
                    </div>

                    <textarea 
                        class="writing-textarea task2" 
                        placeholder="Write your Task 2 essay here..."
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Progress and Navigation -->
        <div class="progress-section">
            <button class="nav-btn secondary" onclick="window.location.href='index.html'">← Back to Menu</button>
            
            <div class="progress-info">
                <div style="display: flex; gap: 2rem; align-items: center;">
                    <div>
                        <div class="progress-bar" style="width: 150px; margin: 0;">
                            <div class="progress-fill task1" style="width: 0%;"></div>
                        </div>
                        <span style="font-size: 0.8rem;">Task 1</span>
                    </div>
                    <div>
                        <div class="progress-bar" style="width: 150px; margin: 0;">
                            <div class="progress-fill task2" style="width: 0%;"></div>
                        </div>
                        <span style="font-size: 0.8rem;">Task 2</span>
                    </div>
                </div>
            </div>
            
            <button class="nav-btn primary" id="submitBtn">Submit Complete Test</button>
        </div>
    </div>

    <script src="js/writing.js"></script>
    <script>
        // Enable task switching for complete test
        function switchTask(taskNum) {
            currentTask = taskNum;
            
            // Update tab active state
            document.querySelectorAll('.task-tab').forEach(tab => {
                tab.classList.toggle('active', parseInt(tab.dataset.task) === taskNum);
            });
            
            // Update content visibility
            document.querySelectorAll('.task-content').forEach(content => {
                content.style.display = content.dataset.task == taskNum ? 'block' : 'none';
            });
            
            // Update writing areas
            document.querySelectorAll('.writing-area').forEach(area => {
                area.style.display = area.dataset.task == taskNum ? 'block' : 'none';
            });
            
            updateWordCount();
        }
    </script>


<script src="{{ asset('js/writing-test.js') }}"></script>
</body>
</html>
