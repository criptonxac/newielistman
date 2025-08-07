@extends('layouts.teacher')

@section('title', 'Listening Test Items - ' . $listeningTest->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Listening Test Items</h1>
            <p class="text-gray-600 mt-1">{{ $listeningTest->title }} uchun itemlar</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('listening-tests.show', $listeningTest) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Orqaga
            </a>
            <a href="{{ route('listening-tests.items.create', $listeningTest) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Yangi Item
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Items Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($items->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Body</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yaratilgan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ Str::limit($item->title, 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($item->type == 'audio') bg-blue-100 text-blue-800
                                    @elseif($item->type == 'question') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($item->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if(is_array($item->body))
                                    <div class="max-w-xs">
                                        @if(isset($item->body['title']))
                                            <strong>{{ Str::limit($item->body['title'], 20) }}</strong><br>
                                        @endif
                                        @if(isset($item->body['content']))
                                            {{ Str::limit($item->body['content'], 30) }}
                                        @endif
                                    </div>
                                @else
                                    {{ Str::limit($item->body, 50) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('listening-tests.items.show', [$listeningTest, $item]) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('listening-tests.items.edit', [$listeningTest, $item]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('listening-tests.items.destroy', [$listeningTest, $item]) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Hech qanday item topilmadi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <div class="text-center py-8">
                <i class="fas fa-list text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Hech qanday item yo'q</h3>
                <p class="text-gray-500 mb-4">Bu listening test uchun hali hech qanday item yaratilmagan.</p>
                <a href="{{ route('listening-tests.items.create', $listeningTest) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Birinchi Itemni Yaratish
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
