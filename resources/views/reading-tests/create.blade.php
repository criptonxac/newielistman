@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Yangi Reading Test yaratish</h1>
        <a href="{{ route('reading-tests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
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
        <form action="{{ route('reading-tests.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="app_test_id" class="block text-gray-700 text-sm font-bold mb-2">AppTest:</label>
                <select name="app_test_id" id="app_test_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">AppTest tanlang</option>
                    @foreach(\App\Models\AppTest::active()->get() as $appTest)
                        <option value="{{ $appTest->id }}" {{ old('app_test_id', request('app_test_id')) == $appTest->id ? 'selected' : '' }}>
                            {{ $appTest->title }} ({{ ucfirst($appTest->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Reading Test sarlavhasi:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <!-- Content Title -->
            <div class="mb-4">
                <label for="content_title" class="block text-gray-700 text-sm font-bold mb-2">Content Title:</label>
                <input type="text" name="content_title" id="content_title" value="{{ old('content_title') }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="Masalan: Reading Passage 1">
            </div>
            
            <!-- Content Image -->
            <div class="mb-4">
                <label for="content_image" class="block text-gray-700 text-sm font-bold mb-2">Content Image URL:</label>
                <input type="url" name="content_image" id="content_image" value="{{ old('content_image') }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="https://example.com/image.jpg (ixtiyoriy)">
                <p class="text-xs text-gray-500 mt-1">Rasm URL manzilini kiriting (ixtiyoriy)</p>
            </div>
            
            <!-- Content Body -->
            <div class="mb-4">
                <label for="content_body" class="block text-gray-700 text-sm font-bold mb-2">Content Body:</label>
                <textarea name="content_body" id="content_body" rows="8" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          placeholder="Reading passage matnini shu yerga yozing...">{{ old('content_body') }}</textarea>
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
                    Saqlash
                </button>
                <a href="{{ route('reading-tests.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Bekor qilish
                </a>
            </div>
        </form>
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
