@extends('layouts.teacher')

@section('title', 'Natijalar - O\'qituvchi Panel')

@section('page_title', 'Test Natijalari')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <i class="fas fa-chart-bar text-blue-600 text-xl mr-3"></i>
            <h2 class="text-xl font-semibold text-gray-800">Test Natijalari</h2>
        </div>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talaba</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ball</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Export</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($results as $result)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            @if($result->user)
                                {{ $result->user->name }}
                                <div class="text-xs text-gray-500">{{ $result->user->email }}</div>
                            @else
                                <span class="text-gray-400 italic">O'chirilgan foydalanuvchi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->test->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->score ?? 0 }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($result->status === 'completed') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $result->status === 'completed' ? 'Tugallangan' : 'Jarayonda' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($result->user)
                            <div class="flex space-x-1">
                                <!-- PDF Export -->
                                <a href="{{ route('teacher.export.user', ['user' => $result->user->id, 'format' => 'pdf']) }}" 
                                   class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors" 
                                   title="PDF yuklab olish">
                                    <i class="fas fa-download mr-1"></i>
                                    <i class="fas fa-file-pdf mr-1"></i>
                                    PDF
                                </a>
                                
                                <!-- Word Export -->
                                <a href="{{ route('teacher.export.user', ['user' => $result->user->id, 'format' => 'word']) }}" 
                                   class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors" 
                                   title="Word yuklab olish">
                                    <i class="fas fa-download mr-1"></i>
                                    <i class="fas fa-file-word mr-1"></i>
                                    Word
                                </a>
                                
                                <!-- Excel Export -->
                                <a href="{{ route('teacher.export.user', ['user' => $result->user->id, 'format' => 'excel']) }}" 
                                   class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded transition-colors" 
                                   title="Excel yuklab olish">
                                    <i class="fas fa-download mr-1"></i>
                                    <i class="fas fa-file-excel mr-1"></i>
                                    Excel
                                </a>
                            </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection