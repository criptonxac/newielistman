@extends($layout)

@section('title', 'Listening Test boshqaruvi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Listening Test boshqaruvi</h1>
        <a href="{{ route('listening-tests.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Yangi Listening Test
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('listening-tests.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Qidirish</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Listening test nomi">
            </div>
            
            <div class="min-w-48">
                <label for="app_test_id" class="block text-sm font-medium text-gray-700 mb-1">AppTest</label>
                <select name="app_test_id" id="app_test_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Barcha AppTestlar</option>
                    @foreach($appTests as $appTest)
                        <option value="{{ $appTest->id }}" {{ request('app_test_id') == $appTest->id ? 'selected' : '' }}>
                            {{ $appTest->title }} ({{ ucfirst($appTest->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Qidirish
                </button>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sarlavha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AppTest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yaratilgan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($listeningTests as $listeningTest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $listeningTest->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $listeningTest->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $listeningTest->appTest->title ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($listeningTest->appTest->type ?? '') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $listeningTest->created_at ? $listeningTest->created_at->format('d.m.Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3">
                                <!-- Ko'rish tugmasi -->
                                <a href="{{ route('listening-tests.show', $listeningTest) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" 
                                   title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Items tugmasi -->
                                <a href="{{ route('listening-tests.items.index', $listeningTest) }}" 
                                   class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50" 
                                   title="Items">
                                    <i class="fas fa-list"></i>
                                </a>
                                
                                <!-- Tahrirlash tugmasi -->
                                <a href="{{ route('listening-tests.edit', $listeningTest) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50" 
                                   title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- O'chirish tugmasi -->
                                <form action="{{ route('listening-tests.destroy', $listeningTest) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
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
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Hech qanday listening test topilmadi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($listeningTests->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $listeningTests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
