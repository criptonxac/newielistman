@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">AppTest tahrirlash</h1>
        <div class="flex space-x-2">
            <a href="{{ route('tests.app-tests.show', $appTest) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                Ko'rish
            </a>
            <a href="{{ route('tests.app-tests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
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
        <form action="{{ route('tests.app-tests.update', $appTest) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Test nomi:</label>
                <input type="text" name="title" id="title" value="{{ old('title', $appTest->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-4">
                <label for="desc" class="block text-gray-700 text-sm font-bold mb-2">Test tavsifi:</label>
                <textarea name="desc" id="desc" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('desc', $appTest->desc) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Test turi:</label>
                <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Test turini tanlang</option>
                    <option value="listening" {{ old('type', $appTest->type) == 'listening' ? 'selected' : '' }}>Listening</option>
                    <option value="reading" {{ old('type', $appTest->type) == 'reading' ? 'selected' : '' }}>Reading</option>
                    <option value="writing" {{ old('type', $appTest->type) == 'writing' ? 'selected' : '' }}>Writing</option>
                    <option value="speaking" {{ old('type', $appTest->type) == 'speaking' ? 'selected' : '' }}>Speaking</option>
                    <option value="familiarisation" {{ old('type', $appTest->type) == 'familiarisation' ? 'selected' : '' }}>Familiarisation</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="test_time" class="block text-gray-700 text-sm font-bold mb-2">Test vaqti:</label>
                <input type="text" name="test_time" id="test_time" value="{{ old('test_time', $appTest->test_time) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masalan: 60 daqiqa" required>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $appTest->is_active) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700 text-sm font-bold">Faol</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Yangilash
                </button>
                <a href="{{ route('tests.app-tests.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>

    <!-- ReadingTest bo'limi -->
    @if($appTest->type == 'reading')
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Reading Testlar</h2>
            <a href="{{ route('reading-tests.create') }}?app_test_id={{ $appTest->id }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                Reading Test qo'shish
            </a>
        </div>
        
        @if($appTest->readingTests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sarlavha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itemlar soni</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($appTest->readingTests as $readingTest)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $readingTest->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $readingTest->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $readingTest->items->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('reading-tests.show', $readingTest) }}" class="text-blue-600 hover:text-blue-900">Ko'rish</a>
                                <a href="{{ route('reading-tests.edit', $readingTest) }}" class="text-indigo-600 hover:text-indigo-900">Tahrirlash</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Hali reading testlar qo'shilmagan</p>
        @endif
    </div>
    @endif
</div>
@endsection
