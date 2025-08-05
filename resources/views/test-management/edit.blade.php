@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Testni tahrirlash</h1>
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
        <form action="{{ route('test-management.update', $test) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Test nomi:</label>
                <input type="text" name="title" id="title" value="{{ old('title', $test->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Tavsif:</label>
                <textarea name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $test->description) }}</textarea>
            </div>
            
            <div class="mb-4 reading-passage-container" style="display: none;">
                <label for="reading_passage" class="block text-gray-700 text-sm font-bold mb-2">O'qish uchun matn (Reading test uchun):</label>
                <textarea name="reading_passage" id="reading_passage" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('reading_passage', $test->reading_passage) }}</textarea>
                <p class="text-sm text-gray-600 mt-1">Bu matn reading test sahifasida ko'rsatiladi.</p>
            </div>
            
            <div class="mb-4">
                <label for="test_category_id" class="block text-gray-700 text-sm font-bold mb-2">Kategoriya:</label>
                <select name="test_category_id" id="test_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Kategoriyani tanlang</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('test_category_id', $test->test_category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Test turi:</label>
                <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Test turini tanlang</option>
                    <option value="familiarisation" {{ old('type', $test->type) == 'familiarisation' ? 'selected' : '' }}>Tanishuv</option>
                    <option value="practice" {{ old('type', $test->type) == 'practice' ? 'selected' : '' }}>Amaliyot</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="duration_minutes" class="block text-gray-700 text-sm font-bold mb-2">Davomiyligi (daqiqalarda):</label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $test->duration_minutes) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label for="pass_score" class="block text-gray-700 text-sm font-bold mb-2">O'tish bali (0-100):</label>
                <input type="number" name="pass_score" id="pass_score" value="{{ old('pass_score', $test->pass_score ?? 60) }}" min="0" max="100" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs mt-1">Testni muvaffaqiyatli yakunlash uchun kerakli ball</p>
            </div>
            
            <div class="mb-4">
                <label for="attempts_allowed" class="block text-gray-700 text-sm font-bold mb-2">Ruxsat etilgan urinishlar soni:</label>
                <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', $test->attempts_allowed ?? 1) }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <p class="text-gray-600 text-xs mt-1">Student necha marta test topshira olishini belgilaydi</p>
            </div>
            
            <div class="mb-4">
                <label for="time_limit" class="block text-gray-700 text-sm font-bold mb-2">Vaqt chegarasi (daqiqalarda):</label>
                <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $test->time_limit ?? 30) }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <p class="text-gray-600 text-xs mt-1">Test uchun berilgan vaqt (daqiqalarda)</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ko'rsatmalar:</label>
                <div id="instructions-container" class="space-y-2">
                    @if(is_array($test->instructions) && count($test->instructions) > 0)
                        @foreach($test->instructions as $instruction)
                        <div class="flex items-center">
                            <input type="text" name="instructions[]" value="{{ $instruction }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline instruction-remove">
                                X
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center">
                            <input type="text" name="instructions[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <button type="button" class="ml-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline instruction-remove">
                                X
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-instruction" class="mt-2 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    + Ko'rsatma qo'shish
                </button>
            </div>
            
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $test->is_active) ? 'checked' : '' }} class="mr-2">
                <label for="is_active" class="text-gray-700 text-sm font-bold">Faol</label>
            </div>
            
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_timed" id="is_timed" value="1" {{ old('is_timed', $test->is_timed) ? 'checked' : '' }} class="mr-2">
                <label for="is_timed" class="text-gray-700 text-sm font-bold">Vaqt chegarasi bor</label>
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Saqlash
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
        
        // Reading passage ko'rsatish/yashirish
        const categorySelect = document.getElementById('test_category_id');
        const readingPassageContainer = document.querySelector('.reading-passage-container');
        
        // Kategoriya o'zgarganida tekshirish
        categorySelect.addEventListener('change', function() {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption.textContent.trim().toLowerCase();
            
            // Agar reading kategoriyasi bo'lsa, matn maydonini ko'rsatish
            if (categoryName.includes('reading') || categoryName.includes('o\'qish')) {
                readingPassageContainer.style.display = 'block';
            } else {
                readingPassageContainer.style.display = 'none';
            }
        });
        
        // Sahifa yuklanganda ham tekshirish
        if (categorySelect.selectedIndex > 0) {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption.textContent.trim().toLowerCase();
            
            if (categoryName.includes('reading') || categoryName.includes('o\'qish')) {
                readingPassageContainer.style.display = 'block';
            }
        }
    });
</script>
@endpush
@endsection
