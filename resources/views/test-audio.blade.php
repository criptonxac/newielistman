<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Audio Upload Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .drag-active {
            border: 2px dashed #4299e1 !important;
            background-color: rgba(66, 153, 225, 0.1) !important;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">Audio Upload Test</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Upload Audio Files</h2>
            
            <!-- Audio Upload Section -->
            <div id="audioUploadSection" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-6 hover:bg-gray-50 transition-colors">
                <input type="file" id="audio-upload" accept="audio/*" class="hidden" multiple />
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Drag and drop audio files here or click the button below</p>
                <button id="selectFilesBtn" type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Fayllarni tanlash
                </button>
            </div>
            
            <!-- Progress Section (Hidden by default) -->
            <div id="filesProgressSection" class="hidden">
                <h3 class="text-lg font-medium mb-3">Upload Progress</h3>
                <div id="filesList" class="space-y-3"></div>
            </div>
            
            <!-- Audio Preview Section (Hidden by default) -->
            <div id="audioPreviewSection" class="hidden">
                <h3 class="text-lg font-medium mb-3">Uploaded Audio Files</h3>
                <div id="audioList" class="space-y-3"></div>
            </div>
        </div>
        
        <!-- Debug Console -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-4 text-white">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-medium">Debug Console</h3>
                <button id="clearConsole" class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded">Clear</button>
            </div>
            <div id="debugConsole" class="font-mono text-sm h-64 overflow-y-auto bg-gray-900 p-3 rounded"></div>
        </div>
    </div>
    
    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 right-4 w-80 space-y-2"></div>

    <!-- Direct fix script for the file upload button -->
    <script>
        // Function to show notifications
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            
            let bgColor = 'bg-blue-500';
            if (type === 'success') bgColor = 'bg-green-500';
            if (type === 'error') bgColor = 'bg-red-500';
            if (type === 'warning') bgColor = 'bg-yellow-500';
            
            notification.className = `${bgColor} text-white p-3 rounded-lg shadow-lg transition-opacity duration-500`;
            notification.textContent = message;
            
            container.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    container.removeChild(notification);
                }, 500);
            }, 3000);
        }
        
        // Function to log debug messages
        function logDebug(message) {
            const console = document.getElementById('debugConsole');
            const line = document.createElement('div');
            const time = new Date().toLocaleTimeString();
            line.className = 'pb-1 border-b border-gray-800 mb-1';
            line.innerHTML = `<span class="text-gray-500">[${time}]</span> ${message}`;
            console.appendChild(line);
            console.scrollTop = console.scrollHeight;
        }
        
        // Clear console button
        document.getElementById('clearConsole').addEventListener('click', function() {
            document.getElementById('debugConsole').innerHTML = '';
        });
        
        // Direct fix for the file upload button
        document.addEventListener('DOMContentLoaded', function() {
            logDebug('🔄 Page loaded, setting up direct button fix');
            
            function setupSelectButton() {
                const selectBtn = document.getElementById('selectFilesBtn');
                const uploadInput = document.getElementById('audio-upload');
                
                if (selectBtn && uploadInput) {
                    logDebug('✅ Found select button and upload input');
                    
                    // Remove any existing click listeners by cloning
                    const newSelectBtn = selectBtn.cloneNode(true);
                    selectBtn.parentNode.replaceChild(newSelectBtn, selectBtn);
                    
                    // Add direct onclick attribute as a fallback
                    newSelectBtn.setAttribute('onclick', "document.getElementById('audio-upload').click(); return false;");
                    
                    // Add event listener
                    newSelectBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        logDebug('🖱️ Select button clicked');
                        uploadInput.click();
                    });
                    
                    logDebug('✅ Button event listeners set up');
                } else {
                    logDebug('❌ Could not find select button or upload input');
                    // Try again after a short delay
                    setTimeout(setupSelectButton, 500);
                }
            }
            
            // Initial setup
            setupSelectButton();
        });
    </script>
    
    <!-- Enhanced Audio Upload Manager -->
    <script src="{{ asset('js/enhanced-audio-upload.js') }}"></script>
</body>
</html>
