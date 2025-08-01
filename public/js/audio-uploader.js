/**
 * Audio Uploader - Simple and efficient audio file uploader
 * Created: 2025-08-01
 */
class AudioUploader {
    constructor(options = {}) {
        // Default configuration
        this.config = {
            debug: true,                                // Enable debug logging
            uploadUrl: '/audio/upload',                 // Main upload endpoint
            chunkUploadUrl: '/audio/upload/chunk',      // Chunk upload endpoint
            finalizeUrl: '/audio/upload/finalize',      // Finalize endpoint
            maxFileSize: 100 * 1024 * 1024,             // 100MB max file size
            allowedTypes: [                             // Allowed audio MIME types
                'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg',
                'audio/webm', 'audio/m4a', 'audio/aac', 'audio/flac', 'audio/mp4'
            ],
            allowedExtensions: [                        // Allowed file extensions
                'mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'webm'
            ],
            useChunkedUpload: true,                     // Enable chunked uploads
            chunkSize: 2 * 1024 * 1024,                 // 2MB chunk size
            minChunkFileSize: 5 * 1024 * 1024,          // Only chunk files larger than 5MB
            retryAttempts: 3,                           // Number of retry attempts
            retryDelay: 2000,                           // Delay between retries (ms)
            ...options                                  // Override with user options
        };

        // State
        this.uploadQueue = [];                          // Queue of files to upload
        this.activeUploads = 0;                         // Number of active uploads
        this.maxConcurrentUploads = 2;                  // Max concurrent uploads

        // Initialize
        this.init();
    }

    /**
     * Initialize the uploader
     */
    init() {
        this.log('🎵 Audio Uploader initialized');
        this.setupElements();
        this.setupEventListeners();
        this.createProgressContainer();
    }

    /**
     * Set up DOM elements
     */
    setupElements() {
        // Find upload elements
        this.elements = {
            uploadSection: document.getElementById('audioUploadSection'),
            fileInput: document.getElementById('audio-upload'),
            selectButton: document.getElementById('selectFilesBtn'),
            progressContainer: document.getElementById('audioProgressContainer'),
            fileList: document.getElementById('audioFileList')
        };

        // Create file input if it doesn't exist
        if (!this.elements.fileInput && this.elements.uploadSection) {
            this.elements.fileInput = document.createElement('input');
            this.elements.fileInput.type = 'file';
            this.elements.fileInput.id = 'audio-upload';
            this.elements.fileInput.name = 'audio_file';
            this.elements.fileInput.accept = this.config.allowedExtensions.map(ext => `.${ext}`).join(',');
            this.elements.fileInput.multiple = false;
            this.elements.fileInput.style.display = 'none';
            this.elements.uploadSection.appendChild(this.elements.fileInput);
        }

        this.log('🔍 Elements found:', {
            uploadSection: !!this.elements.uploadSection,
            fileInput: !!this.elements.fileInput,
            selectButton: !!this.elements.selectButton,
            progressContainer: !!this.elements.progressContainer,
            fileList: !!this.elements.fileList
        });
    }

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // File input change event
        if (this.elements.fileInput) {
            this.elements.fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    this.handleFileSelection(Array.from(e.target.files));
                }
            });
        }

        // Select button click event
        if (this.elements.selectButton) {
            this.elements.selectButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (this.elements.fileInput) {
                    this.elements.fileInput.click();
                }
            });
        }

        // Upload section click event
        if (this.elements.uploadSection) {
            this.elements.uploadSection.addEventListener('click', (e) => {
                // Only trigger if clicking directly on the upload section (not on buttons)
                if (e.target === this.elements.uploadSection && this.elements.fileInput) {
                    this.elements.fileInput.click();
                }
            });

            // Set up drag and drop
            this.setupDragAndDrop();
        }
    }

    /**
     * Set up drag and drop functionality
     */
    setupDragAndDrop() {
        const uploadSection = this.elements.uploadSection;
        if (!uploadSection) return;

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadSection.addEventListener(eventName, this.preventDefaults, false);
            document.body.addEventListener(eventName, this.preventDefaults, false);
        });

        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadSection.addEventListener(eventName, () => {
                uploadSection.classList.add('drag-over');
            }, false);
        });

        // Remove highlight when item is dragged out or dropped
        ['dragleave', 'drop'].forEach(eventName => {
            uploadSection.addEventListener(eventName, () => {
                uploadSection.classList.remove('drag-over');
            }, false);
        });

        // Handle dropped files
        uploadSection.addEventListener('drop', (e) => {
            const files = Array.from(e.dataTransfer.files);
            this.handleFileSelection(files);
        }, false);
    }

    /**
     * Prevent default drag and drop behaviors
     */
    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    /**
     * Create progress container if it doesn't exist
     */
    createProgressContainer() {
        if (!document.getElementById('audioProgressContainer')) {
            const container = document.createElement('div');
            container.id = 'audioProgressContainer';
            container.className = 'mt-4 space-y-4 hidden';

            const heading = document.createElement('h3');
            heading.className = 'text-lg font-medium text-gray-900';
            heading.textContent = 'Yuklanayotgan fayllar';
            container.appendChild(heading);

            const fileList = document.createElement('div');
            fileList.id = 'audioFileList';
            fileList.className = 'space-y-3';
            container.appendChild(fileList);

            // Insert after upload section
            if (this.elements.uploadSection) {
                this.elements.uploadSection.parentNode.insertBefore(
                    container,
                    this.elements.uploadSection.nextSibling
                );

                this.elements.progressContainer = container;
                this.elements.fileList = fileList;
            }
        }
    }

    /**
     * Handle file selection
     */
    handleFileSelection(files) {
        if (!files || files.length === 0) return;

        // Filter valid files
        const validFiles = files.filter(file => this.validateFile(file));
        if (validFiles.length === 0) return;

        this.log(`📂 Selected ${validFiles.length} valid files for upload`);

        // Add files to upload queue
        validFiles.forEach(file => {
            const fileId = this.generateId();
            this.uploadQueue.push({
                file,
                id: fileId,
                status: 'pending',
                progress: 0,
                retryCount: 0
            });

            // Create file element in UI
            this.createFileElement(file, fileId);
        });

        // Start processing the queue
        this.processUploadQueue();
    }

    /**
     * Validate file type and size
     */
    validateFile(file) {
        // Check file type by MIME type
        if (!this.config.allowedTypes.includes(file.type)) {
            // Fallback: check by extension
            const extension = file.name.split('.').pop()?.toLowerCase();
            if (!extension || !this.config.allowedExtensions.includes(extension)) {
                this.showError(`${file.name} - Noto'g'ri fayl turi. Faqat audio fayllar qabul qilinadi.`);
                return false;
            }
        }

        // Check file size
        if (file.size > this.config.maxFileSize) {
            const maxSizeMB = (this.config.maxFileSize / (1024 * 1024)).toFixed(0);
            this.showError(`${file.name} - Fayl hajmi juda katta. Maksimal: ${maxSizeMB}MB`);
            return false;
        }

        // Check for empty files
        if (file.size === 0) {
            this.showError(`${file.name} - Bo'sh fayl`);
            return false;
        }

        return true;
    }

    /**
     * Process upload queue
     */
    processUploadQueue() {
        if (this.uploadQueue.length === 0 || this.activeUploads >= this.maxConcurrentUploads) {
            return;
        }

        // Get next file from queue
        const fileData = this.uploadQueue.shift();
        this.activeUploads++;

        // Show progress container
        if (this.elements.progressContainer) {
            this.elements.progressContainer.classList.remove('hidden');
        }

        // Start upload
        this.uploadFile(fileData);

        // Process more files if available
        if (this.activeUploads < this.maxConcurrentUploads && this.uploadQueue.length > 0) {
            this.processUploadQueue();
        }
    }

    /**
     * Create file element in UI
     */
    createFileElement(file, fileId) {
        if (!this.elements.fileList) return;

        const fileElement = document.createElement('div');
        fileElement.id = `file-${fileId}`;
        fileElement.className = 'bg-white rounded-lg shadow p-4 border border-gray-200';

        fileElement.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="text-2xl">🎵</div>
                    <div>
                        <div class="font-medium text-gray-800">${file.name}</div>
                        <div class="text-sm text-gray-500">${this.formatBytes(file.size)}</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-blue-600">
                    Kutilmoqda...
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar-${fileId}" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        `;

        this.elements.fileList.appendChild(fileElement);
    }

    /**
     * Update file progress in UI
     */
    updateFileProgress(fileId, status, progress) {
        const fileElement = document.getElementById(`file-${fileId}`);
        if (!fileElement) return;

        const progressBar = document.getElementById(`progress-bar-${fileId}`);
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        // Status text and styling
        const statusConfig = {
            pending: { text: 'Kutilmoqda...', color: 'text-blue-600', bgColor: 'bg-blue-600' },
            uploading: { text: `Yuklanmoqda... ${progress}%`, color: 'text-blue-600', bgColor: 'bg-blue-600' },
            success: { text: 'Muvaffaqiyatli yuklandi', color: 'text-green-600', bgColor: 'bg-green-600' },
            error: { text: 'Xatolik yuz berdi', color: 'text-red-600', bgColor: 'bg-red-600' },
            retrying: { text: 'Qayta urinilmoqda...', color: 'text-yellow-600', bgColor: 'bg-yellow-600' }
        };

        const config = statusConfig[status] || statusConfig.pending;

        // Update status text
        const statusElement = fileElement.querySelector('.text-sm.font-medium');
        if (statusElement) {
            statusElement.className = `text-sm font-medium ${config.color}`;
            statusElement.textContent = config.text;
        }

        // Update progress bar color
        if (progressBar) {
            progressBar.className = `${config.bgColor} h-2 rounded-full transition-all duration-300`;
        }

        // Add success icon or remove file after delay
        if (status === 'success') {
            setTimeout(() => {
                fileElement.classList.add('opacity-50');
                setTimeout(() => {
                    fileElement.remove();

                    // Hide container if empty
                    if (this.elements.fileList && this.elements.fileList.children.length === 0) {
                        this.elements.progressContainer.classList.add('hidden');
                    }
                }, 3000);
            }, 2000);
        }
    }

    /**
     * Upload file - decides between regular and chunked upload
     */
    async uploadFile(fileData) {
        const { file, id } = fileData;
        this.log(`📤 Uploading: ${file.name} (${this.formatBytes(file.size)})`);

        try {
            this.updateFileProgress(id, 'uploading', 0);

            // Determine if we should use chunked upload
            const useChunks = this.config.useChunkedUpload &&
                              file.size > this.config.minChunkFileSize;

            let result;
            if (useChunks) {
                this.log(`🧩 Using chunked upload for ${file.name}`);
                result = await this.uploadFileInChunks(fileData);
            } else {
                this.log(`📄 Using regular upload for ${file.name}`);
                result = await this.uploadFileRegular(fileData);
            }

            if (result.success) {
                this.log(`✅ Upload successful: ${file.name}`);
                this.updateFileProgress(id, 'success', 100);
                this.onUploadSuccess(result, fileData);
            } else {
                throw new Error(result.message || 'Upload failed');
            }

        } catch (error) {
            this.log(`❌ Upload error: ${error.message}`);

            // Retry logic
            if (fileData.retryCount < this.config.retryAttempts) {
                fileData.retryCount++;
                this.log(`🔄 Retrying upload (${fileData.retryCount}/${this.config.retryAttempts}): ${file.name}`);
                this.updateFileProgress(id, 'retrying', 0);

                // Add back to queue for retry
                setTimeout(() => {
                    this.uploadQueue.unshift(fileData);
                    this.processUploadQueue();
                }, this.config.retryDelay);
            } else {
                this.updateFileProgress(id, 'error', 0);
                this.showError(`${file.name} yuklashda xatolik: ${error.message}`);
            }
        } finally {
            this.activeUploads--;
            this.processUploadQueue(); // Process next file in queue
        }
    }

    /**
     * Upload file using regular (non-chunked) method
     */
    async uploadFileRegular(fileData) {
        const { file, id } = fileData;

        return new Promise((resolve, reject) => {
            // Create form data
            const formData = new FormData();
            formData.append('audio_file', file);
            formData.append('_token', this.getCSRFToken());

            // Add test data if available
            const testId = document.querySelector('input[name="test_id"]')?.value;
            const partId = document.querySelector('input[name="part_id"]')?.value ||
                          document.querySelector('input[name="part"]')?.value;

            if (testId) formData.append('test_id', testId);
            if (partId) formData.append('part_id', partId);

            // Create and configure XHR
            const xhr = new XMLHttpRequest();

            // Track upload progress
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    this.updateFileProgress(id, 'uploading', percentComplete);
                }
            });

            // Handle response
            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            resolve(result);
                        } catch (error) {
                            reject(new Error(`JSON Parse Error: ${error.message}`));
                        }
                    } else {
                        reject(new Error(`HTTP Error: ${xhr.status} ${xhr.statusText}`));
                    }
                }
            };

            // Handle errors
            xhr.onerror = () => reject(new Error('Network error occurred'));
            xhr.ontimeout = () => reject(new Error('Upload timeout'));

            // Send request
            xhr.open('POST', this.config.uploadUrl, true);
            xhr.timeout = 5 * 60 * 1000; // 5 minutes

            // Set headers
            const csrfToken = this.getCSRFToken();
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.send(formData);
        });
    }

    /**
     * Upload file in chunks
     */
    async uploadFileInChunks(fileData) {
        const { file, id } = fileData;
        const chunkSize = this.config.chunkSize;
        const totalChunks = Math.ceil(file.size / chunkSize);
        const fileId = `${id}_${Date.now()}`;

        this.log(`🧩 Starting chunked upload: ${file.name}`);
        this.log(`Total chunks: ${totalChunks}, Chunk size: ${this.formatBytes(chunkSize)}`);

        try {
            // Upload each chunk sequentially
            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                // Update progress based on chunks completed
                const percentComplete = Math.round((chunkIndex / totalChunks) * 100);
                this.updateFileProgress(id, 'uploading', percentComplete);

                this.log(`Uploading chunk ${chunkIndex + 1}/${totalChunks} (${this.formatBytes(chunk.size)})`);

                // Upload this chunk
                const result = await this.uploadChunk({
                    chunk,
                    file,
                    fileId,
                    chunkIndex,
                    totalChunks
                });

                if (!result.success) {
                    throw new Error(`Chunk upload failed: ${result.message || 'Unknown error'}`);
                }
            }

            // All chunks uploaded, finalize the upload
            this.log(`All ${totalChunks} chunks uploaded, finalizing...`);
            return await this.finalizeChunkedUpload({
                fileId,
                fileName: file.name,
                fileSize: file.size,
                totalChunks
            });

        } catch (error) {
            this.log(`❌ Chunked upload error: ${error.message}`);
            throw error;
        }
    }

    /**
     * Upload a single chunk
     */
    async uploadChunk({ chunk, file, fileId, chunkIndex, totalChunks }) {
        // Create a File object from the chunk blob
        const chunkFile = new File([chunk], `chunk_${chunkIndex}.bin`, { type: file.type });

        // Create form data for the chunk
        const formData = new FormData();
        formData.append('chunk', chunkFile);
        formData.append('_token', this.getCSRFToken());
        formData.append('chunk_index', chunkIndex);
        formData.append('total_chunks', totalChunks);
        formData.append('file_id', fileId);
        formData.append('file_name', file.name);
        formData.append('file_size', file.size);

        // Add test data if available
        const testId = document.querySelector('input[name="test_id"]')?.value;
        const partId = document.querySelector('input[name="part_id"]')?.value ||
                      document.querySelector('input[name="part"]')?.value;

        if (testId) formData.append('test_id', testId);
        if (partId) formData.append('part_id', partId);

        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            resolve(result);
                        } catch (error) {
                            reject(new Error(`JSON Parse Error: ${error.message}`));
                        }
                    } else {
                        reject(new Error(`HTTP Error: ${xhr.status} ${xhr.statusText}`));
                    }
                }
            };

            xhr.onerror = () => reject(new Error('Network error occurred'));
            xhr.ontimeout = () => reject(new Error('Upload timeout'));

            xhr.open('POST', this.config.chunkUploadUrl, true);
            xhr.timeout = 2 * 60 * 1000; // 2 minutes per chunk

            // Set headers
            const csrfToken = this.getCSRFToken();
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.send(formData);
        });
    }

    /**
     * Finalize a chunked upload
     */
    async finalizeChunkedUpload({ fileId, fileName, fileSize, totalChunks }) {
        const formData = new FormData();
        formData.append('_token', this.getCSRFToken());
        formData.append('file_id', fileId);
        formData.append('file_name', fileName);
        formData.append('file_size', fileSize);
        formData.append('total_chunks', totalChunks);

        // Add test data if available
        const testId = document.querySelector('input[name="test_id"]')?.value;
        const partId = document.querySelector('input[name="part_id"]')?.value ||
                      document.querySelector('input[name="part"]')?.value;

        if (testId) formData.append('test_id', testId);
        if (partId) formData.append('part_id', partId);

        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            resolve(result);
                        } catch (error) {
                            reject(new Error(`JSON Parse Error: ${error.message}`));
                        }
                    } else {
                        reject(new Error(`HTTP Error: ${xhr.status} ${xhr.statusText}`));
                    }
                }
            };

            xhr.onerror = () => reject(new Error('Network error occurred'));
            xhr.ontimeout = () => reject(new Error('Upload timeout'));

            xhr.open('POST', this.config.finalizeUrl, true);
            xhr.timeout = 3 * 60 * 1000; // 3 minutes for finalization

            // Set headers
            const csrfToken = this.getCSRFToken();
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.send(formData);
        });
    }

    /**
     * Handle successful upload
     */
    onUploadSuccess(result, fileData) {
        if (!result.data) return;

        // Add hidden input for form submission
        if (result.data.url) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'audio_files[]';
            hiddenInput.value = result.data.url;

            const form = document.querySelector('form');
            if (form) {
                form.appendChild(hiddenInput);
            }
        }

        // Show success message
        this.showSuccess(`${fileData.file.name} muvaffaqiyatli yuklandi`);

        // Add to audio preview section if it exists
        this.addToAudioPreview(result.data);
    }

    /**
     * Add uploaded audio to preview section
     */
    addToAudioPreview(data) {
        // Check if preview section exists, create if not
        let previewSection = document.getElementById('audioPreviewSection');
        if (!previewSection) {
            previewSection = document.createElement('div');
            previewSection.id = 'audioPreviewSection';
            previewSection.className = 'mt-6 bg-white rounded-lg shadow p-4 border border-gray-200';

            const heading = document.createElement('h3');
            heading.className = 'text-lg font-medium text-gray-900 mb-4';
            heading.innerHTML = '<span class="mr-2">🎵</span> Yuklangan Audio Fayllar';
            previewSection.appendChild(heading);

            const audioList = document.createElement('div');
            audioList.id = 'audioList';
            audioList.className = 'space-y-3';
            previewSection.appendChild(audioList);

            // Insert after progress container
            if (this.elements.progressContainer) {
                this.elements.progressContainer.parentNode.insertBefore(
                    previewSection,
                    this.elements.progressContainer.nextSibling
                );
            }
        }

        // Add audio item to preview
        const audioList = document.getElementById('audioList');
        if (!audioList) return;

        const audioItem = document.createElement('div');
        audioItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200';

        audioItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">${data.original_name}</div>
                    <div class="text-sm text-gray-500">${data.size_formatted} • ${data.duration_formatted || 'Audio'}</div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <audio controls class="h-8">
                    <source src="${data.full_url}" type="${data.mime_type}">
                    Your browser does not support the audio element.
                </audio>
            </div>
        `;

        audioList.appendChild(audioItem);
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show notification
     */
    showNotification(message, type) {
        // Check if global notification function exists
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }

        // Create custom notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 max-w-sm transform transition-transform duration-300 ease-in-out';

        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? '✅' : '❌';

        notification.innerHTML = `
            <div class="${bgColor} text-white rounded-lg shadow-lg p-4 flex items-center">
                <span class="mr-2">${icon}</span>
                <span>${message}</span>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    /**
     * Get CSRF token
     */
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
               window.csrfToken ||
               document.querySelector('input[name="_token"]')?.value || '';
    }

    /**
     * Format bytes to human-readable format
     */
    formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Generate unique ID
     */
    generateId() {
        return 'file_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
    }

    /**
     * Log message to console if debug is enabled
     */
    log(...args) {
        if (this.config.debug) {
            console.log('🎵 [AudioUploader]', ...args);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the audio uploader
    window.audioUploader = new AudioUploader();
    console.log('✅ Audio Uploader initialized');
});
