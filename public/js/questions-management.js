/**
 * Questions Management JavaScript
 * Enhanced with audio upload integration
 * Version: 2.0
 */

// Global variables
window.questionIndex = 0;
window.questionsContainer = null;
window.questionTemplate = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Questions management system initializing...');
    initializeQuestionsManagement();
});

function initializeQuestionsManagement() {
    // Get DOM elements
    window.questionsContainer = document.getElementById('questions-container');
    window.questionTemplate = document.getElementById('question-template');
    
    if (!window.questionsContainer) {
        console.warn('Questions container not found');
        return;
    }

    console.log('Questions container found:', window.questionsContainer);
    console.log('Question template found:', window.questionTemplate);
    
    // Set initial question index
    const existingQuestions = window.questionsContainer.querySelectorAll('.question-item').length;
    window.questionIndex = existingQuestions;
    console.log(`Initial question count: ${existingQuestions}`);
    
    // Initialize drag and drop sorting
    initializeSortable();
    
    // Setup event listeners
    setupEventListeners();
    
    // Initialize form validation
    setupFormValidation();
    
    console.log('Questions management system initialized successfully');
}

function initializeSortable() {
    if (window.questionsContainer && typeof Sortable !== 'undefined') {
        new Sortable(window.questionsContainer, {
            animation: 150,
            handle: '.handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                console.log('Question reordered:', evt);
                updateQuestionNumbers();
                updateSortOrders();
            }
        });
        console.log('Sortable initialized');
    } else {
        console.warn('Sortable.js not available or container not found');
    }
}

function setupEventListeners() {
    // Question removal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            e.preventDefault();
            const questionItem = e.target.closest('.question-item');
            if (questionItem && confirm('Haqiqatan ham bu savolni o\'chirmoqchimisiz?')) {
                removeQuestionWithAnimation(questionItem);
            }
        }
    });
    
    // Question type change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('question-type')) {
            handleQuestionTypeChange(e.target);
        }
    });
    
    // Option management
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-option')) {
            e.preventDefault();
            addOption(e.target);
        } else if (e.target.classList.contains('remove-option')) {
            e.preventDefault();
            removeOption(e.target);
        } else if (e.target.classList.contains('add-correct-answer')) {
            e.preventDefault();
            addCorrectAnswer(e.target);
        } else if (e.target.classList.contains('remove-correct-answer')) {
            e.preventDefault();
            removeCorrectAnswer(e.target);
        }
    });
    
    // Add question button
    const addQuestionBtn = document.querySelector('a[href*="questions/add"]');
    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addQuestion();
        });
    }
}

function setupFormValidation() {
    const questionsForm = document.getElementById('questions-form');
    if (questionsForm) {
        questionsForm.addEventListener('submit', function(e) {
            console.log('Form submission started');
            
            const questions = questionsForm.querySelectorAll('.question-item');
            if (questions.length === 0) {
                e.preventDefault();
                alert('Kamida bitta savol qo\'shishingiz kerak!');
                return false;
            }
            
            // Show loading state
            const submitBtn = questionsForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Saqlanmoqda...';
            }
            
            console.log('Form validation passed, submitting...');
        });
    }
}

function removeQuestionWithAnimation(questionItem) {
    // Add removal animation
    questionItem.style.transition = 'all 0.3s ease';
    questionItem.style.opacity = '0';
    questionItem.style.transform = 'translateX(-100%)';
    
    setTimeout(() => {
        questionItem.remove();
        updateQuestionNumbers();
        updateSortOrders();
        
        // Check if no questions left
        const remainingQuestions = window.questionsContainer.querySelectorAll('.question-item');
        if (remainingQuestions.length === 0) {
            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'bg-yellow-50 p-4 rounded border border-yellow-200 text-yellow-800';
            emptyMessage.innerHTML = '<p>Hozircha savollar yo\'q. Savol qo\'shish tugmasini bosing.</p>';
            window.questionsContainer.appendChild(emptyMessage);
        }
        
        console.log('Question removed');
    }, 300);
}

function handleQuestionTypeChange(selectElement) {
    const questionItem = selectElement.closest('.question-item');
    if (!questionItem) return;
    
    const optionsContainer = questionItem.querySelector('.options-container');
    const correctAnswerContainer = questionItem.querySelector('.correct-answer-container');
    const multipleAnswersContainer = questionItem.querySelector('.multiple-answers-container');
    const mappingContainer = questionItem.querySelector('.mapping-container');
    
    // Hide all containers first
    if (optionsContainer) optionsContainer.classList.add('hidden');
    if (correctAnswerContainer) correctAnswerContainer.classList.add('hidden');
    if (multipleAnswersContainer) multipleAnswersContainer.classList.add('hidden');
    if (mappingContainer) mappingContainer.classList.add('hidden');
    
    const selectedType = selectElement.value;
    console.log('Question type changed to:', selectedType);
    
    switch(selectedType) {
        case 'multiple_choice':
            if (optionsContainer) optionsContainer.classList.remove('hidden');
            if (correctAnswerContainer) correctAnswerContainer.classList.remove('hidden');
            break;
            
        case 'short_answer':
            if (optionsContainer) optionsContainer.classList.remove('hidden');
            if (multipleAnswersContainer) multipleAnswersContainer.classList.remove('hidden');
            break;
            
        case 'true_false':
            if (correctAnswerContainer) correctAnswerContainer.classList.remove('hidden');
            break;
            
        case 'fill_blank':
            if (correctAnswerContainer) correctAnswerContainer.classList.remove('hidden');
            break;
            
        case 'matching':
            if (optionsContainer) optionsContainer.classList.remove('hidden');
            if (mappingContainer) mappingContainer.classList.remove('hidden');
            break;
            
        case 'drag_drop':
            if (optionsContainer) optionsContainer.classList.remove('hidden');
            if (mappingContainer) mappingContainer.classList.remove('hidden');
            break;
            
        case 'essay':
            // Essay doesn't need any additional fields
            break;
    }
}

