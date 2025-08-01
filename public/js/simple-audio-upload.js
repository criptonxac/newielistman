/**
 * Simple Audio Upload Manager - FIXED VERSION
 * Minimal kod, maksimal samaradorlik
 */
class SimpleAudioUploadManager {
    constructor(options = {}) {
        this.config = {
            debug: true,
            uploadUrl: '/audio/upload',
            maxFileSize: 100 * 1024 * 1024, // 100MB
            allowedTypes: ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/webm', 'audio/m4a', 'audio/aac', 'audio/flac', 'audio/mp4'],
            allowedExtensions: ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'webm'],
            timeout: 10 * 60 * 1000, // 10 minutes
            retryAttempts: 2,
            // Chunked upload settings
            useChunkedUpload: false, // Disable chunked uploads to match simplified controller
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            minFileSize: 10 * 1024 * 1024, // Only chunk files larger than 10MB
            ...options
        };
        
        this.uploadQueue = [];
        this.currentUploads = 0;
        this.maxConcurrentUploads = 2;
        
        this.init();
    }
    
    init() {
        this.log('🎵 Simple Audio Upload Manager initialized');
        this.setupEvents();
        this.setupDragAndDrop();
        this.createProgressSection();
    }
    
    setupEvents() {
        // File input change
        const fileInput = document.getElementById('audio-upload');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    this.handleFileSelection(Array.from(e.target.files));
                }
            });
        }
        
        // Select button click
        const selectBtn = document.getElementById('selectFilesBtn');
        if (selectBtn) {
            selectBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (fileInput) fileInput.click();
            });
        }
        
        // Upload section click
        const uploadSection = document.getElementById('audioUploadSection');
        if (uploadSection) {
            uploadSection.addEventListener('click', (e) => {
                if (e.target === uploadSection && fileInput) {
                    fileInput.click();
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();
                if (fileInput) fileInput.click();
            }
        });
    }

    setupDragAndDrop() {
        const uploadSection = document.getElementById('audioUploadSection');
        if (!uploadSection) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadSection.addEventListener(eventName, this.preventDefaults, false);
            document.body.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadSection.addEventListener(eventName, () => {
                uploadSection.classList.add('drag-over');
                uploadSection.classList.add('drag-active');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadSection.addEventListener(eventName, () => {
                uploadSection.classList.remove('drag-over');
                uploadSection.classList.remove('drag-active');
            }, false);
        });

        uploadSection.addEventListener('drop', (e) => {
            const files = Array.from(e.dataTransfer.files);
            this.handleFileSelection(files);
        }, false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    handleFileSelection(files) {
        const validFiles = files.filter(file => this.validateFile(file));
        if (validFiles.length === 0) return;

        this.log(`📁 Selected ${validFiles.length} files for upload`);
        
        // Add files to upload queue
        validFiles.forEach(file => {
            this.uploadQueue.push({
                file: file,
                id: this.generateId(),
                status: 'pending',
                progress: 0,
                retryCount: 0
            });
        });

        this.processUploadQueue();
    }
    
    async processUploadQueue() {
        if (this.currentUploads >= this.maxConcurrentUploads || this.uploadQueue.length === 0) {
            return;
        }
        
        const fileData = this.uploadQueue.shift();
        this.currentUploads++;
        this.uploadFile(fileData);
        
        // Process more if available
        if (this.currentUploads < this.maxConcurrentUploads && this.uploadQueue.length > 0) {
            this.processUploadQueue();
        }
    }

    async uploadFile(fileData) {
        const { file, id } = fileData;
        this.log(`📤 Uploading: ${file.name} (${this.formatBytes(file.size)})`);
         // Log detailed file information
        this.log(`File details:`, {
            name: file.name,
            size: file.size,
            sizeFormatted: this.formatBytes(file.size),
            type: file.type,
            lastModified: new Date(file.lastModified).toISOString()
        });
        this.updateFileProgress(fileData, 'uploading', 0);
        
        try {
            // Determine if we should use chunked upload
            const useChunks = this.config.useChunkedUpload && file.size > this.config.minFileSize;
            
            if (useChunks) {
                this.log(`🧩 Using chunked upload for ${file.name} (${this.formatBytes(file.size)})`);
                return await this.uploadFileInChunks(fileData);
            }
            
            // Regular upload (no chunks)
            // Create form data
            const formData = new FormData();
            formData.append('audio_file', file);
            formData.append('_token', this.getCSRFToken());
            
            // Add test data
            const testId = document.querySelector('input[name="test_id"]')?.value;
            const partId = document.querySelector('input[name="part_id"]')?.value;
            if (testId) formData.append('test_id', testId);
            if (partId) formData.append('part_id', partId);
            
            this.log('📋 Form data prepared:', {
                fileName: file.name,
                fileSize: file.size,
                testId: testId,
                partId: partId
            });

            // Create XMLHttpRequest for progress tracking
            const xhr = new XMLHttpRequest();
            
            // Setup progress tracking
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    this.updateFileProgress(fileData, 'uploading', percentComplete);
                }
            });

            // Setup response handlers
            const uploadPromise = new Promise((resolve, reject) => {
                xhr.onreadystatechange = () => {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            try {
                                const result = JSON.parse(xhr.responseText);
                                resolve(result);
                            } catch (parseError) {
                                this.log('❌ JSON Parse Error:', parseError);
                                this.log('📄 Response Text:', xhr.responseText);
                                reject(new Error(`JSON Parse Error: ${parseError.message}. Response: ${xhr.responseText.substring(0, 200)}...`));
                            }
                        } else {
                            const errorMsg = `HTTP ${xhr.status}: ${xhr.statusText}`;
                            this.log('❌ HTTP Error:', errorMsg);
                            this.log('📄 Response Text:', xhr.responseText);
                            reject(new Error(errorMsg));
                        }
                    }
                };

                xhr.onerror = () => {
                    reject(new Error('Network error occurred'));
                };

                xhr.ontimeout = () => {
                    reject(new Error('Upload timeout'));
                };
            });

            // Configure and send request
            xhr.open('POST', this.config.uploadUrl, true);
            xhr.timeout = this.config.timeout;
            
            // Set headers
            const csrfToken = this.getCSRFToken();
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            // Send the request
            xhr.send(formData);
            
            // Wait for response
            const result = await uploadPromise;
            
            if (result.success) {
                this.log('✅ Upload successful:', result);
                this.updateFileProgress(fileData, 'success', 100);
                this.onUploadSuccess(result, fileData);
            } else {
                throw new Error(result.message || 'Upload failed');
            }
            
        } catch (error) {
            this.log('❌ Upload error:', error);
            
            // Retry logic
            if (fileData.retryCount < this.config.retryAttempts) {
                fileData.retryCount++;
                this.log(`🔄 Retrying upload (${fileData.retryCount}/${this.config.retryAttempts}): ${file.name}`);
                this.updateFileProgress(fileData, 'retrying', 0);
                
                // Add back to queue for retry
                setTimeout(() => {
                    this.uploadQueue.unshift(fileData);
                    this.processUploadQueue();
                }, 2000);
            } else {
                this.updateFileProgress(fileData, 'error', 0);
                this.showError(`${file.name} yuklashda xatolik: ${error.message}`);
            }
        } finally {
            this.currentUploads--;
            this.processUploadQueue(); // Process next files in queue
        }
    }
    
    validateFile(file) {
        // Check file type by MIME type
        if (!this.config.allowedTypes.includes(file.type)) {
            // Fallback: check by extension
            const extension = file.name.split('.').pop()?.toLowerCase();
            if (!extension || !this.config.allowedExtensions.includes(extension)) {
                this.showError(`${file.name} - Noto'g'ri fayl turi. Faqat audio fayllar qabul qilinadi. (${file.type})`);
                return false;
            }
        }
        
        // Check file size
        if (file.size > this.config.maxFileSize) {
            const maxSizeMB = (this.config.maxFileSize / (1024 * 1024)).toFixed(0);
            this.showError(`${file.name} - Fayl hajmi juda katta. Maksimal: ${maxSizeMB}MB (Sizniki: ${this.formatBytes(file.size)})`);
            return false;
        }
        
        if (file.size === 0) {
            this.showError(`${file.name} - Bo'sh fayl`);
            return false;
        }
        
        return true;
    }

    updateFileProgress(fileData, status, progress) {
        fileData.status = status;
        fileData.progress = progress;
        
        // Update progress section
        this.updateProgressDisplay(fileData);
    }
    
    /**
     * Upload a file in chunks
     * @param {Object} fileData - File data object containing file and id
     * @returns {Promise} - Promise that resolves when upload is complete
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
                this.updateFileProgress(fileData, 'uploading', percentComplete);
                
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
                
                this.log(`Chunk ${chunkIndex + 1}/${totalChunks} uploaded successfully`);
            }
            
            // All chunks uploaded, finalize the upload
            this.log(`All ${totalChunks} chunks uploaded, finalizing...`);
            const finalResult = await this.finalizeChunkedUpload({
                fileId,
                fileName: file.name,
                fileSize: file.size,
                totalChunks
            });
            
            if (finalResult.success) {
                this.log('✅ Chunked upload successful:', finalResult);
                this.updateFileProgress(fileData, 'success', 100);
                this.onUploadSuccess(finalResult, fileData);
                return finalResult;
            } else {
                throw new Error(finalResult.message || 'Finalize upload failed');
            }
            
        } catch (error) {
            this.log('❌ Chunked upload error:', error);
            throw error; // Let the main uploadFile method handle retries
        }
    }
    
    /**
     * Upload a single chunk
     * @param {Object} options - Chunk upload options
     * @returns {Promise} - Promise that resolves with the chunk upload result
     */
    async uploadChunk({ chunk, file, fileId, chunkIndex, totalChunks }) {
        // Create a File object from the chunk blob to give it a name
        const chunkFile = new File([chunk], file.name, { type: file.type });
        
        // Create form data for the chunk
        const formData = new FormData();
        // Use 'audio_file' as the key for the chunk file to match backend expectations
        formData.append('audio_file', chunkFile);
        formData.append('_token', this.getCSRFToken());
        formData.append('chunk_index', chunkIndex);
        formData.append('total_chunks', totalChunks);
        formData.append('file_id', fileId);
        formData.append('file_name', file.name);
        formData.append('file_size', file.size);
        
        // Add test data
        const testId = document.querySelector('input[name="test_id"]')?.value;
        const partId = document.querySelector('input[name="part_id"]')?.value;
        if (testId) formData.append('test_id', testId);
         if (partId) {
            formData.append("part_id", partId);
            formData.append("part", partId); // Add part parameter to match backend expectation
        } else {
            formData.append("part", "part1"); // Default part value
        }
        
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            resolve(result);
                        } catch (parseError) {
                            this.log('❌ JSON Parse Error:', parseError);
                            reject(new Error(`JSON Parse Error: ${parseError.message}`));
                        }
                    } else {
                        const errorMsg = `HTTP ${xhr.status}: ${xhr.statusText}`;
                        this.log('❌ HTTP Error:', errorMsg);
                         this.log("Response text:", xhr.responseText || "No response text");
                        this.log("Request details:", {
                            fileId,
                            chunkIndex,
                            totalChunks,
                            chunkSize: chunk.size,
                            fileName: file.name
                        });
                        reject(new Error(errorMsg));
                    }
                }
            };
            
            xhr.onerror = () => reject(new Error('Network error occurred'));
            xhr.ontimeout = () => reject(new Error('Upload timeout'));
            
            xhr.open('POST', this.config.uploadUrl, true);
            xhr.timeout = this.config.timeout;
            
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
     * @param {Object} options - Finalize options
     * @returns {Promise} - Promise that resolves with the finalize result
     */
    async finalizeChunkedUpload({ fileId, fileName, fileSize, totalChunks }) {
        const formData = new FormData();
        formData.append('_token', this.getCSRFToken());
        formData.append('file_id', fileId);
        formData.append('file_name', fileName);
        formData.append('file_size', fileSize);
        formData.append('total_chunks', totalChunks);
        
        // Add test data
        const testId = document.querySelector('input[name="test_id"]')?.value;
        const partId = document.querySelector('input[name="part_id"]')?.value;
        if (testId) formData.append('test_id', testId);
        if (partId) {
            formData.append('part_id', partId);
            formData.append('part', partId); // Add part parameter to match backend expectation
        } else {
            formData.append('part', 'part1'); // Default part value
        }
        
        this.log('📤 Finalizing chunked upload:', { fileId, fileName, fileSize, totalChunks, testId, partId });
        
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            resolve(result);
                        } catch (parseError) {
                            this.log('❌ JSON Parse Error:', parseError);
                            reject(new Error(`JSON Parse Error: ${parseError.message}`));
                        }
                    } else {
                        const errorMsg = `HTTP ${xhr.status}: ${xhr.statusText}`;
                        this.log('❌ HTTP Error:', errorMsg);
                         this.log("Response text:", xhr.responseText || "No response text");
                        this.log("Finalize details:", {
                            fileId,
                            fileName,
                            fileSize,
                            totalChunks,
                            testId: document.querySelector("input[name=\"test_id\"]")?.value,
                            partId: document.querySelector("input[name=\"part_id\"]")?.value
                        });
                        reject(new Error(errorMsg));
                    }
                }
            };
            
            xhr.onerror = () => reject(new Error('Network error occurred'));
            xhr.ontimeout = () => reject(new Error('Upload timeout'));
            
            xhr.open('POST', this.config.uploadUrl, true);
            xhr.timeout = this.config.timeout;
            
            // Set headers
            const csrfToken = this.getCSRFToken();
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.send(formData);
        });
    }

    createProgressSection() {
        let progressSection = document.getElementById('filesProgressSection');
        if (!progressSection) {
            // Create progress section if it doesn't exist
            const container = document.querySelector('.container');
            if (container) {
                const progressHTML = `
                    <div id="filesProgressSection" class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100" style="display: none;">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Fayl yuklash jarayoni
                            </h2>
                        </div>
                        <div id="progressContainer" class="p-6 space-y-4">
                        </div>
                    </div>
                `;
                
                // Insert before questions section
                const questionsSection = document.querySelector('.bg-white.shadow-lg.rounded-xl:last-of-type');
                if (questionsSection) {
                    questionsSection.insertAdjacentHTML('beforebegin', progressHTML);
                }
            }
        }
    }

    updateProgressDisplay(fileData) {
        const { file, id, status, progress } = fileData;
        
        // Show progress section
        const progressSection = document.getElementById('filesProgressSection');
        if (progressSection) {
            progressSection.style.display = 'block';
        }

        const progressContainer = document.getElementById('progressContainer');
        if (!progressContainer) return;

        let fileElement = document.getElementById(`progress-${id}`);
        
        if (!fileElement) {
            // Create new progress element
            fileElement = document.createElement('div');
            fileElement.id = `progress-${id}`;
            fileElement.className = 'file-item bg-gray-50 border border-gray-200 rounded-lg p-4';
            progressContainer.appendChild(fileElement);
        }

        // Status-based styling
        const statusConfig = {
            pending: { class: 'bg-gray-50 border-gray-200', color: 'text-gray-600', icon: '⏳' },
            uploading: { class: 'bg-blue-50 border-blue-200 uploading', color: 'text-blue-600', icon: '📤' },
            success: { class: 'bg-green-50 border-green-200 success', color: 'text-green-600', icon: '✅' },
            error: { class: 'bg-red-50 border-red-200 error', color: 'text-red-600', icon: '❌' },
            retrying: { class: 'bg-yellow-50 border-yellow-200', color: 'text-yellow-600', icon: '🔄' }
        };

        const config = statusConfig[status] || statusConfig.pending;
        fileElement.className = `file-item ${config.class} rounded-lg p-4 transition-all duration-300`;

        const progressBarClass = status === 'success' ? 'bg-green-500' : 
                                status === 'error' ? 'bg-red-500' : 
                                status === 'retrying' ? 'bg-yellow-500' : 'bg-blue-500';

        fileElement.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">${config.icon}</span>
                    <div>
                        <div class="font-medium ${config.color}">${file.name}</div>
                        <div class="text-sm text-gray-500">${this.formatBytes(file.size)} • ${this.getStatusText(status)}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="${config.color} font-semibold">${progress}%</div>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="progress-bar ${progressBarClass} h-2 rounded-full transition-all duration-300" style="width: ${progress}%"></div>
                </div>
            </div>
        `;
    }

    getStatusText(status) {
        const statusTexts = {
            pending: 'Kutilmoqda',
            uploading: 'Yuklanmoqda',
            success: 'Muvaffaqiyatli yuklandi',
            error: 'Xatolik yuz berdi',
            retrying: 'Qayta urinilmoqda'
        };
        return statusTexts[status] || status;
    }
    
    onUploadSuccess(result, fileData) {
        // Add hidden input for form submission
        if (result.data && result.data.url) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'audio_files[]';
            hiddenInput.value = result.data.url;
            
            const form = document.querySelector('form');
            if (form) {
                form.appendChild(hiddenInput);
            }
        }
        
        // Show file info in preview section
        this.showFileInfo(result.data);
        
        // Remove from progress after delay
        setTimeout(() => {
            const fileElement = document.getElementById(`progress-${fileData.id}`);
            if (fileElement) {
                fileElement.style.opacity = '0';
                setTimeout(() => {
                    fileElement.remove();
                    
                    // Hide progress section if empty
                    const progressContainer = document.getElementById('progressContainer');
                    if (progressContainer && progressContainer.children.length === 0) {
                        const progressSection = document.getElementById('filesProgressSection');
                        if (progressSection) {
                            progressSection.style.display = 'none';
                        }
                    }
                }, 300);
            }
        }, 3000);
    }
    
    showFileInfo(data) {
        let previewSection = document.getElementById('audioPreviewSection');
        
        if (!previewSection) {
            // Create preview section
            const container = document.querySelector('.container');
            if (container) {
                const previewHTML = `
                    <div id="audioPreviewSection" class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-100">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100">
                            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                </svg>
                                Yuklangan Audio Fayllar
                            </h2>
                        </div>
                        <div id="previewContainer" class="p-6 space-y-4">
                        </div>
                    </div>
                `;
                
                // Insert before questions section
                const questionsSection = document.querySelector('.bg-white.shadow-lg.rounded-xl:last-of-type');
                if (questionsSection) {
                    questionsSection.insertAdjacentHTML('beforebegin', previewHTML);
                    previewSection = document.getElementById('audioPreviewSection');
                }
            }
        }

        if (previewSection) {
            previewSection.style.display = 'block';
        }

        const previewContainer = document.getElementById('previewContainer');
        if (!previewContainer) return;
        
        const fileElement = document.createElement('div');
        fileElement.className = 'file-item bg-gradient-to-r from-green-50 to-white border border-green-200 rounded-lg p-4 hover:shadow-md transition-all duration-300';
        fileElement.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">${data.original_name}</div>
                        <div class="text-sm text-gray-600">
                            ${data.size_formatted} • ${data.duration_formatted || 'Audio fayl'} • ${data.extension.toUpperCase()}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Yuklangan: ${new Date(data.uploaded_at).toLocaleString('uz-UZ')}
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <audio controls class="w-48">
                        <source src="${data.full_url}" type="${data.mime_type}">
                        Brauzeringiz audio faylni qo'llab-quvvatlamaydi.
                    </audio>
                    <button class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-100 transition-colors" title="Muvaffaqiyatli yuklandi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        previewContainer.appendChild(fileElement);
        
        // Animate in
        fileElement.style.opacity = '0';
        fileElement.style.transform = 'translateY(20px)';
        setTimeout(() => {
            fileElement.style.transition = 'all 0.3s ease';
            fileElement.style.opacity = '1';
            fileElement.style.transform = 'translateY(0)';
        }, 100);
    }
    
    showSuccess(message) {
        if (typeof showNotification === 'function') {
            showNotification(message, 'success');
        } else {
            // Create custom notification
            this.showCustomNotification(message, 'success');
        }
    }
    
    showError(message) {
        if (typeof showNotification === 'function') {
            showNotification(message, 'error');
        } else {
            // Create custom notification
            this.showCustomNotification(message, 'error');
        }
    }

    showCustomNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? '✅' : '❌';
        
        notification.innerHTML = `
            <div class="${bgColor} text-white p-4 rounded-lg flex items-center space-x-3">
                <span class="text-xl">${icon}</span>
                <span class="flex-1">${message}</span>
                <button class="text-white hover:text-gray-200 ml-2" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
    
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               window.csrfToken || 
               document.querySelector('input[name="_token"]')?.value || '';
    }
    
    formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    generateId() {
        return 'file_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }
    
    log(...args) {
        if (this.config.debug) {
            console.log('🎵 [SimpleUpload]', ...args);
        }
    }
}

// Initialize
function initSimpleAudioUpload() {
    if (document.getElementById('audioUploadSection')) {
        window.simpleAudioUpload = new SimpleAudioUploadManager();
        console.log('✅ Simple Audio Upload initialized');
        return true;
    }
    return false;
}

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSimpleAudioUpload);
} else {
    initSimpleAudioUpload();
}