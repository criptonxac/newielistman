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
    initializeQuestionsManagement();
    
    // Agar sessionda xabar saqlangan bo'lsa, uni ko'rsatish
    const successMessage = sessionStorage.getItem('showSuccessMessage');
    if (successMessage) {
        try {
            const { message, questionsCount } = JSON.parse(successMessage);
            let fullMessage = message;
            
            // Agar savollar soni mavjud bo'lsa, qo'shamiz
            if (questionsCount !== undefined && questionsCount !== null) {
                fullMessage = `${message}<br><span class="text-green-700 font-semibold">Jami savollar soni: ${questionsCount}</span>`;
            }
            
            // Modalni ko'rsatish
            showSuccessModal(fullMessage);
            
            // Sessiondan o'chirib tashlash
            sessionStorage.removeItem('showSuccessMessage');
        } catch (e) {
            console.error('Error parsing success message:', e);
        }
    }
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

// Form yuborishni boshqarish
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('questions-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form ma'lumotlarini yuborish
            const formData = new FormData(form);
            
            // Yuborishni boshlash
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug uchun
                if (data.redirect) {
                    // Redirect qilish va session orqali xabarni o'tkazish
                    const successData = {
                        message: data.message || 'Savollar muvaffaqiyatli saqlandi!',
                        questionsCount: data.questions_count || 0
                    };
                    console.log('Saving to session:', successData);
                    sessionStorage.setItem('showSuccessMessage', JSON.stringify(successData));
                    window.location.href = data.redirect;
                } else {
                    // Agar redirect bo'lmasa, modalni ko'rsatish
                    const message = data.message || 'Savol muvaffaqiyatli saqlandi!';
                    if (data.questions_count !== undefined) {
                        message += `<br><span class="text-green-700 font-semibold">Jami savollar soni: ${data.questions_count}</span>`;
                    }
                    showSuccessModal(message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Xatolik yuz berdi';
                if (error.message) {
                    errorMessage = error.message;
                } else if (error.errors) {
                    errorMessage = Object.values(error.errors).join('\n');
                }
                alert(errorMessage);
            });
        });
    }
});

// Muvaffaqiyat modalini ko'rsatish
function showSuccessModal(message) {
    const modal = document.getElementById('success-modal');
    const messageElement = document.getElementById('success-message');
    const progressBar = document.getElementById('progress-bar');
    
    if (modal && messageElement && progressBar) {
        // Xabar matnini yangilash (HTML formatini qo'llab-quvvatlash uchun innerHTML ishlatamiz)
        messageElement.innerHTML = message;
        
        // Progress bar ni qayta tiklash
        progressBar.style.width = '0';
        
        // Modalni ko'rsatish
        modal.classList.remove('hidden');
        
        // Animatsiya uchun vaqt berish
        setTimeout(() => {
            modal.classList.add('opacity-100');
            document.getElementById('success-modal-content').classList.remove('scale-95', '-translate-y-5');
            document.getElementById('success-modal-content').classList.add('scale-100', 'translate-y-0');
            
            // Progress bar animatsiyasi
            progressBar.style.width = '100%';
            
            // 3 soniyadan keyin modalni yopish
            setTimeout(hideSuccessModal, 3000);
        }, 50);
    }
}

// Modalni yopish funksiyasi
function hideSuccessModal() {
    const modal = document.getElementById('success-modal');
    if (modal) {
        // Chiqish animatsiyasi
        modal.classList.remove('opacity-100');
        document.getElementById('success-modal-content').classList.remove('scale-100', 'translate-y-0');
        document.getElementById('success-modal-content').classList.add('scale-95', '-translate-y-5');
        
        // Animatsiya tugagach modalni yashirish
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}

// ESC tugmasi bosilganda modalni yopish
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideSuccessModal();
    }
});

// Modal tashqarisiga bosilganda yopish
const modal = document.getElementById('success-modal');
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideSuccessModal();
        }
    });
}

// Export functions for external use
window.addQuestion = addQuestion;
window.updateQuestionNumbers = updateQuestionNumbers;
window.updateSortOrders = updateSortOrders;