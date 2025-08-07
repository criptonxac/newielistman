@extends($layout)

@section('title', 'Yangi Listening Test Item Yaratish')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Yangi Item Yaratish</h1>
                <p class="text-gray-600 mt-1">{{ $listeningTest->title }} uchun</p>
            </div>
            <a href="{{ route('listening-tests.items.index', $listeningTest) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Orqaga
            </a>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('listening-tests.items.store', $listeningTest) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            
            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Item sarlavhasi:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="Masalan: Audio Track 1" required>
            </div>
            
            <!-- Type -->
            <div class="mb-4">
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Item turi:</label>
                <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Item turini tanlang</option>
                    <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                    <option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>Question</option>
                    <option value="instruction" {{ old('type') == 'instruction' ? 'selected' : '' }}>Instruction</option>
                </select>
            </div>
            
            <!-- Item Title -->
            <div class="mb-4">
                <label for="item_title" class="block text-gray-700 text-sm font-bold mb-2">Item Title:</label>
                <input type="text" name="item_title" id="item_title" value="{{ old('item_title') }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="Masalan: Part 1 - Conversation">
            </div>
            
            <!-- Item Content -->
            <div class="mb-4">
                <label for="item_content" class="block text-gray-700 text-sm font-bold mb-2">Item Content:</label>
                <textarea name="item_content" id="item_content" rows="4" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          placeholder="Item mazmuni...">{{ old('item_content') }}</textarea>
            </div>
            
            <!-- Item Options -->
            <div class="mb-4">
                <label for="item_options" class="block text-gray-700 text-sm font-bold mb-2">Item Options:</label>
                <textarea name="item_options" id="item_options" rows="3" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          placeholder="Qo'shimcha parametrlar (JSON format)...">{{ old('item_options') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Ixtiyoriy: JSON formatda qo'shimcha parametrlar</p>
            </div>

            <!-- Hidden JSON field -->
            <input type="hidden" name="body" id="body_json">

            <div class="flex items-center justify-between">
                <button type="button" onclick="previewData()" 
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-eye mr-2"></i>Ko'rib chiqish
                </button>
                
                <div class="space-x-2">
                    <button type="button" onclick="clearForm()" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-broom mr-2"></i>Tozalash
                    </button>
                    
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-save mr-2"></i>Saqlash
                    </button>
                </div>
            </div>
        </form>

        <!-- Preview Section -->
        <div id="preview-section" class="bg-gray-100 rounded-lg p-4 mt-4 hidden">
            <h3 class="text-lg font-semibold mb-2">Ko'rib chiqish:</h3>
            <pre id="preview-content" class="bg-white p-3 rounded text-sm overflow-auto"></pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submit da JSON yaratish
    document.querySelector('form').addEventListener('submit', function(e) {
        const itemData = {
            title: document.getElementById('item_title').value || '',
            content: document.getElementById('item_content').value || '',
            options: document.getElementById('item_options').value || ''
        };
        
        document.getElementById('body_json').value = JSON.stringify(itemData);
    });
});

function previewData() {
    const itemData = {
        title: document.getElementById('item_title').value || '',
        content: document.getElementById('item_content').value || '',
        options: document.getElementById('item_options').value || ''
    };
    
    document.getElementById('preview-content').textContent = JSON.stringify(itemData, null, 2);
    document.getElementById('preview-section').classList.remove('hidden');
}

function clearForm() {
    if (confirm('Rostdan ham formani tozalamoqchimisiz?')) {
        document.getElementById('item_title').value = '';
        document.getElementById('item_content').value = '';
        document.getElementById('item_options').value = '';
        document.getElementById('preview-section').classList.add('hidden');
    }
}
</script>
@endsection
