@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">AppTest ma'lumotlari</h1>
        <div class="flex space-x-2">
            <a href="{{ route('tests.app-tests.edit', $appTest) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                Tahrirlash
            </a>
            <a href="{{ route('tests.app-tests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
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
                        <span class="ml-2 text-sm text-gray-900">{{ $appTest->id }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Test nomi:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $appTest->title }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Test turi:</span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($appTest->type == 'listening') bg-blue-100 text-blue-800
                            @elseif($appTest->type == 'reading') bg-green-100 text-green-800
                            @elseif($appTest->type == 'writing') bg-yellow-100 text-yellow-800
                            @elseif($appTest->type == 'speaking') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($appTest->type) }}
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Test vaqti:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $appTest->test_time }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $appTest->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $appTest->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Vaqt ma'lumotlari</h3>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yaratilgan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $appTest->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yangilangan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $appTest->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Test tavsifi</h3>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $appTest->desc }}</p>
        </div>
    </div>

    <!-- Reading Tests bo'limi -->
    @if($appTest->type == 'reading')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Reading Testlar ({{ $appTest->readingTests->count() }})</h2>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yaratilgan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($appTest->readingTests as $readingTest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $readingTest->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $readingTest->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $readingTest->items->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $readingTest->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('reading-tests.show', $readingTest) }}" class="text-blue-600 hover:text-blue-900">Ko'rish</a>
                                <a href="{{ route('reading-tests.edit', $readingTest) }}" class="text-indigo-600 hover:text-indigo-900">Tahrirlash</a>
                                <a href="{{ route('reading-tests.items.index', $readingTest) }}" class="text-green-600 hover:text-green-900">Itemlar</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Reading testlar yo'q</h3>
            <p class="mt-1 text-sm text-gray-500">Yangi reading test qo'shish uchun yuqoridagi tugmani bosing.</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Amallar bo'limi -->
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Amallar</h3>
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('tests.app-tests.toggle-status', $appTest) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                    {{ $appTest->is_active ? 'Nofaol qilish' : 'Faol qilish' }}
                </button>
            </form>
            
            <form action="{{ route('tests.app-tests.destroy', $appTest) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz? Bu amal qaytarilmaydi!')">
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
