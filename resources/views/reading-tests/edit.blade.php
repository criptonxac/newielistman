@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reading Test tahrirlash</h1>
        <div class="flex space-x-2">
            <a href="{{ route('reading-tests.show', $readingTest) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                Ko'rish
            </a>
            <a href="{{ route('reading-tests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                Orqaga
            </a>
        </div>
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
        <form action="{{ route('reading-tests.update', $readingTest) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="app_test_id" class="block text-gray-700 text-sm font-bold mb-2">AppTest:</label>
                <select name="app_test_id" id="app_test_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">AppTest tanlang</option>
                    @foreach(\App\Models\AppTest::active()->get() as $appTest)
                        <option value="{{ $appTest->id }}" {{ old('app_test_id', $readingTest->app_test_id) == $appTest->id ? 'selected' : '' }}>
                            {{ $appTest->title }} ({{ ucfirst($appTest->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Reading Test sarlavhasi:</label>
                <input type="text" name="title" id="title" value="{{ old('title', $readingTest->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            @php
                $bodyData = $readingTest->body ?? [];
                $contentTitle = old('content_title', $bodyData['title'] ?? '');
                $contentImage = old('content_image', $bodyData['image'] ?? '');
                $contentBody = old('content_body', $bodyData['body'] ?? '');
            @endphp
            
            <!-- Content Title -->
            <div class="mb-4">
                <label for="content_title" class="block text-gray-700 text-sm font-bold mb-2">Content Title:</label>
                <input type="text" name="content_title" id="content_title" value="{{ $contentTitle }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="Masalan: Reading Passage 1">
            </div>
            
            <!-- Content Image -->
            <div class="mb-4">
                <label for="content_image" class="block text-gray-700 text-sm font-bold mb-2">Content Image URL:</label>
                <input type="text" name="content_image" id="content_image" value="{{ $contentImage }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="https://example.com/image.jpg (ixtiyoriy)">
                <p class="text-xs text-gray-500 mt-1">Rasm URL manzilini kiriting (ixtiyoriy)</p>
            </div>
            
            <!-- Content Body -->
            <div class="mb-4">
                <label for="content_body" class="block text-gray-700 text-sm font-bold mb-2">Content Body:</label>
                <textarea name="content_body" id="content_body" rows="8" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          placeholder="Reading passage matnini shu yerga yozing...">{{ $contentBody }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Reading test uchun asosiy matnni kiriting</p>
            </div>
            
            <!-- Hidden JSON field -->
            <input type="hidden" name="body" id="body">

            <div class="mb-6 flex space-x-4">
                <button type="button" onclick="previewContent()" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                    <i class="fas fa-eye mr-2"></i>Ko'rib chiqish
                </button>
                <button type="button" onclick="clearForm()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                    <i class="fas fa-eraser mr-2"></i>Tozalash
                </button>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Yangilash
                </button>
                <a href="{{ route('reading-tests.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>

    <!-- Items bo'limi -->
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Reading Test Itemlar ({{ $readingTest->items->count() }})</h2>
            <a href="{{ route('reading-tests.items.create', $readingTest) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                Item qo'shish
            </a>
        </div>
        
        @if($readingTest->items->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tartib</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($readingTest->items->sortBy('order') as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($item->type == 'passage') bg-blue-100 text-blue-800
                                @elseif($item->type == 'question') bg-green-100 text-green-800
                                @elseif($item->type == 'instruction') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($item->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('reading-tests.items.show', [$readingTest, $item]) }}" class="text-blue-600 hover:text-blue-900">Ko'rish</a>
                                <a href="{{ route('reading-tests.items.edit', [$readingTest, $item]) }}" class="text-indigo-600 hover:text-indigo-900">Tahrirlash</a>
                                <form action="{{ route('reading-tests.items.destroy', [$readingTest, $item]) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">O'chirish</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Hali itemlar qo'shilmagan</p>
        @endif
    </div>
</div>

<script>
// Form submit qilishdan oldin JSON yaratish
document.querySelector('form').addEventListener('submit', function(e) {
    const contentTitle = document.getElementById('content_title').value;
    const contentImage = document.getElementById('content_image').value;
    const contentBody = document.getElementById('content_body').value;
    
    // JSON obyekt yaratish
    const jsonData = {
        title: contentTitle || '',
        image: contentImage || '',
        body: contentBody || ''
    };
    
    // JSON stringga o'tkazish va yashirin maydoniga yozish
    document.getElementById('body').value = JSON.stringify(jsonData);
});

// Ko'rib chiqish funksiyasi
function previewContent() {
    const contentTitle = document.getElementById('content_title').value;
    const contentImage = document.getElementById('content_image').value;
    const contentBody = document.getElementById('content_body').value;
    
    if (!contentTitle && !contentBody) {
        alert('Kamida title yoki body to\'ldirilishi kerak!');
        return;
    }
    
    let preview = 'CONTENT PREVIEW:\n\n';
    if (contentTitle) preview += 'Title: ' + contentTitle + '\n\n';
    if (contentImage) preview += 'Image: ' + contentImage + '\n\n';
    if (contentBody) preview += 'Body: ' + contentBody.substring(0, 200) + (contentBody.length > 200 ? '...' : '');
    
    alert(preview);
}

// Formani tozalash funksiyasi
function clearForm() {
    if (confirm('Barcha ma\'lumotlarni tozalamoqchimisiz?')) {
        document.getElementById('content_title').value = '';
        document.getElementById('content_image').value = '';
        document.getElementById('content_body').value = '';
        document.getElementById('body').value = '';
    }
}
</script>
@endsection
