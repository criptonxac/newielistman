document.addEventListener('DOMContentLoaded', function() {
    // Har bir savol uchun event listener'larni qo'shish
    for (let i = 0; i < 10; i++) {
        setupQuestionHandlers(i);
    }
    
    // Savol uchun barcha event listener'larni sozlash
    function setupQuestionHandlers(index) {
        const questionType = document.getElementById(`question_type_${index}`);
        
        // Yangi konteynerlar
        const multipleChoiceContainer = document.getElementById(`multiple-choice-container-${index}`);
        const fillBlankContainer = document.getElementById(`fill-blank-container-${index}`);
        const trueFalseContainer = document.getElementById(`true-false-container-${index}`);
        const dragDropContainer = document.getElementById(`drag-drop-container-${index}`);
        const essaySettingsContainer = document.getElementById(`essay-settings-container-${index}`);
        const questionFormatContainer = document.getElementById(`question-format-container-${index}`);
        const formStructureContainer = document.getElementById(`form-structure-container-${index}`);
        
        // Savol turini o'zgartirish
        questionType.addEventListener('change', function() {
            // Barcha konteynerlarni yashirish
            hideAllContainers();
            
            const selectedType = this.value;
            
            // Tanlangan turga qarab konteynerlarni ko'rsatish
            switch(selectedType) {
                case 'multiple_choice':
                    if (multipleChoiceContainer) multipleChoiceContainer.classList.remove('hidden');
                    break;
                case 'fill_blank':
                    if (fillBlankContainer) fillBlankContainer.classList.remove('hidden');
                    if (questionFormatContainer) questionFormatContainer.classList.remove('hidden');
                    break;
                case 'true_false':
                    if (trueFalseContainer) trueFalseContainer.classList.remove('hidden');
                    break;
                case 'drag_drop':
                    if (dragDropContainer) dragDropContainer.classList.remove('hidden');
                    break;
                case 'essay':
                    if (essaySettingsContainer) essaySettingsContainer.classList.remove('hidden');
                    break;
            }
        });
        
        // Barcha konteynerlarni yashirish funksiyasi
        function hideAllContainers() {
            if (multipleChoiceContainer) multipleChoiceContainer.classList.add('hidden');
            if (fillBlankContainer) fillBlankContainer.classList.add('hidden');
            if (trueFalseContainer) trueFalseContainer.classList.add('hidden');
            if (dragDropContainer) dragDropContainer.classList.add('hidden');
            if (essaySettingsContainer) essaySettingsContainer.classList.add('hidden');
            if (questionFormatContainer) questionFormatContainer.classList.add('hidden');
            if (formStructureContainer) formStructureContainer.classList.add('hidden');
        }
        
        // Question format o'zgarganda
        const questionFormatSelect = document.querySelector(`select[name="questions[${index}][question_format]"]`);
        if (questionFormatSelect) {
            questionFormatSelect.addEventListener('change', function() {
                if (this.value === 'form_completion') {
                    if (formStructureContainer) formStructureContainer.classList.remove('hidden');
                } else {
                    if (formStructureContainer) formStructureContainer.classList.add('hidden');
                }
            });
        }
    }
    
    // LocalStorage funksiyalari - data attributes dan olish
    const scriptTag = document.querySelector('script[data-test-id]');
    const testId = scriptTag.getAttribute('data-test-id');
    const currentPage = parseInt(scriptTag.getAttribute('data-current-page'));
    const addQuestionUrl = scriptTag.getAttribute('data-add-question-url');
    const storeQuestionsUrl = scriptTag.getAttribute('data-store-questions-url');
    const createQuestionsUrl = scriptTag.getAttribute('data-create-questions-url');
    
    console.log('JavaScript loaded! TestId:', testId, 'CurrentPage:', currentPage);
    
    // Sahifa yuklanganda oldingi ma'lumotlarni yuklash
    loadPageData();
    
    // Saqlash va keyingi sahifa tugmasi
    const saveAndNextBtn = document.getElementById('saveAndNext');
    console.log('Save and Next button found:', saveAndNextBtn);
    
    if (saveAndNextBtn) {
        saveAndNextBtn.addEventListener('click', function() {
            console.log('Save and Next button clicked!');
            saveCurrentPageData();
            window.location.href = addQuestionUrl + `?page=${currentPage + 1}`;
        });
    }
    
    // Barcha savollarni saqlash tugmasi
    const saveAllBtn = document.getElementById('saveAllQuestions');
    if (saveAllBtn) {
        saveAllBtn.addEventListener('click', function() {
            saveCurrentPageData();
            submitAllQuestions();
        });
    }
    
    // Oldingi sahifani yuklash tugmasi
    const loadPrevBtn = document.getElementById('loadPrevious');
    if (loadPrevBtn) {
        loadPrevBtn.addEventListener('click', function() {
            saveCurrentPageData();
            window.location.href = addQuestionUrl + `?page=${currentPage - 1}`;
        });
    }
    
    // Joriy sahifa ma'lumotlarini saqlash
    function saveCurrentPageData() {
        console.log('Saving current page data...');
        const formData = new FormData(document.querySelector('form'));
        const pageData = {};
        
        for (let i = 0; i < 10; i++) {
            const questionData = {};
            
            // Barcha input maydonlarini yig'ish
            const inputs = document.querySelectorAll(`[name^="questions[${i}]"]`);
            inputs.forEach(input => {
                if (input.type === 'radio' && !input.checked) return;
                if (input.type === 'checkbox' && !input.checked) return;
                
                const fieldName = input.name.replace(`questions[${i}][`, '').replace(']', '');
                if (input.name.includes('[]')) {
                    if (!questionData[fieldName]) questionData[fieldName] = [];
                    if (input.value.trim()) questionData[fieldName].push(input.value);
                } else {
                    questionData[fieldName] = input.value;
                }
            });
            
            if (Object.keys(questionData).length > 0) {
                pageData[i] = questionData;
            }
        }
        
        localStorage.setItem(`test_${testId}_page_${currentPage}`, JSON.stringify(pageData));
    }
    
    // Sahifa ma'lumotlarini yuklash
    function loadPageData() {
        const savedData = localStorage.getItem(`test_${testId}_page_${currentPage}`);
        if (!savedData) return;
        
        const pageData = JSON.parse(savedData);
        
        Object.keys(pageData).forEach(questionIndex => {
            const questionData = pageData[questionIndex];
            
            Object.keys(questionData).forEach(fieldName => {
                const value = questionData[fieldName];
                
                if (Array.isArray(value)) {
                    // Array uchun (options, drag_items, etc.)
                    value.forEach((val, index) => {
                        const input = document.querySelector(`[name="questions[${questionIndex}][${fieldName}][]"]`);
                        if (input && input.parentElement.children[index]) {
                            input.parentElement.children[index].value = val;
                        }
                    });
                } else {
                    // Oddiy maydon uchun
                    const input = document.querySelector(`[name="questions[${questionIndex}][${fieldName}]"]`);
                    if (input) {
                        if (input.type === 'radio') {
                            const radioInput = document.querySelector(`[name="questions[${questionIndex}][${fieldName}]"][value="${value}"]`);
                            if (radioInput) radioInput.checked = true;
                        } else {
                            input.value = value;
                        }
                        
                        // Question type o'zgarganda containerlarni ko'rsatish
                        if (fieldName === 'question_type') {
                            const event = new Event('change');
                            input.dispatchEvent(event);
                        }
                    }
                }
            });
        });
    }
    
    // Barcha savollarni yuborish
    function submitAllQuestions() {
        const allQuestions = {};
        
        // Barcha sahifalardan ma'lumotlarni yig'ish
        for (let page = 1; page <= 4; page++) {
            const pageData = localStorage.getItem(`test_${testId}_page_${page}`);
            if (pageData) {
                const parsedData = JSON.parse(pageData);
                Object.keys(parsedData).forEach(questionIndex => {
                    const globalIndex = (page - 1) * 10 + parseInt(questionIndex);
                    allQuestions[globalIndex] = parsedData[questionIndex];
                });
            }
        }
        
        // AJAX orqali yuborish
        const formData = new FormData();
        formData.append('_token', document.querySelector('[name="_token"]').value);
        formData.append('questions', JSON.stringify(allQuestions));
        
        fetch(storeQuestionsUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // LocalStorage tozalash
                for (let page = 1; page <= 4; page++) {
                    localStorage.removeItem(`test_${testId}_page_${page}`);
                }
                
                alert('Barcha savollar muvaffaqiyatli saqlandi!');
                window.location.href = createQuestionsUrl;
            } else {
                alert('Xatolik: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Saqlashda xatolik yuz berdi!');
        });
    }
});
