@extends($layout)

@section('title', 'Writing Test Tahrirlash - ' . $writingTest->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Writing Test Tahrirlash</h1>
                    <p class="text-gray-600 mt-1">{{ $writingTest->title }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('writing-tests.show', $writingTest) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Ko'rish
                    </a>
                    <a href="{{ route('writing-tests.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('writing-tests.update', $writingTest) }}" method="POST" class="px-6 py-6">
            @csrf
            @method('PUT')

            <!-- App Test Selection -->
            <div class="mb-6">
                <label for="app_test_id" class="block text-sm font-medium text-gray-700 mb-2">
                    App Test <span class="text-red-500">*</span>
                </label>
                <select name="app_test_id" id="app_test_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('app_test_id') border-red-500 @enderror">
                    <option value="">App Test tanlang</option>
                    @foreach($appTests as $appTest)
                        <option value="{{ $appTest->id }}" 
                                {{ (old('app_test_id', $writingTest->app_test_id) == $appTest->id) ? 'selected' : '' }}>
                            {{ $appTest->title }}
                        </option>
                    @endforeach
                </select>
                @error('app_test_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Test Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Test Nomi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $writingTest->title) }}"
                       required
                       placeholder="Masalan: IELTS Writing Task 1 - Academic"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @php
                $questions = $writingTest->formatted_questions;
                $answer = $writingTest->formatted_answer;
            @endphp

            <!-- Questions Section -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Questions Ma'lumotlari</h3>
                
                <!-- Question Title -->
                <div class="mb-4">
                    <label for="question_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Question Title
                    </label>
                    <input type="text" 
                           name="question_title" 
                           id="question_title" 
                           value="{{ old('question_title', $questions['title'] ?? '') }}"
                           placeholder="Masalan: Task 1 - Academic Writing"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('question_title') border-red-500 @enderror">
                    @error('question_title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Question Content -->
                <div class="mb-4">
                    <label for="question_content" class="block text-sm font-medium text-gray-700 mb-2">
                        Question Content
                    </label>
                    <textarea name="question_content" 
                              id="question_content" 
                              rows="4"
                              placeholder="Test savoli matnini kiriting..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('question_content') border-red-500 @enderror">{{ old('question_content', $questions['content'] ?? '') }}</textarea>
                    @error('question_content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Question Instructions -->
                <div class="mb-4">
                    <label for="question_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                        Instructions
                    </label>
                    <textarea name="question_instructions" 
                              id="question_instructions" 
                              rows="3"
                              placeholder="Test bo'yicha ko'rsatmalar..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('question_instructions') border-red-500 @enderror">{{ old('question_instructions', $questions['instructions'] ?? '') }}</textarea>
                    @error('question_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Answer Section -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Answer Ma'lumotlari</h3>
                
                <!-- Answer Sample -->
                <div class="mb-4">
                    <label for="answer_sample" class="block text-sm font-medium text-gray-700 mb-2">
                        Sample Answer
                    </label>
                    <textarea name="answer_sample" 
                              id="answer_sample" 
                              rows="6"
                              placeholder="Namuna javob..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('answer_sample') border-red-500 @enderror">{{ old('answer_sample', $answer['sample'] ?? '') }}</textarea>
                    @error('answer_sample')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Answer Criteria -->
                <div class="mb-4">
                    <label for="answer_criteria" class="block text-sm font-medium text-gray-700 mb-2">
                        Grading Criteria
                    </label>
                    <textarea name="answer_criteria" 
                              id="answer_criteria" 
                              rows="4"
                              placeholder="Baholash mezonlari..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('answer_criteria') border-red-500 @enderror">{{ old('answer_criteria', $answer['criteria'] ?? '') }}</textarea>
                    @error('answer_criteria')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Data Preview -->
            <div class="mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Joriy Ma'lumotlar</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="text-xs font-medium text-gray-600 mb-2">Questions:</h5>
                            <div class="bg-white rounded p-3 text-xs font-mono overflow-auto max-h-32">
                                <pre>{{ json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-xs font-medium text-gray-600 mb-2">Answer:</h5>
                            <div class="bg-white rounded p-3 text-xs font-mono overflow-auto max-h-32">
                                <pre>{{ json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JSON Preview Section -->
            <div class="mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-sm font-medium text-gray-700">Yangi JSON Preview</h4>
                        <div class="space-x-2">
                            <button type="button" id="preview-btn" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                Preview
                            </button>
                            <button type="button" id="clear-btn" class="text-sm bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">
                                Clear
                            </button>
                        </div>
                    </div>
                    <div id="json-preview" class="bg-white border rounded p-3 text-sm font-mono text-gray-600 min-h-20">
                        JSON preview bu yerda ko'rsatiladi...
                    </div>
                </div>
            </div>

            <!-- Hidden JSON Fields -->
            <input type="hidden" name="questions" id="questions-json">
            <input type="hidden" name="answer" id="answer-json">

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('writing-tests.show', $writingTest) }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Bekor qilish
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Yangilash
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('preview-btn');
    const clearBtn = document.getElementById('clear-btn');
    const jsonPreview = document.getElementById('json-preview');
    const questionsJson = document.getElementById('questions-json');
    const answerJson = document.getElementById('answer-json');

    // Preview function
    previewBtn.addEventListener('click', function() {
        const questionsData = {
            title: document.getElementById('question_title').value || '',
            content: document.getElementById('question_content').value || '',
            instructions: document.getElementById('question_instructions').value || ''
        };

        const answerData = {
            sample: document.getElementById('answer_sample').value || '',
            criteria: document.getElementById('answer_criteria').value || ''
        };

        const previewData = {
            questions: questionsData,
            answer: answerData
        };

        questionsJson.value = JSON.stringify(questionsData);
        answerJson.value = JSON.stringify(answerData);

        jsonPreview.innerHTML = '<pre>' + JSON.stringify(previewData, null, 2) + '</pre>';
    });

    // Clear function
    clearBtn.addEventListener('click', function() {
        document.getElementById('question_title').value = '';
        document.getElementById('question_content').value = '';
        document.getElementById('question_instructions').value = '';
        document.getElementById('answer_sample').value = '';
        document.getElementById('answer_criteria').value = '';
        questionsJson.value = '';
        answerJson.value = '';
        jsonPreview.innerHTML = 'JSON preview bu yerda ko\'rsatiladi...';
    });

    // Auto-update JSON on form submit
    document.querySelector('form').addEventListener('submit', function() {
        previewBtn.click(); // Trigger preview to update JSON fields
    });
});
</script>
@endsection
