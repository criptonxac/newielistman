@extends('layouts.teacher')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Test boshqaruvi</h1>
        <div class="flex space-x-2">
            <a href="{{ route('test-management.enums') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded">
                Enumlar jadvali
            </a>
            <a href="{{ route('test-management.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                Yangi test qo'shish
            </a>
        </div>
    </div>
    
    <div class="flex space-x-2 mb-4">
        <a href="{{ route('test-management.index') }}" class="px-4 py-2 rounded {{ !request()->has('type') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Barchasi
        </a>
        <a href="{{ route('test-management.index', ['type' => 'familiarisation']) }}" class="px-4 py-2 rounded {{ request('type') === 'familiarisation' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Tanishuv
        </a>
        <a href="{{ route('test-management.index', ['type' => 'sample']) }}" class="px-4 py-2 rounded {{ request('type') === 'sample' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Namuna
        </a>
        <a href="{{ route('test-management.index', ['type' => 'practice']) }}" class="px-4 py-2 rounded {{ request('type') === 'practice' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Amaliyot
        </a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test nomi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoriya</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Savollar soni</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faollik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tests as $test)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $test->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $test->category->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">
                            {{ $test->type ? $test->type->label() : 'Belgilanmagan' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $test->total_questions }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($test->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Faol
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Faol emas
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($test->status)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->status->value === 'completed' ? 'bg-green-100 text-green-800' : ($test->status->value === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $test->status->label() }}
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Belgilanmagan
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('test-management.edit', $test) }}" class="text-indigo-600 hover:text-indigo-900">Tahrirlash</a>
                            
                            @if($test->total_questions > 0)
                                <a href="{{ route('test-management.questions.edit', $test) }}" class="text-blue-600 hover:text-blue-900">Savollarni tahrirlash</a>
                            @else
                                <a href="{{ route('test-management.questions.create', $test) }}" class="text-green-600 hover:text-green-900">Savollar qo'shish</a>
                            @endif
                            
                            <form action="{{ route('test-management.destroy', $test) }}" method="POST" onsubmit="return confirm('Haqiqatan ham bu testni o\'chirmoqchimisiz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">O'chirish</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Hech qanday test topilmadi. Yangi test qo'shish uchun yuqoridagi tugmani bosing.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
