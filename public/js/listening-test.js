// public/js/listening-test.js

document.addEventListener('DOMContentLoaded', () => {
    const audioPlayer = document.getElementById('audioPlayer');
    const playBtn = document.getElementById('playBtn');
    const volumeSlider = document.querySelector('.volume-slider');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const timerDisplay = document.getElementById('timer');
    const partButtons = document.querySelectorAll('.part-btn');
    const parts = document.querySelectorAll('.part-content');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const form = document.getElementById('testForm');

    let currentPart = 1;
    let totalParts = 4;
    let totalQuestions = 40;
    let answeredCount = 0;
    let currentAnswers = {};
    let timerInterval = null;

    // Audio autoplay logic
    setTimeout(() => {
        if (audioPlayer) {
            audioPlayer.play()
                .then(() => updatePlayBtn(true))
                .catch(() => {
                    alert("Audio avtomatik ijro etilmadi. 'Play' tugmasini bosing.");
                    updatePlayBtn(false);
                });
        }
    }, 10000);

    // Timer setup
    const totalSeconds = parseInt(timerDisplay.dataset.timeSeconds || "1800");
    startCountdown(totalSeconds);

    // Volume control
    if (volumeSlider && audioPlayer) {
        volumeSlider.addEventListener('input', (e) => {
            audioPlayer.volume = e.target.value / 100;
        });
    }

    // Play/Pause control
    playBtn.addEventListener('click', () => {
        if (audioPlayer.paused) {
            audioPlayer.play().then(() => updatePlayBtn(true));
        } else {
            audioPlayer.pause();
            updatePlayBtn(false);
        }
    });

    audioPlayer.addEventListener('ended', () => updatePlayBtn(false));

    function updatePlayBtn(isPlaying) {
        playBtn.textContent = isPlaying ? '⏸' : '▶';
        playBtn.setAttribute('aria-label', isPlaying ? 'Pause' : 'Play');
    }

    // Show part
    function showPart(partNum) {
        parts.forEach(p => p.classList.remove('active'));
        document.getElementById(`part${partNum}-content`).classList.add('active');

        partButtons.forEach(btn => btn.classList.remove('active'));
        document.getElementById(`part${partNum}-btn`).classList.add('active');

        prevBtn.style.display = partNum > 1 ? 'inline-block' : 'none';
        nextBtn.textContent = partNum < totalParts ? 'Keyingi →' : 'Testni yakunlash';

        currentPart = partNum;
    }

    // Navigation buttons
    prevBtn.addEventListener('click', () => {
        if (currentPart > 1) showPart(currentPart - 1);
    });

    nextBtn.addEventListener('click', () => {
        if (currentPart < totalParts) {
            showPart(currentPart + 1);
        } else {
            form.submit();
        }
    });

    // Input answer logic
    document.addEventListener('input', (e) => {
        if (e.target.classList.contains('answer-input')) {
            const qid = e.target.dataset.question;
            const val = e.target.value.trim();

            if (val) {
                if (!currentAnswers[qid]) {
                    answeredCount++;
                }
                currentAnswers[qid] = val;
            } else {
                if (currentAnswers[qid]) {
                    answeredCount--;
                    delete currentAnswers[qid];
                }
            }

            updateProgress();
        }
    });

    function updateProgress() {
        const percent = Math.round((answeredCount / totalQuestions) * 100);
        progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
        progressBar.style.width = `${percent}%`;
    }

    // Timer countdown
    function startCountdown(seconds) {
        let remaining = seconds;
        timerInterval = setInterval(() => {
            const min = Math.floor(remaining / 60).toString().padStart(2, '0');
            const sec = (remaining % 60).toString().padStart(2, '0');
            timerDisplay.textContent = `${min}:${sec}`;

            if (remaining <= 0) {
                clearInterval(timerInterval);
                alert('Vaqt tugadi! Test yakunlanmoqda.');
                form.submit();
            }

            if (remaining < 300) timerDisplay.style.color = '#e74c3c';
            else if (remaining < 600) timerDisplay.style.color = '#f39c12';

            remaining--;
        }, 1000);
    }

    // Initialize part 1
    showPart(1);
});
