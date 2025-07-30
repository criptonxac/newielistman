// IELTS Reading Test JavaScript
let currentPart = 1;
let totalParts = 3; // IELTS Reading test has 3 parts
let currentAnswers = {};
let answeredCount = 0;
let totalQuestions = 40; // IELTS Reading test has 40 questions
let testStarted = false;
let testTimer;
let highlightedElements = []; // Track highlighted elements

// Test selection functions
function startFullTest() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentPart = 1;
    totalQuestions = 40; // IELTS Reading test has 40 questions total
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
    
    // Set questions count based on part
    if (partNum === 1) {
        // Part 1: Questions 1-13
        totalQuestions = 13;
    } else if (partNum === 2) {
        // Part 2: Questions 14-26
        totalQuestions = 13;
    } else if (partNum === 3) {
        // Part 3: Questions 27-40
        totalQuestions = 14;
    }
    
    showPart(partNum);
    testStarted = true;
}

function backToSelection() {
    document.getElementById('parts-selection').style.display = 'none';
    document.getElementById('test-selection').style.display = 'block';
}

// Part navigation
function showPart(partNum) {
    console.log('Showing part:', partNum);
    currentPart = parseInt(partNum);
    
    // Hide all parts
    document.querySelectorAll('.test-content').forEach(content => {
        content.classList.remove('active');
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.questions-content').forEach(content => {
        content.classList.remove('active');
        content.classList.add('hidden');
    });
    
    // Show selected part
    const contentElement = document.getElementById(`part${partNum}-content`);
    const questionsElement = document.getElementById(`part${partNum}-questions`);
    
    if (contentElement) {
        contentElement.classList.add('active');
        contentElement.classList.remove('hidden');
    } else {
        console.error(`Element part${partNum}-content not found`);
    }
    
    if (questionsElement) {
        questionsElement.classList.add('active');
        questionsElement.classList.remove('hidden');
    } else {
        console.error(`Element part${partNum}-questions not found`);
    }
    
    // Update part buttons styling
    document.querySelectorAll('.part-button').forEach(button => {
        // Reset all buttons to default style
        button.classList.remove('bg-blue-600');
        button.classList.remove('text-white');
        button.classList.add('bg-gray-200');
        button.classList.add('text-gray-700');
    });
    
    // Highlight active part button
    const activeButton = document.getElementById(`part${partNum}-btn`);
    if (activeButton) {
        activeButton.classList.remove('bg-gray-200');
        activeButton.classList.remove('text-gray-700');
        activeButton.classList.add('bg-blue-600');
        activeButton.classList.add('text-white');
    }
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prevPartBtn');
    const nextBtn = document.getElementById('nextPartBtn');
    
    if (prevBtn) {
        prevBtn.style.display = partNum > 1 ? 'block' : 'none';
    }
    
    if (nextBtn) {
        nextBtn.textContent = partNum < totalParts ? 'Keyingi →' : 'Yakunlash';
    }
    
    // Update current part display
    if (document.getElementById('currentPartDisplay')) {
        document.getElementById('currentPartDisplay').textContent = partNum;
    }
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
    console.log('Starting timer...');
    const timerElement = document.getElementById('timer');
    if (!timerElement) {
        console.error('Timer element not found!');
        return;
    }
    
    // Check if there's a saved timer value in localStorage
    let remainingSeconds;
    const savedTime = localStorage.getItem('readingTestRemainingTime');
    
    if (savedTime) {
        remainingSeconds = parseInt(savedTime);
        console.log('Resuming timer with saved time:', remainingSeconds);
    } else {
        // Get time from data attribute or default to 60 minutes
        remainingSeconds = parseInt(timerElement.dataset.timeSeconds) || 3600;
        console.log('Starting new timer with seconds:', remainingSeconds);
    }
    
    testTimer = setInterval(() => {
        if (remainingSeconds <= 0) {
            clearInterval(testTimer);
            console.log('Time expired!');
            localStorage.removeItem('readingTestRemainingTime'); // Clear saved time
            
            // Submit the form when time expires
            const form = document.querySelector('form');
            if (form) {
                console.log('Auto-submitting form...');
                form.submit();
            } else {
                console.error('Form not found!');
                // Redirect to results page as fallback
                window.location.href = '/student/results';
            }
            return;
        }
        
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        const displayMinutes = minutes.toString().padStart(2, '0');
        const displaySeconds = seconds.toString().padStart(2, '0');
        const timeString = `${displayMinutes}:${displaySeconds}`;
        
        timerElement.textContent = timeString;
        const timeRemaining = document.getElementById('timeRemaining');
        if (timeRemaining) timeRemaining.textContent = timeString;
        
        // Change color based on time remaining
        if (minutes < 10) {
            timerElement.style.color = '#e74c3c'; // Red when less than 10 minutes
        } else if (minutes < 20) {
            timerElement.style.color = '#f39c12'; // Orange when less than 20 minutes
        }
        
        remainingSeconds--;
        
        // Save remaining time to localStorage to persist between page navigations
        localStorage.setItem('readingTestRemainingTime', remainingSeconds);
    }, 1000);
}

// Input handling for text inputs
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

// Input handling for radio buttons
function initializeRadioHandling() {
    document.addEventListener('change', function(e) {
        if (e.target.type === 'radio') {
            const questionName = e.target.name;
            const answer = e.target.value;
            
            // Check if this question was already answered
            const wasAnswered = currentAnswers[questionName] !== undefined;
            
            currentAnswers[questionName] = answer;
            
            // If this is a new answer, increment count
            if (!wasAnswered) {
                answeredCount++;
            }
            
            updateProgress();
        }
    });
}

