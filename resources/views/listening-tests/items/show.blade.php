@extends($layout)

@section('title', 'Listening Test Item - ' . $item->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Item Ma'lumotlari</h1>
                <p class="text-gray-600 mt-1">{{ $listeningTest->title }} - {{ $item->title }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('listening-tests.items.edit', [$listeningTest, $item]) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Tahrirlash
                </a>
                <a href="{{ route('listening-tests.items.index', $listeningTest) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
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
                        <span class="ml-2 text-sm text-gray-900">{{ $item->id }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Sarlavha:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $item->title }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Turi:</span>
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($item->type == 'audio') bg-blue-100 text-blue-800
                            @elseif($item->type == 'question') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($item->type) }}
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Listening Test:</span>
                        <div class="ml-2 text-sm text-gray-900">
                            <a href="{{ route('listening-tests.show', $listeningTest) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                {{ $listeningTest->title }}
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yaratilgan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $item->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Yangilangan:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $item->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Body ma'lumotlari -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Body Ma'lumotlari</h3>
                
                @php
                    $bodyData = is_array($item->body) ? $item->body : json_decode($item->body, true);
                @endphp
                
                @if($bodyData && is_array($bodyData))
                    <div class="space-y-3">
                        @if(isset($bodyData['title']) && $bodyData['title'])
                        <div>
                            <span class="text-sm font-medium text-gray-500">Item Title:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $bodyData['title'] }}</span>
                        </div>
                        @endif
                        
                        @if(isset($bodyData['content']) && $bodyData['content'])
                        <div>
                            <span class="text-sm font-medium text-gray-500">Content:</span>
                            <div class="ml-2 text-sm text-gray-900 mt-1">
                                <div class="bg-gray-50 p-3 rounded">
                                    {{ $bodyData['content'] }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($bodyData['options']) && $bodyData['options'])
                        <div>
                            <span class="text-sm font-medium text-gray-500">Options:</span>
                            <div class="ml-2 text-sm text-gray-900 mt-1">
                                <div class="bg-gray-50 p-3 rounded">
                                    <pre class="text-xs">{{ $bodyData['options'] }}</pre>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Body ma'lumotlari mavjud emas yoki noto'g'ri formatda.</p>
                @endif
            </div>
        </div>

        <!-- JSON Debug -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">JSON Debug</h3>
            <div class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto">
                <pre class="text-sm">{{ json_encode($item->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center mt-6">
            <div class="flex space-x-2">
                <a href="{{ route('listening-tests.items.edit', [$listeningTest, $item]) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Tahrirlash
                </a>
                
                <form action="{{ route('listening-tests.items.destroy', [$listeningTest, $item]) }}" 
                      method="POST" class="inline" 
                      onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>O'chirish
                    </button>
                </form>
            </div>
            
            <a href="{{ route('listening-tests.items.index', $listeningTest) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-list mr-2"></i>Barcha Items
            </a>
        </div>
    </div>
</div>
@endsection
