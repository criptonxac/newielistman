@extends('layouts.main')

@section('title', 'IELTS Platform - Welcome')
@section('description', 'IELTS imtihoniga tayyorgarlik platformasi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 lg:p-12">
            <div class="mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 w-20 h-20 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-graduation-cap text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    IELTS Platform
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    IELTS imtihoniga tayyorgarlik uchun professional platforma
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 rounded-xl p-6">
                    <i class="fas fa-headphones text-blue-600 text-2xl mb-3"></i>
                    <h3 class="font-semibold text-gray-900 mb-2">Listening</h3>
                    <p class="text-gray-600 text-sm">4 qismli audio testlar</p>
                </div>
                <div class="bg-green-50 rounded-xl p-6">
                    <i class="fas fa-book-open text-green-600 text-2xl mb-3"></i>
                    <h3 class="font-semibold text-gray-900 mb-2">Reading</h3>
                    <p class="text-gray-600 text-sm">Academic va General Training</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-6">
                    <i class="fas fa-pen text-purple-600 text-2xl mb-3"></i>
                    <h3 class="font-semibold text-gray-900 mb-2">Writing</h3>
                    <p class="text-gray-600 text-sm">Task 1 va Task 2</p>
                </div>
            </div>

            <div class="space-y-4">
                <a href="/home" 
                   class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 px-8 rounded-xl font-semibold text-lg transition-all">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Platformaga kirish
                </a>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/register" 
                       class="flex-1 border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white py-3 px-6 rounded-xl font-medium transition-all">
                        Ro'yxatdan o'tish
                    </a>
                    <a href="/login" 
                       class="flex-1 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-xl font-medium transition-all">
                        Kirish
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection