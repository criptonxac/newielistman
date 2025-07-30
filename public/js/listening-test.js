// IELTS Listening Test JavaScript
let currentPart = 1;
let totalParts = 4;
let currentAnswers = {};
let answeredCount = 0;
let totalQuestions = 10;
let testStarted = false;
let testTimer;
let audioFiles = {};
let timeRemaining = 0;

// Test selection functions
function startFullTest() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentPart = 1;
    totalQuestions = 40;
    showPart(1);
    startTimer();
    testStarted = true;
}

function showParts() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('parts-selection').style.display = 'block';
}

function startPart(partNum) {
    document.getElementById('parts-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentPart = partNum;
    totalQuestions = 10;
    showPart(partNum);
    testStarted = true;
}

function backToSelection() {
    document.getElementById('parts-selection').style.display = 'none';
    document.getElementById('test-selection').style.display = 'block';
}

// Part navigation
function showPart(partNum) {
    console.log(`Showing part ${partNum}`);
    
    // Hide all parts
    document.querySelectorAll('.part-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Show selected part
    const partContent = document.getElementById(`part${partNum}-content`);
    if (partContent) {
        partContent.classList.add('active');
    } else {
        console.error(`Part content #part${partNum}-content not found`);
        return;
    }
    
    // Update part buttons
    document.querySelectorAll('.part-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    const partBtn = document.getElementById(`part${partNum}-btn`);
    if (partBtn) {
        partBtn.classList.add('active');
    }
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) {
        prevBtn.style.display = partNum > 1 ? 'block' : 'none';
    }
    
    if (nextBtn) {
        nextBtn.textContent = partNum < totalParts ? 'Keyingi →' : 'Yakunlash';
    }
    
    // Update audio source
    const audioPlayer = document.getElementById('audioPlayer');
    const audioSource = document.getElementById('audioSource');
    const playBtn = document.getElementById('playBtn');
    const loadingIndicator = document.getElementById('audioLoadingIndicator');
    const loadingProgress = document.getElementById('audioLoadingProgress');
    
    if (audioSource && window.audioFiles && window.audioFiles[`part${partNum}`]) {
        console.log(`Setting audio source to: ${window.audioFiles[`part${partNum}`]}`);
        
        // Show loading indicator
        if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
            loadingProgress.style.width = '0%';
            loadingProgress.textContent = '0%';
        }
        
        audioSource.src = window.audioFiles[`part${partNum}`];
        audioPlayer.load();
        
        // Automatically play audio when loaded
        audioPlayer.oncanplaythrough = function() {
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
            audioPlayer.play();
            if (playBtn) {
                playBtn.textContent = '⏸';
            }
        };
        
        // Track loading progress
        audioPlayer.addEventListener('progress', function() {
            if (audioPlayer.buffered.length > 0) {
                const bufferedEnd = audioPlayer.buffered.end(audioPlayer.buffered.length - 1);
                const duration = audioPlayer.duration;
                
                if (duration > 0) {
                    const loadedPercentage = Math.round((bufferedEnd / duration) * 100);
                    if (loadingProgress) {
                        loadingProgress.style.width = loadedPercentage + '%';
                        loadingProgress.textContent = loadedPercentage + '%';
                    }
                }
            }
        });
        
        // Handle loading error
        audioPlayer.onerror = function() {
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
            alert('Audio yuklanmadi. Iltimos, qayta urinib ko\'ring yoki boshqa formatdagi audioga o\'ting.');
            console.error('Audio loading error:', audioPlayer.error);
        };
        
        // Reset play button
        if (playBtn) {
            playBtn.textContent = '▶';
        }
    } else {
        console.error(`Audio source or audio files for part${partNum} not found`);
        alert('Audio fayl topilmadi. Iltimos, administratorga murojaat qiling.');
    }
    
    // Update current part
    currentPart = partNum;
}

function nextPart() {
    if (currentPart < totalParts) {
        currentPart++;
        showPart(currentPart);
    } else {
        finishTest();
    }
}

function previousPart() {
    if (currentPart > 1) {
        currentPart--;
        showPart(currentPart);
    }
}

// Timer function
function startTimer() {
    let minutes = 30;
    let seconds = 0;
    
    testTimer = setInterval(() => {
        const timerDisplay = document.getElementById('timer');
        const displayMinutes = minutes.toString().padStart(2, '0');
        const displaySeconds = seconds.toString().padStart(2, '0');
        timerDisplay.textContent = `${displayMinutes}:${displaySeconds}`;
        
        // Change color based on time remaining
        if (minutes < 5) {
            timerDisplay.style.color = '#e74c3c';
        } else if (minutes < 10) {
            timerDisplay.style.color = '#f39c12';
        }
        
        if (seconds === 0) {
            if (minutes === 0) {
                clearInterval(testTimer);
                alert('Vaqt tugadi! Test yakunlandi.');
                finishTest();
                return;
            }
            minutes--;
            seconds = 59;
        } else {
            seconds--;
        }
    }, 1000);
}

// Audio player controls
function initializeAudioPlayer() {
    const playBtn = document.getElementById('playBtn');
    const audioPlayer = document.getElementById('audioPlayer');
    
    if (playBtn && audioPlayer) {
        playBtn.addEventListener('click', function() {
            if (audioPlayer.paused) {
                audioPlayer.play()
                .then(() => {
                    playBtn.textContent = '⏸'; // Pause symbol
                })
                .catch(error => {
                    console.error('Audio playback failed:', error);
                    alert('Audio ijro etilmadi. Iltimos, qayta urinib ko\'ring.');
                });
            } else {
                audioPlayer.pause();
                playBtn.textContent = '▶'; // Play symbol
            }
        });
        
        // Update play button when audio ends
        audioPlayer.addEventListener('ended', function() {
            playBtn.textContent = '▶'; // Play symbol
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Listening test initialized');
    
    // Initialize audio player
    initializeAudioPlayer();
    
    // Initialize input handling
    initializeInputHandling();
    
    // Initialize drag and drop
    initializeDragAndDrop();
    
    // Initialize volume slider
    const volumeSlider = document.querySelector('.volume-slider');
    const audioPlayer = document.getElementById('audioPlayer');
    
    if (volumeSlider && audioPlayer) {
        volumeSlider.addEventListener('input', function() {
            audioPlayer.volume = this.value / 100;
        });
    }
    
    // Show first part
    showPart(1);
});

// Input handling
function initializeInputHandling() {
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('answer-input')) {
            const questionNum = e.target.dataset.question;
            const answer = e.target.value.trim();
            
            if (answer) {
                currentAnswers[questionNum] = answer;
                e.target.classList.add('filled');
                if (!e.target.dataset.counted) {
                    answeredCount++;
                    e.target.dataset.counted = 'true';
                }
            } else {
                delete currentAnswers[questionNum];
                e.target.classList.remove('filled');
                if (e.target.dataset.counted) {
                    answeredCount--;
                    e.target.dataset.counted = '';
                }
            }
            
            updateProgress();
        }
    });
}

