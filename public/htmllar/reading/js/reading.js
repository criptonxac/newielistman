// Global variables for Reading Test
let currentAnswers = {};
let totalQuestions = 40;
let answeredCount = 0;
let timerMinutes = 60; // 60 minutes for reading
let timerSeconds = 0;
let timerInterval;

// Correct answers for demonstration
const correctAnswers = {
    // Part 1: Marie Curie (Questions 1-13)
    '1': 'FALSE',
    '2': 'NOT GIVEN', 
    '3': 'TRUE',
    '4': 'FALSE',
    '5': 'TRUE',
    '6': 'FALSE',
    '7': 'thorium',
    '8': 'pitchblende',
    '9': 'radium',
    '10': 'soldiers',
    '11': 'illness',
    '12': 'neutron',
    '13': 'leukaemia',
    
    // Part 2: Traffic Physics (Questions 14-26)
    '14': 'B',
    '15': 'D',
    '16': 'F',
    '17': 'G',
    '18': 'A',
    '19': 'E',
    '20': 'chaos theory',
    '21': 'vapor',
    '22': 'dust',
    '23': 'synchronized',
    '24': 'density',
    '25': 'computers',
    '26': 'psychology',
    
    // Part 3: (Questions 27-40) - Will be defined based on the third passage
    '27': 'multiple choice answer',
    '28': 'fill blank answer',
    // ... more answers
};

// Initialize Reading Test
function initReadingTest() {
    initTrueFalseQuestions();
    initFillBlankQuestions();
    initMultipleChoiceQuestions();
    initDragAndDropHeadings();
    updateProgress();
}

// Initialize True/False/Not Given Questions
function initTrueFalseQuestions() {
    const tfngInputs = document.querySelectorAll('input[name^="tfng"]');
    
    tfngInputs.forEach(input => {
        input.addEventListener('change', function() {
            const questionNum = this.name.replace('tfng', '');
            currentAnswers[questionNum] = this.value;
            
            // Update answered count
            updateAnsweredCount();
            updateProgress();
            saveToStorage();
        });
    });
}

// Initialize Fill in the Blank Questions  
function initFillBlankQuestions() {
    const fillInputs = document.querySelectorAll('.fill-blank-input');
    
    fillInputs.forEach(input => {
        // Auto-save on input
        input.addEventListener('input', function() {
            const questionNum = this.dataset.question;
            const answer = this.value.trim();
            
            if (answer) {
                currentAnswers[questionNum] = answer;
                this.classList.add('filled');
                if (!this.dataset.counted) {
                    this.dataset.counted = 'true';
                }
            } else {
                delete currentAnswers[questionNum];
                this.classList.remove('filled');
                this.dataset.counted = '';
            }
            
            updateAnsweredCount();
            updateProgress();
            saveToStorage();
        });

        // Enter key to go to next input
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const allInputs = Array.from(document.querySelectorAll('.fill-blank-input'));
                const currentIndex = allInputs.indexOf(this);
                if (currentIndex < allInputs.length - 1) {
                    allInputs[currentIndex + 1].focus();
                }
            }
        });
    });
}

// Initialize Multiple Choice Questions
function initMultipleChoiceQuestions() {
    const mcInputs = document.querySelectorAll('input[name^="mc"]');
    
    mcInputs.forEach(input => {
        input.addEventListener('change', function() {
            const questionNum = this.name.replace('mc', '');
            currentAnswers[questionNum] = this.value;
            
            updateAnsweredCount();
            updateProgress();
            saveToStorage();
        });
    });
}

// Initialize Drag and Drop for Headings
function initDragAndDropHeadings() {
    const draggableItems = document.querySelectorAll('.heading-item');
    const dropZones = document.querySelectorAll('.drop-zone');

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
    
    if (draggedElement && e.target.classList.contains('drop-zone')) {
        const questionNum = e.target.dataset.question;
        const value = draggedElement.textContent.trim();
        
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
        draggedElement.setAttribute('data-value', value);
        
        // Update answers
        currentAnswers[questionNum] = value;
        
        // Animation
        e.target.classList.add('drop-success');
        setTimeout(() => {
            e.target.classList.remove('drop-success');
        }, 300);
        
        updateAnsweredCount();
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
        e.target.querySelector('.placeholder').textContent = `Question ${questionNum}`;
        
        // Un-mark the item as used
        const item = document.querySelector(`[data-value="${value}"]`);
        if (item) {
            item.classList.remove('used');
        }
        
        updateAnsweredCount();
        updateProgress();
        saveToStorage();
    }
}

// Update answered count
function updateAnsweredCount() {
    answeredCount = Object.keys(currentAnswers).length;
}

// Update progress display
function updateProgress() {
    const progressInfo = document.querySelector('.progress-info');
    const progressFill = document.querySelector('.progress-fill');
    
    if (progressInfo && progressFill) {
        const percentage = (answeredCount / totalQuestions) * 100;
        progressInfo.textContent = `${answeredCount}/${totalQuestions} questions answered`;
        progressFill.style.width = percentage + '%';
    }
}

// Timer functionality
function startTimer() {
    timerInterval = setInterval(() => {
        if (timerSeconds === 0) {
            if (timerMinutes === 0) {
                clearInterval(timerInterval);
                alert('Time is up! Test completed.');
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
    if (timer) {
        const minutes = timerMinutes.toString().padStart(2, '0');
        const seconds = timerSeconds.toString().padStart(2, '0');
        timer.textContent = `${minutes}:${seconds}`;
        
        // Change color when time is running low
        if (timerMinutes < 10) {
            timer.style.color = '#e74c3c';
        } else if (timerMinutes < 20) {
            timer.style.color = '#f39c12';
        } else {
            timer.style.color = '#27ae60';
        }
    }
}

// Save answers to localStorage
function saveToStorage() {
    localStorage.setItem('ielts_reading_answers', JSON.stringify(currentAnswers));
}

// Load answers from localStorage
function loadFromStorage() {
    const saved = localStorage.getItem('ielts_reading_answers');
    if (saved) {
        currentAnswers = JSON.parse(saved);
        
        // Restore answers to form elements
        Object.keys(currentAnswers).forEach(questionNum => {
            const answer = currentAnswers[questionNum];
            
            // True/False/Not Given questions
            const tfngInput = document.querySelector(`input[name="tfng${questionNum}"][value="${answer}"]`);
            if (tfngInput) {
                tfngInput.checked = true;
            }
            
            // Fill blank questions
            const fillInput = document.querySelector(`input[data-question="${questionNum}"]`);
            if (fillInput) {
                fillInput.value = answer;
                fillInput.classList.add('filled');
            }
            
            // Multiple choice questions
            const mcInput = document.querySelector(`input[name="mc${questionNum}"][value="${answer}"]`);
            if (mcInput) {
                mcInput.checked = true;
            }
            
            // Drop zones
            const dropZone = document.querySelector(`.drop-zone[data-question="${questionNum}"]`);
            if (dropZone) {
                dropZone.dataset.answer = answer;
                dropZone.classList.add('filled');
                dropZone.querySelector('.placeholder').textContent = answer;
                
                // Mark corresponding heading as used
                const headingItem = Array.from(document.querySelectorAll('.heading-item'))
                    .find(item => item.textContent.trim() === answer);
                if (headingItem) {
                    headingItem.classList.add('used');
                    headingItem.setAttribute('data-value', answer);
                }
            }
        });
        
        updateAnsweredCount();
        updateProgress();
    }
}

// Check answers function
function checkAnswers() {
    let correctCount = 0;
    const totalAnswered = Object.keys(currentAnswers).length;
    
    Object.keys(currentAnswers).forEach(questionNum => {
        const userAnswer = currentAnswers[questionNum].toLowerCase().trim();
        const correctAnswer = correctAnswers[questionNum] ? correctAnswers[questionNum].toLowerCase() : '';
        
        if (userAnswer === correctAnswer) {
            correctCount++;
        }
    });
    
    const percentage = Math.round((correctCount / totalAnswered) * 100);
    alert(`Reading Test Results:\nCorrect: ${correctCount}/${totalAnswered}\nPercentage: ${percentage}%`);
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', () => {
    initReadingTest();
    if (document.getElementById('timer')) {
        startTimer();
    }
    loadFromStorage();
    
    // Auto-save every 30 seconds
    setInterval(saveToStorage, 30000);
    
    // Add check answers functionality if button exists
    const checkBtn = document.getElementById('checkBtn');
    if (checkBtn) {
        checkBtn.addEventListener('click', checkAnswers);
    }
});

// Prevent page reload on drag
document.addEventListener('dragover', (e) => {
    e.preventDefault();
});

document.addEventListener('drop', (e) => {
    e.preventDefault();
});