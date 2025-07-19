
@extends('layouts.main')

@section('title', 'Haqida - IELTS Platform')
@section('description', 'IELTS Platform haqida ma\'lumot va platformaning maqsadlari.')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-6xl font-bold mb-6">IELTS Platform haqida</h1>
        <p class="text-xl lg:text-2xl text-blue-100 max-w-3xl mx-auto">
            IELTS imtihoniga tayyorgarlik ko'rish uchun bepul va qulay platforma
        </p>
    </div>
</div>

<!-- About Content -->
<div class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Bizning maqsadimiz</h2>
                <p class="text-gray-600 mb-6">
                    IELTS Platform O'zbekiston talabalari va IELTS imtihoniga tayyorgarlik ko'rayotgan barcha 
                    kishilar uchun yaratilgan. Bizning asosiy maqsadimiz - sifatli va bepul ta'lim resurslarini 
                    taqdim etish.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Bepul familiarisation testlar</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Haqiqiy imtihon muhiti</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Darhol natijalarni tekshirish</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">O'zbek tilida qo'llab-quvvatlash</span>
                    </li>
                </ul>
            </div>
            <div class="bg-blue-50 rounded-xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Platforma statistikasi</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">500+</div>
                        <div class="text-gray-600">Faol foydalanuvchilar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">1000+</div>
                        <div class="text-gray-600">Yakunlangan testlar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">50+</div>
                        <div class="text-gray-600">Test varangtlari</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">24/7</div>
                        <div class="text-gray-600">Xizmat ko'rsatish</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Platformaning afzalliklari</h2>
            <p class="text-gray-600">IELTS Platform nima uchun eng yaxshi tanlov?</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-laptop text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Kompyuterda test</h3>
                <p class="text-gray-600">Haqiqiy IELTS imtihoni kabi kompyuterda test topshiring</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg">
                <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Vaqt nazorati</h3>
                <p class="text-gray-600">Real vaqt chegaralari bilan mashq qiling</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg">
                <div class="bg-purple-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Natijalar tahlili</h3>
                <p class="text-gray-600">Batafsil natijalar va takomillashtirish tavsiyalari</p>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Biz bilan bog'laning</h2>
        <p class="text-gray-600 mb-8">
            Savollaringiz bormi? Biz sizga yordam berishga tayyormiz!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('help') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                <i class="fas fa-question-circle mr-2"></i>
                Yordam markazi
            </a>
            <a href="mailto:support@ieltsplatform.uz" class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-3 rounded-lg font-medium transition-colors">
                <i class="fas fa-envelope mr-2"></i>
                Email yozing
            </a>
        </div>
    </div>
</div>
@endsection
