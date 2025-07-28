@extends('layouts.teacher')

@section('title', 'Talabalar - O\'qituvchi Panel')

@section('page_title', 'Talabalar')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <i class="fas fa-users text-blue-600 text-xl mr-3"></i>
            <h2 class="text-xl font-semibold text-gray-800">Ro'yxatdan o'tgan talabalar</h2>
        </div>
    </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ism</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ro'yxatdan o'tgan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Testlar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->created_at->format('d.m.Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->test_attempts_count ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
</div>
@endsection