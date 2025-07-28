// Global variables for Writing Test
let currentTask = 1; // 1 or 2
let task1Content = '';
let task2Content = '';
let timerMinutes = 60; // 60 minutes for writing
let timerSeconds = 0;
let timerInterval;
let autoSaveInterval;

// Word count targets
const WORD_TARGETS = {
    task1: { min: 150, target: 200 },
    task2: { min: 250, target: 300 }
};

// Initialize Writing Test
function initWritingTest() {
    initTextareas();
    initTaskTabs();
    initWritingTools();
    updateWordCount();
    loadFromStorage();
}

// Initialize textareas
function initTextareas() {
    const textareas = document.querySelectorAll('.writing-textarea');
    
    textareas.forEach(textarea => {
        // Auto-save on input
        textarea.addEventListener('input', function() {
            const taskNum = this.classList.contains('task1') ? 1 : 2;
            
            if (taskNum === 1) {
                task1Content = this.value;
            } else {
                task2Content = this.value;
            }
            
            updateWordCount();
            showAutoSaveIndicator('saving');
            
            // Debounced save
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                saveToStorage();
                showAutoSaveIndicator('saved');
            }, 1000);
        });

        // Tab key handling
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                
                // Insert tab character
                this.value = this.value.substring(0, start) + '\t' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 1;
            }
        });
    });
}

// Initialize task tabs
function initTaskTabs() {
    const tabs = document.querySelectorAll('.task-tab');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const taskNum = parseInt(this.dataset.task);
            switchTask(taskNum);
        });
    });
}

// Switch between tasks
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
    
    updateWordCount();
}

// Initialize writing tools
function initWritingTools() {
    const toolBtns = document.querySelectorAll('.tool-btn');
    
    toolBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const textarea = document.querySelector(`.writing-textarea.task${currentTask}`);
            
            if (!textarea) return;
            
            switch(action) {
                case 'bold':
                    wrapSelectedText(textarea, '**', '**');
                    break;
                case 'italic':
                    wrapSelectedText(textarea, '*', '*');
                    break;
                case 'underline':
                    wrapSelectedText(textarea, '_', '_');
                    break;
                case 'clear':
                    if (confirm('Are you sure you want to clear all text?')) {
                        textarea.value = '';
                        textarea.dispatchEvent(new Event('input'));
                    }
                    break;
                case 'copy':
                    navigator.clipboard.writeText(textarea.value);
                    showNotification('Text copied to clipboard');
                    break;
                case 'paste':
                    navigator.clipboard.readText().then(text => {
                        insertAtCursor(textarea, text);
                    });
                    break;
            }
        });
    });
}

// Wrap selected text with formatting
function wrapSelectedText(textarea, prefix, suffix) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    if (selectedText) {
        const wrappedText = prefix + selectedText + suffix;
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);
        textarea.selectionStart = start + prefix.length;
        textarea.selectionEnd = start + prefix.length + selectedText.length;
    } else {
        // Insert at cursor
        insertAtCursor(textarea, prefix + suffix);
        textarea.selectionStart = textarea.selectionEnd = start + prefix.length;
    }
    
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
}

// Insert text at cursor
function insertAtCursor(textarea, text) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    
    textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + text.length;
    textarea.dispatchEvent(new Event('input'));
}

// Count words in text
function countWords(text) {
    if (!text || !text.trim()) return 0;
    return text.trim().split(/\s+/).length;
}

// Update word count display
function updateWordCount() {
    const task1Textarea = document.querySelector('.writing-textarea.task1');
    const task2Textarea = document.querySelector('.writing-textarea.task2');
    
    if (task1Textarea) {
        updateTaskWordCount(1, task1Textarea.value);
    }
    
    if (task2Textarea) {
        updateTaskWordCount(2, task2Textarea.value);
    }
}

