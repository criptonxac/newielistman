@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ $pageTitle }}</h1>
    
    <!-- Test turlari bo'yicha tab -->
    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            @foreach($types as $typeItem)
                <li class="mr-2">
                    <a href="{{ route('tests.by-type', $typeItem->value) }}" 
                       class="inline-block p-4 {{ $currentType == $typeItem->value ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-600 hover:border-gray-300' }}">
                        {{ $typeItem->label() }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    
    <!-- Testlar jadvali -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomi</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tavsif</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoriya</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Savollar</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Davomiyligi</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holati</th>
                    <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tests as $test)
                    <tr>
                        <td class="py-3 px-4 text-sm">{{ $test->id }}</td>
                        <td class="py-3 px-4 text-sm font-medium">{{ $test->title }}</td>
                        <td class="py-3 px-4 text-sm">{{ $test->description }}</td>
                        <td class="py-3 px-4 text-sm">{{ $test->category->name }}</td>
                        <td class="py-3 px-4 text-sm">{{ $test->questions_count ?? $test->questions->count() }}</td>
                        <td class="py-3 px-4 text-sm">{{ $test->duration }} daqiqa</td>
                        <td class="py-3 px-4 text-sm">
                            @if($test->status)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $test->status->value == 'completed' ? 'bg-green-100 text-green-800' : 
                                      ($test->status->value == 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $test->status->label() }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Cheklanmagan
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm">
                            <a href="{{ route('student.tests.show', $test) }}" class="text-blue-600 hover:text-blue-900">Ko'rish</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 px-4 text-center text-gray-500">
                            Bu turdagi testlar topilmadi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Enum ma'lumotlari -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Enum qiymatlari</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Test turlari -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="py-3 px-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Test turlari</h3>
                </div>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qiymat</th>
                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ko'rinishi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($types as $typeItem)
                            <tr>
                                <td class="py-3 px-4 text-sm">{{ $typeItem->value }}</td>
                                <td class="py-3 px-4 text-sm">{{ $typeItem->label() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Test holatlari -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="py-3 px-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Test holatlari</h3>
                </div>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qiymat</th>
                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ko'rinishi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($statuses as $statusItem)
                            <tr>
                                <td class="py-3 px-4 text-sm">{{ $statusItem->value }}</td>
                                <td class="py-3 px-4 text-sm">{{ $statusItem->label() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
