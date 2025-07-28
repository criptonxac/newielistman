@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Enum qiymatlari jadvali</h1>
        <a href="{{ route('test-management.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            Orqaga qaytish
        </a>
    </div>

    @foreach($enums as $enumKey => $enumData)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">{{ $enumData['title'] }}</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-sm font-semibold text-gray-700">Qiymat</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-sm font-semibold text-gray-700">Ko'rinishi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enumData['values'] as $key => $value)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $key }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
