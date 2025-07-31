/**
 * Enhanced Audio Upload Manager
 * Simplified version focused only on audio file uploads
 */
class EnhancedAudioUploadManager {
    /**
     * Constructor
     */
    constructor(options = {}) {
        // Default configuration
        this.config = {
            debug: true, // Always enable debug for troubleshooting
            uploadUrl: '/audio/upload',
            maxFiles: 10,
            maxFileSize: 100 * 1024 * 1024, // 100MB
            allowedTypes: ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/webm'],
            enableDragDrop: true,
            autoUpload: true,
            ...options
        };
        
        // Log configuration
        console.log('📝 Enhanced Audio Upload Manager Configuration:', this.config);

        // State
        this.uploadQueue = [];
        this.activeUploads = new Map();
        this.uploadedFiles = new Map();
        this.elements = {};
        this.aborted = false;
        this.isSelecting = false;
        
        // Initialize
        this.init();
    }
    
    /**
     * Initialize the upload manager
     */
    init() {
        this.log('🎵 Initializing Enhanced Audio Upload Manager...');
        this.getElements();
        
        // Debug element detection
        this.log('🔍 Elements found:', {
            uploadSection: !!this.elements.uploadSection,
            uploadInput: !!this.elements.uploadInput,
            progressSection: !!this.elements.progressSection,
            fileList: !!this.elements.fileList,
            selectBtn: !!this.elements.selectBtn
        });
        
        // Check required elements
        if (!this.elements.uploadSection) {
            this.log('⚠️ Upload section not found');
            return;
        }
        
        // Create upload input if it doesn't exist
        if (!this.elements.uploadInput) {
            this.log('⚠️ Upload input not found');
            this.createUploadInput();
        }
        
        // Create loading overlay if it doesn't exist
        this.createLoadingOverlay();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Setup drag and drop if enabled
        if (this.config.enableDragDrop) {
            this.setupDragDrop();
        }
        
        this.log('✅ Initialization complete');
    }
    
    /**
     * Create loading overlay
     */
    createLoadingOverlay() {
        // Check if overlay already exists
        if (document.getElementById('audio-upload-loading-overlay')) {
            return;
        }
        
        this.log('Creating loading overlay');
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.id = 'audio-upload-loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden';
        
        // Create spinner
        const spinner = document.createElement('div');
        spinner.className = 'inline-block h-16 w-16 animate-spin rounded-full border-4 border-solid border-white border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]';
        spinner.role = 'status';
        
        // Create text
        const text = document.createElement('div');
        text.className = 'text-white font-medium mt-4';
        text.id = 'audio-upload-loading-text';
        text.textContent = 'Fayl yuklanmoqda...';
        
        // Assemble overlay
        const container = document.createElement('div');
        container.className = 'text-center';
        container.appendChild(spinner);
        container.appendChild(text);
        overlay.appendChild(container);
        
        // Add to body
        document.body.appendChild(overlay);
        
        this.log('Loading overlay created');
    }
    
    /**
     * Show loading indicator for file selection
     */
    showSelectingIndicator() {
        this.log('Showing file selection indicator');
        const overlay = document.getElementById('audio-upload-loading-overlay');
        const text = document.getElementById('audio-upload-loading-text');
        
        if (overlay && text) {
            text.textContent = 'Fayl tanlanmoqda...';
            overlay.classList.remove('hidden');
            
            // Auto-hide after 5 seconds if no file was selected
            setTimeout(() => {
                if (this.isSelecting) {
                    this.log('File selection timeout - no file selected');
                    this.isSelecting = false;
                    this.hideLoadingIndicator();
                    
                    // Show notification if available
                    if (typeof showNotification === 'function') {
                        showNotification('Fayl tanlanmadi', 'warning');
                    }
                }
            }, 5000);
        }
    }
    
    /**
     * Show loading indicator for upload
     */
    showLoadingIndicator(message = 'Fayl yuklanmoqda...') {
        this.log('Showing loading indicator: ' + message);
        const overlay = document.getElementById('audio-upload-loading-overlay');
        const text = document.getElementById('audio-upload-loading-text');
        
        if (overlay && text) {
            text.textContent = message;
            overlay.classList.remove('hidden');
        }
    }
    
    /**
     * Hide loading indicator
     */
    hideLoadingIndicator() {
        this.log('Hiding loading indicator');
        const overlay = document.getElementById('audio-upload-loading-overlay');
        
        if (overlay) {
            overlay.classList.add('hidden');
        }
    }
    
    /**
     * Create upload input if it doesn't exist
     */
    createUploadInput() {
        if (!this.elements.uploadSection) return;
        
        this.log('🔧 Creating upload input element');
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'audio-upload';
        input.className = 'hidden';
        input.accept = 'audio/*';
        input.multiple = true;
        
        this.elements.uploadSection.appendChild(input);
        this.elements.uploadInput = input;
        this.log('✅ Upload input created');
    }
    
    /**
     * Get DOM elements
     */
    getElements() {
        this.elements = {
            uploadSection: document.getElementById('audioUploadSection'),
            uploadInput: document.getElementById('audio-upload'),
            progressSection: document.getElementById('filesProgressSection'),
            fileList: document.getElementById('audioPreviewSection'),
            selectBtn: document.getElementById('selectFilesBtn')
        };
    }
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Track if file selection is in progress to prevent multiple dialogs
        this.isSelecting = false;
        
        // File input change
        if (this.elements.uploadInput) {
            this.log('Setting up file input change listener');
            this.elements.uploadInput.addEventListener('change', (e) => {
                this.log('File input change event triggered');
                
                // Reset selection flag immediately
                this.isSelecting = false;
                
                // Hide selection indicator
                this.hideLoadingIndicator();
                
                if (e.target.files.length > 0) {
                    this.log(`Selected ${e.target.files.length} files`);
                    
                    // Show upload loading indicator
                    this.showLoadingIndicator(`${e.target.files[0].name} yuklanmoqda...`);
                    
                    // Handle the files
                    this.handleFiles(e.target.files);
                } else {
                    this.log('No files selected');
                }
            });
        } else {
            this.log('Warning: Could not set up file input change listener - element not found');
        }

