@extends('layouts.teacher')

@section('title', 'Reading Test Items')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reading Test Items</h1>
                    <p class="text-gray-600 mt-1">{{ $readingTest->title }} - Items boshqaruvi</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reading-tests.show', $readingTest) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Test ga qaytish
                    </a>
                    <a href="{{ route('reading-tests.items.create', $readingTest) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Yangi Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Items List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Turi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Content Preview
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amallar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($item->type == 'passage') bg-blue-100 text-blue-800
                                            @elseif($item->type == 'question') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            @if(is_array($item->body) && isset($item->body['title']))
                                                <strong>{{ $item->body['title'] }}</strong>
                                            @endif
                                            @if(is_array($item->body) && isset($item->body['body']))
                                                <br><span class="text-gray-600">{{ Str::limit($item->body['body'], 100) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('reading-tests.items.show', [$readingTest, $item]) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('reading-tests.items.edit', [$readingTest, $item]) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 transition duration-200">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('reading-tests.items.destroy', [$readingTest, $item]) }}" 
                                                  method="POST" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('Bu itemni o\'chirishga ishonchingiz komilmi?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition duration-200">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($items->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200">
                        {{ $items->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hech qanday item topilmadi</h3>
                    <p class="text-gray-600 mb-4">Bu test uchun hali item yaratilmagan.</p>
                    <a href="{{ route('reading-tests.items.create', $readingTest) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Birinchi Item yaratish
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
