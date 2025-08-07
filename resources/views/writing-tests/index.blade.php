@extends($layout)

@section('title', 'Writing Test Boshqaruvi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Writing Test Boshqaruvi</h1>
                    <p class="text-gray-600 mt-1">Writing testlarini yaratish va boshqarish</p>
                </div>
                <a href="{{ route('writing-tests.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Yangi Writing Test
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('writing-tests.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Test nomi bo'yicha qidirish..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="min-w-48">
                    <select name="app_test_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Barcha App Testlar</option>
                        @foreach($appTests as $appTest)
                            <option value="{{ $appTest->id }}" {{ request('app_test_id') == $appTest->id ? 'selected' : '' }}>
                                {{ $appTest->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Qidirish
                </button>
                @if(request()->hasAny(['search', 'app_test_id']))
                    <a href="{{ route('writing-tests.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-2"></i>Tozalash
                    </a>
                @endif
            </form>
        </div>

        <!-- Writing Tests Table -->
        <div class="overflow-x-auto">
            @if($writingTests->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Nomi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App Test</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yaratilgan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($writingTests as $writingTest)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $writingTest->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $writingTest->title }}</div>
                                    <div class="text-sm text-gray-500">
                                        Questions: {{ is_array($writingTest->formatted_questions) ? count($writingTest->formatted_questions) : 0 }} ta
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $writingTest->appTest->title ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $writingTest->app_test_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $writingTest->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('writing-tests.show', $writingTest) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Ko'rish">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('writing-tests.edit', $writingTest) }}" 
                                           class="text-yellow-600 hover:text-yellow-900" title="Tahrirlash">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('writing-tests.destroy', $writingTest) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Haqiqatan ham bu writing testni o\'chirmoqchimisiz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="O'chirish">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-pen-fancy text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hech qanday writing test topilmadi</h3>
                    <p class="text-gray-500 mb-4">Birinchi writing testingizni yarating</p>
                    <a href="{{ route('writing-tests.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Yangi Writing Test
                    </a>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($writingTests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $writingTests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="success-message">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('success-message').remove();
        }, 5000);
    </script>
@endif
@endsection
