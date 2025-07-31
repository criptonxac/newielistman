<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testlar - Admin Panel</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
<div class="flex bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-slate-900 via-purple-900 to-slate-900 shadow-2xl">
        <div class="p-6 border-b border-purple-800/30">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <span class="ml-3 text-xl font-bold text-white">IELTS Platform</span>
            </div>
        </div>
        
        <nav class="mt-6">
            <div class="px-4 text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3">Dashboard</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                <i class="fas fa-home mr-3 text-purple-400"></i>
                Home
            </a>
            
            <div class="px-4 text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3 mt-6">Management</div>
            <a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                <i class="fas fa-users mr-3 text-purple-400"></i>
                Users
            </a>
            <a href="{{ route('test-management.index') }}" class="flex items-center px-6 py-3 text-white bg-gradient-to-r from-blue-600 to-purple-600 border-r-4 border-blue-400 shadow-lg">
                <i class="fas fa-file-alt mr-3 text-blue-200"></i>
                Tests
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Testlar</h1>
                
                <!-- User Avatar Dropdown -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" onclick="toggleUserMenu()">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Admin' }}&background=6366f1&color=fff" 
                             class="w-8 h-8 rounded-full" alt="Avatar">
                        <div class="text-left">
                            <div class="font-medium text-gray-900">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <div class="text-xs text-gray-500">Administrator</div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>
                            Profil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>
                            Sozlamalar
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
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
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Barcha testlar ro'yxati</h2>
                <a href="{{ route('test-management.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Yangi test qo'shish
                </a>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoriya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Savollar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yaratilgan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tests as $test)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('test-management.edit', $test->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ $test->title }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->questions_count ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($test->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        {{ $test->is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->created_at->format('d.m.Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('test-management.edit', $test->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i> Tahrirlash
                                        </a>
                                        <a href="{{ route('test-management.questions.create', $test->id) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-question-circle"></i> Savollar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $tests->links() }}
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
// User Avatar Dropdown Menu
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');
    
    if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
    }
});
</script>
</body>
</html>