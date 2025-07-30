/**
 * Enhanced Audio Upload System
 * Version: 2.2 - Fixed upload functionality
 */

class EnhancedAudioUpload {
    constructor() {
        this.uploadedFiles = [];
        this.maxFileSize = 100 * 1024 * 1024; // 100MB
        this.supportedFormats = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
        this.currentlyPlaying = null;
        this.uploadQueue = [];
        this.isInitialized = false;
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        console.log('🎵 Enhanced Audio Upload System initializing...');
        
        // Check if elements exist
        const uploadSection = document.getElementById('audioUploadSection');
        const uploadInput = document.getElementById('audio-upload');
        
        if (!uploadSection || !uploadInput) {
            console.warn('⚠️ Audio upload elements not found, skipping initialization');
            return;
        }
        
        this.setupEventListeners();
        this.createRequiredElements();
        this.isInitialized = true;
        
        console.log('✅ Enhanced Audio Upload System initialized successfully');
    }

    createRequiredElements() {
        // Create progress section if it doesn't exist
        if (!document.getElementById('filesProgressSection')) {
            const progressSection = document.createElement('div');
            progressSection.className = 'files-progress-section';
            progressSection.id = 'filesProgressSection';
            progressSection.style.display = 'none';
            
            const heading = document.createElement('h3');
            heading.textContent = '📊 Yuklash jarayoni';
            progressSection.appendChild(heading);
            
            const filesList = document.createElement('div');
            filesList.id = 'filesList';
            progressSection.appendChild(filesList);
            
            const uploadSection = document.getElementById('audioUploadSection');
            if (uploadSection) {
                uploadSection.parentNode.insertBefore(progressSection, uploadSection.nextSibling);
            }
        }
        
        // Create preview section if it doesn't exist
        if (!document.getElementById('audioPreviewSection')) {
            const previewSection = document.createElement('div');
            previewSection.className = 'audio-preview-section';
            previewSection.id = 'audioPreviewSection';
            previewSection.style.display = 'none';
            
            const heading = document.createElement('h3');
            heading.textContent = '🎧 Yuklangan Audio Fayllar';
            previewSection.appendChild(heading);
            
            const previewList = document.createElement('div');
            previewList.id = 'audioPreviewList';
            previewSection.appendChild(previewList);
            
            const progressSection = document.getElementById('filesProgressSection');
            if (progressSection) {
                progressSection.parentNode.insertBefore(previewSection, progressSection.nextSibling);
            }
        }
    }

    setupEventListeners() {
        const uploadInput = document.getElementById('audio-upload');
        const uploadSection = document.getElementById('audioUploadSection');

        if (!uploadInput || !uploadSection) {
            console.error('❌ Required upload elements not found');
            return;
        }

        // File input change
        uploadInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFiles(e.target.files);
            }
        });

        // Drag and drop events
        uploadSection.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadSection.classList.add('drag-over');
        });

        uploadSection.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadSection.classList.remove('drag-over');
        });

        uploadSection.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadSection.classList.remove('drag-over');
            
            if (e.dataTransfer.files.length > 0) {
                this.handleFiles(e.dataTransfer.files);
            }
        });

        // Click to select files
        uploadSection.addEventListener('click', () => {
            uploadInput.click();
        });
    }

    handleFiles(files) {
        console.log(`📂 Handling ${files.length} files`);
        
        if (files.length === 0) return;
        
        this.showProgressSection();
        
        // Filter and add valid files to queue
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            if (this.validateFile(file)) {
                this.uploadQueue.push(file);
                console.log(`✅ Added to queue: ${file.name}`);
            }
        }
        
        // Start processing if not already in progress
        if (this.uploadQueue.length > 0) {
            this.processUploadQueue();
        }
    }

    validateFile(file) {
        console.log(`🔎 Validating file: ${file.name}, size: ${(file.size / 1024 / 1024).toFixed(2)}MB`);
        
        // Check file type
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!this.supportedFormats.includes(fileExtension)) {
            this.showMessage(`❌ ${file.name}: Qo'llab-quvvatlanmaydigan format!`, 'error');
            return false;
        }

        // Check file size
        if (file.size > this.maxFileSize) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            this.showMessage(`❌ ${file.name}: Fayl hajmi ${sizeMB}MB. Maksimal 100MB!`, 'error');
            return false;
        }

        // Check if file is actually an audio file
        if (!file.type.startsWith('audio/')) {
            this.showMessage(`❌ ${file.name}: Bu audio fayl emas!`, 'error');
            return false;
        }

        console.log(`✅ File validation passed: ${file.name}`);
        return true;
    }

    async processUploadQueue() {
        if (this.uploadQueue.length === 0) {
            console.log('📭 Upload queue is empty');
            return;
        }

        const file = this.uploadQueue.shift();
        const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substring(2);
        
        console.log(`🚀 Processing file: ${file.name} with ID: ${fileId}`);
        
        try {
            await this.uploadFile(file, fileId);
            
            // Process next file in queue
            if (this.uploadQueue.length > 0) {
                setTimeout(() => this.processUploadQueue(), 500);
            }
        } catch (error) {
            console.error('❌ Error processing file:', error);
            this.onUploadError(fileId, error);
        }
    }

    uploadFile(file, fileId) {
        return new Promise((resolve, reject) => {
            console.log(`📤 Starting upload for ${file.name} (${fileId})`);
            
            // Create file item in the UI
            const fileItem = this.createFileItem(file, fileId);
            const filesList = document.getElementById('filesList');
            
            if (!filesList) {
                reject(new Error('Files list container not found'));
                return;
            }
            
            filesList.appendChild(fileItem);
            
            // Create FormData for actual upload
            const formData = new FormData();
            formData.append('audio_file', file);
            formData.append('part', fileId.split('_')[1] || 'part1'); // Extract part number from fileId
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value;
            
            if (csrfToken) {
                // Use fetch API for upload with proper headers
                fetch('/upload-audio', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Upload successful:', data);
                    this.onUploadComplete(file, fileId, data);
                    resolve(data);
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    this.onUploadError(fileId, error);
                    reject(error);
                });
            } else {
                // Fallback to simulation if CSRF token is not available
                console.warn('⚠️ CSRF token not found, falling back to simulation');
                this.simulateUpload(file, fileId, resolve, reject);
            }
        });
    }

    createFileItem(file, fileId) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
    
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.id = fileId;
        
        fileItem.innerHTML = `
            <div class="file-header">
                <div class="file-name">
                    <span class="file-icon ${fileExtension}"></span>
                    <span class="file-title">${file.name}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <div class="file-size">${fileSizeMB} MB</div>
                    <div class="file-status status-uploading" id="status-${fileId}">Yuklanmoqda...</div>
                    <button class="remove-btn" data-file-id="${fileId}" type="button">×</button>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-bar" id="progress-${fileId}" style="width: 0%"></div>
            </div>
            <div class="progress-text" id="progress-text-${fileId}">0%</div>
            
            <div class="audio-preview" id="preview-${fileId}" style="display: none;">
                <div class="audio-controls">
                    <button class="play-btn" data-file-id="${fileId}" type="button">
                        ▶️
                    </button>
                    <div class="audio-info">
                        <div class="audio-title">${file.name}</div>
                        <div class="audio-duration" id="duration-${fileId}">Yuklanmoqda...</div>
                    </div>
                </div>
                <audio id="audio-${fileId}" preload="metadata" style="display: none;">
                    <source src="" type="${file.type}">
                </audio>
            </div>
        `;
    
        // Event listener'larni alohida qo'shish
        fileItem.querySelector('.remove-btn').addEventListener('click', () => {
            this.removeFile(fileId);
        });
    
        fileItem.querySelector('.play-btn').addEventListener('click', () => {
            this.togglePlayback(fileId);
        });
    
        return fileItem;
    }

    simulateUpload(file, fileId, resolve, reject) {
        console.log(`🔄 Simulating upload for ${file.name}`);
        
        const progressBar = document.getElementById(`progress-${fileId}`);
        const progressText = document.getElementById(`progress-text-${fileId}`);
        
        if (!progressBar || !progressText) {
            reject(new Error('Progress elements not found'));
            return;
        }
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                
                setTimeout(() => {
                    this.onUploadComplete(file, fileId);
                    resolve({
                        success: true,
                        message: 'Simulation complete',
                        filename: file.name,
                        url: URL.createObjectURL(file),
                        part: fileId.split('_')[1] || 'part1'
                    });
                }, 500);
            }
            
            progressBar.style.width = `${progress}%`;
            progressText.textContent = `${Math.round(progress)}%`;
        }, 300);
    }

    onUploadComplete(file, fileId, data = null) {
        console.log(`✅ Upload complete for ${file.name}`);
        
        // Update status
        const statusElement = document.getElementById(`status-${fileId}`);
        if (statusElement) {
            statusElement.textContent = 'Yuklandi';
            statusElement.className = 'file-status status-success';
        }
        
        // Update progress
        const progressBar = document.getElementById(`progress-${fileId}`);
        const progressText = document.getElementById(`progress-text-${fileId}`);
        
        if (progressBar) progressBar.style.width = '100%';
        if (progressText) progressText.textContent = '100%';
        
        // Show preview
        const previewElement = document.getElementById(`preview-${fileId}`);
        if (previewElement) {
            previewElement.style.display = 'block';
        }
        
        // Set audio source
        const audioElement = document.getElementById(`audio-${fileId}`);
        if (audioElement) {
            const url = data?.url || URL.createObjectURL(file);
            audioElement.src = url;
            
            // Get duration when metadata is loaded
            audioElement.addEventListener('loadedmetadata', () => {
                const durationElement = document.getElementById(`duration-${fileId}`);
                if (durationElement) {
                    durationElement.textContent = this.formatDuration(audioElement.duration);
                }
            });
        }
        
        // Add to uploaded files list
        this.uploadedFiles.push({
            id: fileId,
            file: file,
            url: data?.url || URL.createObjectURL(file),
            objectURL: !data?.url ? URL.createObjectURL(file) : null,
            part: data?.part || fileId.split('_')[1] || 'part1'
        });
        
        // Show preview section if it's the first file
        if (this.uploadedFiles.length === 1) {
            const previewSection = document.getElementById('audioPreviewSection');
            if (previewSection) {
                previewSection.style.display = 'block';
            }
            
            // Show auto-play info
            this.showAutoPlayInfo();
        }
    }

    onUploadError(fileId, error) {
        console.error(`❌ Upload error for ${fileId}:`, error);
        
        const statusElement = document.getElementById(`status-${fileId}`);
        if (statusElement) {
            statusElement.textContent = 'Xatolik';
            statusElement.className = 'file-status status-error';
        }
        
        this.showMessage(`❌ Yuklashda xatolik: ${error.message}`, 'error');
    }

    removeFile(fileId) {
        console.log(`🗑️ Removing file ${fileId}`);
        
        // Remove from DOM
        const fileItem = document.getElementById(fileId);
        if (fileItem) {
            fileItem.remove();
        }
        
        // Stop audio if playing
        this.stopAudio(fileId);
        
        // Remove from uploaded files
        const fileIndex = this.uploadedFiles.findIndex(f => f.id === fileId);
        if (fileIndex !== -1) {
            const file = this.uploadedFiles[fileIndex];
            
            // Revoke object URL if created
            if (file.objectURL) {
                URL.revokeObjectURL(file.objectURL);
            }
            
            this.uploadedFiles.splice(fileIndex, 1);
        }
        
        // Hide preview section if no files left
        if (this.uploadedFiles.length === 0) {
            const previewSection = document.getElementById('audioPreviewSection');
            if (previewSection) {
                previewSection.style.display = 'none';
            }
        }
    }

    togglePlayback(fileId) {
        console.log(`🎵 Toggle playback for ${fileId}`);
        
        const audioElement = document.getElementById(`audio-${fileId}`);
        const playButton = document.querySelector(`#${fileId} .play-btn`);
        
        if (!audioElement || !playButton) {
            console.error('❌ Audio elements not found');
            return;
        }
        
        if (this.currentlyPlaying === fileId) {
            // Pause current
            if (audioElement.paused) {
                audioElement.play();
                playButton.textContent = '⏸️';
            } else {
                audioElement.pause();
                playButton.textContent = '▶️';
            }
        } else {
            // Stop any playing audio
            this.pauseAllAudios();
            
            // Play new audio
            audioElement.play();
            playButton.textContent = '⏸️';
            this.currentlyPlaying = fileId;
            
            // Reset when ended
            audioElement.addEventListener('ended', () => {
                playButton.textContent = '▶️';
                this.currentlyPlaying = null;
            });
        }
    }

    pauseAllAudios() {
        console.log('⏹️ Pausing all audios');
        
        this.uploadedFiles.forEach(file => {
            const audioElement = document.getElementById(`audio-${file.id}`);
            const playButton = document.querySelector(`#${file.id} .play-btn`);
            
            if (audioElement && !audioElement.paused) {
                audioElement.pause();
                if (playButton) {
                    playButton.textContent = '▶️';
                }
            }
        });
        
        this.currentlyPlaying = null;
    }

    stopAudio(fileId) {
        const audioElement = document.getElementById(`audio-${fileId}`);
        const playButton = document.querySelector(`#${fileId} .play-btn`);
        
        if (audioElement) {
            audioElement.pause();
            audioElement.currentTime = 0;
            
            if (playButton) {
                playButton.textContent = '▶️';
            }
            
            if (this.currentlyPlaying === fileId) {
                this.currentlyPlaying = null;
            }
        }
    }

    formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    showProgressSection() {
        const progressSection = document.getElementById('filesProgressSection');
        if (progressSection) {
            progressSection.style.display = 'block';
        }
    }

    hideProgressSection() {
        const progressSection = document.getElementById('filesProgressSection');
        if (progressSection) {
            progressSection.style.display = 'none';
        }
    }

    showAutoPlayInfo() {
        console.log('ℹ️ Showing auto-play info');
        
        const uploadSection = document.getElementById('audioUploadSection');
        if (uploadSection) {
            // Remove existing indicator if any
            const existingIndicator = document.querySelector('.auto-play-indicator');
            if (existingIndicator) {
                existingIndicator.remove();
            }
            
            // Add new indicator
            const indicator = document.createElement('div');
            indicator.className = 'auto-play-indicator';
            indicator.innerHTML = '▶️ Test boshlanganda bu audio avtomatik ijro etiladi';
            uploadSection.appendChild(indicator);
        }
    }

    showMessage(message, type = 'info') {
        console.log(`📢 ${type.toUpperCase()}: ${message}`);
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `upload-${type}`;
        messageDiv.textContent = message;
        
        // Insert after upload section
        const uploadSection = document.getElementById('audioUploadSection');
        if (uploadSection) {
            uploadSection.parentNode.insertBefore(messageDiv, uploadSection.nextSibling);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.style.opacity = '0';
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 300);
                }
            }, 5000);
        }
    }

    // Auto-play functionality for listening test
    autoPlayFirstAudio() {
        console.log('🎵 Attempting auto-play...');
        
        if (this.uploadedFiles.length > 0) {
            const firstFile = this.uploadedFiles[0];
            const audioElement = document.getElementById(`audio-${firstFile.id}`);
            
            if (audioElement) {
                audioElement.play()
                    .then(() => {
                        console.log('✅ Auto-play started successfully');
                        const playButton = document.querySelector(`#${firstFile.id} .play-btn`);
                        if (playButton) {
                            playButton.textContent = '⏸️';
                        }
                        this.currentlyPlaying = firstFile.id;
                        this.showMessage('🎵 Audio avtomatik boshlandi!', 'success');
                    })
                    .catch(error => {
                        console.log('⚠️ Auto-play prevented by browser:', error);
                        this.showMessage('⚠️ Browser auto-play ni blokladi. Play tugmasini bosing.', 'warning');
                    });
            }
        } else {
            console.warn('⚠️ No uploaded files available for auto-play');
        }
    }

    // Clean up resources
    destroy() {
        console.log('🧹 Cleaning up audio upload manager...');
        
        // Revoke all object URLs
        this.uploadedFiles.forEach(file => {
            if (file.objectURL) {
                URL.revokeObjectURL(file.objectURL);
            }
        });
        
        // Clear arrays
        this.uploadedFiles = [];
        this.uploadQueue = [];
        this.currentlyPlaying = null;
        this.isInitialized = false;
    }
}

// Global instance management
let audioUploadManager;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if audio upload section exists
    if (document.getElementById('audioUploadSection')) {
        audioUploadManager = new EnhancedAudioUpload();
        
        // Make it globally available
        window.audioUploadManager = audioUploadManager;
        
        console.log('🎵 Global audio upload manager created');
    }
});

// Auto-play when listening test starts
function startListeningTestAudio() {
    if (window.audioUploadManager && window.audioUploadManager.isInitialized) {
        window.audioUploadManager.autoPlayFirstAudio();
    } else {
        console.warn('⚠️ Audio upload manager not available for auto-play');
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (window.audioUploadManager) {
        window.audioUploadManager.destroy();
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedAudioUpload;
}