// Update word count for specific task
function updateTaskWordCount(taskNum, content) {
    const wordCount = countWords(content);
    const target = WORD_TARGETS[`task${taskNum}`];
    const countElement = document.querySelector(`.word-count.task${taskNum}`);
    
    if (!countElement) return;
    
    countElement.textContent = `${wordCount} words`;
    
    // Update color based on target
    countElement.className = `word-count task${taskNum}`;
    
    if (wordCount < target.min) {
        countElement.classList.add('error');
    } else if (wordCount < target.target) {
        countElement.classList.add('warning');
    } else {
        // Normal color (blue)
    }
    
    // Update progress
    const progressElement = document.querySelector(`.progress-fill.task${taskNum}`);
    if (progressElement) {
        const percentage = Math.min((wordCount / target.target) * 100, 100);
        progressElement.style.width = percentage + '%';
    }
}

// Timer functionality
function startTimer() {
    updateTimerDisplay();
    
    timerInterval = setInterval(() => {
        if (timerSeconds === 0) {
            if (timerMinutes === 0) {
                clearInterval(timerInterval);
                alert('Time is up! Writing test completed.');
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

// Auto-save indicator
function showAutoSaveIndicator(status) {
    const indicator = document.querySelector('.auto-save-indicator');
    const dot = document.querySelector('.save-dot');
    
    if (indicator && dot) {
        indicator.className = `auto-save-indicator ${status}`;
        dot.className = `save-dot ${status}`;
        
        if (status === 'saved') {
            setTimeout(() => {
                indicator.className = 'auto-save-indicator';
                dot.className = 'save-dot';
            }, 2000);
        }
    }
}

// Save to localStorage
function saveToStorage() {
    const data = {
        task1Content: task1Content,
        task2Content: task2Content,
        currentTask: currentTask,
        timestamp: Date.now()
    };
    
    localStorage.setItem('ielts_writing_data', JSON.stringify(data));
}

// Load from localStorage
function loadFromStorage() {
    const saved = localStorage.getItem('ielts_writing_data');
    if (saved) {
        const data = JSON.parse(saved);
        
        task1Content = data.task1Content || '';
        task2Content = data.task2Content || '';
        currentTask = data.currentTask || 1;
        
        // Restore content to textareas
        const task1Textarea = document.querySelector('.writing-textarea.task1');
        const task2Textarea = document.querySelector('.writing-textarea.task2');
        
        if (task1Textarea) task1Textarea.value = task1Content;
        if (task2Textarea) task2Textarea.value = task2Content;
        
        // Switch to last active task
        switchTask(currentTask);
        
        updateWordCount();
        showNotification('Previous work restored');
    }
}

// Show notification
function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 6px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Submit writing test
function submitWritingTest() {
    const task1Words = countWords(task1Content);
    const task2Words = countWords(task2Content);
    const totalWords = task1Words + task2Words;
    
    const results = {
        task1: {
            words: task1Words,
            target: WORD_TARGETS.task1.min,
            status: task1Words >= WORD_TARGETS.task1.min ? 'Complete' : 'Incomplete'
        },
        task2: {
            words: task2Words,
            target: WORD_TARGETS.task2.min,
            status: task2Words >= WORD_TARGETS.task2.min ? 'Complete' : 'Incomplete'
        },
        total: totalWords
    };
    
    let message = `Writing Test Submission:\n\n`;
    message += `Task 1: ${results.task1.words} words (${results.task1.status})\n`;
    message += `Task 2: ${results.task2.words} words (${results.task2.status})\n`;
    message += `Total: ${results.total} words\n\n`;
    
    if (results.task1.status === 'Incomplete' || results.task2.status === 'Incomplete') {
        message += `Warning: Some tasks don't meet minimum word requirements.`;
    } else {
        message += `All tasks completed successfully!`;
    }
    
    alert(message);
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', () => {
    initWritingTest();
    
    if (document.getElementById('timer')) {
        startTimer();
    }
    
    // Auto-save every 30 seconds
    autoSaveInterval = setInterval(() => {
        saveToStorage();
        showAutoSaveIndicator('saved');
    }, 30000);
    
    // Submit button
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', submitWritingTest);
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Prevent accidental page reload
window.addEventListener('beforeunload', (e) => {
    if (task1Content || task2Content) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    }
});