function addQuestion() {
    if (!window.questionTemplate) {
        console.error('Question template not found');
        return;
    }
    
    // Remove empty message if exists
    const emptyMessage = window.questionsContainer.querySelector('.bg-yellow-50');
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    // Clone template
    const template = window.questionTemplate.content.cloneNode(true);
    const questionItem = template.querySelector('.question-item');
    
    // Generate unique index
    const timestamp = Date.now();
    const uniqueIndex = `new_${timestamp}_${Math.random().toString(36).substring(2, 9)}`;
    
    // Replace INDEX placeholder with unique index
    questionItem.innerHTML = questionItem.innerHTML.replace(/INDEX/g, uniqueIndex);
    
    // Update question number
    const questionNumber = window.questionsContainer.querySelectorAll('.question-item').length + 1;
    questionItem.querySelector('.question-number').textContent = `Savol #${questionNumber}`;
    
    // Set sort order
    const sortOrderInput = questionItem.querySelector('.sort-order');
    if (sortOrderInput) {
        sortOrderInput.value = questionNumber;
    }
    
    // Add to container with animation
    questionItem.style.opacity = '0';
    questionItem.style.transform = 'translateY(20px)';
    window.questionsContainer.appendChild(questionItem);
    
    // Animate in
    setTimeout(() => {
        questionItem.style.transition = 'all 0.3s ease';
        questionItem.style.opacity = '1';
        questionItem.style.transform = 'translateY(0)';
    }, 10);
    
    // Focus on first input
    setTimeout(() => {
        const firstInput = questionItem.querySelector('textarea, input[type="text"]');
        if (firstInput) {
            firstInput.focus();
        }
    }, 320);
    
    console.log('New question added with index:', uniqueIndex);
}

function addOption(button) {
    const optionsList = button.previousElementSibling;
    if (!optionsList) return;
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'flex items-center';
    
    const questionItem = button.closest('.question-item');
    const questionIndex = questionItem.querySelector('input[name*="["][name*="]"]').name.match(/\[([^\]]+)\]/)[1];
    
    optionDiv.innerHTML = `
        <input type="text" name="questions[${questionIndex}][options][]" 
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               placeholder="Variant kiriting">
        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
    `;
    
    optionsList.appendChild(optionDiv);
    
    // Focus on new input
    const newInput = optionDiv.querySelector('input');
    if (newInput) {
        newInput.focus();
    }
}

function removeOption(button) {
    const optionDiv = button.parentElement;
    optionDiv.style.transition = 'all 0.2s ease';
    optionDiv.style.opacity = '0';
    optionDiv.style.transform = 'translateX(-20px)';
    
    setTimeout(() => {
        optionDiv.remove();
    }, 200);
}

function addCorrectAnswer(button) {
    const answersList = button.previousElementSibling;
    if (!answersList) return;
    
    const answerDiv = document.createElement('div');
    answerDiv.className = 'flex items-center';
    
    const questionItem = button.closest('.question-item');
    const questionIndex = questionItem.querySelector('input[name*="["][name*="]"]').name.match(/\[([^\]]+)\]/)[1];
    
    answerDiv.innerHTML = `
        <input type="text" name="questions[${questionIndex}][correct_answers][]" 
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               placeholder="To'g'ri javob kiriting">
        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
    `;
    
    answersList.appendChild(answerDiv);
    
    // Focus on new input
    const newInput = answerDiv.querySelector('input');
    if (newInput) {
        newInput.focus();
    }
}

function removeCorrectAnswer(button) {
    const answerDiv = button.parentElement;
    answerDiv.style.transition = 'all 0.2s ease';
    answerDiv.style.opacity = '0';
    answerDiv.style.transform = 'translateX(-20px)';
    
    setTimeout(() => {
        answerDiv.remove();
    }, 200);
}

function updateQuestionNumbers() {
    const questions = window.questionsContainer.querySelectorAll('.question-item');
    questions.forEach((question, index) => {
        const numberElement = question.querySelector('.question-number');
        if (numberElement) {
            numberElement.textContent = `Savol #${index + 1}`;
        }
    });
}

function updateSortOrders() {
    const questions = window.questionsContainer.querySelectorAll('.question-item');
    questions.forEach((question, index) => {
        const sortOrderInput = question.querySelector('.sort-order');
        if (sortOrderInput) {
            sortOrderInput.value = index + 1;
        }
    });
}

function validateQuestionsForm() {
    const questions = window.questionsContainer.querySelectorAll('.question-item');
    
    for (let i = 0; i < questions.length; i++) {
        const question = questions[i];
        const questionText = question.querySelector('textarea[name*="question_text"]');
        const questionType = question.querySelector('select[name*="question_type"]');
        const points = question.querySelector('input[name*="points"]');
        
        if (!questionText || !questionText.value.trim()) {
            alert(`Savol #${i + 1}: Savol matni kiritilmagan!`);
            questionText.focus();
            return false;
        }
        
        if (!questionType || !questionType.value) {
            alert(`Savol #${i + 1}: Savol turi tanlanmagan!`);
            questionType.focus();
            return false;
        }
        
        if (!points || !points.value || points.value < 1) {
            alert(`Savol #${i + 1}: Ball kiritilmagan yoki noto'g'ri!`);
            points.focus();
            return false;
        }
    }
    
    return true;
}

// Export functions for external use
window.addQuestion = addQuestion;
window.updateQuestionNumbers = updateQuestionNumbers;
window.updateSortOrders = updateSortOrders;