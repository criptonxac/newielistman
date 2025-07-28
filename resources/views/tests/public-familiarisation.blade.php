@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ $pageTitle }}</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tests as $test)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-2">{{ $test->title }}</h2>
                    <p class="text-gray-600 mb-4">{{ $test->description }}</p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kategoriya:</span>
                            <span class="font-medium">{{ $category }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Savollar soni:</span>
                            <span class="font-medium">{{ $test->questions_count ?? $test->questions->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Davomiyligi:</span>
                            <span class="font-medium">{{ $test->duration }} daqiqa</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Qiyinlik:</span>
                            <span class="font-medium">{{ $test->difficulty ?? 'Beginner' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Holati:</span>
                            <span class="font-medium">{{ $test->status ? $test->status->label() : 'Cheklanmagan' }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('tests.show', $test) }}" class="inline-block w-full py-2 px-4 bg-blue-600 text-white font-medium text-center rounded hover:bg-blue-700 transition">
                            Testni ko'rish
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-8 text-center">
                <p class="text-gray-500 text-lg">Hozircha testlar mavjud emas.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
