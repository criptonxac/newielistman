@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $test->title }} - Yangi savollar qo'shish ({{ $currentPage ?? request()->query('page', 1) }}/4)</h1>
        <a href="{{ route('test-management.questions.create', $test->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
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

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('test-management.questions.store', $test->id) }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="current_page" value="{{ $currentPage ?? request()->query('page', 1) }}">
            @csrf
            
            <div class="flex justify-between mb-4">
                <div>
                    <span class="text-lg font-semibold">Sahifa: {{ $currentPage ?? request()->query('page', 1) }}/4</span>
                </div>
                <div class="space-x-2">
                    @if(($currentPage ?? request()->query('page', 1)) > 1)
                        <a href="{{ route('test-management.questions.add', [$test->id, 'page' => (($currentPage ?? request()->query('page', 1)) - 1)]) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                            &laquo; Oldingi
                        </a>
                    @endif
                    
                    @if(($currentPage ?? request()->query('page', 1)) < 4)
                        <a href="{{ route('test-management.questions.add', [$test->id, 'page' => (($currentPage ?? request()->query('page', 1)) + 1)]) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Keyingi &raquo;
                        </a>
                    @endif
                </div>
            </div>
            
            @php
                // Calculate the starting index for questions based on the current page
                $startIndex = (($currentPage ?? request()->query('page', 1)) - 1) * 10;
            @endphp
            
            @for ($i = 0; $i < 10; $i++)
                @php $questionIndex = $startIndex + $i; @endphp
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-bold mb-4">Savol #{{ $questionIndex + 1 }}</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="question_text_{{ $i }}">
                            Savol matni:
                        </label>
                        <textarea id="question_text_{{ $i }}" name="questions[{{ $i }}][question_text]" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="question_type_{{ $i }}">
                            Savol turi:
                        </label>
                        <select id="question_type_{{ $i }}" name="questions[{{ $i }}][question_type]" class="question-type shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
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
                    
                    <div id="options-container-{{ $i }}" class="options-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Variantlar:
                        </label>
                        <div id="options-list-{{ $i }}" class="options-list space-y-2">
                            <div class="flex items-center">
                                <input type="text" name="questions[{{ $i }}][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
                            </div>
                        </div>
                        <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-option" data-index="{{ $i }}">
                            + Variant qo'shish
                        </button>
                    </div>
                    
                    <div id="correct-answer-container-{{ $i }}" class="correct-answer-container mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="correct_answer_{{ $i }}">
                            To'g'ri javob:
                        </label>
                        <input type="text" id="correct_answer_{{ $i }}" name="questions[{{ $i }}][correct_answer]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div id="incorrect-answers-container-{{ $i }}" class="incorrect-answers-container mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Xato javoblar:
                        </label>
                        <div id="incorrect-answers-list-{{ $i }}" class="incorrect-answers-list space-y-2">
                            <div class="flex items-center">
                                <input type="text" name="questions[{{ $i }}][incorrect_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Xato javob">
                                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-incorrect-answer">X</button>
                            </div>
                        </div>
                        <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-incorrect-answer" data-index="{{ $i }}">
                            + Xato javob qo'shish
                        </button>
                    </div>
                    
                    <div id="multiple-answers-container-{{ $i }}" class="multiple-answers-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            To'g'ri javoblar (bir nechta):
                        </label>
                        <div id="correct-answers-list-{{ $i }}" class="correct-answers-list space-y-2">
                            <div class="flex items-center">
                                <input type="text" name="questions[{{ $i }}][correct_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
                            </div>
                        </div>
                        <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline add-correct-answer" data-index="{{ $i }}">
                            + To'g'ri javob qo'shish
                        </button>
                    </div>
                    
                    <div id="mapping-container-{{ $i }}" class="mapping-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="mapping_target_{{ $i }}">
                            Mapping target (drag-and-drop uchun):
                        </label>
                        <textarea id="mapping_target_{{ $i }}" name="questions[{{ $i }}][mapping_target]" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Drag-and-drop uchun maqsad elementni kiriting</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="points_{{ $i }}">
                            Ball:
                        </label>
                        <input type="number" id="points_{{ $i }}" name="questions[{{ $i }}][points]" min="1" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    
                    <input type="hidden" name="questions[{{ $i }}][sort_order]" value="{{ $questionIndex + 1 }}">
                </div>
            @endfor
            
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Saqlash
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Har bir savol uchun event listener'larni qo'shish
        for (let i = 0; i < 10; i++) {
            setupQuestionHandlers(i);
        }
        
        // Savol uchun barcha event listener'larni sozlash
        function setupQuestionHandlers(index) {
            const questionType = document.getElementById(`question_type_${index}`);
            const optionsContainer = document.getElementById(`options-container-${index}`);
            const correctAnswerContainer = document.getElementById(`correct-answer-container-${index}`);
            const multipleAnswersContainer = document.getElementById(`multiple-answers-container-${index}`);
            const mappingContainer = document.getElementById(`mapping-container-${index}`);
            
            // Savol turini o'zgartirish
            questionType.addEventListener('change', function() {
                // Barcha konteynerlarni yashirish
                optionsContainer.classList.add('hidden');
                correctAnswerContainer.classList.add('hidden');
                multipleAnswersContainer.classList.add('hidden');
                mappingContainer.classList.add('hidden');
                
                // Tanlangan turga qarab konteynerlarni ko'rsatish
                switch(this.value) {
                    case 'multiple_choice':
                    case 'multiple_answer':
                        optionsContainer.classList.remove('hidden');
                        if (this.value === 'multiple_choice') {
                            correctAnswerContainer.classList.remove('hidden');
                        } else {
                            multipleAnswersContainer.classList.remove('hidden');
                        }
                        break;
                    case 'true_false':
                        correctAnswerContainer.classList.remove('hidden');
                        break;
                    case 'fill_blank':
                        correctAnswerContainer.classList.remove('hidden');
                        break;
                    case 'matching':
                    case 'drag_drop':
                        optionsContainer.classList.remove('hidden');
                        multipleAnswersContainer.classList.remove('hidden');
                        mappingContainer.classList.remove('hidden');
                        break;
                    case 'essay':
                        // Essay uchun hech narsa ko'rsatilmaydi
                        break;
                }
            });
        }
        
        // Variant qo'shish tugmalarini sozlash
        document.querySelectorAll('.add-option').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const optionsList = document.getElementById(`options-list-${index}`);
                const newOption = document.createElement('div');
                newOption.className = 'flex items-center';
                newOption.innerHTML = `
                    <input type="text" name="questions[${index}][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
                `;
                optionsList.appendChild(newOption);
                
                // Variant o'chirish tugmasini ishlatish
                newOption.querySelector('.remove-option').addEventListener('click', function() {
                    optionsList.removeChild(newOption);
                });
            });
        });
        
        // To'g'ri javob qo'shish tugmalarini sozlash
        document.querySelectorAll('.add-correct-answer').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const correctAnswersList = document.getElementById(`correct-answers-list-${index}`);
                const newCorrectAnswer = document.createElement('div');
                newCorrectAnswer.className = 'flex items-center';
                newCorrectAnswer.innerHTML = `
                    <input type="text" name="questions[${index}][correct_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
                `;
                correctAnswersList.appendChild(newCorrectAnswer);
                
                // To'g'ri javob o'chirish tugmasini ishlatish
                newCorrectAnswer.querySelector('.remove-correct-answer').addEventListener('click', function() {
                    correctAnswersList.removeChild(newCorrectAnswer);
                });
            });
        });
        
        // Xato javob qo'shish tugmalarini sozlash
        document.querySelectorAll('.add-incorrect-answer').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const incorrectAnswersList = document.getElementById(`incorrect-answers-list-${index}`);
                const newIncorrectAnswer = document.createElement('div');
                newIncorrectAnswer.className = 'flex items-center';
                newIncorrectAnswer.innerHTML = `
                    <input type="text" name="questions[${index}][incorrect_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Xato javob">
                    <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-incorrect-answer">X</button>
                `;
                incorrectAnswersList.appendChild(newIncorrectAnswer);
                
                // Xato javob o'chirish tugmasini ishlatish
                newIncorrectAnswer.querySelector('.remove-incorrect-answer').addEventListener('click', function() {
                    incorrectAnswersList.removeChild(newIncorrectAnswer);
                });
            });
        });
        
        // Mavjud o'chirish tugmalarini ishlatish
        document.querySelectorAll('.remove-option').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.remove();
            });
        });
        
        document.querySelectorAll('.remove-correct-answer').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.remove();
            });
        });
        
        document.querySelectorAll('.remove-incorrect-answer').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.remove();
            });
        });
    });
</script>
@endpush
