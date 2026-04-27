// Main JS for E-Voting System

// Audio Effects & Speech Logic
// Preload Audio
const audioCache = {
    default: new Audio('https://www.soundjay.com/buttons/sounds/button-37a.mp3'),
    register: new Audio('audio/login.mp3'),
    verify: new Audio('https://www.soundjay.com/buttons/sounds/button-10.mp3')
};

function playSoundEffect(type) {
    const audio = audioCache[type] || audioCache.default;
    audio.currentTime = 0; // Reset to start
    audio.play().catch(error => console.error("Audio play failed:", error));
}

function speakAction(message) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(message);
        utterance.rate = 1;
        utterance.pitch = 1.1;
        window.speechSynthesis.speak(utterance);
    }
}

async function handleFormSubmit(event, buttonText, speechMessage, type = 'default') {
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');

    event.preventDefault();

    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${buttonText}`;

    // Play both sound and speech
    playSoundEffect(type);
    speakAction(speechMessage);

    // Wait for a short duration for the experience
    const delay = type === 'register' ? 3000 : 1500;
    setTimeout(() => {
        form.submit();
    }, delay);
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('E-Voting System Initialized');

    // Smooth scroll for nav links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add animation to glass cards on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.glass-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });

    // Mobile Menu Toggle
    const menuToggle = document.getElementById('mobile-menu');
    const navList = document.getElementById('nav-list');

    if (menuToggle && navList) {
        menuToggle.addEventListener('click', () => {
            navList.classList.toggle('active');
            const icon = menuToggle.querySelector('i');
            if (navList.classList.contains('active')) {
                icon.classList.replace('fa-bars', 'fa-times');
            } else {
                icon.classList.replace('fa-times', 'fa-bars');
            }
        });
    }
});
