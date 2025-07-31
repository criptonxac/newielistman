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
    
    // Save current answers before changing parts
    saveCurrentAnswers();
    
    // Get test slug and attempt code from meta tags
    const testSlug = document.querySelector('meta[name="test-slug"]')?.getAttribute('content');
    const attemptCode = document.querySelector('meta[name="attempt-code"]')?.getAttribute('content');
    
    if (testSlug && attemptCode) {
        // Option 1: Use AJAX to update the current part on the server
        updateCurrentPartOnServer(currentPart);
        
        // Option 2: Navigate to the part URL directly if AJAX fails
        // This is a fallback that causes a page reload
        setTimeout(() => {
            // If we're still here after a short delay, AJAX might have failed
            // Navigate to the part URL directly
            window.location.href = `/reading/${testSlug}/part${partNum}/${attemptCode}`;
        }, 500); // 500ms delay to give AJAX a chance
        
        return;
    }
    
    // Fallback to client-side part switching if navigation fails
    console.log('Falling back to client-side part switching');
    clientSidePartSwitch(partNum);
}

// Update current part on server via AJAX
function updateCurrentPartOnServer(partNum) {
    const form = document.querySelector('form');
    if (!form) return;
    
    // Get test slug and attempt code from meta tags
    const testSlug = document.querySelector('meta[name="test-slug"]')?.getAttribute('content');
    const attemptCode = document.querySelector('meta[name="attempt-code"]')?.getAttribute('content');
    
    if (!testSlug || !attemptCode) {
        console.error('Missing test slug or attempt code meta tags');
        return;
    }
    
    // Create form data with current answers and part info
    const formData = new FormData(form);
    formData.append('current_part', partNum);
    
    // Send AJAX request
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(response => {
        console.log('Part updated on server');
    }).catch(error => {
        console.error('Error updating part on server:', error);
    });
}

// Save current answers via AJAX
function saveCurrentAnswers() {
    const form = document.querySelector('form');
    if (!form) return;
    
    // Get all filled inputs
    const inputs = form.querySelectorAll('input[name^="answers"], select[name^="answers"], textarea[name^="answers"]');
    if (inputs.length === 0) return;
    
    // Get test slug and attempt code from meta tags
    const testSlug = document.querySelector('meta[name="test-slug"]')?.getAttribute('content');
    const attemptCode = document.querySelector('meta[name="attempt-code"]')?.getAttribute('content');
    
    if (!testSlug || !attemptCode) {
        console.error('Missing test slug or attempt code meta tags');
        return;
    }
    
    // Process each answer individually
    inputs.forEach(input => {
        // Skip empty inputs or unchecked radio/checkboxes
        if ((input.type === 'radio' || input.type === 'checkbox') && !input.checked) return;
        if (input.value.trim() === '') return;
        
        // Extract question ID from input name (format: answers[question_id])
        const questionIdMatch = input.name.match(/answers\[(\d+)\]/);
        if (!questionIdMatch || !questionIdMatch[1]) return;
        
        const questionId = questionIdMatch[1];
        const answer = input.value;
        
        // Create form data for this answer
        const formData = new FormData();
        formData.append('question_id', questionId);
        formData.append('answer', answer);
        
        // Add CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.value);
        }
        
        // Send AJAX request to save-answer endpoint
        const saveUrl = `/reading/${testSlug}/attempt/${attemptCode}/save`;
        fetch(saveUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => response.json())
          .then(data => {
              console.log(`Answer saved for question ${questionId}:`, data);
              
              // Update progress if available
              if (data.progress) {
                  updateProgress(data.progress);
              }
          })
          .catch(error => {
              console.error(`Error saving answer for question ${questionId}:`, error);
          });
    });
}

// Client-side part switching (fallback)
function clientSidePartSwitch(partNum) {
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
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const finishBtn = document.getElementById('finishBtn');
    
    if (prevBtn) {
        prevBtn.classList.toggle('hidden', partNum <= 1);
    }
    
    if (nextBtn && finishBtn) {
        if (partNum < totalParts) {
            nextBtn.classList.remove('hidden');
            finishBtn.classList.add('hidden');
        } else {
            nextBtn.classList.add('hidden');
            finishBtn.classList.remove('hidden');
        }
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

function finishTest() {
    console.log('Finishing test...');
    
    // Stop the timer if it's running
    if (testTimer) {
        clearInterval(testTimer);
    }
    
    // Save current answers before submitting
    saveCurrentAnswers();
    
    // Get test slug and attempt code from meta tags
    const testSlug = document.querySelector('meta[name="test-slug"]')?.getAttribute('content');
    const attemptCode = document.querySelector('meta[name="attempt-code"]')?.getAttribute('content');
    
    if (!testSlug || !attemptCode) {
        console.error('Missing test slug or attempt code meta tags');
        return;
    }
    
    // Add complete flag to form
    const form = document.querySelector('form');
    if (form) {
        const completeInput = document.createElement('input');
        completeInput.type = 'hidden';
        completeInput.name = 'complete';
        completeInput.value = '1';
        form.appendChild(completeInput);
        
        console.log('Submitting test for completion...');
        form.submit();
    } else {
        // Fallback if form not found - redirect to completion URL
        window.location.href = `/reading/${testSlug}/attempt/${attemptCode}/complete`;
    }
}

// Timer function with server synchronization
function startTimer() {
    console.log('Starting timer with server synchronization...');
    const timerElement = document.getElementById('timer');
    if (!timerElement) {
        console.error('Timer element not found!');
        return;
    }
    
    // Get attempt code from the form action URL
    const form = document.querySelector('form');
    const testSlug = document.querySelector('meta[name="test-slug"]')?.getAttribute('content');
    const attemptCode = document.querySelector('meta[name="attempt-code"]')?.getAttribute('content');
    
    if (!testSlug || !attemptCode) {
        console.error('Missing test slug or attempt code meta tags');
        return;
    }
    
    console.log('Test slug:', testSlug, 'Attempt code:', attemptCode);
    
    // Initial timer display
    const initialSeconds = parseInt(timerElement.dataset.timeSeconds) || 3600;
    updateTimerDisplay(initialSeconds);
    
    // Function to get time from server
    function syncWithServer() {
        const url = `/reading/${testSlug}/attempt/${attemptCode}/time`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('Server time sync:', data);
                
                if (data.status === 'completed') {
                    console.log('Test already completed');
                    clearInterval(testTimer);
                    window.location.href = `/reading/${testSlug}/attempt/${attemptCode}/complete`;
                    return;
                }
                
                // Update timer display
                updateTimerDisplay(data.time_remaining);
                
                // Update progress if available
                if (data.progress) {
                    updateServerProgress(data.progress);
                }
                
                // If time expired, submit the form
                if (data.time_remaining <= 0) {
                    console.log('Time expired according to server!');
                    clearInterval(testTimer);
                    
                    // Add complete flag to form
                    const completeInput = document.createElement('input');
                    completeInput.type = 'hidden';
                    completeInput.name = 'complete';
                    completeInput.value = '1';
                    form.appendChild(completeInput);
                    
                    console.log('Auto-submitting form...');
                    form.submit();
                }
            })
            .catch(error => {
                console.error('Error syncing with server:', error);
            });
    }
    
    // Update timer display
    function updateTimerDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        const displayMinutes = minutes.toString().padStart(2, '0');
        const displaySeconds = remainingSeconds.toString().padStart(2, '0');
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
    }
    
    // Update progress using the global function
    function updateServerProgress(progress) {
        if (progress) {
            updateProgress(progress);
        }
    }
    
    // Initial sync
    syncWithServer();
    
    // Set up interval to sync with server every 30 seconds
    testTimer = setInterval(syncWithServer, 30000);
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
    
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[type="text"], textarea')) {
            // Auto-save after typing stops (debounce)
            clearTimeout(e.target.saveTimeout);
            e.target.saveTimeout = setTimeout(() => {
                console.log('Auto-saving answer for', e.target.name);
                saveInputAnswer(e.target);
            }, 1000); // 1 second debounce
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.matches('input[type="radio"], input[type="checkbox"], select')) {
            console.log('Auto-saving answer for', e.target.name);
            saveInputAnswer(e.target);
        }
    });
}

// Function to save a single input answer
function saveInputAnswer(input) {
    // Skip if input is empty or unchecked
    if ((input.type === 'radio' || input.type === 'checkbox') && !input.checked) return;
    if (input.value.trim() === '') return;
    
    // Extract question ID from input name (format: answers[question_id])
    const questionIdMatch = input.name.match(/answers\[(\d+)\]/);
    if (!questionIdMatch || !questionIdMatch[1]) return;
    
    const questionId = questionIdMatch[1];
    const answer = input.value;
    
    // Get form for CSRF token and attempt info
    const form = document.querySelector('form');
    if (!form) return;
    
    // Get test slug and attempt code from meta tags
    const testSlug = document.querySelector('meta[name="test-slug"]')?.getAttribute('content');
    const attemptCode = document.querySelector('meta[name="attempt-code"]')?.getAttribute('content');
    
    if (!testSlug || !attemptCode) {
        console.error('Missing test slug or attempt code meta tags');
        return;
    }
    
    // Create form data for this answer
    const formData = new FormData();
    formData.append('question_id', questionId);
    formData.append('answer', answer);
    
    // Add CSRF token
    const csrfToken = document.querySelector('input[name="_token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.value);
    }
    
    // Send AJAX request to save-answer endpoint
    const saveUrl = `/reading/${testSlug}/attempt/${attemptCode}/save`;
    fetch(saveUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(response => response.json())
      .then(data => {
          console.log(`Answer saved for question ${questionId}:`, data);
          
          // Update progress if available
          if (data.progress) {
              updateProgress(data.progress);
          }
      })
      .catch(error => {
          console.error(`Error saving answer for question ${questionId}:`, error);
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

function updateProgress(serverProgress) {
    // If server provided progress data, use that
    if (serverProgress) {
        const progressText = document.getElementById('progressText');
        const progressBar = document.getElementById('progressBar');
        const answeredDisplay = document.getElementById('answeredDisplay');
        
        if (progressText) {
            progressText.textContent = `${serverProgress.answered_questions}/${serverProgress.total_questions} questions answered`;
        }
        
        if (progressBar) {
            progressBar.style.width = serverProgress.percentage + '%';
        }
        
        if (answeredDisplay) {
            answeredDisplay.textContent = serverProgress.answered_questions;
        }
        
        // Update our client-side tracking to match server
        answeredCount = serverProgress.answered_questions;
        totalQuestions = serverProgress.total_questions;
    } 
    // Otherwise use client-side tracking
    else {
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
}

// Duplicate finishTest function removed - using the consolidated version above

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
    console.log('DOM fully loaded, initializing reading test functionality...');
    
    // Initialize all input handling
    initializeInputHandling();
    initializeRadioHandling();
    
    // Initialize drag and drop if needed
    if (document.querySelector('.draggable') || document.querySelector('.drop-zone')) {
        console.log('Initializing drag and drop functionality');
        initializeDragAndDrop();
    }
    
    // Initialize keyboard shortcuts and highlighting
    initializeKeyboardShortcuts();
    initializeHighlighting();
    
    // Set up initial part display
    const initialPart = document.querySelector('meta[name="current-part"]')?.getAttribute('content') || 1;
    currentPart = parseInt(initialPart);
    showPart(currentPart);
    
    // Count total questions for progress tracking
    const allQuestions = document.querySelectorAll('.question-item');
    if (allQuestions.length > 0) {
        totalQuestions = allQuestions.length;
        console.log(`Total questions detected: ${totalQuestions}`);
    }
    
    // Count already answered questions
    const filledInputs = document.querySelectorAll('input[name^="answers"]:checked, input[name^="answers"][type="text"]:not([value=""]), textarea[name^="answers"]:not(:empty), select[name^="answers"] option:checked:not([value=""])');
    answeredCount = filledInputs.length;
    console.log(`Already answered questions: ${answeredCount}`);
    
    // Update progress display
    updateProgress();
    
    // Auto-start timer for test pages
    if (document.getElementById('timer')) {
        console.log('Starting timer automatically');
        startTimer();
        testStarted = true;
    }
});
