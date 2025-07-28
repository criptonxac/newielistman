// IELTS Listening Test JavaScript
let currentPart = 1;
let totalParts = 4;
let currentAnswers = {};
let answeredCount = 0;
let totalQuestions = 10;
let testStarted = false;
let testTimer;

// Test selection functions
function startFullTest() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentPart = 1;
    totalQuestions = 40;
    showPart(1);
    startTimer();
    testStarted = true;
}

function showParts() {
    document.getElementById('test-selection').style.display = 'none';
    document.getElementById('parts-selection').style.display = 'block';
}

function startPart(partNum) {
    document.getElementById('parts-selection').style.display = 'none';
    document.getElementById('test-area').style.display = 'block';
    currentPart = partNum;
    totalQuestions = 10;
    showPart(partNum);
    testStarted = true;
}

function backToSelection() {
    document.getElementById('parts-selection').style.display = 'none';
    document.getElementById('test-selection').style.display = 'block';
}

// Part navigation
function showPart(partNum) {
    // Hide all parts
    document.querySelectorAll('.test-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Show selected part
    document.getElementById(`part${partNum}-content`).classList.add('active');
    
    // Update navigation buttons
    document.getElementById('prevBtn').style.display = partNum > 1 ? 'block' : 'none';
    document.getElementById('nextBtn').textContent = partNum < totalParts ? 'Keyingi →' : 'Yakunlash';
    
    // Update audio source
    document.getElementById('audioPlayer').src = `/audio/listening-part${partNum}.mp3`;
}

function nextPart() {
    if (currentPart < totalParts) {
        currentPart++;
        showPart(currentPart);
    } else {
        finishTest();
    }
}

function previousPart() {
    if (currentPart > 1) {
        currentPart--;
        showPart(currentPart);
    }
}

// Timer function
function startTimer() {
    let minutes = 30;
    let seconds = 0;
    
    testTimer = setInterval(() => {
        const timerDisplay = document.getElementById('timer');
        const displayMinutes = minutes.toString().padStart(2, '0');
        const displaySeconds = seconds.toString().padStart(2, '0');
        timerDisplay.textContent = `${displayMinutes}:${displaySeconds}`;
        
        // Change color based on time remaining
        if (minutes < 5) {
            timerDisplay.style.color = '#e74c3c';
        } else if (minutes < 10) {
            timerDisplay.style.color = '#f39c12';
        }
        
        if (seconds === 0) {
            if (minutes === 0) {
                clearInterval(testTimer);
                alert('Vaqt tugadi! Test yakunlandi.');
                finishTest();
                return;
            }
            minutes--;
            seconds = 59;
        } else {
            seconds--;
        }
    }, 1000);
}

// Audio player controls
function initializeAudioPlayer() {
    const playBtn = document.getElementById('playBtn');
    const audioPlayer = document.getElementById('audioPlayer');
    
    if (playBtn && audioPlayer) {
        playBtn.addEventListener('click', function() {
            if (audioPlayer.paused) {
                audioPlayer.play();
                this.textContent = '⏸';
            } else {
                audioPlayer.pause();
                this.textContent = '▶';
            }
        });
    }
}

// Input handling
function initializeInputHandling() {
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('answer-input')) {
            const questionNum = e.target.dataset.question;
            const answer = e.target.value.trim();
            
            if (answer) {
                currentAnswers[questionNum] = answer;
                e.target.classList.add('filled');
                if (!e.target.dataset.counted) {
                    answeredCount++;
                    e.target.dataset.counted = 'true';
                }
            } else {
                delete currentAnswers[questionNum];
                e.target.classList.remove('filled');
                if (e.target.dataset.counted) {
                    answeredCount--;
                    e.target.dataset.counted = '';
                }
            }
            
            updateProgress();
        }
    });
}

function updateProgress() {
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const percentage = (answeredCount / totalQuestions) * 100;
    
    if (progressText && progressBar) {
        progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
        progressBar.style.width = percentage + '%';
    }
}

function finishTest() {
    if (testTimer) {
        clearInterval(testTimer);
    }
    
    alert(`Test yakunlandi! Siz ${answeredCount}/${totalQuestions} savolga javob berdingiz.`);
    
    // Redirect to results or dashboard
    window.location.href = "/student/dashboard";
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (!testStarted) return;
        
        if (e.key >= '1' && e.key <= '4') {
            const partNum = parseInt(e.key);
            if (partNum <= totalParts) {
                currentPart = partNum;
                showPart(currentPart);
            }
        }
        
        if (e.key === 'ArrowLeft' && currentPart > 1) {
            previousPart();
        }
        if (e.key === 'ArrowRight' && currentPart < totalParts) {
            nextPart();
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeAudioPlayer();
    initializeInputHandling();
    initializeKeyboardShortcuts();
    
    // Auto-start timer for standalone test pages
    if (document.getElementById('timer')) {
        startTimer();
        testStarted = true;
    }
});
