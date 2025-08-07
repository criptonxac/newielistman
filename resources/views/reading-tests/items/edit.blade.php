@extends('layouts.teacher')

@section('title', 'Reading Test Item Tahrirlash')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reading Test Item Tahrirlash</h1>
                    <p class="text-gray-600 mt-1">{{ $readingTest->title }} - {{ $item->title }}</p>
                </div>
                <a href="{{ route('reading-tests.items.show', [$readingTest, $item]) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Orqaga
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('reading-tests.items.update', [$readingTest, $item]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Item Nomi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $item->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                           placeholder="Masalan: Passage 1, Question Set 1, Instructions"
                           required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Item Turi <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                            required>
                        <option value="">Turni tanlang</option>
                        @foreach(\App\Models\ReadingTestItem::getTypes() as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $item->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Content Title -->
                    <div>
                        <label for="content_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Content Title
                        </label>
                        <input type="text" 
                               id="content_title" 
                               name="content_title" 
                               value="{{ old('content_title', is_array($item->body) ? ($item->body['title'] ?? '') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Content sarlavhasi">
                    </div>

                    <!-- Content Image URL -->
                    <div>
                        <label for="content_image_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Content Image URL
                        </label>
                        <input type="text" 
                               id="content_image_url" 
                               name="content_image_url" 
                               value="{{ old('content_image_url', is_array($item->body) ? ($item->body['image_url'] ?? '') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://example.com/image.jpg">
                        <p class="text-gray-500 text-sm mt-1">Rasm URL manzilini kiriting (ixtiyoriy)</p>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="mb-6">
                    <label for="content_body" class="block text-sm font-medium text-gray-700 mb-2">
                        Content Body
                    </label>
                    <textarea id="content_body" 
                              name="content_body" 
                              rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Item mazmuni, matn, ko'rsatmalar va boshqalar...">{{ old('content_body', is_array($item->body) ? ($item->body['body'] ?? '') : '') }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">Item ning asosiy mazmuni</p>
                </div>

                <!-- JSON Preview -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        JSON Preview
                    </label>
                    <div class="bg-gray-50 border border-gray-300 rounded-md p-3">
                        <pre id="json-preview" class="text-sm text-gray-700 whitespace-pre-wrap"></pre>
                    </div>
                    <div class="mt-2">
                        <button type="button" 
                                id="preview-btn" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                            <i class="fas fa-eye mr-1"></i>Preview
                        </button>
                        <button type="button" 
                                id="clear-btn" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm ml-2 transition duration-200">
                            <i class="fas fa-trash mr-1"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Hidden JSON field -->
                <input type="hidden" id="body" name="body" value="{{ old('body', is_string($item->body) ? $item->body : json_encode($item->body)) }}">

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('reading-tests.items.show', [$readingTest, $item]) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Bekor qilish
                    </a>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-save mr-2"></i>Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTitle = document.getElementById('content_title');
    const contentBody = document.getElementById('content_body');
    const contentImageUrl = document.getElementById('content_image_url');
    const jsonPreview = document.getElementById('json-preview');
    const bodyField = document.getElementById('body');
    const previewBtn = document.getElementById('preview-btn');
    const clearBtn = document.getElementById('clear-btn');

    function updatePreview() {
        const data = {
            title: contentTitle.value || '',
            body: contentBody.value || '',
            image_url: contentImageUrl.value || ''
        };
        
        jsonPreview.textContent = JSON.stringify(data, null, 2);
        bodyField.value = JSON.stringify(data);
    }

    function clearFields() {
        contentTitle.value = '';
        contentBody.value = '';
        contentImageUrl.value = '';
        updatePreview();
    }

    // Event listeners
    previewBtn.addEventListener('click', updatePreview);
    clearBtn.addEventListener('click', clearFields);
    
    // Auto-update on input
    [contentTitle, contentBody, contentImageUrl].forEach(field => {
        field.addEventListener('input', updatePreview);
    });

    // Initial preview
    updatePreview();
});
</script>
@endsection
