@extends($layout)

@section('title', 'Test Preview - ' . $test->title)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $test->title }}</h1>
                <p class="text-gray-600 mt-1">Test ko'rish va tekshirish</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('test-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Orqaga
            </a>
            <a href="{{ route('test-management.edit', $test->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Tahrirlash
            </a>
        </div>
    </div>

    <!-- Test Information Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Test ma'lumotlari
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Kategoriya</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $test->category->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Savollar soni</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $test->questions->count() }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Vaqt chegarasi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $test->time_limit ?? 'N/A' }} daqiqa</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Urinishlar soni</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $test->attempts_allowed ?? 1 }}</p>
                    </div>
                </div>
            </div>

            @if($test->description)
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Tavsif</h3>
                <p class="text-gray-700">{{ $test->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Reading Passage (if exists) -->
    @if($test->category && (str_contains(strtolower($test->category->name), 'reading') || str_contains(strtolower($test->category->name), 'o\'qish')) && $test->passage)
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Reading Passage
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="prose max-w-none">
                    {!! nl2br(e($test->passage)) !!}
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Questions Preview -->
    @if($test->questions->count() > 0)
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Savollar ({{ $test->questions->count() }} ta)
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @foreach($test->questions->take(5) as $question)
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Savol {{ $question->question_number_in_part ?? $loop->iteration }}
                            @if($question->part_number)
                                (Part {{ $question->part_number }})
                            @endif
                        </h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            {{ ucfirst($question->type) }}
                        </span>
                    </div>
                    <div class="prose max-w-none">
                        {!! $question->question_text !!}
                    </div>
                    @if($question->options)
                        <div class="mt-3 space-y-2">
                            @foreach(json_decode($question->options, true) as $key => $option)
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-sm font-medium">{{ $key }}</span>
                                    <span class="text-gray-700">{{ $option }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach
                
                @if($test->questions->count() > 5)
                <div class="text-center py-4">
                    <p class="text-gray-600">... va yana {{ $test->questions->count() - 5 }} ta savol</p>
                    <a href="{{ route('test-management.questions.create', $test->id) }}" class="inline-flex items-center mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Barcha savollarni ko'rish
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Savollar mavjud emas</h3>
            <p class="text-gray-600 mb-4">Bu test uchun hali savollar qo'shilmagan</p>
            <a href="{{ route('test-management.questions.create', $test->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Savollar qo'shish
            </a>
        </div>
    </div>
    @endif

    <!-- Audio Files (if any) -->
    @if($test->audioFiles && $test->audioFiles->count() > 0)
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                </svg>
                Audio fayllar ({{ $test->audioFiles->count() }} ta)
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($test->audioFiles as $audio)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">{{ $audio->title }}</h4>
                    <audio controls class="w-full">
                        <source src="{{ asset('storage/' . $audio->file_path) }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-center space-x-4">
        <a href="{{ route('test-management.questions.create', $test->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Savollarni boshqarish
        </a>
        <a href="{{ route('test-management.edit', $test->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Testni tahrirlash
        </a>
    </div>
</div>
@endsection
