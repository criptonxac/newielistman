@extends('layouts.main')

@section('title', 'Testlar - Talaba Panel')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mavjud Testlar</h1>
            <p class="mt-2 text-gray-600">IELTS familiarisation testlarini tanlab boshlang</p>
        </div>

        @foreach($categories as $category)
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ $category->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $category->description }}</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($category->tests as $test)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $test->title }}</h3>
                        <p class="text-gray-600 text-sm mb-3">{{ $test->description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">{{ $test->questions_count ?? 0 }} savol</span>
                            <a href="{{ route('tests.show', $test->slug) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition-colors">
                                Testni boshlash
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection