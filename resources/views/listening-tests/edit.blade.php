@extends($layout)

@section('title', 'Listening Test tahrirlash - ' . $listeningTest->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Listening Test tahrirlash</h1>
            <a href="{{ route('listening-tests.show', $listeningTest) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                Orqaga
            </a>
        </div>

        <form action="{{ route('listening-tests.update', $listeningTest) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="app_test_id" class="block text-gray-700 text-sm font-bold mb-2">AppTest:</label>
                <select name="app_test_id" id="app_test_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">AppTest tanlang</option>
                    @foreach($appTests as $appTest)
                        <option value="{{ $appTest->id }}" {{ old('app_test_id', $listeningTest->app_test_id) == $appTest->id ? 'selected' : '' }}>
                            {{ $appTest->title }} ({{ ucfirst($appTest->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Listening Test sarlavhasi:</label>
                <input type="text" name="title" id="title" value="{{ old('title', $listeningTest->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            @php
                $audioData = $listeningTest->audio ?? [];
                $audioTitle = old('audio_title', $audioData['title'] ?? '');
                $audioUrl = old('audio_url', $audioData['url'] ?? '');
                $audioDescription = old('audio_description', $audioData['description'] ?? '');
            @endphp
            
            <!-- Audio Title -->
            <div class="mb-4">
                <label for="audio_title" class="block text-gray-700 text-sm font-bold mb-2">Audio Title:</label>
                <input type="text" name="audio_title" id="audio_title" value="{{ $audioTitle }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="Masalan: IELTS Listening Test 1">
            </div>
            
            <!-- Hozirgi Audio Fayl -->
            @if(!empty($audioUrl))
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Hozirgi Audio:</label>
                <div class="bg-gray-100 p-3 rounded">
                    <audio controls class="w-full">
                        <source src="{{ $audioUrl }}" type="audio/mpeg">
                        Brauzeringiz audio elementini qo'llab-quvvatlamaydi.
                    </audio>
                    <p class="text-xs text-gray-500 mt-1">{{ $audioUrl }}</p>
                </div>
            </div>
            @endif
            
            <!-- Yangi Audio Fayl Yuklash -->
            <div class="mb-4">
                <label for="audio_file" class="block text-gray-700 text-sm font-bold mb-2">Yangi Audio Fayl Yuklash:</label>
                <input type="file" name="audio_file" id="audio_file" accept=".mp3,.wav,.ogg,.m4a" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">MP3, WAV, OGG, M4A formatlarida, maksimal 20MB (ixtiyoriy)</p>
            </div>
            
            <!-- Yoki Audio URL -->
            <div class="mb-4">
                <label for="audio_url" class="block text-gray-700 text-sm font-bold mb-2">Yoki Audio URL:</label>
                <input type="url" name="audio_url" id="audio_url" value="{{ $audioUrl }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="https://example.com/audio.mp3">
                <p class="text-xs text-gray-500 mt-1">Audio fayl URL manzilini kiriting (ixtiyoriy)</p>
            </div>
            
            <!-- Audio Description -->
            <div class="mb-4">
                <label for="audio_description" class="block text-gray-700 text-sm font-bold mb-2">Audio Description:</label>
                <textarea name="audio_description" id="audio_description" rows="4" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          placeholder="Audio haqida qisqacha ma'lumot...">{{ $audioDescription }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Audio fayl haqida qo'shimcha ma'lumot (ixtiyoriy)</p>
            </div>
            
            <!-- Hidden JSON field -->
            <input type="hidden" name="audio" id="audio">

            <div class="mb-6 flex space-x-4">
                <button type="button" onclick="previewAudio()" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
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
                <a href="{{ route('listening-tests.show', $listeningTest) }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Form submit qilishdan oldin JSON yaratish
document.querySelector('form').addEventListener('submit', function(e) {
    const audioTitle = document.getElementById('audio_title').value;
    const audioUrl = document.getElementById('audio_url').value;
    const audioDescription = document.getElementById('audio_description').value;
    
    // JSON obyekt yaratish
    const jsonData = {
        title: audioTitle || '',
        url: audioUrl || '',
        description: audioDescription || ''
    };
    
    // JSON stringga o'tkazish va yashirin maydoniga yozish
    document.getElementById('audio').value = JSON.stringify(jsonData);
});

// Ko'rib chiqish funksiyasi
function previewAudio() {
    const audioTitle = document.getElementById('audio_title').value;
    const audioUrl = document.getElementById('audio_url').value;
    const audioDescription = document.getElementById('audio_description').value;
    
    if (!audioUrl) {
        alert('Audio URL to\'ldirilishi kerak!');
        return;
    }
    
    let preview = 'AUDIO PREVIEW:\n\n';
    if (audioTitle) preview += 'Title: ' + audioTitle + '\n\n';
    if (audioUrl) preview += 'URL: ' + audioUrl + '\n\n';
    if (audioDescription) preview += 'Description: ' + audioDescription.substring(0, 200) + (audioDescription.length > 200 ? '...' : '');
    
    alert(preview);
}

// Formani tozalash funksiyasi
function clearForm() {
    if (confirm('Barcha ma\'lumotlarni tozalamoqchimisiz?')) {
        document.getElementById('audio_title').value = '';
        document.getElementById('audio_url').value = '';
        document.getElementById('audio_description').value = '';
        document.getElementById('audio').value = '';
    }
}
</script>
@endsection
