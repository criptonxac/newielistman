@extends($layout)

@section('title', 'Listening Test - ' . $listeningTest->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Listening Test Ma'lumotlari</h1>
            <div class="flex space-x-2">
                <a href="{{ route('listening-tests.items.index', $listeningTest) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                    <i class="fas fa-list mr-2"></i>Items
                </a>
                <a href="{{ route('listening-tests.edit', $listeningTest) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Tahrirlash
                </a>
                <a href="{{ route('listening-tests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                    Orqaga
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Asosiy ma'lumotlar -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Asosiy ma'lumotlar</h3>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">ID:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $listeningTest->id }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Sarlavha:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $listeningTest->title }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">AppTest:</span>
                        <span class="ml-2 text-sm text-gray-900">
                            {{ $listeningTest->appTest->title ?? 'N/A' }}
                            @if($listeningTest->appTest)
                                <span class="text-xs text-gray-500">({{ ucfirst($listeningTest->appTest->type) }})</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Audio ma'lumotlari -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Audio ma'lumotlari</h3>
                
                @php
                    $audioData = $listeningTest->audio ?? [];
                @endphp
                
                <div class="space-y-3">
                    @if(isset($audioData['title']) && $audioData['title'])
                    <div>
                        <span class="text-sm font-medium text-gray-500">Audio Title:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $audioData['title'] }}</span>
                    </div>
                    @endif
                    
                    @if(isset($audioData['url']) && $audioData['url'])
                    <div>
                        <span class="text-sm font-medium text-gray-500">Audio Player:</span>
                        <div class="ml-2 mt-2">
                            <audio controls class="w-full max-w-md">
                                <source src="{{ $audioData['url'] }}" type="audio/mpeg">
                                <source src="{{ $audioData['url'] }}" type="audio/wav">
                                <source src="{{ $audioData['url'] }}" type="audio/ogg">
                                Brauzeringiz audio elementini qo'llab-quvvatlamaydi.
                            </audio>
                            <p class="text-xs text-gray-500 mt-1">
                                <a href="{{ $audioData['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    {{ $audioData['url'] }}
                                </a>
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    @if(isset($audioData['description']) && $audioData['description'])
                    <div>
                        <span class="text-sm font-medium text-gray-500">Description:</span>
                        <div class="ml-2 text-sm text-gray-900 mt-1">
                            {{ $audioData['description'] }}
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Audio Player -->
                @if(isset($audioData['url']) && $audioData['url'])
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-500 block mb-2">Audio Player:</span>
                    <audio controls class="w-full">
                        <source src="{{ $audioData['url'] }}" type="audio/mpeg">
                        <source src="{{ $audioData['url'] }}" type="audio/wav">
                        <source src="{{ $audioData['url'] }}" type="audio/ogg">
                        Brauzeringiz audio elementini qo'llab-quvvatlamaydi.
                    </audio>
                </div>
                @endif
            </div>

            @if($listeningTest->created_at || $listeningTest->updated_at)
            <!-- Vaqt ma'lumotlari -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Vaqt ma'lumotlari</h3>
                
                <div class="space-y-3">
                    @if($listeningTest->created_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yaratilgan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $listeningTest->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($listeningTest->updated_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yangilangan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $listeningTest->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- JSON ma'lumotlari (Debug uchun) -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">JSON ma'lumotlari</h3>
                <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto">{{ json_encode($listeningTest->audio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('listening-tests.edit', $listeningTest) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded">
                <i class="fas fa-edit mr-2"></i>Tahrirlash
            </a>
            
            <form action="{{ route('listening-tests.destroy', $listeningTest) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-6 rounded">
                    <i class="fas fa-trash mr-2"></i>O'chirish
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
