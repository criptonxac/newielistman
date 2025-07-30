/**
 * Enhanced Drag & Drop JavaScript for IELTS Test System
 * Author: Your Development Team
 * Version: 1.0
 */

class EnhancedDragDrop {
    constructor() {
        this.draggedElement = null;
        this.questionContainers = new Map();
        this.init();
    }

    init() {
        // Initialize all drag & drop containers on page load
        this.initializeAllContainers();
        
        // Listen for dynamically added content
        this.observeNewContent();
    }

    initializeAllContainers() {
        const containers = document.querySelectorAll('.enhanced-drag-drop-container');
        containers.forEach(container => {
            this.initializeContainer(container);
        });
    }

    initializeContainer(container) {
        const questionId = container.dataset.questionId;
        if (!questionId || this.questionContainers.has(questionId)) {
            return; // Already initialized
        }

        const draggables = container.querySelectorAll('.enhanced-draggable');
        const dropZones = container.querySelectorAll('.enhanced-drop-zone');

        // Store container reference
        this.questionContainers.set(questionId, {
            container,
            draggables,
            dropZones
        });

        // Add drag event listeners to draggable items
        draggables.forEach(item => {
            this.addDragListeners(item, questionId);
        });

        // Add drop event listeners to drop zones
        dropZones.forEach(zone => {
            this.addDropListeners(zone, questionId);
        });

        // Initialize progress tracking
        this.updateProgress(questionId);

        console.log(`Enhanced Drag & Drop initialized for question ${questionId}`);
    }

    addDragListeners(element, questionId) {
        element.addEventListener('dragstart', (e) => this.handleDragStart(e, questionId));
        element.addEventListener('dragend', (e) => this.handleDragEnd(e, questionId));
        element.addEventListener('touchstart', (e) => this.handleTouchStart(e, questionId), { passive: false });
        element.addEventListener('touchmove', (e) => this.handleTouchMove(e, questionId), { passive: false });
        element.addEventListener('touchend', (e) => this.handleTouchEnd(e, questionId), { passive: false });
    }

    addDropListeners(element, questionId) {
        element.addEventListener('dragover', (e) => this.handleDragOver(e, questionId));
        element.addEventListener('dragenter', (e) => this.handleDragEnter(e, questionId));
        element.addEventListener('dragleave', (e) => this.handleDragLeave(e, questionId));
        element.addEventListener('drop', (e) => this.handleDrop(e, questionId));
    }

    handleDragStart(e, questionId) {
        if (e.target.classList.contains('used')) {
            e.preventDefault();
            return false;
        }

        this.draggedElement = e.target;
        e.target.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', e.target.dataset.value);
        e.dataTransfer.setData('application/x-question-id', questionId);

        // Add visual feedback
        this.addDragPreview(e.target);
    }

    handleDragEnd(e, questionId) {
        e.target.classList.remove('dragging');
        this.draggedElement = null;
        this.removeDragPreview();
    }

    handleDragOver(e, questionId) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    handleDragEnter(e, questionId) {
        e.preventDefault();
        if (!e.target.classList.contains('filled') && 
            !e.target.querySelector('.enhanced-dropped-answer')) {
            e.target.classList.add('drag-over');
        }
    }

    handleDragLeave(e, questionId) {
        // Only remove drag-over if we're actually leaving the drop zone
        if (!e.target.contains(e.relatedTarget)) {
            e.target.classList.remove('drag-over');
        }
    }

    handleDrop(e, questionId) {
        e.preventDefault();
        e.target.classList.remove('drag-over');

        const dropZone = e.target.closest('.enhanced-drop-zone');
        if (!dropZone || dropZone.classList.contains('filled')) {
            return;
        }

        const draggedValue = e.dataTransfer.getData('text/plain');
        const draggedQuestionId = e.dataTransfer.getData('application/x-question-id');

        // Ensure we're dropping within the same question
        if (draggedQuestionId !== questionId) {
            return;
        }

        if (this.draggedElement) {
            this.createDroppedAnswer(dropZone, draggedValue, questionId);
            this.markDraggableAsUsed(this.draggedElement);
            this.updateHiddenInput(dropZone, draggedValue);
            this.autoSaveAnswer(dropZone, draggedValue, questionId);
            this.updateProgress(questionId);
        }
    }

    // Touch event handlers for mobile support
    handleTouchStart(e, questionId) {
        if (e.target.classList.contains('used')) {
            return;
        }
        this.touchData = {
            element: e.target,
            startX: e.touches[0].clientX,
            startY: e.touches[0].clientY,
            questionId: questionId
        };
        e.target.classList.add('dragging');
    }