function updateProgress() {
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const percentage = (answeredCount / totalQuestions) * 100;
    
    if (progressText && progressBar) {
        progressBar.style.width = percentage + '%';
    }
    
    if (progressText) {
        progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
    }
}

function finishTest() {
    clearInterval(testTimer);
    // Clear localStorage timer
    localStorage.removeItem('listeningTestTimer');
    alert(`Test yakunlandi! Siz ${answeredCount}/${totalQuestions} savolga javob berdingiz.`);
    
    // Test formini topish va yuborish
    const testForm = document.getElementById('testForm');
    if (testForm) {
        console.log('Test form found, submitting...');
        testForm.submit();
    } else {
        console.error('Test form not found');
    }
}

// Save answer to server via AJAX
function saveAnswer(questionId, answer) {
    // Use the routes provided from the Blade template
    fetch(window.routes.saveAnswer, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrf_token
        },
        body: JSON.stringify({
            question_id: questionId,
            answer: answer
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Answer saved:', data);
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// Drag and Drop functionality
function initializeDragAndDrop() {
    console.log('Initializing drag and drop...');
    const draggables = document.querySelectorAll('.draggable');
    const dropZones = document.querySelectorAll('.drop-zone');
    
    if (draggables.length === 0) {
        console.log('No draggable elements found');
    } else {
        console.log(`Found ${draggables.length} draggable elements`);
    }
    
    if (dropZones.length === 0) {
        console.log('No drop zones found');
    } else {
        console.log(`Found ${dropZones.length} drop zones`);
    }
    
    // Add event listeners to draggable elements
    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', dragStart);
        draggable.addEventListener('dragend', dragEnd);
    });
    
    // Add event listeners to drop zones
    dropZones.forEach(dropZone => {
        dropZone.addEventListener('dragover', dragOver);
        dropZone.addEventListener('dragenter', dragEnter);
        dropZone.addEventListener('dragleave', dragLeave);
        dropZone.addEventListener('drop', drop);
    });
    
    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-answer').forEach(button => {
        button.addEventListener('click', removeAnswer);
    });
}

