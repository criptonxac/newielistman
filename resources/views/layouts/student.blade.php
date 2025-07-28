<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Talaba Panel - IELTS Platform')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @yield('head')
</head>
<body class="bg-gray-100">
<div class="flex bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-gray-900 via-blue-900 to-black shadow-2xl">
        <div class="p-6 border-b border-blue-800/30">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-graduate text-white text-lg"></i>
                </div>
                <span class="ml-3 text-xl font-bold text-white">Talaba Panel</span>
            </div>
        </div>
        
        <nav class="mt-6">
            <div class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-3">Dashboard</div>
            <a href="{{ route('student.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-600 hover:to-blue-800 transition-all duration-300 {{ request()->routeIs('student.dashboard') ? 'text-white bg-gradient-to-r from-blue-600 to-blue-800 border-r-4 border-blue-400 shadow-lg' : '' }}">
                <i class="fas fa-home mr-3 text-blue-400"></i>
                Bosh sahifa
            </a>
            
            <div class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-3 mt-6">Testlar</div>
            <a href="{{ route('student.tests') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-600 hover:to-blue-800 transition-all duration-300 {{ request()->routeIs('student.tests') ? 'text-white bg-gradient-to-r from-blue-600 to-blue-800 border-r-4 border-blue-400 shadow-lg' : '' }}">
                <i class="fas fa-play mr-3 text-blue-400"></i>
                Test Boshlash
            </a>
            <a href="{{ route('student.results') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-600 hover:to-blue-800 transition-all duration-300 {{ request()->routeIs('student.results') ? 'text-white bg-gradient-to-r from-blue-600 to-blue-800 border-r-4 border-blue-400 shadow-lg' : '' }}">
                <i class="fas fa-chart-line mr-3 text-blue-400"></i>
                Mening Natijalarim
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">@yield('page_title', 'Talaba Panel')</h1>
                
                <!-- User Avatar Dropdown -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" onclick="toggleUserMenu()">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Student' }}&background=10b981&color=fff" 
                             class="w-8 h-8 rounded-full" alt="Avatar">
                        <div class="text-left">
                            <div class="font-medium text-gray-900">{{ auth()->user()->name ?? 'Talaba' }}</div>
                            <div class="text-xs text-gray-500">Talaba</div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                        <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>
                            Profil
                        </a>
                        <a href="{{ route('student.results') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-chart-line mr-2"></i>
                            Natijalarim
                        </a>
                        <a href="{{ route('student.tests') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-play mr-2"></i>
                            Test Boshlash
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Chiqish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="p-8">
            @yield('content')
        </div>
    </div>
</div>

<script>
    function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const userMenuButton = document.getElementById('userMenuButton');
        const dropdown = document.getElementById('userDropdown');
        
        if (!userMenuButton.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>

@yield('scripts')

</body>
</html>
