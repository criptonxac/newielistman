@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $test->title }} - Yangi savol qo'shish</h1>
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
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="question_text">
                    Savol matni:
                </label>
                <textarea id="question_text" name="questions[0][question_text]" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="question_type">
                    Savol turi:
                </label>
                <select id="question_type" name="questions[0][question_type]" class="question-type shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
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
            
            <div id="options-container" class="mb-4 hidden">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Variantlar:
                </label>
                <div id="options-list" class="space-y-2">
                    <div class="flex items-center">
                        <input type="text" name="questions[0][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
                    </div>
                </div>
                <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline" id="add-option">
                    + Variant qo'shish
                </button>
            </div>
            
            <div id="correct-answer-container" class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="correct_answer">
                    To'g'ri javob:
                </label>
                <input type="text" id="correct_answer" name="questions[0][correct_answer]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div id="multiple-answers-container" class="mb-4 hidden">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    To'g'ri javoblar (bir nechta):
                </label>
                <div id="correct-answers-list" class="space-y-2">
                    <div class="flex items-center">
                        <input type="text" name="questions[0][correct_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
                    </div>
                </div>
                <button type="button" class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline" id="add-correct-answer">
                    + To'g'ri javob qo'shish
                </button>
            </div>
            
            <div id="mapping-container" class="mb-4 hidden">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mapping_target">
                    Mapping target (drag-and-drop uchun):
                </label>
                <input type="text" id="mapping_target" name="questions[0][mapping_target]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">Drag-and-drop uchun maqsad elementni kiriting</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="points">
                    Ball:
                </label>
                <input type="number" id="points" name="questions[0][points]" value="1" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <input type="hidden" name="questions[0][sort_order]" value="1">
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
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
        const questionType = document.getElementById('question_type');
        const optionsContainer = document.getElementById('options-container');
        const correctAnswerContainer = document.getElementById('correct-answer-container');
        const multipleAnswersContainer = document.getElementById('multiple-answers-container');
        const mappingContainer = document.getElementById('mapping-container');
        const addOptionBtn = document.getElementById('add-option');
        const addCorrectAnswerBtn = document.getElementById('add-correct-answer');
        
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
        
        // Variant qo'shish
        addOptionBtn.addEventListener('click', function() {
            const optionsList = document.getElementById('options-list');
            const newOption = document.createElement('div');
            newOption.className = 'flex items-center';
            newOption.innerHTML = `
                <input type="text" name="questions[0][options][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-option">X</button>
            `;
            optionsList.appendChild(newOption);
            
            // Variant o'chirish tugmasini ishlatish
            newOption.querySelector('.remove-option').addEventListener('click', function() {
                optionsList.removeChild(newOption);
            });
        });
        
        // To'g'ri javob qo'shish
        addCorrectAnswerBtn.addEventListener('click', function() {
            const correctAnswersList = document.getElementById('correct-answers-list');
            const newCorrectAnswer = document.createElement('div');
            newCorrectAnswer.className = 'flex items-center';
            newCorrectAnswer.innerHTML = `
                <input type="text" name="questions[0][correct_answers][]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline remove-correct-answer">X</button>
            `;
            correctAnswersList.appendChild(newCorrectAnswer);
            
            // To'g'ri javob o'chirish tugmasini ishlatish
            newCorrectAnswer.querySelector('.remove-correct-answer').addEventListener('click', function() {
                correctAnswersList.removeChild(newCorrectAnswer);
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
    });
</script>
@endpush
