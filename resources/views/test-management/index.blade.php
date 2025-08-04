@extends($layout)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800">Test boshqaruvi</h1>
        <div class="flex space-x-2">
            <a href="{{ route('test-management.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Yangi test
            </a>
        </div>
    </div>
    
    <!-- Filtir va qidiruv paneli -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('test-management.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Qidiruv maydoni -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Qidirish</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $search }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Test nomi yoki tavsifi">
                    </div>
                </div>
                
                <!-- Kategoriya filtri -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategoriya</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Barcha kategoriyalar</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Holat filtri -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Holati</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Barchasi</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Nofaol</option>
                    </select>
                </div>
                
                <!-- Tartiblash -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Tartiblash</label>
                    <select id="sort" name="sort" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Yangi -> Eski</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Eski -> Yangi</option>
                        <option value="title_asc" {{ $sort === 'title_asc' ? 'selected' : '' }}>Nomi (A-Z)</option>
                        <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Nomi (Z-A)</option>
                        <option value="questions_asc" {{ $sort === 'questions_asc' ? 'selected' : '' }}>Savollar soni (Kamayish bo'yicha)</option>
                        <option value="questions_desc" {{ $sort === 'questions_desc' ? 'selected' : '' }}>Savollar soni (O'sish bo'yicha)</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <div class="flex items-center
                    <select id="per_page" name="per_page" onchange="this.form.submit()" class="mt-1 block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 ta</option>
                        <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30 ta</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 ta</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 ta</option>
                    </select>
                    <span class="ml-2 text-sm text-gray-500">har sahifada</span>
                </div>
                
                <div class="space-x-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Filtrlash
                    </button>
                    <a href="{{ route('test-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Tozalash
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">Test nomi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Kategoriya</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Turi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Savollar</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Holati</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Amallar</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tests as $test)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('test-management.edit', $test->id) }}" class="hover:text-blue-600 hover:underline">
                                        {{ $test->title }}
                                    </a>
                                </div>
                                @if($test->description)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ Str::limit($test->description, 80) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        @if($test->category)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $test->category->name }}
                            </span>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @php
                            $typeClass = [
                                'familiarisation' => 'bg-purple-100 text-purple-800',
                                'practice' => 'bg-yellow-100 text-yellow-800',
                                'mock' => 'bg-indigo-100 text-indigo-800',
                                'real' => 'bg-red-100 text-red-800',
                                'default' => 'bg-gray-100 text-gray-800'
                            ];
                            $typeText = [
                                'familiarisation' => 'Tanishuv',
                                'practice' => 'Amaliyot',
                                'mock' => 'Namuna',
                                'real' => 'Haqiqiy'
                            ];
                            $type = is_object($test->type) ? $test->type->value : $test->type;
                        @endphp
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClass[$type] ?? $typeClass['default'] }}">
                            {{ $typeText[$type] ?? $type }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->questions_count > 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $test->questions_count }} ta
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $test->is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($test->status)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ is_object($test->status) ? ($test->status->value === 'completed' ? 'bg-green-100 text-green-800' : ($test->status->value === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) : 'bg-gray-100 text-gray-800' }}">
                                {{ is_object($test->status) ? $test->status->label() : ($test->status ?: '-') }}
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Belgilanmagan
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <!-- Tahrirlash -->
                            <a href="{{ route('test-management.edit', $test->id) }}" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 p-2 rounded-full transition-colors" title="Tahrirlash">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            <!-- Savollar -->
                            <a href="{{ route('test-management.questions.create', $test->id) }}" class="text-green-600 hover:text-green-900 hover:bg-green-50 p-2 rounded-full transition-colors relative" title="Savollar ({{ $test->questions_count }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </a>
                            
                            <!-- Ko'rish -->
                            <a href="{{ route('test-management.preview', $test->id) }}" class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 p-2 rounded-full transition-colors" target="_blank" title="Ko'rish">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <!-- O'chirish -->
                            <form action="{{ route('test-management.destroy', $test->id) }}" method="POST" class="inline" onsubmit="return confirm('Ishonchingiz komilmi? Bu testni o\'chirish kaskadli tarzda barcha bog\'liq savollarni ham o\'chiradi.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded-full transition-colors" title="O'chirish">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-lg font-medium text-gray-600 mb-2">Testlar topilmadi</p>
                            <p class="text-sm text-gray-500 max-w-md mx-auto mb-4">
                                @if(request()->has('search') || request()->has('category') || request()->has('status'))
                                    Sizning filtrlaringizga mos testlar topilmadi. Boshqa filtrlarni sinab ko'ring yoki filtrlarni tozalang.
                                @else
                                    Hozircha hech qanday test mavjud emas. Yangi test qo'shish uchun pastdagi tugmani bosing.
                                @endif
                            </p>
                            <div class="space-x-3">
                                @if(request()->has('search') || request()->has('category') || request()->has('status'))
                                    <a href="{{ route('test-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                        </svg>
                                        Filtrlarni tozalash
                                    </a>
                                @endif
                                <a href="{{ route('test-management.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 01-1 1h-3a1 1 0 110-2h3V9a1 1 0 011-1v-3a1 1 0 110-2h-3a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Yangi test qo'shish
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($tests->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">{{ $tests->firstItem() }}</span>
                            dan
                            <span class="font-medium">{{ $tests->lastItem() }}</span>
                            gacha, jami
                            <span class="font-medium">{{ $tests->total() }}</span> ta test
                        </p>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        {{ $tests->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Custom styles for better UI */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Smooth scrolling for pagination */
    html {
        scroll-behavior: smooth;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pagination styling
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.classList.add('px-3', 'py-1', 'rounded', 'border', 'text-sm', 'font-medium', 'transition-colors', 'duration-200');
            
            if (link.getAttribute('href') === null) {
                // Current page
                link.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'cursor-default');
            } else {
                // Other pages
                link.classList.add('border-gray-300', 'text-gray-700', 'hover:bg-gray-50', 'hover:border-gray-400');
            }
            
            // Disabled state
            if (link.classList.contains('disabled')) {
                link.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
        
        // Auto-submit filter form when select elements change
        const filterForm = document.querySelector('form[method="GET"]');
        if (filterForm) {
            const selectElements = filterForm.querySelectorAll('select:not([onchange])');
            selectElements.forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
        
        // Tooltips for action buttons
        const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
        tooltipTriggers.forEach(trigger => {
            const tooltip = document.createElement('div');
            tooltip.classList.add('hidden', 'absolute', 'z-10', 'py-1', 'px-2', 'text-xs', 'text-white', 'bg-gray-900', 'rounded', 'whitespace-nowrap', 'mt-1');
            tooltip.textContent = trigger.getAttribute('data-tooltip');
            
            trigger.parentNode.style.position = 'relative';
            trigger.parentNode.appendChild(tooltip);
            
            trigger.addEventListener('mouseenter', () => {
                tooltip.classList.remove('hidden');
                // Position the tooltip
                const rect = trigger.getBoundingClientRect();
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translateX(-50%)';
                tooltip.style.bottom = '100%';
            });
            
            trigger.addEventListener('mouseleave', () => {
                tooltip.classList.add('hidden');
            });
        });
    });
</script>
@endpush
@endsection
