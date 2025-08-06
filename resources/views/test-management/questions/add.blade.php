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
                // Calculate the starting index based on existing questions count
                $startIndex = $questionNumber - 1; // questionNumber keladi controller dan
                $existingQuestionsCount = $test->questions()->count();
            @endphp
            
            <!-- Mavjud savollar haqida ma'lumot -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Mavjud savollar:</strong> {{ $existingQuestionsCount }} ta savol
                            <br>
                            <strong>Keyingi savollar:</strong> {{ $questionNumber }} dan {{ $questionNumber + 9 }} gacha (10 ta savol)
                        </p>
                    </div>
                </div>
            </div>
            
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
                            <option value="fill_blank">Bo'sh joyni to'ldirish</option>
                            <option value="true_false">To'g'ri/Noto'g'ri</option>
                            <option value="drag_drop">Drag & Drop</option>
                            <option value="essay">Insho</option>
                        </select>
                    </div>
                    
                    <!-- Multiple Choice Options -->
                    <div id="multiple-choice-container-{{ $i }}" class="multiple-choice-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Variantlar (1 ta to'g'ri, 3 ta xato):
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <span class="w-8 text-center font-bold">A)</span>
                                <input type="text" name="questions[{{ $i }}][options][]" placeholder="Variant A" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ml-2">
                                <input type="radio" name="questions[{{ $i }}][correct_answer]" value="0" class="ml-2">
                                <span class="ml-1 text-sm text-gray-600">To'g'ri</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-8 text-center font-bold">B)</span>
                                <input type="text" name="questions[{{ $i }}][options][]" placeholder="Variant B" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ml-2">
                                <input type="radio" name="questions[{{ $i }}][correct_answer]" value="1" class="ml-2">
                                <span class="ml-1 text-sm text-gray-600">To'g'ri</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-8 text-center font-bold">C)</span>
                                <input type="text" name="questions[{{ $i }}][options][]" placeholder="Variant C" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ml-2">
                                <input type="radio" name="questions[{{ $i }}][correct_answer]" value="2" class="ml-2">
                                <span class="ml-1 text-sm text-gray-600">To'g'ri</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-8 text-center font-bold">D)</span>
                                <input type="text" name="questions[{{ $i }}][options][]" placeholder="Variant D" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ml-2">
                                <input type="radio" name="questions[{{ $i }}][correct_answer]" value="3" class="ml-2">
                                <span class="ml-1 text-sm text-gray-600">To'g'ri</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fill Blank Container -->
                    <div id="fill-blank-container-{{ $i }}" class="fill-blank-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            To'g'ri javob:
                        </label>
                        <input type="text" name="questions[{{ $i }}][correct_answer]" placeholder="Bo'sh joyga yoziladigan to'g'ri javob" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="text-sm text-gray-600 mt-1">Savol matnida bo'sh joy uchun _____ (chiziqcha) ishlating</p>
                    </div>
                    
                    <!-- True/False Container -->
                    <div id="true-false-container-{{ $i }}" class="true-false-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            To'g'ri javob:
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="questions[{{ $i }}][correct_answer]" value="true" class="mr-2">
                                <span>True (To'g'ri)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="questions[{{ $i }}][correct_answer]" value="false" class="mr-2">
                                <span>False (Noto'g'ri)</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Question Format Container -->
                    <div id="question-format-container-{{ $i }}" class="question-format-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Savol formati:
                        </label>
                        <select name="questions[{{ $i }}][question_format]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Formatni tanlang</option>
                            <option value="simple_fill">Oddiy to'ldirish</option>
                            <option value="form_completion">Form to'ldirish</option>
                            <option value="passage_fill">Matn ichida to'ldirish</option>
                        </select>
                    </div>
                    
                    <!-- Form Structure Container -->
                    <div id="form-structure-container-{{ $i }}" class="form-structure-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Form strukturasi (HTML):
                        </label>
                        <textarea name="questions[{{ $i }}][form_structure]" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder='Misol: Name: <input type="text" placeholder="1"> Court'></textarea>
                    </div>
                    
                    <!-- Drag & Drop Container -->
                    <div id="drag-drop-container-{{ $i }}" class="drag-drop-container mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Drag & Drop elementlari (1 ta to'g'ri, 2 ta noto'g'ri):
                        </label>
                        
                        <!-- Draggable Items -->
                        <div class="mb-4">
                            <label class="block text-gray-600 text-sm font-bold mb-2">Sudraladigan elementlar:</label>
                            <div class="space-y-2">
                                <input type="text" name="questions[{{ $i }}][drag_items][]" placeholder="To'g'ri javob" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <input type="text" name="questions[{{ $i }}][drag_items][]" placeholder="Noto'g'ri javob 1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <input type="text" name="questions[{{ $i }}][drag_items][]" placeholder="Noto'g'ri javob 2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                        
                        <!-- Drop Zone -->
                        <div class="mb-4">
                            <label class="block text-gray-600 text-sm font-bold mb-2">Tashlanadigan joy:</label>
                            <input type="text" name="questions[{{ $i }}][drop_zone_label]" placeholder="Drop zone nomi (masalan: 'Asia', 'Europe')" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        
                        <!-- Correct Answer -->
                        <div class="mb-4">
                            <label class="block text-gray-600 text-sm font-bold mb-2">To'g'ri javob:</label>
                            <select name="questions[{{ $i }}][correct_answer]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="0">Birinchi element (to'g'ri javob)</option>
                                <option value="1">Ikkinchi element</option>
                                <option value="2">Uchinchi element</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Essay Settings Container -->
                    <div id="essay-settings-container-{{ $i }}" class="essay-settings-container mb-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Minimum so'z soni:
                                </label>
                                <input type="number" name="questions[{{ $i }}][min_words]" min="1" value="250" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Maksimum so'z soni:
                                </label>
                                <input type="number" name="questions[{{ $i }}][max_words]" min="1" value="500" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Multiple Choice Settings -->
                    <div id="mc-settings-container-{{ $i }}" class="mc-settings-container mb-4 hidden">
                        <label class="flex items-center">
                            <input type="checkbox" name="questions[{{ $i }}][show_option_letters]" value="1" checked class="mr-2">
                            <span class="text-gray-700 text-sm font-bold">A, B, C, D harflarini ko'rsatish</span>
                        </label>
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

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="points_{{ $i }}">
                            Ball:
                        </label>
                        <input type="number" id="points_{{ $i }}" name="questions[{{ $i }}][points]" min="1" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    
                    <input type="hidden" name="questions[{{ $i }}][question_number]" value="{{ $questionIndex + 1 }}">
                    <input type="hidden" name="questions[{{ $i }}][sort_order]" value="{{ $questionIndex + 1 }}">
                </div>
            @endfor
            
            <div class="flex justify-between mt-6">
                @if($currentPage < 4)
                    <button type="button" id="saveAndNext" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Saqlash va Keyingi Sahifa
                    </button>
                @else
                    <button type="button" id="saveAllQuestions" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Barcha Savollarni Saqlash
                    </button>
                @endif
                
                @if($currentPage > 1)
                    <button type="button" id="loadPrevious" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Oldingi Sahifani Yuklash
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/question-add-pagination.js') }}" 
    data-test-id="{{ $test->id }}" 
    data-current-page="{{ $currentPage }}" 
    data-add-question-url="{{ route('test-management.questions.add', $test->id) }}" 
    data-store-questions-url="{{ route('test-management.questions.store', $test->id) }}" 
    data-create-questions-url="{{ route('test-management.questions.create', $test->id) }}"></script>
@endpush