function dragStart(e) {
    this.classList.add('dragging');
    e.dataTransfer.setData('text/plain', this.getAttribute('data-value'));
}

function dragEnd() {
    this.classList.remove('dragging');
}

function dragOver(e) {
    e.preventDefault();
}

function dragEnter(e) {
    e.preventDefault();
    this.classList.add('drag-over');
}

function dragLeave() {
    this.classList.remove('drag-over');
}

function drop(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    
    // Get the dragged value
    const draggedValue = e.dataTransfer.getData('text/plain');
    const itemId = this.getAttribute('data-item-id');
    
    // Check if drop zone already has an answer
    const existingAnswer = this.querySelector('.dragged-answer');
    if (existingAnswer) {
        existingAnswer.remove();
    }
    
    // Remove placeholder if it exists
    const placeholder = this.querySelector('.placeholder');
    if (placeholder) {
        placeholder.remove();
    }
    
    // Create the dropped element
    const droppedElement = document.createElement('div');
    droppedElement.className = 'dragged-answer bg-blue-100 border-blue-300 p-2 rounded w-full h-full flex items-center justify-center';
    droppedElement.textContent = draggedValue;
    this.appendChild(droppedElement);
    
    // Add remove button
    const removeButton = document.createElement('button');
    removeButton.className = 'remove-answer absolute -top-2 -right-2 bg-white rounded-full w-5 h-5 flex items-center justify-center text-gray-500 hover:text-red-500 border border-gray-300';
    removeButton.innerHTML = '&times;';
    removeButton.addEventListener('click', removeAnswer);
    this.appendChild(removeButton);
    
    // Update hidden input value
    const hiddenInput = this.querySelector('input[type="hidden"]');
    if (hiddenInput) {
        hiddenInput.value = draggedValue;
        
        // Get question number from input name
        const questionName = hiddenInput.name;
        const wasAnswered = currentAnswers[questionName] !== undefined;
        
        // Update current answers
        currentAnswers[questionName] = draggedValue;
        
        // If this is a new answer, increment count
        if (!wasAnswered) {
            answeredCount++;
        }
        
        // Update progress
        updateProgress();
        
        // Save answer to server
        saveAnswer(questionName, draggedValue);
    }
}

function removeAnswer(e) {
    e.stopPropagation();
    const dropZone = this.parentElement;
    const hiddenInput = dropZone.querySelector('input[type="hidden"]');
    
    // Remove the answer element
    const answerElement = dropZone.querySelector('.dragged-answer');
    if (answerElement) {
        answerElement.remove();
    }
    
    // Remove the button itself
    this.remove();
    
    // Add placeholder back
    const placeholder = document.createElement('div');
    placeholder.className = 'placeholder text-gray-400 text-sm';
    placeholder.textContent = 'Drop here';
    dropZone.appendChild(placeholder);
    
    if (hiddenInput) {
        // Get question number from input name
        const questionName = hiddenInput.name;
        
        // Check if this question was already answered
        if (currentAnswers[questionName] !== undefined) {
            delete currentAnswers[questionName];
            answeredCount--;
        }
        
        // Clear hidden input
        hiddenInput.value = '';
        
        // Update progress
        updateProgress();
        
        // Save empty answer to server
        saveAnswer(questionName, '');
    }
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (!testStarted) return;
        
        if (e.key >= '1' && e.key <= '4') {
            const partNum = parseInt(e.key);
            if (partNum <= totalParts) {
                currentPart = partNum;
                showPart(currentPart);
            }
        }
        
        if (e.key === 'ArrowLeft' && currentPart > 1) {
            previousPart();
        }
        if (e.key === 'ArrowRight' && currentPart < totalParts) {
            nextPart();
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Listening test page loaded');
    
    // Initialize audio player
    initializeAudioPlayer();
    
    // Initialize drag and drop functionality
    initializeDragAndDrop();
    
    // Initialize input handling
    initializeInputHandling();
    
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
    
    // Auto-start timer for standalone test pages
    if (document.getElementById('timer')) {
        startTimer();
        testStarted = true;
    }
    
    // Auto-play audio after a short delay
    setTimeout(function() {
        const audioPlayer = document.getElementById('audioPlayer');
        const playBtn = document.getElementById('playBtn');
        
        if (audioPlayer && playBtn) {
            audioPlayer.play()
                .then(() => {
                    playBtn.textContent = '⏸';
                    console.log('Audio started playing automatically');
                })
                .catch(err => {
                    console.log('Auto-play prevented by browser:', err);
                });
        }
    }, 1000);
    
    // Count initial answered questions and update progress
    countAnsweredQuestions();
    updateProgress();
});
