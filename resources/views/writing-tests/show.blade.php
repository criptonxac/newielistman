@extends($layout)

@section('title', 'Writing Test - ' . $writingTest->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $writingTest->title }}</h1>
                    <p class="text-gray-600 mt-1">Writing Test Ma'lumotlari</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('writing-tests.edit', $writingTest) }}" 
                       class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Tahrirlash
                    </a>
                    <a href="{{ route('writing-tests.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Asosiy Ma'lumotlar</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">ID:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $writingTest->id }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Test Nomi:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $writingTest->title }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">App Test:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $writingTest->appTest->title ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Yaratilgan:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $writingTest->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Yangilangan:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $writingTest->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Statistika</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Questions:</span>
                            <span class="ml-2 text-sm text-gray-900">
                                {{ is_array($writingTest->formatted_questions) ? count($writingTest->formatted_questions) : 0 }} ta
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Answer:</span>
                            <span class="ml-2 text-sm text-gray-900">
                                {{ is_array($writingTest->formatted_answer) ? count($writingTest->formatted_answer) : 0 }} ta field
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            @php
                $questions = $writingTest->formatted_questions;
            @endphp
            @if($questions && is_array($questions))
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Questions Ma'lumotlari</h3>
                    <div class="bg-gray-50 rounded-lg p-6">
                        @if(!empty($questions['title']))
                            <div class="mb-4">
                                <h4 class="text-lg font-medium text-gray-800 mb-2">{{ $questions['title'] }}</h4>
                            </div>
                        @endif

                        @if(!empty($questions['content']))
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Content:</h5>
                                <div class="bg-white rounded border p-4 text-gray-800">
                                    {!! nl2br(e($questions['content'])) !!}
                                </div>
                            </div>
                        @endif

                        @if(!empty($questions['instructions']))
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Instructions:</h5>
                                <div class="bg-white rounded border p-4 text-gray-700">
                                    {!! nl2br(e($questions['instructions'])) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Answer Section -->
            @php
                $answer = $writingTest->formatted_answer;
            @endphp
            @if($answer && is_array($answer))
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Answer Ma'lumotlari</h3>
                    <div class="bg-green-50 rounded-lg p-6">
                        @if(!empty($answer['sample']))
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Sample Answer:</h5>
                                <div class="bg-white rounded border p-4 text-gray-800">
                                    {!! nl2br(e($answer['sample'])) !!}
                                </div>
                            </div>
                        @endif

                        @if(!empty($answer['criteria']))
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Grading Criteria:</h5>
                                <div class="bg-white rounded border p-4 text-gray-700">
                                    {!! nl2br(e($answer['criteria'])) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Raw JSON Data (for debugging) -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Raw JSON Ma'lumotlar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Questions JSON:</h4>
                        <div class="bg-gray-100 rounded p-4 text-xs font-mono overflow-auto max-h-64">
                            <pre>{{ json_encode($writingTest->formatted_questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Answer JSON:</h4>
                        <div class="bg-gray-100 rounded p-4 text-xs font-mono overflow-auto max-h-64">
                            <pre>{{ json_encode($writingTest->formatted_answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <form action="{{ route('writing-tests.destroy', $writingTest) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Haqiqatan ham bu writing testni o\'chirmoqchimisiz? Bu amal qaytarilmaydi!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>O'chirish
                    </button>
                </form>
                <a href="{{ route('writing-tests.edit', $writingTest) }}" 
                   class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Tahrirlash
                </a>
                <a href="{{ route('writing-tests.index') }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>Ro'yxatga qaytish
                </a>
            </div>
        </div>
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
