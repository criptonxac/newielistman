// IELTS Writing Test JavaScript
let currentTask = 1;
let totalTasks = 2;
let task1Words = 0;
let task2Words = 0;
let testStarted = false;
let testTimer;
let autoSaveInterval;

// Test selection functions
function startFullTest() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentTask = 1;
    showTask(1);
    startTimer();
    startAutoSave();
    testStarted = true;
}

function showTasks() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('tasks-selection').style.display = 'block';
}

function startTask(taskNum) {
    document.getElementById('tasks-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentTask = taskNum;
    showTask(taskNum);
    startAutoSave();
    testStarted = true;
}

function backToSelection() {
    document.getElementById('tasks-selection').style.display = 'none';
    document.getElementById('test-selection').style.display = 'block';
}

// Task navigation
function showTask(taskNum) {
    // Hide all tasks
    document.querySelectorAll('.test-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.writing-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.writing-tips').forEach(tips => {
        tips.classList.remove('active');
    });
    
    // Show selected task
    document.getElementById(`task${taskNum}-content`).classList.add('active');
    document.getElementById(`task${taskNum}-writing`).classList.add('active');
    document.getElementById(`task${taskNum}-tips`).classList.add('active');
    
    // Update navigation buttons
    document.getElementById('prevBtn').style.display = taskNum > 1 ? 'block' : 'none';
    document.getElementById('nextBtn').textContent = taskNum < totalTasks ? 'Keyingi →' : 'Yakunlash';
    
    // Update current task display
    if (document.getElementById('currentTaskDisplay')) {
        document.getElementById('currentTaskDisplay').textContent = taskNum;
    }
    
    // Update progress text
    if (document.getElementById('progressText')) {
        document.getElementById('progressText').textContent = `Task ${taskNum} of ${totalTasks}`;
    }
    
    // Update progress bar
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        const progress = (taskNum / totalTasks) * 100;
        progressBar.style.width = progress + '%';
    }
}

function nextTask() {
    if (currentTask < totalTasks) {
        currentTask++;
        showTask(currentTask);
    } else {
        finishTest();
    }
}

function previousTask() {
    if (currentTask > 1) {
        currentTask--;
        showTask(currentTask);
    }
}

// Timer function
function startTimer() {
    let minutes = 60;
    let seconds = 0;
    
    testTimer = setInterval(() => {
        const timerDisplay = document.getElementById('timer');
        const timeRemaining = document.getElementById('timeRemaining');
        const displayMinutes = minutes.toString().padStart(2, '0');
        const displaySeconds = seconds.toString().padStart(2, '0');
        const timeString = `${displayMinutes}:${displaySeconds}`;
        
        timerDisplay.textContent = timeString;
        if (timeRemaining) timeRemaining.textContent = timeString;
        
        // Change color based on time remaining
        if (minutes < 10) {
            timerDisplay.style.color = '#e74c3c';
        } else if (minutes < 20) {
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

// Word counting function
function countWords(text) {
    return text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

// Update word counts
function updateWordCounts() {
    const task1Textarea = document.getElementById('task1-textarea');
    const task2Textarea = document.getElementById('task2-textarea');
    const task1Counter = document.getElementById('task1-wordcount');
    const task2Counter = document.getElementById('task2-wordcount');
    const totalWordsDisplay = document.getElementById('totalWordsDisplay');
    
    if (task1Textarea && task1Counter) {
        task1Words = countWords(task1Textarea.value);
        task1Counter.textContent = `${task1Words} words`;
        
        // Update textarea styling based on word count
        if (task1Words >= 150) {
            task1Textarea.classList.add('word-count-ok');
            task1Textarea.classList.remove('word-count-low');
            task1Counter.style.color = '#27ae60';
        } else {
            task1Textarea.classList.add('word-count-low');
            task1Textarea.classList.remove('word-count-ok');
            task1Counter.style.color = '#e74c3c';
        }
    }
    
    if (task2Textarea && task2Counter) {
        task2Words = countWords(task2Textarea.value);
        task2Counter.textContent = `${task2Words} words`;
        
        // Update textarea styling based on word count
        if (task2Words >= 250) {
            task2Textarea.classList.add('word-count-ok');
            task2Textarea.classList.remove('word-count-low');
            task2Counter.style.color = '#27ae60';
        } else {
            task2Textarea.classList.add('word-count-low');
            task2Textarea.classList.remove('word-count-ok');
            task2Counter.style.color = '#e74c3c';
        }
    }
    
    if (totalWordsDisplay) {
        totalWordsDisplay.textContent = task1Words + task2Words;
    }
}

// Auto-save functionality
function startAutoSave() {
    autoSaveInterval = setInterval(() => {
        saveAnswers();
    }, 30000); // Save every 30 seconds
}

function saveAnswers() {
    const task1Text = document.getElementById('task1-textarea').value;
    const task2Text = document.getElementById('task2-textarea').value;
    
    // Save to localStorage as backup
    localStorage.setItem('ielts_writing_task1', task1Text);
    localStorage.setItem('ielts_writing_task2', task2Text);
    
    // Here you would typically send to server
    console.log('Answers auto-saved');
}

// Load saved answers
function loadSavedAnswers() {
    const task1Saved = localStorage.getItem('ielts_writing_task1');
    const task2Saved = localStorage.getItem('ielts_writing_task2');
    
    if (task1Saved) {
        document.getElementById('task1-textarea').value = task1Saved;
    }
    
    if (task2Saved) {
        document.getElementById('task2-textarea').value = task2Saved;
    }
    
    updateWordCounts();
}

function finishTest() {
    if (testTimer) {
        clearInterval(testTimer);
    }
    
    if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
    }
    
    // Final save
    saveAnswers();
    
    const totalWords = task1Words + task2Words;
    const task1Status = task1Words >= 150 ? 'Yetarli' : 'Kam';
    const task2Status = task2Words >= 250 ? 'Yetarli' : 'Kam';
    
    alert(`Test yakunlandi!\n\nTask 1: ${task1Words} so'z (${task1Status})\nTask 2: ${task2Words} so'z (${task2Status})\nJami: ${totalWords} so'z`);
    
    // Clear saved data
    localStorage.removeItem('ielts_writing_task1');
    localStorage.removeItem('ielts_writing_task2');
    
    // Redirect to results or dashboard
    window.location.href = "/student/dashboard";
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (!testStarted) return;
        
        if (e.key >= '1' && e.key <= '2') {
            const taskNum = parseInt(e.key);
            currentTask = taskNum;
            showTask(currentTask);
        }
        
        if (e.key === 'ArrowLeft' && currentTask > 1) {
            previousTask();
        }
        if (e.key === 'ArrowRight' && currentTask < totalTasks) {
            nextTask();
        }
        
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            saveAnswers();
            alert('Javoblar saqlandi!');
        }
    });
}

// Initialize text area event listeners
function initializeTextAreas() {
    const task1Textarea = document.getElementById('task1-textarea');
    const task2Textarea = document.getElementById('task2-textarea');
    
    if (task1Textarea) {
        task1Textarea.addEventListener('input', updateWordCounts);
    }
    
    if (task2Textarea) {
        task2Textarea.addEventListener('input', updateWordCounts);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeTextAreas();
    initializeKeyboardShortcuts();
    loadSavedAnswers();
    
    // Auto-start timer for standalone test pages
    if (document.getElementById('timer')) {
        startTimer();
        startAutoSave();
        testStarted = true;
    }
    
    // Prevent accidental page refresh
    window.addEventListener('beforeunload', function(e) {
        if (testStarted) {
            saveAnswers();
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
