
// Test Player JavaScript for IELTS Platform

class TestPlayer {
    constructor(options = {}) {
        this.testId = options.testId;
        this.timeLimit = options.timeLimit || null;
        this.questions = options.questions || [];
        this.currentQuestion = 0;
        this.answers = {};
        this.timeRemaining = this.timeLimit;
        this.isSubmitted = false;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupTimer();
        this.setupNavigation();
        this.setupAutoSave();
        this.loadSavedAnswers();
    }
    
    setupEventListeners() {
        // Answer selection
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="radio"], input[type="checkbox"]')) {
                this.saveAnswer(e.target);
            }
        });
        
        // Text input
        document.addEventListener('input', (e) => {
            if (e.target.matches('input[type="text"], textarea')) {
                this.saveAnswer(e.target);
            }
        });
        
        // Navigation buttons
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const submitBtn = document.getElementById('submit-btn');
        
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextQuestion());
        if (prevBtn) prevBtn.addEventListener('click', () => this.prevQuestion());
        if (submitBtn) submitBtn.addEventListener('click', () => this.submitTest());
    }
    
    setupTimer() {
        if (!this.timeLimit) return;
        
        const timerElement = document.getElementById('timer');
        if (!timerElement) return;
        
        this.timerInterval = setInterval(() => {
            this.timeRemaining--;
            this.updateTimerDisplay();
            
            // Warning at 5 minutes
            if (this.timeRemaining === 300) {
                this.showWarning('5 daqiqa qoldi!');
            }
            
            // Auto-submit when time is up
            if (this.timeRemaining <= 0) {
                this.autoSubmit();
            }
        }, 1000);
    }
    
    updateTimerDisplay() {
        const timerElement = document.getElementById('timer');
        if (!timerElement) return;
        
        const hours = Math.floor(this.timeRemaining / 3600);
        const minutes = Math.floor((this.timeRemaining % 3600) / 60);
        const seconds = this.timeRemaining % 60;
        
        const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        timerElement.textContent = display;
        
        // Add warning class when time is low
        if (this.timeRemaining <= 300) {
            timerElement.classList.add('timer-warning');
        }
    }
    
    setupNavigation() {
        this.updateNavigationButtons();
        this.updateQuestionIndicators();
    }
    
    nextQuestion() {
        if (this.currentQuestion < this.questions.length - 1) {
            this.currentQuestion++;
            this.showQuestion(this.currentQuestion);
        }
    }
    
    prevQuestion() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
            this.showQuestion(this.currentQuestion);
        }
    }
    
    showQuestion(index) {
        const questions = document.querySelectorAll('.question-container');
        questions.forEach((q, i) => {
            q.style.display = i === index ? 'block' : 'none';
        });
        
        this.currentQuestion = index;
        this.updateNavigationButtons();
        this.updateQuestionIndicators();
        this.updateProgressBar();
    }
    
    updateNavigationButtons() {
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const submitBtn = document.getElementById('submit-btn');
        
        if (prevBtn) {
            prevBtn.style.display = this.currentQuestion === 0 ? 'none' : 'inline-block';
        }
        
        if (nextBtn) {
            nextBtn.style.display = this.currentQuestion === this.questions.length - 1 ? 'none' : 'inline-block';
        }
        
        if (submitBtn) {
            submitBtn.style.display = this.currentQuestion === this.questions.length - 1 ? 'inline-block' : 'none';
        }
    }
    
    updateQuestionIndicators() {
        const indicators = document.querySelectorAll('.question-indicator');
        indicators.forEach((indicator, index) => {
            indicator.classList.remove('current', 'answered');
            
            if (index === this.currentQuestion) {
                indicator.classList.add('current');
            }
            
            if (this.answers[`question_${index + 1}`]) {
                indicator.classList.add('answered');
            }
        });
    }
    
    updateProgressBar() {
        const progressBar = document.querySelector('.progress-fill');
        if (!progressBar) return;
        
        const answeredCount = Object.keys(this.answers).length;
        const progress = (answeredCount / this.questions.length) * 100;
        progressBar.style.width = `${progress}%`;
        
        const progressText = document.getElementById('progress-text');
        if (progressText) {
            progressText.textContent = `${answeredCount}/${this.questions.length} ta savol javoblandi`;
        }
    }
    
    saveAnswer(element) {
        const questionId = element.closest('.question-container').dataset.questionId;
        
        if (element.type === 'radio') {
            this.answers[questionId] = element.value;
        } else if (element.type === 'checkbox') {
            if (!this.answers[questionId]) this.answers[questionId] = [];
            
            if (element.checked) {
                if (!this.answers[questionId].includes(element.value)) {
                    this.answers[questionId].push(element.value);
                }
            } else {
                this.answers[questionId] = this.answers[questionId].filter(v => v !== element.value);
            }
        } else {
            this.answers[questionId] = element.value;
        }
        
        this.updateQuestionIndicators();
        this.updateProgressBar();
        this.autoSaveAnswers();
    }
    
    setupAutoSave() {
        // Save answers every 30 seconds
        setInterval(() => {
            this.autoSaveAnswers();
        }, 30000);
    }
    
    autoSaveAnswers() {
        if (this.isSubmitted) return;
        
        const data = {
            test_id: this.testId,
            answers: this.answers,
            current_question: this.currentQuestion,
            time_remaining: this.timeRemaining
        };
        
        localStorage.setItem(`test_${this.testId}_progress`, JSON.stringify(data));
    }
    
    loadSavedAnswers() {
        const saved = localStorage.getItem(`test_${this.testId}_progress`);
        if (!saved) return;
        
        try {
            const data = JSON.parse(saved);
            this.answers = data.answers || {};
            this.currentQuestion = data.current_question || 0;
            
            // Restore form values
            for (const [questionId, answer] of Object.entries(this.answers)) {
                const questionContainer = document.querySelector(`[data-question-id="${questionId}"]`);
                if (!questionContainer) continue;
                
                if (Array.isArray(answer)) {
                    // Checkbox answers
                    answer.forEach(value => {
                        const checkbox = questionContainer.querySelector(`input[value="${value}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                } else {
                    // Radio or text answers
                    const input = questionContainer.querySelector(`input[value="${answer}"], textarea, input[type="text"]`);
                    if (input) {
                        if (input.type === 'radio') {
                            input.checked = true;
                        } else {
                            input.value = answer;
                        }
                    }
                }
            }
            
            this.showQuestion(this.currentQuestion);
            this.showNotification('Oldingi javoblaringiz tiklandi', 'success');
            
        } catch (e) {
            console.error('Javoblarni yuklashda xatolik:', e);
        }
    }
    
    submitTest() {
        if (this.isSubmitted) return;
        
        // Confirm submission
        if (!confirm('Testni yakunlashni xohlaysizmi? Bu harakatni bekor qilib bo\'lmaydi.')) {
            return;
        }
        
        this.isSubmitted = true;
        clearInterval(this.timerInterval);
        
        // Submit to server
        const form = document.getElementById('test-form');
        if (form) {
            // Add answers to form
            for (const [questionId, answer] of Object.entries(this.answers)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = questionId;
                input.value = Array.isArray(answer) ? answer.join(',') : answer;
                form.appendChild(input);
            }
            
            form.submit();
        }
        
        // Clear saved progress
        localStorage.removeItem(`test_${this.testId}_progress`);
    }
    
    autoSubmit() {
        this.showNotification('Vaqt tugadi! Test avtomatik yakunlanmoqda...', 'warning');
        
        setTimeout(() => {
            this.submitTest();
        }, 3000);
    }
    
    showWarning(message) {
        const warning = document.createElement('div');
        warning.className = 'test-warning';
        warning.innerHTML = `
            <div class="warning-content">
                <i class="fas fa-exclamation-triangle"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;
        
        document.body.appendChild(warning);
        
        setTimeout(() => {
            warning.remove();
        }, 5000);
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `test-notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 4000);
    }
}

// Audio Player for Listening Tests
class AudioPlayer {
    constructor(audioElement) {
        this.audio = audioElement;
        this.isPlaying = false;
        this.setupControls();
    }
    
    setupControls() {
        const container = this.audio.parentElement;
        
        // Create custom controls
        const controls = document.createElement('div');
        controls.className = 'audio-controls';
        controls.innerHTML = `
            <button class="play-btn"><i class="fas fa-play"></i></button>
            <button class="pause-btn" style="display: none;"><i class="fas fa-pause"></i></button>
            <div class="progress-container">
                <div class="progress-bar"></div>
                <div class="progress-handle"></div>
            </div>
            <span class="time-display">00:00 / 00:00</span>
            <button class="volume-btn"><i class="fas fa-volume-up"></i></button>
        `;
        
        container.appendChild(controls);
        
        // Event listeners
        const playBtn = controls.querySelector('.play-btn');
        const pauseBtn = controls.querySelector('.pause-btn');
        const progressBar = controls.querySelector('.progress-bar');
        const timeDisplay = controls.querySelector('.time-display');
        
        playBtn.addEventListener('click', () => this.play());
        pauseBtn.addEventListener('click', () => this.pause());
        
        this.audio.addEventListener('timeupdate', () => {
            this.updateProgress(progressBar, timeDisplay);
        });
        
        this.audio.addEventListener('ended', () => {
            this.reset(playBtn, pauseBtn);
        });
    }
    
    play() {
        this.audio.play();
        this.isPlaying = true;
        document.querySelector('.play-btn').style.display = 'none';
        document.querySelector('.pause-btn').style.display = 'inline-block';
    }
    
    pause() {
        this.audio.pause();
        this.isPlaying = false;
        document.querySelector('.pause-btn').style.display = 'none';
        document.querySelector('.play-btn').style.display = 'inline-block';
    }
    
    updateProgress(progressBar, timeDisplay) {
        const progress = (this.audio.currentTime / this.audio.duration) * 100;
        progressBar.style.width = `${progress}%`;
        
        const current = this.formatTime(this.audio.currentTime);
        const duration = this.formatTime(this.audio.duration);
        timeDisplay.textContent = `${current} / ${duration}`;
    }
    
    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    
    reset(playBtn, pauseBtn) {
        this.isPlaying = false;
        pauseBtn.style.display = 'none';
        playBtn.style.display = 'inline-block';
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize test player if on test page
    const testContainer = document.querySelector('.test-container');
    if (testContainer) {
        const testId = testContainer.dataset.testId;
        const timeLimit = parseInt(testContainer.dataset.timeLimit);
        const questions = JSON.parse(testContainer.dataset.questions || '[]');
        
        window.testPlayer = new TestPlayer({
            testId: testId,
            timeLimit: timeLimit,
            questions: questions
        });
    }
    
    // Initialize audio players
    const audioElements = document.querySelectorAll('audio.listening-audio');
    audioElements.forEach(audio => {
        new AudioPlayer(audio);
    });
});
