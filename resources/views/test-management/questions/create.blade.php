@extends('layouts.teacher')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $test->title }} - Savollar qo'shish</h1>
        <a href="{{ route('test-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
            Orqaga
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('test-management.questions.store', $test) }}" method="POST" enctype="multipart/form-data" id="questions-form">
            @csrf
            <input type="hidden" id="test-category" value="{{ $test->category->name }}" />
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Test ma'lumotlari:</h2>
                <div class="bg-gray-100 p-4 rounded">
                    <p><strong>Kategoriya:</strong> {{ $test->category->name }}</p>
                    <p><strong>Turi:</strong> 
                        @if($test->type == 'familiarisation')
                            Tanishuv
                        @elseif($test->type == 'sample')
                            Namuna
                        @elseif($test->type == 'practice')
                            Amaliyot
                        @endif
                    </p>
                    <p><strong>Davomiyligi:</strong> {{ $test->duration_minutes ?? 'Belgilanmagan' }} daqiqa</p>
                </div>
            </div>
            
            @if($test->category->name == 'Listening')
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Audio fayllar:</h2>
                <div class="border-dashed border-2 border-gray-300 p-4 rounded">
                    <div class="flex flex-col items-center">
                        <label for="audio-upload" class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-2">
                            Audio fayllarni tanlash
                        </label>
                        <input id="audio-upload" type="file" name="audio_files[]" multiple accept="audio/*" class="hidden" onchange="updateFileList(this)">
                        <p class="text-sm text-gray-500">MP3, WAV, OGG formatlar qo'llaniladi (max: 100MB)</p>
                        <div id="selected-files" class="mt-2 w-full"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Savollar:</h2>
                <p class="text-sm text-gray-500 mb-4">Savollarni qo'shish uchun pastdagi tugmani bosing. Savollarni drag-and-drop orqali tartibini o'zgartirishingiz mumkin.</p>
                
                <div id="questions-container" class="space-y-4">
                    <!-- Savollar shu yerga qo'shiladi -->
                </div>
                
                <a href="{{ route('test-management.questions.add', $test->id) }}" class="mt-4 inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    + Savol qo'shish
                </a>
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Savollarni saqlash
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Savol shabloni -->
<template id="question-template">
    <div class="question-item bg-gray-50 p-4 rounded border border-gray-200" draggable="true">
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-semibold question-number">Savol #1</h3>
            <div class="flex space-x-2">
                <button type="button" class="handle cursor-move text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                    </svg>
                </button>
                <button type="button" class="remove-question text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <input type="hidden" name="questions[INDEX][sort_order]" value="1" class="sort-order">
        
        <div class="mb-3">
            <label class="block text-gray-700 text-sm font-bold mb-1">Savol matni:</label>
            <textarea name="questions[INDEX][question_text]" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
        </div>
        
        <div class="mb-3">
            <label class="block text-gray-700 text-sm font-bold mb-1">Savol turi:</label>
            <select name="questions[INDEX][question_type]" class="question-type shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">Turni tanlang</option>
                <option value="multiple_choice">Ko'p tanlovli (Radio)</option>
                <option value="multiple_answer">Ko'p to'g'ri javobli (Checkbox)</option>
                <option value="true_false">To'g'ri/Noto'g'ri</option>
                <option value="fill_blank">Bo'sh joyni to'ldirish</option>
                <option value="matching">Moslashtirish</option>
                <option value="drag_drop">Drag-and-Drop</option>
                <option value="essay">Insho</option>
            </select>
        </div>
        
        <div class="options-container mb-3 hidden">
            <label class="block text-gray-700 text-sm font-bold mb-1">Variantlar:</label>
            <div class="options-list space-y-2">
                <div class="flex items-center">
                    <input type="text" name="questions[INDEX][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
                </div>
            </div>
            <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-option">
                + Variant qo'shish
            </button>
        </div>
        
        <div class="mb-3 correct-answer-container">
            <label class="block text-gray-700 text-sm font-bold mb-1">To'g'ri javob:</label>
            <input type="text" name="questions[INDEX][correct_answer]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        
        <div class="mb-3 multiple-answers-container hidden">
            <label class="block text-gray-700 text-sm font-bold mb-1">To'g'ri javoblar (bir nechta):</label>
            <div class="correct-answers-list space-y-2">
                <div class="flex items-center">
                    <input type="text" name="questions[INDEX][correct_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
                </div>
            </div>
            <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-correct-answer">
                + To'g'ri javob qo'shish
            </button>
        </div>
        
        <div class="mb-3 mapping-container hidden">
            <label class="block text-gray-700 text-sm font-bold mb-1">Mapping target (drag-and-drop uchun):</label>
            <input type="text" name="questions[INDEX][mapping_target]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <p class="text-xs text-gray-500 mt-1">Drag-and-drop uchun maqsad elementni kiriting</p>
        </div>
        
        <div class="mb-3">
            <label class="block text-gray-700 text-sm font-bold mb-1">Ball:</label>
            <input type="number" name="questions[INDEX][points]" value="1" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
    </div>
</template>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    // Sahifa yuklanganda ishga tushiriladigan kod
    window.onload = function() {
        console.log('window.onload ishga tushdi');
        
        // Global o'zgaruvchilar
        window.questionIndex = 0;
        window.questionsContainer = document.getElementById('questions-container');
        window.questionTemplate = document.getElementById('question-template');
        
        console.log('questionsContainer:', window.questionsContainer);
        console.log('questionTemplate:', window.questionTemplate);
        
        // Savol qo'shish tugmasiga hodisa qo'shish
        var addQuestionLink = document.getElementById('add-question-link');
        console.log('addQuestionLink:', addQuestionLink);
        
        if (addQuestionLink) {
            addQuestionLink.addEventListener('click', function(e) {
                console.log('Savol qo\'shish tugmasi bosildi');
                e.preventDefault();
                addQuestion();
            });
        } else {
            console.error('add-question-link topilmadi!');
        }
        
        // Sahifa yuklanganda bitta savol qo'shish
        console.log('Birinchi savolni qo\'shish...');
        addQuestion();
        
        // Drag-and-drop uchun Sortable.js ni ishlatish
        if (questionsContainer) {
            new Sortable(questionsContainer, {
                animation: 150,
                handle: '.handle',
                onEnd: updateQuestionNumbers
            });
        } else {
            console.error('questionsContainer topilmadi!');
        }
        
        // Bu kod endi kerak emas, chunki yuqorida onclick bilan almashtirildi
        // document.getElementById('add-question').addEventListener('click', function(e) {
        //    e.preventDefault();
        //    addQuestion();
        //    console.log('Savol qo\'shish tugmasi bosildi');
        // });
        
        // Savolni o'chirish
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-question')) {
                e.target.closest('.question-item').remove();
                updateQuestionNumbers();
            }
        });
        
        // Savol turini o'zgartirish
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('question-type')) {
                const questionItem = e.target.closest('.question-item');
                const optionsContainer = questionItem.querySelector('.options-container');
                const correctAnswerContainer = questionItem.querySelector('.correct-answer-container');
                const multipleAnswersContainer = questionItem.querySelector('.multiple-answers-container');
                const mappingContainer = questionItem.querySelector('.mapping-container');
                
                // Reset all containers
                optionsContainer.classList.add('hidden');
                correctAnswerContainer.classList.add('hidden');
                multipleAnswersContainer.classList.add('hidden');
                mappingContainer.classList.add('hidden');
                
                // Show appropriate containers based on question type
                switch(e.target.value) {
                    case 'multiple_choice':
                        optionsContainer.classList.remove('hidden');
                        correctAnswerContainer.classList.remove('hidden');
                        break;
                    case 'multiple_answer':
                        optionsContainer.classList.remove('hidden');
                        multipleAnswersContainer.classList.remove('hidden');
                        break;
                    case 'matching':
                        optionsContainer.classList.remove('hidden');
                        correctAnswerContainer.classList.remove('hidden');
                        break;
                    case 'drag_drop':
                        optionsContainer.classList.remove('hidden');
                        mappingContainer.classList.remove('hidden');
                        break;
                    default:
                        correctAnswerContainer.classList.remove('hidden');
                }
            }
        });
        
        // Variant qo'shish
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-option')) {
                const optionsList = e.target.closest('.options-container').querySelector('.options-list');
                const newOption = document.createElement('div');
                newOption.className = 'flex items-center';
                
                const questionIndex = e.target.closest('.question-item').querySelector('.sort-order').name.match(/\d+/)[0];
                
                newOption.innerHTML = `
                    <input type="text" name="questions[${questionIndex}][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
                `;
                
                optionsList.appendChild(newOption);
            }
        });
        
        // Variantni o'chirish
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option')) {
                const optionsList = e.target.closest('.options-list');
                if (optionsList.children.length > 1) {
                    e.target.closest('.flex').remove();
                }
            }
        });
        
        // To'g'ri javob qo'shish (multiple answers uchun)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-correct-answer')) {
                const answersList = e.target.closest('.multiple-answers-container').querySelector('.correct-answers-list');
                const newAnswer = document.createElement('div');
                newAnswer.className = 'flex items-center';
                
                const questionIndex = e.target.closest('.question-item').querySelector('.sort-order').name.match(/\d+/)[0];
                
                newAnswer.innerHTML = `
                    <input type="text" name="questions[${questionIndex}][correct_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
                `;
                
                answersList.appendChild(newAnswer);
            }
        });
        
        // To'g'ri javobni o'chirish (multiple answers uchun)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-correct-answer')) {
                const answersList = e.target.closest('.correct-answers-list');
                if (answersList.children.length > 1) {
                    e.target.closest('.flex').remove();
                }
            }
        });
        
        // Savol raqamlarini yangilash
        function updateQuestionNumbers() {
            const questions = questionsContainer.querySelectorAll('.question-item');
            questions.forEach((question, index) => {
                question.querySelector('.question-number').textContent = `Savol #${index + 1}`;
                question.querySelector('.sort-order').value = index + 1;
                
                // Barcha input va select nomlarini yangilash
                const inputs = question.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });
        }
        
        // Yangi savol qo'shish
        function addQuestion() {
            console.log('addQuestion funksiyasi chaqirildi');
            
            try {
                if (!window.questionTemplate) {
                    window.questionTemplate = document.getElementById('question-template');
                    console.log('questionTemplate olindi:', window.questionTemplate);
                }
                
                if (!window.questionsContainer) {
                    window.questionsContainer = document.getElementById('questions-container');
                    console.log('questionsContainer olindi:', window.questionsContainer);
                }
                
                if (!window.questionTemplate || !window.questionsContainer) {
                    console.error('questionTemplate yoki questionsContainer topilmadi!');
                    return;
                }
                
                const newQuestion = document.importNode(window.questionTemplate.content, true).querySelector('.question-item');
                
                // Savol indeksini yangilash
                const inputs = newQuestion.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/INDEX/, window.questionIndex));
                    }
                });
                
                window.questionsContainer.appendChild(newQuestion);
                window.questionIndex++;
                updateQuestionNumbers();
                console.log('Yangi savol qo\'shildi, jami savollar: ' + window.questionsContainer.querySelectorAll('.question-item').length);
            } catch (error) {
                console.error('Savol qo\'shishda xatolik:', error);
            }
        }
        
        // Yangi qo'shilgan funksiya - tugma uchun
        function manualAddQuestion() {
            console.log('manualAddQuestion funksiyasi chaqirildi');
            addQuestion();
            return false;
        }
        
        // Audio fayllar ro'yxatini ko'rsatish
        window.updateFileList = function(input) {
            const fileList = document.getElementById('selected-files');
            fileList.innerHTML = '';
            
            if (input.files.length > 0) {
                const list = document.createElement('ul');
                list.className = 'list-disc pl-5';
                
                for (let i = 0; i < input.files.length; i++) {
                    const item = document.createElement('li');
                    item.textContent = input.files[i].name;
                    list.appendChild(item);
                }
                
                fileList.appendChild(list);
            }
        };
        
        // Formani yuborishdan oldin tekshirish
        document.getElementById('questions-form').addEventListener('submit', function(e) {
            const questions = questionsContainer.querySelectorAll('.question-item');
            const testCategory = document.getElementById('test-category').value;
            
            // Check if there are any questions
            if (questions.length === 0) {
                e.preventDefault();
                alert('Kamida bitta savol qo\'shing!');
                return;
            }
            
            // For Listening and Reading tests, require at least 40 questions
            if ((testCategory === 'Listening' || testCategory === 'Reading') && questions.length < 40) {
                e.preventDefault();
                alert(`${testCategory} testi uchun kamida 40 ta savol bo'lishi kerak. Hozir: ${questions.length} ta.`);
                return;
            }
        });
        
        // Sahifa yuklanganda bitta savol qo'shish
        addQuestion();
    });
</script>
@endpush
@endsection
