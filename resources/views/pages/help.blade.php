
@extends('layouts.main')

@section('title', 'Yordam - IELTS Platform')
@section('description', 'IELTS Platform bo\'yicha yordam va ko\'p so\'raladigan savollar.')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-green-600 to-teal-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-6xl font-bold mb-6">Yordam markazi</h1>
        <p class="text-xl lg:text-2xl text-green-100 max-w-3xl mx-auto">
            IELTS Platform foydalanish bo'yicha barcha savollaringizga javob
        </p>
    </div>
</div>

<!-- Search Section -->
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Savolingizni qidiring</h2>
            <div class="relative">
                <input type="text" 
                       placeholder="Masalan: test qanday boshlanadi?" 
                       class="w-full px-6 py-4 border border-gray-300 rounded-lg text-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Ko'p so'raladigan savollar</h2>
        
        <div class="space-y-6">
            <!-- FAQ Item 1 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <button class="flex items-center justify-between w-full text-left" onclick="toggleFAQ(1)">
                    <h3 class="text-lg font-semibold text-gray-900">Testlar bepulmi?</h3>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-1"></i>
                </button>
                <div class="mt-4 text-gray-600 hidden" id="answer-1">
                    Ha, barcha familiarisation testlar to'liq bepul. Sizga faqat ro'yxatdan o'tish kerak.
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <button class="flex items-center justify-between w-full text-left" onclick="toggleFAQ(2)">
                    <h3 class="text-lg font-semibold text-gray-900">Test qancha vaqt davom etadi?</h3>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-2"></i>
                </button>
                <div class="mt-4 text-gray-600 hidden" id="answer-2">
                    Har bir test bo'limi 30-60 daqiqa davom etadi. Vaqt chegarasi test turiga bog'liq.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <button class="flex items-center justify-between w-full text-left" onclick="toggleFAQ(3)">
                    <h3 class="text-lg font-semibold text-gray-900">Natijalarni qachon ko'raman?</h3>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-3"></i>
                </button>
                <div class="mt-4 text-gray-600 hidden" id="answer-3">
                    Test yakunlangach darhol natijalarni ko'rishingiz mumkin. Batafsil tahlil ham mavjud.
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <button class="flex items-center justify-between w-full text-left" onclick="toggleFAQ(4)">
                    <h3 class="text-lg font-semibold text-gray-900">Testni qayta topshira olamanmi?</h3>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-4"></i>
                </button>
                <div class="mt-4 text-gray-600 hidden" id="answer-4">
                    Ha, istalgan testni cheksiz marta qayta topshirishingiz mumkin.
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <button class="flex items-center justify-between w-full text-left" onclick="toggleFAQ(5)">
                    <h3 class="text-lg font-semibold text-gray-900">Mobil telefondan foydalansam bo'ladimi?</h3>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-5"></i>
                </button>
                <div class="mt-4 text-gray-600 hidden" id="answer-5">
                    Platform barcha qurilmalarda ishlaydi, lekin eng yaxshi tajriba uchun kompyuter tavsiya etiladi.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="py-16 bg-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Hali ham yordam kerakmi?</h2>
            <p class="text-gray-600">Biz bilan to'g'ridan-to'g'ri bog'laning</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl p-6 text-center shadow-lg">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Email</h3>
                <p class="text-gray-600 mb-4">Yozma savollaringiz uchun</p>
                <a href="mailto:support@ieltsplatform.uz" class="text-blue-600 hover:text-blue-700 font-medium">
                    support@ieltsplatform.uz
                </a>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-lg">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fab fa-telegram text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Telegram</h3>
                <p class="text-gray-600 mb-4">Tezkor javob uchun</p>
                <a href="https://t.me/ieltsplatform_uz" class="text-blue-600 hover:text-blue-700 font-medium">
                    @ieltsplatform_uz
                </a>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-lg">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Qo'llanma</h3>
                <p class="text-gray-600 mb-4">Batafsil ko'rsatmalar</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">
                    PDF yuklab olish
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(id) {
    const answer = document.getElementById(`answer-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        answer.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection
