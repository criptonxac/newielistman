@extends('layouts.teacher')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800">Reading Test boshqaruvi</h1>
        <div class="flex space-x-2">
            <a href="{{ route('reading-tests.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Yangi Reading Test
            </a>
        </div>
    </div>
    
    <!-- Filtir va qidiruv paneli -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('reading-tests.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Qidiruv maydoni -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Qidirish</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Reading test nomi">
                    </div>
                </div>

                <!-- AppTest filter -->
                <div>
                    <label for="app_test_id" class="block text-sm font-medium text-gray-700 mb-1">AppTest</label>
                    <select name="app_test_id" id="app_test_id" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Barcha AppTestlar</option>
                        @foreach($appTests as $appTest)
                            <option value="{{ $appTest->id }}" {{ request('app_test_id') == $appTest->id ? 'selected' : '' }}>
                                {{ $appTest->title }} ({{ ucfirst($appTest->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Qidiruv tugmasi -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                        Qidirish
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Reading Testlar jadvali -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sarlavha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AppTest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itemlar soni</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yaratilgan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($readingTests as $readingTest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $readingTest->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $readingTest->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $readingTest->appTest->title ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($readingTest->appTest->type ?? '') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $readingTest->items->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $readingTest->created_at ? $readingTest->created_at->format('d.m.Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3">
                                <!-- Ko'rish tugmasi -->
                                <a href="{{ route('reading-tests.show', $readingTest) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" 
                                   title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Tahrirlash tugmasi -->
                                <a href="{{ route('reading-tests.edit', $readingTest) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50" 
                                   title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Itemlar tugmasi -->
                                <a href="{{ route('reading-tests.items.index', $readingTest) }}" 
                                   class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50" 
                                   title="Itemlar">
                                    <i class="fas fa-list"></i>
                                </a>
                                
                                <!-- O'chirish tugmasi -->
                                <form action="{{ route('reading-tests.destroy', $readingTest) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" 
                                            title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Hech qanday reading test topilmadi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($readingTests->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $readingTests->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
