
// IELTS Platform JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initMobileMenu();
    initTestTimer();
    initProgressTracker();
    initFormValidation();
    initTestNavigation();
    initAudioControls();
    initSmoothScrolling();
    initNotifications();
});

// Mobile Menu Toggle
function initMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

// Test Timer Functionality
function initTestTimer() {
    const timerElement = document.getElementById('test-timer');
    if (!timerElement) return;
    
    let timeLeft = parseInt(timerElement.dataset.duration) || 0;
    
    const timer = setInterval(function() {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        
        const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        timerElement.textContent = display;
        
        // Warning when 5 minutes left
        if (timeLeft <= 300) {
            timerElement.classList.add('timer-warning');
        }
        
        // Time's up
        if (timeLeft <= 0) {
            clearInterval(timer);
            autoSubmitTest();
        }
        
        timeLeft--;
    }, 1000);
}

// Progress Tracker
function initProgressTracker() {
    const progressBar = document.getElementById('progress-bar');
    const questionInputs = document.querySelectorAll('input[type="radio"], input[type="checkbox"], textarea, input[type="text"]');
    
    if (!progressBar || questionInputs.length === 0) return;
    
    function updateProgress() {
        const answeredQuestions = new Set();
        
        questionInputs.forEach(input => {
            const questionName = input.name;
            if (input.type === 'radio' || input.type === 'checkbox') {
                if (input.checked) {
                    answeredQuestions.add(questionName);
                }
            } else if (input.value.trim() !== '') {
                answeredQuestions.add(questionName);
            }
        });
        
        const totalQuestions = new Set(Array.from(questionInputs).map(input => input.name)).size;
        const progress = (answeredQuestions.size / totalQuestions) * 100;
        
        progressBar.style.width = progress + '%';
        
        // Update progress text
        const progressText = document.getElementById('progress-text');
        if (progressText) {
            progressText.textContent = `${answeredQuestions.size}/${totalQuestions} ta savol javoblandi`;
        }
    }
    
    questionInputs.forEach(input => {
        input.addEventListener('change', updateProgress);
        input.addEventListener('input', updateProgress);
    });
    
    // Initial progress calculation
    updateProgress();
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'Bu maydon to\'ldirilishi shart');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red text-sm mt-1';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.classList.add('border-red');
}

function clearFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    field.classList.remove('border-red');
}

// Test Navigation
function initTestNavigation() {
    const nextButton = document.getElementById('next-question');
    const prevButton = document.getElementById('prev-question');
    const questionCards = document.querySelectorAll('.question-card');
    let currentQuestion = 0;
    
    if (!questionCards.length) return;
    
    function showQuestion(index) {
        questionCards.forEach((card, i) => {
            card.style.display = i === index ? 'block' : 'none';
        });
        
        // Update navigation buttons
        if (prevButton) {
            prevButton.style.display = index === 0 ? 'none' : 'inline-block';
        }
        
        if (nextButton) {
            nextButton.textContent = index === questionCards.length - 1 ? 'Testni yakunlash' : 'Keyingi savol';
        }
        
        // Update question counter
        const counter = document.getElementById('question-counter');
        if (counter) {
            counter.textContent = `Savol ${index + 1} / ${questionCards.length}`;
        }
    }
    
    if (nextButton) {
        nextButton.addEventListener('click', function() {
            if (currentQuestion < questionCards.length - 1) {
                currentQuestion++;
                showQuestion(currentQuestion);
            } else {
                // Submit test
                document.getElementById('test-form').submit();
            }
        });
    }
    
    if (prevButton) {
        prevButton.addEventListener('click', function() {
            if (currentQuestion > 0) {
                currentQuestion--;
                showQuestion(currentQuestion);
            }
        });
    }
    
    // Show first question
    if (questionCards.length > 1) {
        showQuestion(0);
    }
}

// Audio Controls for Listening Tests
function initAudioControls() {
    const audioPlayers = document.querySelectorAll('.audio-player');
    
    audioPlayers.forEach(player => {
        const audio = player.querySelector('audio');
        const playBtn = player.querySelector('.play-btn');
        const pauseBtn = player.querySelector('.pause-btn');
        const progressBar = player.querySelector('.audio-progress');
        const timeDisplay = player.querySelector('.time-display');
        
        if (!audio) return;
        
        // Play/Pause functionality
        if (playBtn) {
            playBtn.addEventListener('click', () => {
                audio.play();
                playBtn.style.display = 'none';
                if (pauseBtn) pauseBtn.style.display = 'inline-block';
            });
        }
        
        if (pauseBtn) {
            pauseBtn.addEventListener('click', () => {
                audio.pause();
                pauseBtn.style.display = 'none';
                if (playBtn) playBtn.style.display = 'inline-block';
            });
        }
        
        // Progress bar
        if (progressBar) {
            audio.addEventListener('timeupdate', () => {
                const progress = (audio.currentTime / audio.duration) * 100;
                progressBar.style.width = progress + '%';
            });
        }
        
        // Time display
        if (timeDisplay) {
            audio.addEventListener('timeupdate', () => {
                const current = formatTime(audio.currentTime);
                const duration = formatTime(audio.duration);
                timeDisplay.textContent = `${current} / ${duration}`;
            });
        }
    });
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Smooth Scrolling
function initSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Notifications
function initNotifications() {
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
}

// Auto-submit test when time runs out
function autoSubmitTest() {
    showNotification('Vaqt tugadi! Test avtomatik tarzda yakunlanmoqda...', 'warning');
    
    setTimeout(() => {
        const testForm = document.getElementById('test-form');
        if (testForm) {
            testForm.submit();
        }
    }, 2000);
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
    
    // Manual close
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.remove();
    });
}

// Save test progress to localStorage
function saveTestProgress() {
    const testForm = document.getElementById('test-form');
    if (!testForm) return;
    
    const formData = new FormData(testForm);
    const progress = {};
    
    for (let [key, value] of formData.entries()) {
        progress[key] = value;
    }
    
    localStorage.setItem('test_progress', JSON.stringify(progress));
}

// Load test progress from localStorage
function loadTestProgress() {
    const saved = localStorage.getItem('test_progress');
    if (!saved) return;
    
    try {
        const progress = JSON.parse(saved);
        
        for (let [key, value] of Object.entries(progress)) {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = true;
                } else {
                    input.value = value;
                }
            }
        }
    } catch (e) {
        console.error('Test progress yuklanmadi:', e);
    }
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Confirmation before leaving test page
window.addEventListener('beforeunload', function(e) {
    if (document.querySelector('.test-container')) {
        e.preventDefault();
        e.returnValue = '';
        return 'Test jarayoni yakunlanmagan. Sahifani tark etishni xohlaysizmi?';
    }
});

// Export functions for external use
window.IELTSPlatform = {
    showNotification,
    saveTestProgress,
    loadTestProgress,
    autoSubmitTest
};
