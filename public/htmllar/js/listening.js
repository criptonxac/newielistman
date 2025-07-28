        // Global variables
        let currentAnswers = {};
        let totalQuestions = 40;
        let answeredCount = 0;
        let timerMinutes = 30;
        let timerSeconds = 0;
        let timerInterval;

        // Correct answers (for demonstration)
        const correctAnswers = {
            // Part 1
            '1': 'oval',
            '2': 'medium', 
            '3': '4',
            '4': 'leather',
            '5': 'good',
            '6': 'lock',
            '7': '45',
            '8': 'Old',
            '9': 'left',
            '10': 'church',
            // Part 2
            '11': 'finance',
            '12': 'health',
            '13': 'key counselling',
            '14': 'rooms',
            '15': 'trips',
            '16': 'cookery room',
            '17': 'pottery room',
            '18': 'games room',
            '19': 'kitchen',
            '20': 'sports complex',
            // Part 3
            '21': 'rare type',
            '22': 'organic matter',
            '23': 'safe ground',
            '24': 'information',
            '25': 'three-dimensional',
            '26': 'plant cells',
            '27': 'surface',
            '28': 'radiation',
            '29': 'vacuum',
            '30': 'heat',
            // Part 4
            '31': 'socio-economic level',
            '32': 'financial constraints',
            '33': 'learning',
            '34': 'school',
            '35': 'grades',
            '36': 'friends',
            '37': 'faculty',
            '38': 'confidence',
            '39': 'mentors',
            '40': 'support'
        };

        // Initialize input fields
        function initInputFields() {
            const answerInputs = document.querySelectorAll('.answer-input');

            answerInputs.forEach(input => {
                // Auto-save on input
                input.addEventListener('input', function() {
                    const questionNum = this.dataset.question;
                    const answer = this.value.trim();
                    
                    if (answer) {
                        currentAnswers[questionNum] = answer;
                        this.classList.add('filled');
                        if (!this.dataset.counted) {
                            answeredCount++;
                            this.dataset.counted = 'true';
                        }
                    } else {
                        delete currentAnswers[questionNum];
                        this.classList.remove('filled');
                        if (this.dataset.counted) {
                            answeredCount--;
                            this.dataset.counted = '';
                        }
                    }
                    
                    updateProgress();
                });

                // Enter key to go to next input
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const allInputs = Array.from(document.querySelectorAll('.answer-input'));
                        const currentIndex = allInputs.indexOf(this);
                        if (currentIndex < allInputs.length - 1) {
                            allInputs[currentIndex + 1].focus();
                        }
                    }
                });

                // For questions 1-10: Uppercase input automatically
                if (parseInt(input.dataset.question) <= 10) {
                    input.addEventListener('input', function() {
                        if (this.value) {
                            this.value = this.value.toUpperCase();
                        }
                    });
                }
                
                // For questions 11-30: lowercase for easier matching
                if (parseInt(input.dataset.question) > 10) {
                    input.addEventListener('input', function() {
                        if (this.value) {
                            this.value = this.value.toLowerCase();
                        }
                    });
                }
            });
        }



        // Save answers to localStorage (optional)
        function saveToStorage() {
            localStorage.setItem('ielts_listening_answers', JSON.stringify(currentAnswers));
        }

        function loadFromStorage() {
            const saved = localStorage.getItem('ielts_listening_answers');
            if (saved) {
                currentAnswers = JSON.parse(saved);
                // Restore answers to inputs and drop zones
                Object.keys(currentAnswers).forEach(questionNum => {
                    const element = document.querySelector(`[data-question="${questionNum}"]`);
                    if (element) {
                        if (element.tagName === 'INPUT') {
                            // For input fields (Part 1 & 2)
                            element.value = currentAnswers[questionNum];
                            element.classList.add('filled');
                            element.dataset.counted = 'true';
                        } else {
                            // For drop zones (Part 3)
                            const value = currentAnswers[questionNum];
                            element.dataset.answer = value;
                            element.classList.add('filled');
                            element.querySelector('.placeholder').textContent = value;
                            
                            // Mark the corresponding draggable item as used
                            const item = document.querySelector(`[data-value="${value}"]`);
                            if (item) {
                                item.classList.add('used');
                            }
                        }
                        answeredCount++;
                    }
                });
                updateProgress();
            }
        }

        function updateProgress() {
            const progressText = document.getElementById('progressText');
            const progressBar = document.getElementById('progressBar');
            const percentage = (answeredCount / totalQuestions) * 100;
            
            progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
            progressBar.style.width = percentage + '%';
        }

        // Timer functionality
        function startTimer() {
            timerInterval = setInterval(() => {
                if (timerSeconds === 0) {
                    if (timerMinutes === 0) {
                        clearInterval(timerInterval);
                        alert('Time is up!');
                        return;
                    }
                    timerMinutes--;
                    timerSeconds = 59;
                } else {
                    timerSeconds--;
                }
                
                updateTimerDisplay();
            }, 1000);
        }

        function updateTimerDisplay() {
            const timer = document.getElementById('timer');
            const minutes = timerMinutes.toString().padStart(2, '0');
            const seconds = timerSeconds.toString().padStart(2, '0');
            timer.textContent = `${minutes}:${seconds}`;
            
            // Change color when time is running low
            if (timerMinutes < 5) {
                timer.style.color = '#e74c3c';
            } else {
                timer.style.color = '#f39c12';
            }
        }

        // Audio player functionality
        function initAudioPlayer() {
            const playBtn = document.getElementById('playBtn');
            const audioPlayer = document.getElementById('audioPlayer');
            const volumeSlider = document.querySelector('.volume-slider');
            
            if (playBtn && audioPlayer) {
                playBtn.addEventListener('click', () => {
                    if (audioPlayer.paused) {
                        audioPlayer.play();
                        playBtn.textContent = '⏸';
                    } else {
                        audioPlayer.pause();
                        playBtn.textContent = '▶';
                    }
                });
                
                audioPlayer.addEventListener('ended', () => {
                    playBtn.textContent = '▶';
                });
                
                // Set initial volume
                audioPlayer.volume = 0.7;
            }
            
            if (volumeSlider && audioPlayer) {
                volumeSlider.addEventListener('input', (e) => {
                    audioPlayer.volume = e.target.value / 100;
                });
            }
        }

        // Check answers function (for demo purposes)
        function checkAnswers() {
            let correctCount = 0;
            
            // Check input fields (Part 1 & 2)
            const answerInputs = document.querySelectorAll('.answer-input');
            answerInputs.forEach(input => {
                const questionNum = input.dataset.question;
                const userAnswer = input.value.toLowerCase().trim();
                const correctAnswer = correctAnswers[questionNum].toLowerCase();
                
                if (userAnswer === correctAnswer) {
                    correctCount++;
                    input.style.borderColor = '#27ae60';
                    input.style.backgroundColor = '#d5f4e6';
                } else {
                    input.style.borderColor = '#e74c3c';
                    input.style.backgroundColor = '#ffeaa7';
                }
            });
            
            // Check drop zones (Part 2, 3, 4)
            const dropZones = document.querySelectorAll('.drop-zone, .drop-zone-flow, .drop-zone-table, .drop-zone-rec');
            dropZones.forEach(zone => {
                const questionNum = zone.dataset.question;
                const userAnswer = zone.dataset.answer ? zone.dataset.answer.toLowerCase().trim() : '';
                const correctAnswer = correctAnswers[questionNum] ? correctAnswers[questionNum].toLowerCase() : '';
                
                if (userAnswer === correctAnswer) {
                    correctCount++;
                    zone.style.borderColor = '#27ae60';
                    zone.style.backgroundColor = '#d5f4e6';
                } else {
                    zone.style.borderColor = '#e74c3c';
                    zone.style.backgroundColor = '#ffeaa7';
                }
            });

            // Check radio buttons (Part 4 multiple choice)
            const radioButtons = document.querySelectorAll('input[type="radio"]:checked');
            radioButtons.forEach(radio => {
                const questionName = radio.name;
                const questionNum = questionName.replace('q', '');
                const userAnswer = radio.value.toLowerCase().trim();
                const correctAnswer = correctAnswers[questionNum] ? correctAnswers[questionNum].toLowerCase() : '';
                
                if (userAnswer === correctAnswer) {
                    correctCount++;
                    radio.parentElement.style.backgroundColor = '#d5f4e6';
                } else {
                    radio.parentElement.style.backgroundColor = '#ffeaa7';
                }
            });
            
            alert(`You got ${correctCount} out of ${totalQuestions} correct!`);
        }

        // Drag and Drop functionality for Part 3
        function initializeDragAndDrop() {
            // Make items draggable
            const draggableItems = document.querySelectorAll('.draggable-item');
            const dropZones = document.querySelectorAll('.drop-zone, .drop-zone-flow, .drop-zone-table, .drop-zone-rec');

            draggableItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            dropZones.forEach(zone => {
                zone.addEventListener('dragover', handleDragOver);
                zone.addEventListener('dragenter', handleDragEnter);
                zone.addEventListener('dragleave', handleDragLeave);
                zone.addEventListener('drop', handleDrop);
                zone.addEventListener('click', handleZoneClick);
            });
        }

        let draggedElement = null;

        function handleDragStart(e) {
            draggedElement = e.target;
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', e.target.outerHTML);
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }

        function handleDragEnter(e) {
            e.preventDefault();
            e.target.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.target.classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.target.classList.remove('drag-over');
            
            if (draggedElement && (e.target.classList.contains('drop-zone') || e.target.classList.contains('drop-zone-flow') || e.target.classList.contains('drop-zone-table') || e.target.classList.contains('drop-zone-rec'))) {
                const questionNum = e.target.dataset.question;
                const value = draggedElement.dataset.value;
                
                // Clear any existing answer in this zone
                if (e.target.dataset.answer) {
                    const oldItem = document.querySelector(`[data-value="${e.target.dataset.answer}"]`);
                    if (oldItem) {
                        oldItem.classList.remove('used');
                    }
                }
                
                // Set new answer
                e.target.dataset.answer = value;
                e.target.classList.add('filled');
                e.target.querySelector('.placeholder').textContent = value;
                
                // Mark the dragged item as used
                draggedElement.classList.add('used');
                
                // Update answer tracking
                if (!currentAnswers[questionNum]) {
                    answeredCount++;
                }
                currentAnswers[questionNum] = value;
                updateProgress();
                saveToStorage();
            }
        }

        function handleZoneClick(e) {
            // Allow clicking to remove answers
            if (e.target.classList.contains('filled')) {
                const questionNum = e.target.dataset.question;
                const value = e.target.dataset.answer;
                
                // Remove answer
                delete currentAnswers[questionNum];
                e.target.dataset.answer = '';
                e.target.classList.remove('filled');
                e.target.querySelector('.placeholder').textContent = questionNum;
                
                // Un-mark the item as used
                const item = document.querySelector(`[data-value="${value}"]`);
                if (item) {
                    item.classList.remove('used');
                }
                
                answeredCount--;
                updateProgress();
                saveToStorage();
            }
        }

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', () => {
            initInputFields();
            initAudioPlayer();
            startTimer();
            loadFromStorage();
            updateProgress();
            initializeDragAndDrop();
            
            // Auto-save every 10 seconds
            setInterval(saveToStorage, 10000);
            
            // Add check answers button for demo (if exists)
            const nextBtn = document.getElementById('nextBtn');
            if (nextBtn) {
                nextBtn.addEventListener('click', checkAnswers);
            }
            
            // Focus first input (if exists)
            const firstInput = document.querySelector('.answer-input');
            if (firstInput) {
                firstInput.focus();
            }
        });

        // Prevent page reload on drag
        document.addEventListener('dragover', (e) => {
            e.preventDefault();
        });
        
        document.addEventListener('drop', (e) => {
            e.preventDefault();
        });
