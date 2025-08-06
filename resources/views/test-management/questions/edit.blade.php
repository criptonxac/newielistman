@extends($layout ?? 'layouts.test-management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Savollarni tahrirlash: {{ $test->title }}</h1>
        <a href="{{ route('test-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
            Orqaga
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="questions-form" action="{{ route('test-management.questions.update', $test->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Test ma'lumotlari</h2>
                <p class="text-gray-600">Kategoriya: {{ $test->category->name }}</p>
                <p class="text-gray-600">Turi: {{ $test->type ? $test->type->label() : 'Belgilanmagan' }}</p>
                <p class="text-gray-600">Savollar soni: {{ $test->total_questions }}</p>
                <input type="hidden" id="test-category" value="{{ $test->category->name }}">
            </div>
            
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Savollar</h2>
                <p class="text-gray-600 mb-4">Savollarni tahrirlang yoki yangi savollar qo'shing.</p>
                
                <div id="questions-container">
                    @if($test->questions->count() > 0)
                        @foreach($test->questions as $question)
                            <div class="question-item bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200" data-id="{{ $question->id }}">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center">
                                        <span class="question-number font-semibold mr-2">Savol #{{ $question->question_number }}</span>
                                        <button type="button" class="drag-handle text-gray-500 cursor-move">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <button type="button" class="delete-question text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <input type="hidden" name="questions[{{ $question->id }}][id]" value="{{ $question->id }}">
                                <input type="hidden" name="questions[{{ $question->id }}][sort_order]" value="{{ $question->sort_order }}" class="sort-order">
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Savol matni:</label>
                                    <textarea name="questions[{{ $question->id }}][question_text]" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ $question->question_text }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Savol turi:</label>
                                    <select name="questions[{{ $question->id }}][question_type]" class="question-type shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>Ko'p tanlovli (Radio)</option>
                                        <option value="fill_blank" {{ $question->question_type == 'fill_blank' ? 'selected' : '' }}>Bo'sh joyni to'ldirish</option>
                                        <option value="true_false" {{ $question->question_type == 'true_false' ? 'selected' : '' }}>To'g'ri/Noto'g'ri</option>
                                        <option value="drag_drop" {{ $question->question_type == 'drag_drop' ? 'selected' : '' }}>Drag & Drop</option>
                                        <option value="essay" {{ $question->question_type == 'essay' ? 'selected' : '' }}>Insho</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Ball:</label>
                                    <input type="number" name="questions[{{ $question->id }}][points]" value="{{ $question->points }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                                
                                <div class="mb-3 options-container {{ in_array($question->question_type, ['multiple_choice', 'drag_drop']) ? '' : 'hidden' }}">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Variantlar:</label>
                                    <div class="options-list">
                                        @if(isset($question->options) && is_array($question->options))
                                            @foreach($question->options as $index => $option)
                                                <div class="option-item flex items-center mb-2">
                                                    <input type="text" name="questions[{{ $question->id }}][options][]" value="{{ $option }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Variant">
                                                    <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="add-option mt-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded text-sm">
                                        + Variant qo'shish
                                    </button>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">To'g'ri javob:</label>
                                    <input type="text" name="questions[{{ $question->id }}][correct_answer]" value="{{ $question->correct_answer }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                
                                <div class="mb-3 incorrect-answers-container">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Xato javoblar:</label>
                                    <div id="incorrect-answers-list-{{ $question->id }}" class="incorrect-answers-list space-y-2">
                                        @if(isset($question->incorrect_answers) && is_array($question->incorrect_answers))
                                            @foreach($question->incorrect_answers as $index => $incorrect_answer)
                                                <div class="flex items-center">
                                                    <input type="text" name="questions[{{ $question->id }}][incorrect_answers][]" value="{{ $incorrect_answer }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Xato javob">
                                                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-incorrect-answer">X</button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex items-center">
                                                <input type="text" name="questions[{{ $question->id }}][incorrect_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Xato javob">
                                                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-incorrect-answer">X</button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-incorrect-answer" data-index="{{ $question->id }}">
                                        + Xato javob qo'shish
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-yellow-50 p-4 rounded border border-yellow-200 text-yellow-800">
                            <p>Hozircha savollar yo'q. Savol qo'shish tugmasini bosing.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <button type="button" id="add-question" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    + Savol qo'shish
                </button>
                
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                    Saqlash
                </button>
            </div>
        </div>
    </form>
    
    <!-- Yangi savol shabloni -->
    <template id="question-template">
        <div class="question-item bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200" data-id="new_QUESTION_INDEX">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <span class="question-number font-semibold mr-2">Yangi savol</span>
                    <button type="button" class="drag-handle text-gray-500 cursor-move">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
                
                <button type="button" class="delete-question text-red-500 hover:text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
            
            <input type="hidden" name="questions[new_QUESTION_INDEX][sort_order]" value="SORT_ORDER" class="sort-order">
            
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-bold mb-1">Savol matni:</label>
                <textarea name="questions[new_QUESTION_INDEX][question_text]" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-bold mb-1">Savol turi:</label>
                <select name="questions[new_QUESTION_INDEX][question_type]" class="question-type shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Savol turini tanlang</option>
                    <option value="multiple_choice">Ko'p tanlovli (Radio)</option>
                    <option value="fill_blank">Bo'sh joyni to'ldirish</option>
                    <option value="true_false">To'g'ri/Noto'g'ri</option>
                    <option value="drag_drop">Drag & Drop</option>
                    <option value="essay">Insho</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-bold mb-1">Ball:</label>
                <input type="number" name="questions[new_QUESTION_INDEX][points]" value="1" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-3 options-container hidden">
                <label class="block text-gray-700 text-sm font-bold mb-1">Variantlar:</label>
                <div class="options-list">
                    <!-- Variantlar dinamik qo'shiladi -->
                </div>
                <button type="button" class="add-option mt-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded text-sm">
                    + Variant qo'shish
                </button>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-bold mb-1">To'g'ri javob:</label>
                <input type="text" name="questions[new_QUESTION_INDEX][correct_answer]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-3 incorrect-answers-container">
                <label class="block text-gray-700 text-sm font-bold mb-1">Xato javoblar:</label>
                <div class="incorrect-answers-list space-y-2">
                    <div class="flex items-center">
                        <input type="text" name="questions[new_QUESTION_INDEX][incorrect_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Xato javob">
                        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-incorrect-answer">X</button>
                    </div>
                </div>
                <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-incorrect-answer" data-index="new_QUESTION_INDEX">
                    + Xato javob qo'shish
                </button>
            </div>
        </div>
    </template>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let questionIndex = {{ $test->questions->count() + 1 }};
        let sortOrder = {{ $test->questions->count() + 1 }};
        
        // Savol qo'shish
        document.getElementById('add-question').addEventListener('click', function() {
            const template = document.getElementById('question-template').innerHTML;
            const questionsContainer = document.getElementById('questions-container');
            
            // Yangi savol uchun HTML yaratish
            let newQuestion = template
                .replace(/new_QUESTION_INDEX/g, 'new_' + questionIndex)
                .replace(/SORT_ORDER/g, sortOrder);
            
            // DOM ga qo'shish
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newQuestion;
            const questionElement = tempDiv.firstElementChild;
            questionsContainer.appendChild(questionElement);
            
            // Yangi savol uchun event listener-larni qo'shish
            addQuestionEventListeners(questionElement);
            
            // Indekslarni oshirish
            questionIndex++;
            sortOrder++;
            
            // Savollar tartibini yangilash
            updateQuestionNumbers();
        });
        
        // Mavjud savollarga event listener-larni qo'shish
        document.querySelectorAll('.question-item').forEach(function(question) {
            addQuestionEventListeners(question);
        });
        
        // Savollarni drag-and-drop qilish imkoniyati
        const questionsContainer = document.getElementById('questions-container');
        new Sortable(questionsContainer, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                updateQuestionNumbers();
                updateSortOrders();
            }
        });
        
        // Savol elementiga event listener-larni qo'shish
        function addQuestionEventListeners(question) {
            // Savolni o'chirish
            const deleteButton = question.querySelector('.delete-question');
            if (deleteButton) {
                deleteButton.addEventListener('click', function() {
                    if (confirm('Haqiqatan ham bu savolni o\'chirmoqchimisiz?')) {
                        question.remove();
                        updateQuestionNumbers();
                        updateSortOrders();
                    }
                });
            }
            
            // Savol turini o'zgartirish
            const questionTypeSelect = question.querySelector('.question-type');
            if (questionTypeSelect) {
                questionTypeSelect.addEventListener('change', function() {
                    const optionsContainer = question.querySelector('.options-container');
                    const type = this.value;
                    if (type === 'multiple_choice' || type === 'drag_drop') {
                        optionsContainer.classList.remove('hidden');
                    } else {
                        optionsContainer.classList.add('hidden');
                    }
                });
            }
            
            // Variant qo'shish
            const addOptionButton = question.querySelector('.add-option');
            if (addOptionButton) {
                addOptionButton.addEventListener('click', function() {
                    const optionsList = question.querySelector('.options-list');
                    const questionId = question.dataset.id;
                    
                    const optionItem = document.createElement('div');
                    optionItem.className = 'option-item flex items-center mb-2';
                    optionItem.innerHTML = `
                        <input type="text" name="questions[${questionId}][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Variant">
                        <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    
                    optionsList.appendChild(optionItem);
                    
                    // Variantni o'chirish
                    const removeOptionButton = optionItem.querySelector('.remove-option');
                    removeOptionButton.addEventListener('click', function() {
                        optionItem.remove();
                    });
                });
            }
            
            // Mavjud variantlarni o'chirish
            question.querySelectorAll('.remove-option').forEach(function(button) {
                button.addEventListener('click', function() {
                    this.closest('.option-item').remove();
                });
            });
            
            // Xato javob qo'shish
            const addIncorrectAnswerButton = question.querySelector('.add-incorrect-answer');
            if (addIncorrectAnswerButton) {
                addIncorrectAnswerButton.addEventListener('click', function() {
                    const incorrectAnswersList = question.querySelector('.incorrect-answers-list');
                    const questionId = question.dataset.id;
                    
                    const incorrectAnswerItem = document.createElement('div');
                    incorrectAnswerItem.className = 'flex items-center';
                    incorrectAnswerItem.innerHTML = `
                        <input type="text" name="questions[${questionId}][incorrect_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Xato javob">
                        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-incorrect-answer">X</button>
                    `;
                    
                    incorrectAnswersList.appendChild(incorrectAnswerItem);
                    
                    // Xato javobni o'chirish
                    const removeIncorrectAnswerButton = incorrectAnswerItem.querySelector('.remove-incorrect-answer');
                    removeIncorrectAnswerButton.addEventListener('click', function() {
                        incorrectAnswerItem.remove();
                    });
                });
            }
            
            // Mavjud xato javoblarni o'chirish
            question.querySelectorAll('.remove-incorrect-answer').forEach(function(button) {
                button.addEventListener('click', function() {
                    this.closest('.flex.items-center').remove();
                });
            });
        }
        
        // Savol raqamlarini yangilash
        function updateQuestionNumbers() {
            document.querySelectorAll('.question-item').forEach(function(question, index) {
                const questionNumber = question.querySelector('.question-number');
                if (questionNumber) {
                    if (question.dataset.id.startsWith('new_')) {
                        questionNumber.textContent = 'Yangi savol #' + (index + 1);
                    } else {
                        questionNumber.textContent = 'Savol #' + (index + 1);
                    }
                }
            });
        }
        
        // Sort order qiymatlarini yangilash
        function updateSortOrders() {
            document.querySelectorAll('.question-item').forEach(function(question, index) {
                const sortOrderInput = question.querySelector('.sort-order');
                if (sortOrderInput) {
                    sortOrderInput.value = index + 1;
                }
            });
        }
        
        // Formani yuborishdan oldin tekshirish
        document.getElementById('questions-form').addEventListener('submit', function(e) {
            const questions = document.querySelectorAll('.question-item');
            
            // Kamida 1 ta savol bo'lishi kerak
            if (questions.length === 0) {
                e.preventDefault();
                alert('Kamida 1 ta savol qo\'shishingiz kerak!');
                return;
            }
            
            // Savollar soni 40 tadan oshmasligi kerak
            if (questions.length > 40) {
                e.preventDefault();
                alert('Testda savollar soni 40 tadan oshmasligi kerak. Hozir: ' + questions.length + ' ta.');
                return;
            }
            
            // Barcha savol turlarini tekshirish va to'g'rilash
            questions.forEach((question, index) => {
                const questionTypeSelect = question.querySelector('select[name*="question_type"]');
                if (questionTypeSelect) {
                    // Savol turini ma'lumotlar bazasidagi ruxsat etilgan qiymatlarga moslashtirish
                    const selectedText = questionTypeSelect.options[questionTypeSelect.selectedIndex].text;
                    
                    // Qiymatlarni to'g'rilash
                    if (selectedText === 'Drag-and-Drop' || selectedText === 'Ko\'p to\'g\'ri javobli (Checkbox)') {
                        questionTypeSelect.value = 'matching';
                    }
                    
                    // multiple_answer qiymatini multiple_choice ga o'zgartirish
                    if (questionTypeSelect.value === 'multiple_answer') {
                        questionTypeSelect.value = 'multiple_choice';
                    }
                    
                    console.log(`Savol #${index + 1} turi: ${questionTypeSelect.value}`);
                }
            });
        });
    });
</script>
@endsection
