@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Yangi test yaratish</h1>
        <a href="{{ route('test-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
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
        <form action="{{ route('test-management.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Test nomi:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Tavsif:</label>
                <textarea name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description') }}</textarea>
            </div>
            

            
            <div class="mb-4">
                <label for="test_category_id" class="block text-gray-700 text-sm font-bold mb-2">Kategoriya:</label>
                <select name="test_category_id" id="test_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Kategoriyani tanlang</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('test_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Test turi:</label>
                <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Test turini tanlang</option>
                    <option value="familiarisation" {{ old('type') == 'familiarisation' ? 'selected' : '' }}>Tanishuv</option>
                    <option value="practice" {{ old('type') == 'practice' ? 'selected' : '' }}>Amaliyot</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="duration_minutes" class="block text-gray-700 text-sm font-bold mb-2">Davomiyligi (daqiqalarda):</label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-4">
                <label for="pass_score" class="block text-gray-700 text-sm font-bold mb-2">O'tish bali (0-100):</label>
                <input type="number" name="pass_score" id="pass_score" value="{{ old('pass_score') }}" min="0" max="100" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-4">
                <label for="attempts_allowed" class="block text-gray-700 text-sm font-bold mb-2">Ruxsat etilgan urinishlar soni:</label>
                <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', 1) }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <p class="text-gray-600 text-xs mt-1">Student necha marta test topshira olishini belgilaydi</p>
            </div>
            
            <div class="mb-4">
                <label for="time_limit" class="block text-gray-700 text-sm font-bold mb-2">Vaqt chegarasi (daqiqalarda):</label>
                <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', 30) }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <p class="text-gray-600 text-xs mt-1">Test uchun berilgan vaqt (daqiqalarda)</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ko'rsatmalar:</label>
                <div id="instructions-container" class="space-y-2">
                    <div class="flex items-center">
                        <input type="text" name="instructions[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline instruction-remove">
                            X
                        </button>
                    </div>
                </div>
                <button type="button" id="add-instruction" class="mt-2 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    + Ko'rsatma qo'shish
                </button>
            </div>
            
            <!-- Reading Passage (faqat Reading testlari uchun) -->
            <div class="mb-4 reading-passage-container" style="display: none;">
                <label for="passage" class="block text-gray-700 text-sm font-bold mb-2">Reading Passage (Matn):</label>
                <textarea name="passage" id="passage" rows="15" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Reading test uchun matnni shu yerga kiriting...">{{ old('passage') }}</textarea>
                <p class="text-gray-600 text-xs mt-1">Bu matn Reading test sahifasida ko'rsatiladi</p>
            </div>
            
            <!-- Audio Files (faqat Listening testlari uchun) -->
            <div class="mb-4 audio-upload-container" style="display: block;">
                <label class="block text-gray-700 text-sm font-bold mb-2">Audio Fayllar:</label>
                
                <!-- Audio fayllar yuklash -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                    <label for="audio_files" class="block text-sm text-gray-600 mb-2">Audio fayllar yuklash:</label>
                    <input type="file" name="audio_files[]" id="audio_files" multiple accept="audio/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">MP3, WAV, OGG formatlarini qo'llab-quvvatlaydi. Bir nechta fayl tanlash mumkin.</p>
                </div>
            </div>
            
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="mr-2">
                <label for="is_active" class="text-gray-700 text-sm font-bold">Faol</label>
            </div>
            
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_timed" id="is_timed" value="1" {{ old('is_timed') ? 'checked' : '' }} class="mr-2">
                <label for="is_timed" class="text-gray-700 text-sm font-bold">Vaqt chegarasi bor</label>
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Saqlash va savollar qo'shish
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ko'rsatma qo'shish
        document.getElementById('add-instruction').addEventListener('click', function() {
            const container = document.getElementById('instructions-container');
            const newInstruction = document.createElement('div');
            newInstruction.className = 'flex items-center';
            newInstruction.innerHTML = `
                <input type="text" name="instructions[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline instruction-remove">
                    X
                </button>
            `;
            container.appendChild(newInstruction);
        });
        
        // Ko'rsatmani o'chirish
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('instruction-remove')) {
                const container = document.getElementById('instructions-container');
                if (container.children.length > 1) {
                    e.target.parentElement.remove();
                }
            }
        });
        
        // Reading passage va Audio upload ko'rsatish/yashirish
        const categorySelect = document.getElementById('test_category_id');
        const readingPassageContainer = document.querySelector('.reading-passage-container');
        const audioUploadContainer = document.querySelector('.audio-upload-container');
        
        console.log('Category select:', categorySelect);
        console.log('Reading passage container:', readingPassageContainer);
        console.log('Audio upload container:', audioUploadContainer);
        
        // Kategoriya o'zgarganida tekshirish
        categorySelect.addEventListener('change', function() {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption.textContent.trim().toLowerCase();
            
            console.log('Selected category:', categoryName);
            
            // Agar reading kategoriyasi bo'lsa, matn maydonini ko'rsatish
            if (categoryName.includes('reading') || categoryName.includes('o\'qish')) {
                console.log('Showing reading passage container');
                readingPassageContainer.style.display = 'block';
            } else {
                console.log('Hiding reading passage container');
                readingPassageContainer.style.display = 'none';
            }
            
            // Agar listening kategoriyasi bo'lsa, audio upload ko'rsatish
            if (categoryName.includes('listening') || categoryName.includes('tinglash')) {
                console.log('Showing audio upload container');
                audioUploadContainer.style.display = 'block';
            } else {
                console.log('Hiding audio upload container');
                audioUploadContainer.style.display = 'none';
            }
        });
        
        // Sahifa yuklanganda ham tekshirish
        if (categorySelect.selectedIndex > 0) {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption.textContent.trim().toLowerCase();
            
            if (categoryName.includes('reading') || categoryName.includes('o\'qish')) {
                readingPassageContainer.style.display = 'block';
            }
            
            if (categoryName.includes('listening') || categoryName.includes('tinglash')) {
                audioUploadContainer.style.display = 'block';
            }
        }
    });
</script>
@endpush
@endsection
