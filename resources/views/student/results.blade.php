@extends('layouts.main')

@section('title', 'Natijalarim - Talaba Panel')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mening Natijalarim</h1>
            <p class="mt-2 text-gray-600">Topshirgan testlaringizning natijalari</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                @if($attempts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test nomi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ball</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sana</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attempts as $attempt)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $attempt->test->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($attempt->score !== null)
                                        <span class="font-semibold">{{ $attempt->score }}%</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($attempt->status === 'completed') bg-green-100 text-green-800 
                                        @elseif($attempt->status === 'in_progress') bg-yellow-100 text-yellow-800 
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($attempt->status === 'completed') Tugallangan
                                        @elseif($attempt->status === 'in_progress') Jarayonda
                                        @else {{ $attempt->status }} @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attempt->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($attempt->status === 'completed')
                                        <a href="{{ route('tests.results', ['test' => $attempt->test->slug, 'attempt' => $attempt->id]) }}" 
                                           class="text-blue-600 hover:text-blue-900">Natijalarni ko'rish</a>
                                    @elseif($attempt->status === 'in_progress')
                                        <a href="{{ route('tests.take', ['test' => $attempt->test->slug, 'attempt' => $attempt->id]) }}" 
                                           class="text-green-600 hover:text-green-900">Davom etish</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $attempts->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hali test topshirmadingiz</h3>
                    <p class="text-gray-500 mb-6">IELTS familiarisation testlarini boshlash uchun test sahifasiga o'ting</p>
                    <a href="{{ route('student.tests') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Testlarni ko'rish
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection