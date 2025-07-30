@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $test->title }} - Savollar qo'shish</h1>
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

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('test-management.questions.store', $test->id) }}" method="POST" enctype="multipart/form-data" id="questions-form">
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
                
                <!-- Enhanced Audio Upload Section -->
                <div class="audio-upload-section" id="audioUploadSection">
                    <div class="upload-icon">🎵</div>
                    <h3>Audio fayllarni yuklash</h3>
                    <label for="audio-upload" class="upload-label">
                        📁 Fayllarni tanlash yoki bu yerga tashlang
                    </label>
                    <input id="audio-upload" type="file" name="audio_files[]" multiple accept="audio/*" class="upload-input">
                    
                    <div class="upload-info">
                        <p><strong>Qo'llab-quvvatlanadigan formatlar:</strong> MP3, WAV, OGG, M4A</p>
                        <p><strong>Maksimal hajm:</strong> 100MB har bir fayl uchun</p>
                        <p><strong>Auto-play:</strong> Test boshlanganda avtomatik ijro etiladi</p>
                    </div>
                </div>

                <!-- Files Progress Section -->
                <div class="files-progress-section" id="filesProgressSection" style="display: none;">
                    <h3>📊 Yuklash jarayoni</h3>
                    <div id="filesList"></div>
                </div>

                <!-- Audio Preview Section -->
                <div class="audio-preview-section" id="audioPreviewSection" style="display: none;">
                    <h3>🎧 Yuklangan Audio Fayllar</h3>
                    <div id="audioPreviewList"></div>
                </div>
            </div>
            @endif
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Savollar:</h2>
                <p class="text-sm text-gray-500 mb-4">Savollarni qo'shish uchun pastdagi tugmani bosing. Savollarni drag-and-drop orqali tartibini o'zgartirishingiz mumkin.</p>
                
                <div id="questions-container" class="space-y-4">
                    @if($questions->count() > 0)
                        @foreach($questions as $index => $question)
                            <div class="question-item bg-gray-50 p-4 rounded border border-gray-200" draggable="true" data-question-id="{{ $question->id }}">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="font-semibold question-number">Savol #{{ ($questions->currentPage() - 1) * 10 + $loop->iteration }}</h3>
                                    <div class="flex space-x-2">
                                        <button type="button" class="handle cursor-move text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                            </svg>
                                        </button>
                                        <button type="button" class="remove-question text-red-500" data-question-id="{{ $question->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="questions[{{ $question->id }}][sort_order]" value="{{ $question->sort_order }}" class="sort-order">
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Savol matni:</label>
                                    <textarea name="questions[{{ $question->id }}][question_text]" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ $question->question_text }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Savol turi:</label>
                                    <select name="questions[{{ $question->id }}][question_type]" class="question-type shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>Ko'p tanlovli (Radio)</option>
                                        <option value="short_answer" {{ $question->question_type == 'short_answer' ? 'selected' : '' }}>Ko'p to'g'ri javobli (Checkbox)</option>
                                        <option value="true_false" {{ $question->question_type == 'true_false' ? 'selected' : '' }}>To'g'ri/Noto'g'ri</option>
                                        <option value="fill_blank" {{ $question->question_type == 'fill_blank' ? 'selected' : '' }}>Bo'sh joyni to'ldirish</option>
                                        <option value="matching" {{ $question->question_type == 'matching' ? 'selected' : '' }}>Moslashtirish</option>
                                        <option value="drag_drop" {{ $question->question_type == 'drag_drop' ? 'selected' : '' }}>Drag & Drop</option>
                                        <option value="essay" {{ $question->question_type == 'essay' ? 'selected' : '' }}>Insho</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Ball:</label>
                                    <input type="number" name="questions[{{ $question->id }}][points]" value="{{ $question->points }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-yellow-50 p-4 rounded border border-yellow-200 text-yellow-800">
                            <p>Hozircha savollar yo'q. Savol qo'shish tugmasini bosing.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Pagination Controls -->
                @if($questions->hasPages())
                <div class="mt-6 flex justify-center">
                    <div class="flex space-x-1">
                        @if($questions->onFirstPage())
                            <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded-md">Oldingi</span>
                        @else
                            <a href="{{ route('test-management.questions.create', [$test->id, 'page' => $questions->currentPage() - 1]) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">Oldingi</a>
                        @endif
                        
                        @for($i = 1; $i <= $questions->lastPage(); $i++)
                            @if($i == $questions->currentPage())
                                <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded-md">{{ $i }}</span>
                            @else
                                <a href="{{ route('test-management.questions.create', [$test->id, 'page' => $i]) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">{{ $i }}</a>
                            @endif
                        @endfor
                        
                        @if($questions->hasMorePages())
                            <a href="{{ route('test-management.questions.create', [$test->id, 'page' => $questions->currentPage() + 1]) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">Keyingi</a>
                        @else
                            <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded-md">Keyingi</span>
                        @endif
                    </div>
                </div>
                @endif
                
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
                <option value="short_answer">Ko'p to'g'ri javobli (Checkbox)</option>
                <option value="true_false">To'g'ri/Noto'g'ri</option>
                <option value="fill_blank">Bo'sh joyni to'ldirish</option>
                <option value="matching">Moslashtirish</option>
                <option value="drag_drop">Drag & Drop</option>
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

@push('styles')
<link rel="stylesheet" href="{{ asset('css/enhanced-audio-upload.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="{{ asset('js/enhanced-audio-upload.js') }}"></script>
<script src="{{ asset('js/questions-management.js') }}"></script>
@endpush
@endsection