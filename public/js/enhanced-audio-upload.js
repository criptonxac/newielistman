document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('audio-upload');
    const uploadZone = document.getElementById('audioUploadSection');
    const overlay = document.getElementById('audio-upload-loading-overlay');
    const loadingText = document.getElementById('audio-upload-loading-text');
    const selectBtn = document.getElementById('selectFilesBtn');

    if (!fileInput || !uploadZone || !overlay || !loadingText || !selectBtn) {
        console.warn("Audio upload: necessary elements not found in DOM.");
        return;
    }

    // === Helper functions ===
    const showLoading = (message = 'Yuklanmoqda...') => {
        loadingText.textContent = message;
        overlay.classList.remove('hidden');
    };

    const hideLoading = () => {
        overlay.classList.add('hidden');
    };

    const showToast = (msg, success = true) => {
        alert(msg); // You can replace with a custom toast later
    };

    const getCsrfToken = () => {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        return tokenMeta ? tokenMeta.content : '';
    };

    const getFormData = (file) => {
        const formData = new FormData();
        formData.append('audio_file', file);
        formData.append('test_id', document.querySelector('input[name="test_id"]').value);
        formData.append('part_id', document.querySelector('input[name="part_id"]').value);
        return formData;
    };

    // === Core upload logic ===
    const uploadFile = async (file) => {
        try {
            showLoading('Yuklanmoqda: ' + file.name);

            const response = await fetch('/audio/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: getFormData(file)
            });

            if (!response.ok) {
                throw new Error('Yuklashda xatolik yuz berdi.');
            }

            const result = await response.json();
            hideLoading();
            showToast('✅ ' + file.name + ' muvaffaqiyatli yuklandi!');
        } catch (error) {
            hideLoading();
            console.error(error);
            showToast('❌ Yuklashda xatolik: ' + error.message, false);
        }
    };

    // === Event listeners ===

    // File input
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            [...fileInput.files].forEach(uploadFile);
        }
    });

    // Click event for label (fallback)
    selectBtn.addEventListener('click', (e) => {
        fileInput.click();
    });

    // Drag & drop events
    ['dragenter', 'dragover'].forEach(event =>
        uploadZone.addEventListener(event, e => {
            e.preventDefault();
            uploadZone.classList.add('bg-blue-100');
        })
    );

    ['dragleave', 'drop'].forEach(event =>
        uploadZone.addEventListener(event, e => {
            e.preventDefault();
            uploadZone.classList.remove('bg-blue-100');
        })
    );

    uploadZone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files.length > 0) {
            [...e.dataTransfer.files].forEach(uploadFile);
        }
    });
});