function updateProgress() {
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const answeredDisplay = document.getElementById('answeredDisplay');
    const percentage = (answeredCount / totalQuestions) * 100;
    
    if (progressText) {
        progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
    }
    
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }
    
    if (answeredDisplay) {
        answeredDisplay.textContent = answeredCount;
    }
}

function finishTest() {
    if (testTimer) {
        clearInterval(testTimer);
    }
    
    alert(`Test yakunlandi! Siz ${answeredCount}/${totalQuestions} savolga javob berdingiz.`);
    
    // Redirect to results or dashboard
    window.location.href = "/student/dashboard";
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
        
        // Save answer to server if needed
        if (typeof saveAnswer === 'function') {
            saveAnswer(questionName, draggedValue);
        }
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
        
        // Save empty answer to server if needed
        if (typeof saveAnswer === 'function') {
            saveAnswer(questionName, '');
        }
    }
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (!testStarted) return;
        
        if (e.key >= '1' && e.key <= '3') {
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

// Highlight functionality
function initializeHighlighting() {
    // Add context menu for highlighting
    document.addEventListener('contextmenu', function(e) {
        // Only show context menu in passage or questions container
        if (e.target.closest('.passage-container') || e.target.closest('.questions-container')) {
            e.preventDefault();
            
            // Remove any existing context menu
            const existingMenu = document.querySelector('.context-menu');
            if (existingMenu) existingMenu.remove();
            
            // Create context menu
            const menu = document.createElement('div');
            menu.className = 'context-menu';
            menu.style.position = 'absolute';
            menu.style.top = `${e.pageY}px`;
            menu.style.left = `${e.pageX}px`;
            menu.style.backgroundColor = 'white';
            menu.style.border = '1px solid #ccc';
            menu.style.borderRadius = '4px';
            menu.style.padding = '5px 0';
            menu.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
            menu.style.zIndex = '1000';
            
            // Add menu items
            const items = [
                { text: 'Highlight', color: '#ffff00', action: 'highlight' },
                { text: 'Add Note', color: '#90ee90', action: 'note' },
                { text: 'Clear', action: 'clear' },
                { text: 'Clear All', action: 'clearAll' }
            ];
            
            items.forEach(item => {
                const menuItem = document.createElement('div');
                menuItem.className = 'context-menu-item';
                menuItem.textContent = item.text;
                menuItem.style.padding = '8px 12px';
                menuItem.style.cursor = 'pointer';
                menuItem.style.fontSize = '14px';
                
                menuItem.addEventListener('mouseover', () => {
                    menuItem.style.backgroundColor = '#f0f0f0';
                });
                
                menuItem.addEventListener('mouseout', () => {
                    menuItem.style.backgroundColor = 'transparent';
                });
                
                menuItem.addEventListener('click', () => {
                    menu.remove();
                    
                    // Handle menu item actions
                    if (item.action === 'highlight') {
                        highlightSelection(item.color);
                    } else if (item.action === 'note') {
                        addNoteToSelection();
                    } else if (item.action === 'clear') {
                        clearHighlight(e.target);
                    } else if (item.action === 'clearAll') {
                        clearAllHighlights();
                    }
                });
                
                menu.appendChild(menuItem);
            });
            
            document.body.appendChild(menu);
            
            // Close menu when clicking elsewhere
            document.addEventListener('click', function closeMenu() {
                menu.remove();
                document.removeEventListener('click', closeMenu);
            });
        }
    });
}

function highlightSelection(color) {
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        
        // Create highlight span
        const highlightSpan = document.createElement('span');
        highlightSpan.className = 'highlighted';
        highlightSpan.style.backgroundColor = color;
        
        // Add to tracked elements
        highlightedElements.push(highlightSpan);
        
        // Apply highlight
        range.surroundContents(highlightSpan);
        selection.removeAllRanges();
    }
}

function addNoteToSelection() {
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        
        // Prompt for note text
        const noteText = prompt('Enter your note:');
        if (noteText) {
            // Create note span
            const noteSpan = document.createElement('span');
            noteSpan.className = 'note-highlight';
            noteSpan.style.backgroundColor = '#90ee90';
            noteSpan.title = noteText;
            
            // Add to tracked elements
            highlightedElements.push(noteSpan);
            
            // Apply note highlight
            range.surroundContents(noteSpan);
            selection.removeAllRanges();
        }
    }
}

function clearHighlight(element) {
    // Find the closest highlight or note parent
    const highlight = element.closest('.highlighted, .note-highlight');
    if (highlight) {
        // Replace the highlight with its text content
        const parent = highlight.parentNode;
        while (highlight.firstChild) {
            parent.insertBefore(highlight.firstChild, highlight);
        }
        parent.removeChild(highlight);
        
        // Remove from tracked elements
        const index = highlightedElements.indexOf(highlight);
        if (index > -1) {
            highlightedElements.splice(index, 1);
        }
    }
}

function clearAllHighlights() {
    // Clear all highlights and notes
    document.querySelectorAll('.highlighted, .note-highlight').forEach(highlight => {
        const parent = highlight.parentNode;
        while (highlight.firstChild) {
            parent.insertBefore(highlight.firstChild, highlight);
        }
        parent.removeChild(highlight);
    });
    
    // Clear tracked elements
    highlightedElements = [];
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeInputHandling();
    initializeRadioHandling();
    initializeKeyboardShortcuts();
    initializeHighlighting();
    
    // Auto-start timer for standalone test pages
    if (document.getElementById('timer')) {
        startTimer();
        testStarted = true;
    }
});