    handleTouchMove(e, questionId) {
        if (!this.touchData) return;
        
        e.preventDefault();
        const touch = e.touches[0];
        const element = document.elementFromPoint(touch.clientX, touch.clientY);
        
        // Remove previous drag-over classes
        document.querySelectorAll('.drag-over').forEach(el => {
            el.classList.remove('drag-over');
        });
        
        // Add drag-over to current element if it's a drop zone
        if (element && element.classList.contains('enhanced-drop-zone') && 
            !element.classList.contains('filled')) {
            element.classList.add('drag-over');
        }
    }

    handleTouchEnd(e, questionId) {
        if (!this.touchData) return;

        const touch = e.changedTouches[0];
        const dropElement = document.elementFromPoint(touch.clientX, touch.clientY);
        const dropZone = dropElement?.closest('.enhanced-drop-zone');

        this.touchData.element.classList.remove('dragging');
        
        // Remove all drag-over classes
        document.querySelectorAll('.drag-over').forEach(el => {
            el.classList.remove('drag-over');
        });

        if (dropZone && !dropZone.classList.contains('filled') && 
            dropZone.closest('.enhanced-drag-drop-container').dataset.questionId === questionId) {
            
            const draggedValue = this.touchData.element.dataset.value;
            this.createDroppedAnswer(dropZone, draggedValue, questionId);
            this.markDraggableAsUsed(this.touchData.element);
            this.updateHiddenInput(dropZone, draggedValue);
            this.autoSaveAnswer(dropZone, draggedValue, questionId);
            this.updateProgress(questionId);
        }

        this.touchData = null;
    }

    createDroppedAnswer(dropZone, value, questionId) {
        // Create dropped answer element
        const droppedAnswer = document.createElement('div');
        droppedAnswer.className = 'enhanced-dropped-answer';
        droppedAnswer.textContent = value;

        // Create remove button
        const removeBtn = document.createElement('button');
        removeBtn.className = 'enhanced-remove-btn';
        removeBtn.innerHTML = '&times;';
        removeBtn.onclick = (e) => {
            e.stopPropagation();
            this.removeAnswer(dropZone, value, questionId);
        };

        droppedAnswer.appendChild(removeBtn);
        dropZone.appendChild(droppedAnswer);
        dropZone.classList.add('filled');
    }

    markDraggableAsUsed(draggableElement) {
        draggableElement.classList.add('used');
        draggableElement.draggable = false;
    }

    updateHiddenInput(dropZone, value) {
        const hiddenInput = dropZone.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    }

    autoSaveAnswer(dropZone, value, questionId) {
        const index = dropZone.dataset.index;
        const fullQuestionId = `${questionId}_${index}`;
        
        // Integration with existing saveAnswer function
        if (window.saveAnswer && typeof window.saveAnswer === 'function') {
            window.saveAnswer(fullQuestionId, value);
        }

        // Custom event for additional integrations
        const event = new CustomEvent('enhancedDragDropAnswer', {
            detail: {
                questionId: questionId,
                index: index,
                value: value,
                dropZone: dropZone
            }
        });
        document.dispatchEvent(event);
    }

    removeAnswer(dropZone, value, questionId) {
        // Find and restore the draggable element
        const container = this.questionContainers.get(questionId)?.container;
        if (container) {
            const draggable = container.querySelector(`[data-value="${value}"]`);
            if (draggable) {
                draggable.classList.remove('used');
                draggable.draggable = true;
            }
        }

        // Remove the dropped answer
        const droppedAnswer = dropZone.querySelector('.enhanced-dropped-answer');
        if (droppedAnswer) {
            droppedAnswer.remove();
        }

        // Update drop zone state
        dropZone.classList.remove('filled', 'correct', 'incorrect');

        // Clear hidden input
        this.updateHiddenInput(dropZone, '');

        // Auto-save empty answer
        this.autoSaveAnswer(dropZone, '', questionId);

        // Update progress
        this.updateProgress(questionId);
    }

    updateProgress(questionId) {
        const container = this.questionContainers.get(questionId)?.container;
        if (!container) return;

        const dropZones = container.querySelectorAll('.enhanced-drop-zone');
        const filledZones = container.querySelectorAll('.enhanced-drop-zone.filled');
        const totalQuestions = dropZones.length;
        const completedQuestions = filledZones.length;
        const percentage = totalQuestions > 0 ? (completedQuestions / totalQuestions) * 100 : 0;

        // Update progress bar if exists
        const progressBar = container.querySelector('.enhanced-progress-fill');
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }

        // Update progress text if exists
        const progressText = container.querySelector('.enhanced-progress-text');
        if (progressText) {
            progressText.textContent = `${completedQuestions}/${totalQuestions} completed`;
        }

        // Emit progress event
        const event = new CustomEvent('enhancedDragDropProgress', {
            detail: {
                questionId: questionId,
                completed: completedQuestions,
                total: totalQuestions,
                percentage: percentage
            }
        });
        document.dispatchEvent(event);
    }

    checkAnswers(questionId) {
        const container = this.questionContainers.get(questionId)?.container;
        if (!container) return;

        const dropZones = container.querySelectorAll('.enhanced-drop-zone');
        let correctCount = 0;
        let totalAnswered = 0;

        dropZones.forEach((zone, index) => {
            const droppedAnswer = zone.querySelector('.enhanced-dropped-answer');
            if (droppedAnswer) {
                totalAnswered++;
                const userAnswer = droppedAnswer.textContent.replace('×', '').trim();
                const correctAnswer = zone.dataset.correct;

                if (userAnswer === correctAnswer) {
                    zone.classList.add('correct');
                    zone.classList.remove('incorrect');
                    correctCount++;
                } else {
                    zone.classList.add('incorrect');
                    zone.classList.remove('correct');
                }
            }
        });

        // Emit check results event
        const event = new CustomEvent('enhancedDragDropCheck', {
            detail: {
                questionId: questionId,
                correct: correctCount,
                total: totalAnswered,
                percentage: totalAnswered > 0 ? (correctCount / totalAnswered) * 100 : 0
            }
        });
        document.dispatchEvent(event);

        return {
            correct: correctCount,
            total: totalAnswered,
            percentage: totalAnswered > 0 ? (correctCount / totalAnswered) * 100 : 0
        };
    }

    resetQuestion(questionId) {
        const container = this.questionContainers.get(questionId)?.container;
        if (!container) return;

        // Clear all drop zones
        const dropZones = container.querySelectorAll('.enhanced-drop-zone');
        dropZones.forEach(zone => {
            const droppedAnswer = zone.querySelector('.enhanced-dropped-answer');
            if (droppedAnswer) {
                droppedAnswer.remove();
            }
            zone.classList.remove('filled', 'correct', 'incorrect', 'drag-over');
            this.updateHiddenInput(zone, '');
        });

        // Restore all draggables
        const draggables = container.querySelectorAll('.enhanced-draggable');
        draggables.forEach(draggable => {
            draggable.classList.remove('used');
            draggable.draggable = true;
        });

        // Update progress
        this.updateProgress(questionId);
    }

    addDragPreview(element) {
        // Add visual preview during drag
        element.style.opacity = '0.7';
    }

    removeDragPreview() {
        // Remove visual preview
        if (this.draggedElement) {
            this.draggedElement.style.opacity = '';
        }
    }

    observeNewContent() {
        // Watch for dynamically added drag & drop containers
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const containers = node.querySelectorAll ? 
                            node.querySelectorAll('.enhanced-drag-drop-container') : [];
                        
                        containers.forEach(container => {
                            this.initializeContainer(container);
                        });

                        // Check if the node itself is a container
                        if (node.classList && node.classList.contains('enhanced-drag-drop-container')) {
                            this.initializeContainer(node);
                        }
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Public API methods
    static checkQuestion(questionId) {
        return window.enhancedDragDrop?.checkAnswers(questionId);
    }

    static resetQuestion(questionId) {
        return window.enhancedDragDrop?.resetQuestion(questionId);
    }

    static updateProgress(questionId) {
        return window.enhancedDragDrop?.updateProgress(questionId);
    }
}

// Global remove function for backward compatibility
window.removeEnhancedAnswer = function(button) {
    const dropZone = button.closest('.enhanced-drop-zone');
    const container = dropZone.closest('.enhanced-drag-drop-container');
    const questionId = container.dataset.questionId;
    const droppedAnswer = button.closest('.enhanced-dropped-answer');
    const value = droppedAnswer.textContent.replace('×', '').trim();
    
    if (window.enhancedDragDrop) {
        window.enhancedDragDrop.removeAnswer(dropZone, value, questionId);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.enhancedDragDrop = new EnhancedDragDrop();
    console.log('Enhanced Drag & Drop system initialized');
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedDragDrop;
}