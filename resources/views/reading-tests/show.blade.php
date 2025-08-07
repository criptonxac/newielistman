@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reading Test ma'lumotlari</h1>
        <div class="flex space-x-2">
            <a href="{{ route('reading-tests.edit', $readingTest) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                Tahrirlash
            </a>
            <a href="{{ route('reading-tests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                Orqaga
            </a>
        </div>
    </div>

    <!-- Asosiy ma'lumotlar -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Asosiy ma'lumotlar</h3>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">ID:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $readingTest->id }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Sarlavha:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $readingTest->title }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">AppTest:</span>
                        <span class="ml-2 text-sm text-gray-900">
                            @if($readingTest->appTest)
                                <a href="{{ route('tests.app-tests.show', $readingTest->appTest) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $readingTest->appTest->title }}
                                </a>
                                <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($readingTest->appTest->type == 'reading') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($readingTest->appTest->type) }}
                                </span>
                            @else
                                <span class="text-red-500">AppTest topilmadi</span>
                            @endif
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Itemlar soni:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $readingTest->items->count() }}</span>
                    </div>
                </div>
            </div>
            
            @if($readingTest->created_at || $readingTest->updated_at)
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Vaqt ma'lumotlari</h3>
                
                <div class="space-y-3">
                    @if($readingTest->created_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yaratilgan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $readingTest->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($readingTest->updated_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yangilangan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $readingTest->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Body ma'lumotlari -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Body ma'lumotlari</h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <pre class="text-sm text-gray-700 whitespace-pre-wrap overflow-x-auto">{{ json_encode($readingTest->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <!-- Items bo'limi -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Reading Test Itemlar ({{ $readingTest->items->count() }})</h2>
            <a href="{{ route('reading-tests.items.create', $readingTest) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                Item qo'shish
            </a>
        </div>
        
        @if($readingTest->items->count() > 0)
        <div class="space-y-4">
            @foreach($readingTest->items->sortBy('order') as $item)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($item->type == 'passage') bg-blue-100 text-blue-800
                            @elseif($item->type == 'question') bg-green-100 text-green-800
                            @elseif($item->type == 'instruction') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($item->type) }}
                        </span>
                        <span class="text-sm text-gray-500">Tartib: {{ $item->order }}</span>
                        <span class="text-sm text-gray-500">ID: {{ $item->id }}</span>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('reading-tests.items.show', [$readingTest, $item]) }}" class="text-blue-600 hover:text-blue-900 text-sm">Ko'rish</a>
                        <a href="{{ route('reading-tests.items.edit', [$readingTest, $item]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Tahrirlash</a>
                        <form action="{{ route('reading-tests.items.destroy', [$readingTest, $item]) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">O'chirish</button>
                        </form>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded p-3">
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($item->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Itemlar yo'q</h3>
            <p class="mt-1 text-sm text-gray-500">Yangi item qo'shish uchun yuqoridagi tugmani bosing.</p>
        </div>
        @endif
    </div>

    <!-- Amallar bo'limi -->
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Amallar</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('reading-tests.items.index', $readingTest) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                Itemlarni boshqarish
            </a>
            
            <form action="{{ route('reading-tests.destroy', $readingTest) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz? Bu amal qaytarilmaydi!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">
                    O'chirish
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
