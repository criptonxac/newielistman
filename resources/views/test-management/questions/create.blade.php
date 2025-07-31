@extends($layout)

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.csrfToken = '{{ csrf_token() }}';
    window.testId = {{ $test->id ?? 'null' }};
    window.userId = {{ auth()->id() }};
</script>
<style>
    /* Enhanced Animations */
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
        40%, 43% { transform: translate3d(0,-30px,0); }
        70% { transform: translate3d(0,-15px,0); }
        90% { transform: translate3d(0,-4px,0); }
    }

    /* Component Animations */
    .upload-message { animation: slideInDown 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
    .file-item { animation: slideInUp 0.3s ease-out; }
    .shake-animation { animation: shake 0.6s ease-in-out; }
    .pulse-animation { animation: pulse 2s infinite; }
    .bounce-animation { animation: bounce 1s; }

    /* Enhanced Upload Zone */
    .upload-zone {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-size: 200% 200%;
        animation: gradientShift 4s ease infinite;
        transition: all 0.3s ease;
    }
    
    .upload-zone:hover {
        background-position: right center;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Modern Volume Slider */
    .volume-slider {
        appearance: none;
        background: linear-gradient(to right, #3b82f6 0%, #3b82f6 100%, #e2e8f0 100%);
        outline: none;
        border-radius: 3px;
        height: 6px;
        transition: all 0.2s ease;
    }
    
    .volume-slider:hover {
        height: 8px;
        background: linear-gradient(to right, #2563eb 0%, #2563eb 100%, #cbd5e1 100%);
    }
    
    .volume-slider::-webkit-slider-thumb {
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }
    
    .volume-slider::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    /* File Item Enhancements */
    .file-item {
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border-left: 4px solid transparent;
    }
    
    .file-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-left-color: #3b82f6;
    }
    
    .file-item.uploading {
        border-left-color: #f59e0b;
        background: linear-gradient(90deg, #fef3c7 0%, #ffffff 100%);
    }
    
    .file-item.success {
        border-left-color: #10b981;
        background: linear-gradient(90deg, #d1fae5 0%, #ffffff 100%);
    }
    
    .file-item.error {
        border-left-color: #ef4444;
        background: linear-gradient(90deg, #fee2e2 0%, #ffffff 100%);
    }

    /* Progress Bar Enhancements */
    .progress-bar {
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        transition: width 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255,255,255,0.3), 
            transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Audio Control Buttons */
    .play-btn, .control-btn {
        transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
    }
    
    .play-btn:hover, .control-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .play-btn:active, .control-btn:active {
        transform: scale(0.95);
    }
    
    .play-btn::before, .control-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        transition: all 0.3s ease;
        transform: translate(-50%, -50%);
    }
    
    .play-btn:active::before, .control-btn:active::before {
        width: 100%;
        height: 100%;
    }

    /* Drag and Drop States */
    .drag-over {
        border-color: #3b82f6 !important;
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%) !important;
        transform: scale(1.02);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
    }
    
    .drag-active {
        animation: bounce 0.6s ease-in-out;
    }

    /* Statistics Cards */
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .stat-card:hover::before {
        left: 100%;
    }

    /* Message Types */
    .message-success {
        background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        border-left: 4px solid #10b981;
        color: #065f46;
    }
    
    .message-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }
    
    .message-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        border-left: 4px solid #f59e0b;
        color: #92400e;
    }
    
    .message-info {
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        border-left: 4px solid #3b82f6;
        color: #1e40af;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .file-item {
            padding: 12px;
        }
        
        .file-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .audio-controls {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        
        .volume-control {
            width: 100%;
            justify-content: center;
        }
        
        .stat-card {
            text-align: center;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .file-item {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border-color: #374151;
            color: #f9fafb;
        }
        
        .upload-zone {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            color: #e5e7eb;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #3b82f6, #6366f1);
        }
    }

    /* Accessibility Improvements */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
    
    .focus-visible:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
    
    /* Loading States */
    .loading-shimmer {
        background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <!-- Enhanced Header -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $test->title ?? 'Audio Test' }}</h1>
                <p class="text-gray-600 mt-1">Savollar va audio fayllarni boshqarish</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('test-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Orqaga
            </a>
            <button id="helpBtn" class="bg-blue-100 hover:bg-blue-200 text-blue-600 p-2 rounded-lg transition-colors" title="Yordam">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Enhanced Alert Messages -->
    @if(session('success'))
    <div class="message-success p-4 rounded-lg mb-6 flex items-center animate-slideInDown">
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
        <button class="ml-auto p-1 hover:bg-green-200 rounded transition-colors" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="message-error p-4 rounded-lg mb-6 flex items-center animate-slideInDown">
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>{{ session('error') }}</span>
        <button class="ml-auto p-1 hover:bg-red-200 rounded transition-colors" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="message-error p-4 rounded-lg mb-6 animate-slideInDown">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">Xatoliklar:</span>
        </div>
        <ul class="list-disc pl-7 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Enhanced Test Information Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Test ma'lumotlari
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Kategoriya</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $test->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Turi</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($test->type == 'familiarisation')
                                Tanishuv
                            @elseif($test->type == 'practice')
                                Amaliyot
                            @else
                                {{ ucfirst($test->type) }}
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Davomiyligi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $test->duration_minutes ?? 'N/A' }} daqiqa</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Audio Upload Section (Only for Listening tests) -->
    @if(isset($test->category) && $test->category->name == 'Listening')
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 text-white">
            <h2 class="text-xl font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                </svg> 
                Audio Fayllar Boshqaruvi
            </h2>
            <p class="text-blue-100 mt-1">Audio fayllarni yuklang va ularni test bilan bog'lang</p>
        </div>
        
        <div class="p-6">
            <!-- Modern Upload Zone -->
            <div id="audioUploadSection" class="upload-zone relative flex flex-col items-center justify-center py-12 px-6 border-2 border-dashed border-blue-300 rounded-xl transition-all duration-300 cursor-pointer group">
                <!-- Loading Overlay -->
                <div id="audio-upload-loading-overlay" class="absolute inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center rounded-xl z-20 hidden">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white mb-4"></div>
                        <p id="audio-upload-loading-text" class="text-white font-medium">Fayl tanlanmoqda...</p>
                    </div>
                </div>
                <div class="text-center z-10">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-white mb-2">Audio fayllarni yuklash</h3>
                    <p class="text-white/80 text-sm mb-6 max-w-md">Fayllarni tanlash uchun bosing yoki bu yerga olib tashlang.<br>Bir vaqtda bir nechta faylni yuklashingiz mumkin.</p>
                    
                    <input type="file" id="audio-upload" class="hidden" accept="audio/*" multiple>
                    
                    <button type="button" id="selectFilesBtn" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white py-3 px-8 rounded-lg font-semibold transition-all duration-300 flex items-center mx-auto group-hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Fayllarni tanlash
                    </button>
                    
                    <div class="text-xs text-white/60 mt-6 space-y-1">
                        <p>✓ Qo'llab-quvvatlanadigan formatlar: MP3, WAV, OGG, M4A, AAC, FLAC</p>
                        <p>✓ Maksimal fayl hajmi: 100MB</p>
                        <p>✓ Drag & Drop qo'llab-quvvatlanadi</p>
                    </div>
                </div>
                
                <!-- Background decoration -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-white rounded-full"></div>
                    <div class="absolute top-1/3 right-1/3 w-1 h-1 bg-white rounded-full"></div>
                    <div class="absolute bottom-1/4 right-1/4 w-3 h-3 bg-white rounded-full"></div>
                    <div class="absolute bottom-1/3 left-1/3 w-1.5 h-1.5 bg-white rounded-full"></div>
                </div>
            </div>

            <!-- Enhanced Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Formatlar</p>
                            <p class="text-xs text-gray-600">MP3, WAV, OGG, M4A</p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Maksimal hajm</p>
                            <p class="text-xs text-gray-600">100MB / fayl</p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Auto-play</p>
                            <p class="text-xs text-gray-600">Test boshlanishida</p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Tez yuklash</p>
                            <p class="text-xs text-gray-600">Bulk upload</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Section (will be populated by JavaScript) -->
    <div id="statisticsSection" class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100" style="display: none;">
        <!-- Statistics content will be injected here -->
    </div>

    <!-- Progress Section (will be shown when files are uploading) -->
    <div id="filesProgressSection" class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100" style="display: none;">
        <!-- Progress content will be injected here -->
    </div>

    <!-- Preview Section (will be shown when files are uploaded) -->
    <div id="audioPreviewSection" class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100" style="display: none;">
        <!-- Preview content will be injected here -->
    </div>

    <!-- Enhanced Questions Management Form -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Savollar Boshqaruvi
            </h2>
            <p class="text-gray-600 mt-1">Test savollarini qo'shing va tahrirlang</p>
        </div>

        <form action="{{ route('test-management.questions.store', $test->id) }}" method="POST" enctype="multipart/form-data" id="questions-form" class="p-6">
            @csrf
            <input type="hidden" id="test-category" value="{{ $test->category->name ?? '' }}" />
            <input type="hidden" id="test-id" value="{{ $test->id ?? '' }}" />
            
            <!-- Questions Container -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Savollar ro'yxati</h3>
                        <p class="text-sm text-gray-500 mt-1">Savollarni drag-and-drop orqali tartibini o'zgartirishingiz mumkin</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">Jami: {{ $questions->total() ?? 0 }} ta savol</span>
                        <a href="{{ route('test-management.questions.add', $test->id) }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Savol qo'shish
                        </a>
                    </div>
                </div>
                
                <div id="questions-container" class="space-y-6">
                    @if(isset($questions) && $questions->count() > 0)
                        @foreach($questions as $index => $question)
                            <div class="question-item bg-gradient-to-r from-gray-50 to-white p-6 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300" draggable="true" data-question-id="{{ $question->id }}">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold text-sm">
                                            {{ ($questions->currentPage() - 1) * 10 + $loop->iteration }}
                                        </div>
                                        <h3 class="font-semibold text-gray-800 question-number">Savol #{{ ($questions->currentPage() - 1) * 10 + $loop->iteration }}</h3>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" class="handle cursor-move text-gray-400 hover:text-gray-600 p-2 rounded transition-colors" title="Tartibini o'zgartirish">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                            </svg>
                                        </button>
                                        <button type="button" class="remove-question text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition-colors" data-question-id="{{ $question->id }}" title="O'chirish">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="questions[{{ $question->id }}][sort_order]" value="{{ $question->sort_order }}" class="sort-order">
                                
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-semibold mb-2">Savol matni:</label>
                                            <textarea name="questions[{{ $question->id }}][question_text]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none" required placeholder="Savolingizni yozing...">{{ $question->question_text }}</textarea>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-gray-700 text-sm font-semibold mb-2">Savol turi:</label>
                                            <select name="questions[{{ $question->id }}][question_type]" class="question-type w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                                                <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>Ko'p tanlovli (Radio)</option>
                                                <option value="short_answer" {{ $question->question_type == 'short_answer' ? 'selected' : '' }}>Ko'p to'g'ri javobli (Checkbox)</option>
                                                <option value="true_false" {{ $question->question_type == 'true_false' ? 'selected' : '' }}>To'g'ri/Noto'g'ri</option>
                                                <option value="fill_blank" {{ $question->question_type == 'fill_blank' ? 'selected' : '' }}>Bo'sh joyni to'ldirish</option>
                                                <option value="matching" {{ $question->question_type == 'matching' ? 'selected' : '' }}>Moslashtirish</option>
                                                <option value="drag_drop" {{ $question->question_type == 'drag_drop' ? 'selected' : '' }}>Drag & Drop</option>
                                                <option value="essay" {{ $question->question_type == 'essay' ? 'selected' : '' }}>Insho</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-2">Ball:</label>
                                        <input type="number" name="questions[{{ $question->id }}][points]" value="{{ $question->points }}" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Hozircha savollar yo'q</h3>
                            <p class="text-gray-500 mb-6">Test uchun savollar qo'shishni boshlang</p>
                            <a href="{{ route('test-management.questions.add', $test->id) }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Birinchi savolni qo'shish
                            </a>
                        </div>
                    @endif
                </div>
                
                <!-- Enhanced Pagination -->
                @if(isset($questions) && $questions->hasPages())
                <div class="mt-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        @if($questions->onFirstPage())
                            <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">Oldingi</span>
                        @else
                            <a href="{{ route('test-management.questions.create', [$test->id, 'page' => $questions->currentPage() - 1]) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">Oldingi</a>
                        @endif
                        
                        @foreach(range(1, $questions->lastPage()) as $page)
                            @if($page == $questions->currentPage())
                                <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">{{ $page }}</span>
                            @else
                                <a href="{{ route('test-management.questions.create', [$test->id, 'page' => $page]) }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-lg transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($questions->hasMorePages())
                            <a href="{{ route('test-management.questions.create', [$test->id, 'page' => $questions->currentPage() + 1]) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">Keyingi</a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">Keyingi</span>
                        @endif
                    </nav>
                </div>
                @endif
            </div>
            
            <!-- Enhanced Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button type="button" id="previewBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Testni ko'rish
                    </button>
                    <span class="text-sm text-gray-500">Avtomatik saqlash: Yoqilgan</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" id="draftBtn" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Qoralama sifatida saqlash
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Saqlash va Nashr etish
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Help Modal -->
<div id="helpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800">Yordam va Ko'rsatmalar</h3>
                <button id="closeHelpModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                        Audio Fayllar
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1 ml-7">
                        <li>• Qo'llab-quvvatlanadigan formatlar: MP3, WAV, OGG, M4A, AAC, FLAC</li>
                        <li>• Maksimal fayl hajmi: 100MB</li>
                        <li>• Drag & Drop orqali fayllarni olib kerishingiz mumkin</li>
                        <li>• Bir vaqtda ko'p fayllarni yuklash mumkin</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Savollar
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1 ml-7">
                        <li>• Savollar turlarini tanlashingiz mumkin</li>
                        <li>• Drag & Drop orqali savollar tartibini o'zgartirishingiz mumkin</li>
                        <li>• Har bir savol uchun ball belgilashingiz mumkin</li>
                        <li>• Savollarni tahrirlash va o'chirish mumkin</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Klaviatura Yorliqlari
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1 ml-7">
                        <li>• <kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + U</kbd> - Fayl yuklash</li>
                        <li>• <kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Space</kbd> - Audio ijro etish/to'xtatish</li>
                        <li>• <kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + S</kbd> - Formani saqlash</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
        <svg class="animate-spin w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-lg font-medium text-gray-700">Yuklanmoqda...</span>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="{{ asset('js/questions-management.js') }}"></script>
<script src="{{ asset('js/enhanced-audio-upload.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎵 Enhanced page loaded, initializing systems...');
    
    // Initialize help modal
    const helpBtn = document.getElementById('helpBtn');
    const helpModal = document.getElementById('helpModal');
    const closeHelpModal = document.getElementById('closeHelpModal');
    
    helpBtn?.addEventListener('click', () => {
        helpModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    
    closeHelpModal?.addEventListener('click', () => {
        helpModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    });
    
    // Close modal on outside click
    helpModal?.addEventListener('click', (e) => {
        if (e.target === helpModal) {
            helpModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
    
    // Enhanced form handling
    const questionsForm = document.getElementById('questions-form');
    const previewBtn = document.getElementById('previewBtn');
    const draftBtn = document.getElementById('draftBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Auto-save functionality
    let autoSaveTimeout;
    function scheduleAutoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Auto-save logic here
            console.log('Auto-saving...');
        }, 5000); // 5 seconds
    }
    
    // Form change detection
    questionsForm?.addEventListener('input', scheduleAutoSave);
    
    // Preview functionality
    previewBtn?.addEventListener('click', () => {
        // Open test preview in new tab
        const testId = document.getElementById('test-id')?.value;
        if (testId) {
            window.open(`/test-preview/${testId}`, '_blank');
        }
    });
    
    // Draft save
    draftBtn?.addEventListener('click', async () => {
        const formData = new FormData(questionsForm);
        formData.append('save_as_draft', 'true');
        
        try {
            loadingOverlay?.classList.remove('hidden');
            const response = await fetch(questionsForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Qoralama saqlandi', 'success');
            } else {
                showNotification('Xatolik yuz berdi', 'error');
            }
        } catch (error) {
            console.error('Draft save error:', error);
            showNotification('Saqlashda xatolik', 'error');
        } finally {
            loadingOverlay?.classList.add('hidden');
        }
    });
    
    // Enhanced form submission
    questionsForm?.addEventListener('submit', function(e) {
        console.log('📝 Enhanced form submitting...');
        
        // Show loading
        loadingOverlay?.classList.remove('hidden');
        
        // Get uploaded audio files if any
        if (window.audioUploadManager) {
            const uploadedFiles = window.audioUploadManager.getUploadedFiles();
            console.log('🎵 Uploaded audio files:', uploadedFiles);
            
            if (uploadedFiles.length > 0) {
                const audioDataInput = document.createElement('input');
                audioDataInput.type = 'hidden';
                audioDataInput.name = 'uploaded_audio_files';
                audioDataInput.value = JSON.stringify(uploadedFiles);
                questionsForm.appendChild(audioDataInput);
            }
        }
    });
    
    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg message-${type} animate-slideInDown`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span>${message}</span>
                <button class="ml-4 p-1 hover:bg-black hover:bg-opacity-10 rounded transition-colors" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'u':
                    e.preventDefault();
                    document.getElementById('audio-upload')?.click();
                    break;
                case 's':
                    e.preventDefault();
                    questionsForm?.requestSubmit();
                    break;
                case 'h':
                    e.preventDefault();
                    helpBtn?.click();
                    break;
            }
        }
        
        // ESC key
        if (e.key === 'Escape') {
            helpModal?.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
    
    // Enhanced audio upload manager initialization
    if (document.getElementById('audioUploadSection')) {
        // Wait a bit for the enhanced manager to load
        setTimeout(() => {
            if (window.EnhancedAudioUploadManager) {
                window.audioUploadManager = new window.EnhancedAudioUploadManager({
                    debug: true,
                    enableBulkUpload: true,
                    enableDragDrop: true,
                    enablePreview: true,
                    showStatistics: true,
                    testId: window.testId,
                    part: 'part1'
                });
                
                // Enhanced event listeners
                window.audioUploadManager.on('filesSelected', (data) => {
                    console.log('Files selected:', data.files);
                    showNotification(`${data.files.length} ta fayl tanlandi`, 'info');
                });
                
                window.audioUploadManager.on('uploadSuccess', (data) => {
                    console.log('Upload success:', data);
                    showNotification(`${data.file.name} yuklandi`, 'success');
                });
                
                window.audioUploadManager.on('uploadError', (data) => {
                    console.log('Upload error:', data);
                    showNotification(`Yuklashda xatolik: ${data.error.message}`, 'error');
                });
                
                console.log('✅ Enhanced Audio Upload Manager initialized');
            } else {
                console.warn('⚠️ Enhanced Audio Upload Manager not found, falling back to basic version');
                
                // Fallback to basic version
                if (window.AudioUploadManager) {
                    window.audioUploadManager = new window.AudioUploadManager({
                        debug: true
                    });
                }
            }
        }, 1000);
    }
    
    // Debug info
    console.log('🔧 Enhanced global objects available:', {
        audioUploadManager: !!window.audioUploadManager,
        EnhancedAudioUploadManager: !!window.EnhancedAudioUploadManager,
        AudioUploadManager: !!window.AudioUploadManager,
        csrfToken: !!window.csrfToken,
        testId: window.testId,
        userId: window.userId
    });
});

// Enhanced global functions
window.startListeningTestAudio = function() {
    if (window.audioUploadManager && window.audioUploadManager.autoPlayFirstAudio) {
        window.audioUploadManager.autoPlayFirstAudio();
    } else {
        console.warn('⚠️ Audio upload manager or autoPlayFirstAudio method not available');
    }
};

window.checkAudioUploadStatus = function() {
    if (window.audioUploadManager && window.audioUploadManager.getUploadedFiles) {
        const files = window.audioUploadManager.getUploadedFiles();
        console.log('📊 Enhanced Audio Upload Status:', {
            initialized: window.audioUploadManager.isInitialized || false,
            uploadedFiles: files.length,
            files: files
        });
        return files;
    } else {
        console.log('❌ Enhanced audio upload manager not available');
        return [];
    }
};

// Performance monitoring
window.addEventListener('load', () => {
    const loadTime = performance.now();
    console.log(`⚡ Page loaded in ${Math.round(loadTime)}ms`);
});

// Direct fix for file upload button with improved reliability
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Applying direct fix for file upload button');
    
    // Function to set up the button
    function setupFileUploadButton() {
        // Get elements
        const selectFilesBtn = document.getElementById('selectFilesBtn');
        const audioUploadInput = document.getElementById('audio-upload');
        
        if (selectFilesBtn && audioUploadInput) {
            console.log('✅ Found both select button and file input');
            
            // Remove any existing event listeners (in case of duplicates)
            const newBtn = selectFilesBtn.cloneNode(true);
            selectFilesBtn.parentNode.replaceChild(newBtn, selectFilesBtn);
            
            // Add direct event listener
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🖱️ Select files button clicked directly');
                
                // Show loading indicator
                const overlay = document.getElementById('audio-upload-loading-overlay');
                const text = document.getElementById('audio-upload-loading-text');
                if (overlay && text) {
                    text.textContent = 'Fayl tanlanmoqda...';
                    overlay.classList.remove('hidden');
                }
                
                // Set a global flag to prevent multiple clicks
                window.audioUploadIsSelecting = true;
                
                // Auto-hide after 5 seconds if no file was selected
                setTimeout(() => {
                    if (window.audioUploadIsSelecting) {
                        console.log('File selection timeout - no file selected');
                        window.audioUploadIsSelecting = false;
                        if (overlay) overlay.classList.add('hidden');
                    }
                }, 5000);
                
                audioUploadInput.click();
            });
            
            console.log('✅ Direct event listener added to select files button');
            
            // Add change event listener to file input
            audioUploadInput.addEventListener('change', function(e) {
                console.log('📁 File input change event triggered');
                
                // Reset selection flag
                window.audioUploadIsSelecting = false;
                
                // Hide loading indicator
                const overlay = document.getElementById('audio-upload-loading-overlay');
                if (overlay) overlay.classList.add('hidden');
                
                if (e.target.files.length > 0) {
                    console.log(`Selected ${e.target.files.length} files`);
                    
                    // Show upload loading indicator with file name
                    const text = document.getElementById('audio-upload-loading-text');
                    if (overlay && text) {
                        text.textContent = `${e.target.files[0].name} yuklanmoqda...`;
                        overlay.classList.remove('hidden');
                    }
                } else {
                    console.log('No files selected');
                }
            });
            
            // Also add a click handler to the upload section as a backup
            const uploadSection = document.getElementById('audioUploadSection');
            if (uploadSection) {
                uploadSection.addEventListener('click', function(e) {
                    // Only trigger if clicking on the section itself, not on buttons
                    if (e.target === uploadSection || e.target.closest('#audioUploadSection') && !e.target.closest('button')) {
                        console.log('🖱️ Upload section clicked');
                        
                        // Show loading indicator
                        const overlay = document.getElementById('audio-upload-loading-overlay');
                        const text = document.getElementById('audio-upload-loading-text');
                        if (overlay && text) {
                            text.textContent = 'Fayl tanlanmoqda...';
                            overlay.classList.remove('hidden');
                        }
                        
                        // Set a global flag to prevent multiple clicks
                        window.audioUploadIsSelecting = true;
                        
                        audioUploadInput.click();
                    }
                });
            }
        } else {
            console.error('❌ Could not find select button or file input');
            console.log('selectFilesBtn:', !!selectFilesBtn);
            console.log('audioUploadInput:', !!audioUploadInput);
            
            // Try again after a short delay
            setTimeout(setupFileUploadButton, 500);
        }
    }
    
    // Initial setup
    setupFileUploadButton();
    
    // Also set up a global click handler as a last resort
    document.querySelector('button#selectFilesBtn')?.addEventListener('click', function() {
        console.log('🔄 Global button click handler triggered');
        document.getElementById('audio-upload')?.click();
    });
});
</script>

<!-- Immediate fix for file upload button -->
<script>
// Execute immediately without waiting for DOMContentLoaded
(function() {
    console.log('🚀 Immediate file upload button fix');
    
    // Try to fix the button immediately
    const fixButton = function() {
        const btn = document.getElementById('selectFilesBtn');
        const input = document.getElementById('audio-upload');
        
        if (btn && input) {
            console.log('🔗 Connecting button to file input directly');
            btn.onclick = function(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                console.log('📁 File selection triggered');
                
                // Show loading indicator
                const overlay = document.getElementById('audio-upload-loading-overlay');
                const text = document.getElementById('audio-upload-loading-text');
                if (overlay && text) {
                    text.textContent = 'Fayl tanlanmoqda...';
                    overlay.classList.remove('hidden');
                }
                
                // Set a global flag to prevent multiple clicks
                window.audioUploadIsSelecting = true;
                
                // Auto-hide after 5 seconds if no file was selected
                setTimeout(() => {
                    if (window.audioUploadIsSelecting) {
                        console.log('File selection timeout - no file selected');
                        window.audioUploadIsSelecting = false;
                        if (overlay) overlay.classList.add('hidden');
                    }
                }, 5000);
                
                input.click();
                return false;
            };
        } else {
            console.log('⏱️ Elements not ready yet, will try again');
            setTimeout(fixButton, 100);
        }
    };
    
    // Start fixing
    fixButton();
})();
</script>

@endsection