// Question Types JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Initialize drag and drop functionality
    initializeDragDrop();
    
    // Initialize essay word counter
    initializeEssayCounter();
    
    // Initialize form validation
    initializeFormValidation();
});

// Drag and Drop functionality
function initializeDragDrop() {
    const dragDropQuestions = document.querySelectorAll('.drag-drop');
    
    dragDropQuestions.forEach(question => {
        const draggableItems = question.querySelectorAll('.draggable-item');
        const dropZones = question.querySelectorAll('.drop-zone');
        
        // Make items draggable
        draggableItems.forEach(item => {
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', this.dataset.value);
                e.dataTransfer.setData('text/html', this.outerHTML);
                this.style.opacity = '0.5';
            });
            
            item.addEventListener('dragend', function(e) {
                this.style.opacity = '1';
            });
        });
        
        // Setup drop zones
        dropZones.forEach(dropZone => {
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });
            
            dropZone.addEventListener('dragleave', function(e) {
                this.classList.remove('drag-over');
            });
            
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                const value = e.dataTransfer.getData('text/plain');
                const html = e.dataTransfer.getData('text/html');
                
                // Update hidden input
                const hiddenInput = this.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = value;
                }
                
                // Update visual feedback
                const placeholder = this.querySelector('.drop-placeholder');
                if (placeholder) {
                    placeholder.textContent = value;
                    placeholder.style.color = '#374151';
                    placeholder.style.fontStyle = 'normal';
                    placeholder.style.fontWeight = 'bold';
                }
            });
        });
    });
}

// Essay word counter
function initializeEssayCounter() {
    const essayTextareas = document.querySelectorAll('.essay-textarea');
    
    essayTextareas.forEach(textarea => {
        const wordCountElement = textarea.parentElement.querySelector('.word-count');
        
        if (wordCountElement) {
            // Initial count
            updateWordCount(textarea, wordCountElement);
            
            // Update on input
            textarea.addEventListener('input', function() {
                updateWordCount(this, wordCountElement);
            });
        }
    });
}

function updateWordCount(textarea, wordCountElement) {
    const text = textarea.value.trim();
    const wordCount = text === '' ? 0 : text.split(/\s+/).length;
    wordCountElement.textContent = wordCount;
    
    // Color coding based on word count
    if (wordCount < 250) {
        wordCountElement.style.color = '#dc2626'; // Red
    } else if (wordCount < 300) {
        wordCountElement.style.color = '#f59e0b'; // Yellow
    } else {
        wordCountElement.style.color = '#059669'; // Green
    }
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showValidationErrors();
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const errors = [];
    
    // Check required fields
    const requiredInputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            errors.push(`${input.name || 'Field'} is required`);
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    // Check essay minimum word count
    const essayTextareas = form.querySelectorAll('.essay-textarea');
    essayTextareas.forEach(textarea => {
        const wordCount = textarea.value.trim() === '' ? 0 : textarea.value.trim().split(/\s+/).length;
        if (wordCount < 250) {
            isValid = false;
            errors.push('Essay must be at least 250 words');
            textarea.classList.add('border-red-500');
        } else {
            textarea.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

function showValidationErrors() {
    // You can customize this to show errors in your preferred way
    alert('Please fill in all required fields correctly.');
}

// Utility functions
function resetForm(form) {
    form.reset();
    
    // Reset drag and drop zones
    const dropZones = form.querySelectorAll('.drop-zone');
    dropZones.forEach(zone => {
        const placeholder = zone.querySelector('.drop-placeholder');
        if (placeholder) {
            placeholder.textContent = 'Drop answer here';
            placeholder.style.color = '#9ca3af';
            placeholder.style.fontStyle = 'italic';
            placeholder.style.fontWeight = 'normal';
        }
        
        const hiddenInput = zone.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
    });
    
    // Reset word counters
    const wordCountElements = form.querySelectorAll('.word-count');
    wordCountElements.forEach(element => {
        element.textContent = '0';
        element.style.color = '#dc2626';
    });
}

// Export functions for external use
window.QuestionTypes = {
    initializeDragDrop,
    initializeEssayCounter,
    initializeFormValidation,
    resetForm,
    updateWordCount
};