        // Select button - with improved reliability
        if (this.elements.selectBtn) {
            this.log('Setting up select button click listener');
            
            // Remove existing listeners by cloning
            const newBtn = this.elements.selectBtn.cloneNode(true);
            this.elements.selectBtn.parentNode.replaceChild(newBtn, this.elements.selectBtn);
            this.elements.selectBtn = newBtn;
            
            // Add new listener
            this.elements.selectBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.log('Select button clicked');
                
                // Prevent multiple dialogs
                if (this.isSelecting) {
                    this.log('File selection already in progress, ignoring click');
                    return;
                }
                
                // Set selecting flag
                this.isSelecting = true;
                
                // Get the input element directly to ensure we have the latest reference
                const uploadInput = document.getElementById('audio-upload');
                if (uploadInput) {
                    this.log('Triggering click on upload input');
                    uploadInput.click();
                    
                    // Show loading indicator while waiting for file selection
                    this.showSelectingIndicator();
                } else {
                    this.log('Error: Upload input element not found');
                    // Create input if missing
                    this.createUploadInput();
                    // Try again
                    const newInput = document.getElementById('audio-upload');
                    if (newInput) {
                        this.log('Created new input and triggering click');
                        newInput.click();
                        
                        // Show loading indicator while waiting for file selection
                        this.showSelectingIndicator();
                    } else {
                        // Reset selecting flag if failed
                        this.isSelecting = false;
                    }
                }
            });
            
            // Also add a direct onclick attribute as a fallback, but with selection check
            this.elements.selectBtn.setAttribute('onclick', `
                if(window.audioUploadIsSelecting) return false; 
                window.audioUploadIsSelecting = true; 
                const overlay = document.getElementById('audio-upload-loading-overlay');
                const text = document.getElementById('audio-upload-loading-text');
                if (overlay && text) {
                    text.textContent = 'Fayl tanlanmoqda...';
                    overlay.classList.remove('hidden');
                }
                document.getElementById('audio-upload')?.click(); 
                event.preventDefault(); 
                event.stopPropagation(); 
                return false;
            `);
        } else {
            this.log('Warning: Could not set up select button click listener - element not found');
            // Try to find the button with a more flexible approach
            const possibleButtons = document.querySelectorAll('button');
            for (const btn of possibleButtons) {
                if (btn.textContent.includes('Fayllarni tanlash') || btn.id === 'selectFilesBtn') {
                    this.log('Found select button with alternative method');
                    this.elements.selectBtn = btn;
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        document.getElementById('audio-upload')?.click();
                    });
                    break;
                }
            }
        }

        // Upload section click
        if (this.elements.uploadSection && this.config.enableDragDrop) {
            this.log('Setting up upload section click listener');
            this.elements.uploadSection.addEventListener('click', (e) => {
                // Only trigger if clicking on the section itself, not on buttons
                if (e.target === this.elements.uploadSection || 
                    (e.target.closest('#audioUploadSection') && !e.target.closest('button'))) {
                    this.log('Upload section clicked');
                    
                    // Prevent multiple dialogs
                    if (this.isSelecting) {
                        this.log('File selection already in progress, ignoring click');
                        return;
                    }
                    
                    // Set selecting flag
                    this.isSelecting = true;
                    
                    // Get the input element directly to ensure we have the latest reference
                    const uploadInput = document.getElementById('audio-upload');
                    if (uploadInput) {
                        uploadInput.click();
                        // Show loading indicator while waiting for file selection
                        this.showSelectingIndicator();
                    } else {
                        this.log('Error: Upload input element not found on section click');
                        // Reset selecting flag if failed
                        this.isSelecting = false;
                    }
                }
            });
        } else {
            this.log('Warning: Could not set up upload section click listener - element not found');
        }

        // Keyboard shortcuts - only for upload
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'u') {
                    e.preventDefault();
                    this.log('Keyboard shortcut Ctrl+U pressed');
                    
                    // Get the input element directly to ensure we have the latest reference
                    const uploadInput = document.getElementById('audio-upload');
                    if (uploadInput) {
                        this.log('Triggering click on upload input from keyboard shortcut');
                        uploadInput.click();
                    } else {
                        this.log('Error: Upload input element not found from keyboard shortcut');
                    }
                }
            }
        });
    }
    
    /**
     * Setup drag and drop
     */
    setupDragDrop() {
        if (!this.elements.uploadSection) return;
        
        const dropZone = this.elements.uploadSection;
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        
        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('drag-active');
            }, false);
        });
        
        // Remove highlight when item is dragged out or dropped
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('drag-active');
            }, false);
        });
        
        // Handle dropped files
        dropZone.addEventListener('drop', (e) => {
            this.log('Files dropped');
            if (e.dataTransfer.files.length > 0) {
                this.handleFiles(e.dataTransfer.files);
            }
        }, false);
    }
    
    /**
     * Handle files selected or dropped
     */
    handleFiles(files) {
        this.log(`📂 ${files.length} files selected`);
        
        // Convert FileList to Array
        const fileArray = Array.from(files);
        
        // Filter files
        const validFiles = fileArray.filter(file => this.validateFile(file));
        
        if (validFiles.length === 0) {
            this.log('⚠️ No valid files found');
            // Hide loading indicator if no valid files
            this.hideLoadingIndicator();
            
            // Show error notification
            if (typeof showNotification === 'function') {
                showNotification('Hech qanday to\'g\'ri formatdagi fayl topilmadi', 'error');
            }
            return;
        }
        
        // Show progress section
        this.showElement(this.elements.progressSection);
        
        // Add to queue
        validFiles.forEach(file => {
            this.uploadQueue.push(file);
        });
        
        // Trigger event
        this.trigger('filesSelected', { files: validFiles });
        
        // Start upload if auto upload is enabled
        if (this.config.autoUpload) {
            this.processUploadQueue();
        } else {
            // Hide loading indicator if not auto uploading
            this.hideLoadingIndicator();
        }
    }
    
    /**
     * Validate file
     */
    validateFile(file) {
        // Check file type
        if (!this.config.allowedTypes.includes(file.type)) {
            this.log(`❌ Invalid file type: ${file.type}`);
            this.showMessage(`❌ ${file.name} - Noto'g'ri fayl turi`, 'error');
            return false;
        }
        
        // Check file size
        if (file.size > this.config.maxFileSize) {
            this.log(`❌ File too large: ${this.formatBytes(file.size)}`);
            this.showMessage(`❌ ${file.name} - Fayl hajmi juda katta`, 'error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Process upload queue
     */
    processUploadQueue() {
        if (this.uploadQueue.length === 0) {
            // Hide progress section if no active uploads
            if (this.activeUploads.size === 0) {
                this.hideElement(this.elements.progressSection);
            }
            return;
        }
        
        // Get next file from queue
        const file = this.uploadQueue.shift();
        const fileId = `file_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        // Create file element
        const fileElement = this.createFileElement(file, fileId);
        this.elements.fileList?.appendChild(fileElement);
        
        // Upload file
        this.uploadFile(file, fileId);
    }
    
    /**
     * Upload file directly
     */
    uploadFile(file, fileId) {
        this.log(`📤 Starting upload for ${file.name} (${this.formatBytes(file.size)})`);
        
        // Show loading indicator
        this.showLoadingIndicator(`${file.name} yuklanmoqda...`);
        
        // Create form data
        const formData = new FormData();
        formData.append('audio', file);
        
        // Add CSRF token if available
        const csrfToken = this.getCSRFToken();
        if (csrfToken) {
            formData.append('_token', csrfToken);
            this.log('✅ CSRF token added to request');
        } else {
            this.log('⚠️ Warning: No CSRF token found');
            // Try to get it from the page directly as a fallback
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                const token = metaToken.getAttribute('content');
                if (token) {
                    formData.append('_token', token);
                    this.log('✅ CSRF token found in meta tag');
                }
            }
        }
        
        // Add test ID and part if available
        const testId = document.querySelector('input[name="test_id"]')?.value;
        const partId = document.querySelector('input[name="part_id"]')?.value;
        
        if (testId) {
            formData.append('test_id', testId);
            this.log(`✅ Test ID added: ${testId}`);
        }
        
        if (partId) {
            formData.append('part_id', partId);
            this.log(`✅ Part ID added: ${partId}`);
        }
        
        // Create XHR
        const xhr = new XMLHttpRequest();
        
        // Track upload
        this.activeUploads.set(fileId, { xhr, file, startTime: Date.now() });
        this.log(`🔾 Upload tracked with ID: ${fileId}`);
        
        // Setup event listeners
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                this.updateProgress(fileId, percent, e.loaded, e.total);
                
                // Calculate and display upload speed
                const uploadInfo = this.activeUploads.get(fileId);
                if (uploadInfo) {
                    const elapsedSeconds = (Date.now() - uploadInfo.startTime) / 1000;
                    if (elapsedSeconds > 0) {
                        const bytesPerSecond = e.loaded / elapsedSeconds;
                        const speedText = this.formatBytes(bytesPerSecond) + '/s';
                        
                        // Update speed in UI
                        const fileElement = document.getElementById(fileId);
                        if (fileElement) {
                            const speedElement = fileElement.querySelector('.upload-speed');
                            if (speedElement) {
                                speedElement.textContent = speedText;
                            }
                        }
                        
                        // Log progress occasionally (not on every update)
                        if (percent % 10 === 0 || percent === 100) {
                            this.log(`📊 Upload progress: ${percent}% (${speedText})`);
                        }
                    }
                }
            }
        });
        
        xhr.addEventListener('load', () => {
            this.activeUploads.delete(fileId);
            this.log(`📥 Upload completed with status: ${xhr.status}`);
            
            // Hide loading indicator
            this.hideLoadingIndicator();
            
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    this.log('✅ Upload successful, response:', response);
                    this.onUploadSuccess(fileId, file, response);
                    
                    // Show success notification if available
                    if (typeof showNotification === 'function') {
                        showNotification(`${file.name} yuklandi`, 'success');
                    }
                } catch (error) {
                    this.log('❌ Response parsing error:', error);
                    this.onUploadError(fileId, { message: 'Response parsing error' });
                    
                    // Show error notification if available
                    if (typeof showNotification === 'function') {
                        showNotification('Serverdan javob olishda xatolik', 'error');
                    }
                }
            } else {
                let errorMessage = `Server error: ${xhr.status}`;
                
                // Try to parse error message from response
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Ignore parsing error
                }
                
                this.log(`❌ Upload failed: ${errorMessage}`);
                this.onUploadError(fileId, { message: errorMessage });
                
                // Show error notification if available
                if (typeof showNotification === 'function') {
                    showNotification(`Yuklashda xatolik: ${errorMessage}`, 'error');
                }
            }
            
            // Process next in queue
            this.processUploadQueue();
        });
        
        xhr.addEventListener('error', () => {
            this.activeUploads.delete(fileId);
            this.log('❌ Network error during upload');
            
            // Hide loading indicator
            this.hideLoadingIndicator();
            
            this.onUploadError(fileId, { message: 'Network error' });
            
            // Show error notification if available
            if (typeof showNotification === 'function') {
                showNotification('Tarmoq xatosi yuz berdi', 'error');
            }
            
            this.processUploadQueue();
        });
        
        xhr.addEventListener('abort', () => {
            this.activeUploads.delete(fileId);
            this.log('⛔ Upload aborted');
            
            // Hide loading indicator
            this.hideLoadingIndicator();
            
            // Show abort notification if available
            if (typeof showNotification === 'function') {
                showNotification(`${file.name} yuklash bekor qilindi`, 'warning');
            }
            
            this.processUploadQueue();
        });
        
        // Send request
        try {
            xhr.open('POST', this.config.uploadUrl);
            this.log(`🔗 XHR opened to ${this.config.uploadUrl}`);
            xhr.send(formData);
            this.log('📣 XHR request sent');
        } catch (error) {
            this.log('❌ Error sending XHR request:', error);
            this.activeUploads.delete(fileId);
            
            // Hide loading indicator
            this.hideLoadingIndicator();
            
            this.onUploadError(fileId, { message: error.message || 'Error sending request' });
            this.processUploadQueue();
            
            // Show error notification if available
            if (typeof showNotification === 'function') {
                showNotification('So\'rov yuborishda xatolik', 'error');
            }
        }
    }
    
    /**
     * Get CSRF token from meta tag or global variable
     */
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               window.csrfToken || 
               null;
    }
    
    /**
     * Create file element in UI
     */
    createFileElement(file, fileId) {
        const element = document.createElement('div');
        element.id = fileId;
        element.className = 'file-item bg-white p-4 rounded-lg shadow-sm border border-gray-200 transition-all duration-300 hover:shadow-md';
        element.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 truncate" title="${file.name}">
                        <span class="file-icon mr-2">🎵</span>
                        ${file.name}
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        ${this.formatBytes(file.size)} • ${file.type || 'Audio file'}
                    </div>
                </div>
                <div class="flex items-center gap-3 ml-4">
                    <div class="status-indicator text-sm font-medium text-blue-600">
                        <div class="flex items-center">
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Yuklanmoqda...
                        </div>
                    </div>
                    <button class="cancel-btn p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Bekor qilish">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span class="progress-text">0%</span>
                    <span class="upload-speed"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="progress-bar bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        `;
        
        // Add cancel button event listener
        const cancelBtn = element.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.cancelUpload(fileId);
            });
        }
        
        return element;
    }
    
    /**
     * Update progress in UI
     */
    updateProgress(fileId, percent, loaded, total) {
        const fileElement = document.getElementById(fileId);
        if (!fileElement) return;
        
        // Update progress bar
        const progressBar = fileElement.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${percent}%`;
        }
        
        // Update progress text
        const progressText = fileElement.querySelector('.progress-text');
        if (progressText) {
            progressText.textContent = `${percent}%`;
        }
    }
    
    /**
     * Cancel upload
     */
    cancelUpload(fileId) {
        this.log(`🚫 Cancelling upload: ${fileId}`);
        
        const upload = this.activeUploads.get(fileId);
        if (upload && upload.xhr) {
            upload.xhr.abort();
            this.activeUploads.delete(fileId);
            
            // Update UI
            const fileElement = document.getElementById(fileId);
            if (fileElement) {
                const statusIndicator = fileElement.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.innerHTML = `
                        <div class="flex items-center text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Bekor qilindi
                        </div>
                    `;
                }
            }
            
            // Process next in queue
            this.processUploadQueue();
        }
    }
    
    /**
     * Handle successful upload
     */
    onUploadSuccess(fileId, file, response) {
        this.log(`✅ Upload success: ${file.name}`);
        
        // Update UI
        const fileElement = document.getElementById(fileId);
        if (fileElement) {
            const statusIndicator = fileElement.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.className = 'status-indicator text-sm font-medium text-green-600';
                statusIndicator.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Yuklandi
                    </div>
                `;
            }
            
            // Remove cancel button
            const cancelBtn = fileElement.querySelector('.cancel-btn');
            if (cancelBtn) {
                cancelBtn.remove();
            }
            
            // Add success class
            fileElement.classList.add('border-green-200', 'bg-green-50');
            
            // Show file URL if available
            if (response && response.data && response.data.url) {
                // Create hidden input with file URL
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'audio_files[]';
                hiddenInput.value = response.data.url;
                
                // Add to form if exists
                const form = document.querySelector('form');
                if (form) {
                    form.appendChild(hiddenInput);
                    this.log(`📎 Added hidden input with file URL: ${response.data.url}`);
                } else {
                    // Add to file element if no form found
                    fileElement.appendChild(hiddenInput);
                }
                
                // Add file info to element
                const fileInfo = document.createElement('div');
                fileInfo.className = 'text-xs text-gray-500 mt-2';
                fileInfo.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span>URL: ${response.data.url.split('/').pop()}</span>
                        ${response.data.duration ? `<span>Duration: ${this.formatTime(response.data.duration)}</span>` : ''}
                    </div>
                `;
                fileElement.appendChild(fileInfo);
            }
        }
        
        // Store uploaded file info
        this.uploadedFiles.set(fileId, {
            file,
            response,
            url: response.data?.url,
            timestamp: Date.now()
        });
        
        // Trigger event
        this.trigger('uploadSuccess', { fileId, file, response });
    }
    
    /**
     * Format time in seconds to MM:SS format
     */
    formatTime(seconds) {
        if (!seconds) return '00:00';
        seconds = Math.round(parseFloat(seconds));
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    /**
     * Handle upload error
     */
    onUploadError(fileId, error) {
        this.log(`❌ Upload error for ${fileId}:`, error);
        
        const fileElement = document.getElementById(fileId);
        if (!fileElement) return;
        
        // Update UI
        const statusIndicator = fileElement.querySelector('.status-indicator');
        if (statusIndicator) {
            statusIndicator.innerHTML = `
                <div class="flex items-center text-red-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Xatolik
                </div>
            `;
        }
        
        // Add error class
        fileElement.classList.add('border-red-200', 'bg-red-50');
        
        // Add error message
        const errorMsg = document.createElement('div');
        errorMsg.className = 'text-xs text-red-600 mt-2';
        errorMsg.textContent = error.message || 'Unknown error';
        fileElement.appendChild(errorMsg);
        
        // Trigger event
        this.trigger('uploadError', { fileId, error });
    }
    
    /**
     * Format bytes to human readable format
     */
    formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    /**
     * Show element
     */
    showElement(element) {
        if (element) {
            element.style.display = 'block';
        }
    }
    
    /**
     * Hide element
     */
    hideElement(element) {
        if (element) {
            element.style.display = 'none';
        }
    }
    
    /**
     * Log message if debug is enabled
     */
    log(...args) {
        if (this.config.debug) {
            console.log('🎵 [AudioUpload]', ...args);
        }
    }
    
    /**
     * Show message in UI or console
     */
    showMessage(message, type = 'info') {
        this.log(`Message (${type}): ${message}`);
        
        // Check if showNotification function exists
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        }
    }
    
    /**
     * Event system
     */
    on(event, callback) {
        if (!this._events) this._events = {};
        if (!this._events[event]) this._events[event] = [];
        this._events[event].push(callback);
    }
    
    /**
     * Trigger event
     */
    trigger(event, data = {}) {
        if (!this._events) return;
        if (!this._events[event]) return;
        
        this._events[event].forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error(`Error in event handler for ${event}:`, error);
            }
        });
    }
}

// Function to initialize the audio upload manager
function initAudioUploadManager() {
    console.log('🔍 Initializing Audio Upload Manager');
    const uploadSection = document.getElementById('audioUploadSection');
    console.log('🔍 audioUploadSection found:', !!uploadSection);
    
    if (uploadSection) {
        try {
            console.log('🔍 Creating EnhancedAudioUploadManager instance');
            window.audioUploadManager = new EnhancedAudioUploadManager({
                debug: true,
                enableDragDrop: true,
                autoUpload: true
            });
            console.log('✅ EnhancedAudioUploadManager initialized successfully');
            return true;
        } catch (error) {
            console.error('Error initializing EnhancedAudioUploadManager:', error);
            return false;
        }
    }
    return false;
}

// Try to initialize immediately if the DOM is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    console.log('🔍 Document already loaded, initializing immediately');
    initAudioUploadManager();
}

// Also initialize on DOMContentLoaded for safety
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DOMContentLoaded event fired');
    if (!window.audioUploadManager) {
        initAudioUploadManager();
    }
});

// Final fallback - try again after a short delay
setTimeout(function() {
    if (!window.audioUploadManager) {
        console.log('🔍 Delayed initialization attempt');
        initAudioUploadManager();
    }
}, 1000);
