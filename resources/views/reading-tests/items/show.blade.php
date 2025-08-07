@extends('layouts.teacher')

@section('title', 'Reading Test Item - ' . $item->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $item->title }}</h1>
                    <p class="text-gray-600 mt-1">{{ $readingTest->title }} - Item tafsilotlari</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reading-tests.items.index', $readingTest) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Orqaga
                    </a>
                    <a href="{{ route('reading-tests.items.edit', [$readingTest, $item]) }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Item Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Asosiy Ma'lumotlar</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item ID</label>
                            <p class="text-gray-900">{{ $item->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomi</label>
                            <p class="text-gray-900">{{ $item->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Turi</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($item->type == 'passage') bg-blue-100 text-blue-800
                                @elseif($item->type == 'question') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($item->type) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Reading Test Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Ma'lumotlari</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Test Nomi</label>
                            <p class="text-gray-900">{{ $readingTest->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Test ID</label>
                            <p class="text-gray-900">{{ $readingTest->id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Display -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Content Ma'lumotlari</h3>
            
            @if(is_array($item->body))
                <!-- Content Title -->
                @if(!empty($item->body['title']))
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content Title</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-md p-3">
                            <h4 class="text-lg font-medium text-gray-900">{{ $item->body['title'] }}</h4>
                        </div>
                    </div>
                @endif

                <!-- Content Image -->
                @if(!empty($item->body['image_url']))
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content Image</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-md p-3">
                            <img src="{{ $item->body['image_url'] }}" 
                                 alt="Content Image" 
                                 class="max-w-full h-auto rounded-md"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div style="display: none;" class="text-gray-500 text-center py-8">
                                <i class="fas fa-image text-2xl mb-2"></i>
                                <p>Rasm yuklanmadi</p>
                                <p class="text-sm">URL: {{ $item->body['image_url'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Content Body -->
                @if(!empty($item->body['body']))
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content Body</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-md p-4">
                            <div class="prose max-w-none">
                                {!! nl2br(e($item->body['body'])) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Raw JSON -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raw JSON Data</label>
                    <div class="bg-gray-900 text-green-400 rounded-md p-4 overflow-x-auto">
                        <pre class="text-sm">{{ json_encode($item->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Content ma'lumotlari mavjud emas yoki noto'g'ri formatda</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('reading-tests.items.index', $readingTest) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    Items ro'yxati
                </a>
                <a href="{{ route('reading-tests.items.edit', [$readingTest, $item]) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Tahrirlash
                </a>
                <form action="{{ route('reading-tests.items.destroy', [$readingTest, $item]) }}" 
                      method="POST" 
                      class="inline-block"
                      onsubmit="return confirm('Bu itemni o\'chirishga ishonchingiz komilmi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>O'chirish
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
