<?php
session_start();
include 'includes/header.php';
?>

<div class="about-developer-container">
    <div class="glass-card developer-card animate-on-load">
        <div class="dev-header">
            <div class="dev-avatar">
               <img src="https://img.freepik.com/free-photo/person-playing-3d-video-games-device_23-2151005751.jpg?semt=ais_hybrid&w=740&q=80https://img.freepik.com/free-photo/person-playing-3d-video-games-device_23-2151005751.jpg?semt=ais_hybrid&w=740&q=80" alt="Harshad Teli Profile Image">
            </div>
            <div class="dev-info">
                <h1 class="dev-name">Harshad Teli</h1>
                <p class="dev-role">Full Stack Developer & Creative Designer</p>
            </div>
        </div>

        <div class="dev-body">
            <div class="dev-bio">
                <h3><i class="fas fa-quote-left"></i> About Me</h3>
                <p>Hello! I'm Harshad, a passionate developer dedicated to building secure, modern, and user-friendly digital experiences. I specialize in crafting elegant web applications with a focus on both functionality and aesthetics.</p>
            </div>

            <div class="dev-skills">
                <h3><i class="fas fa-code"></i> Skills & Expertise</h3>
                <div class="skill-chips">
                    <span class="skill-chip">PHP</span>
                    <span class="skill-chip">MySQL</span>
                    <span class="skill-chip">JavaScript</span>
                    <span class="skill-chip">HTML5 / CSS3</span>
                    <span class="skill-chip">UI/UX Design</span>
                    <span class="skill-chip">AI and Machine Learning</span>
                    <span class="skill-chip">Security Best Practices</span>
                </div>
            </div>

            <div class="dev-contact">
                <h3><i class="fas fa-envelope"></i> Get In Touch</h3>
                <div class="social-links-dev">
                    <a href="https://www.linkedin.com/in/harshad-teli-560700330?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"
                     class="social-icon" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="https://github.com/harshadteli" class="social-icon" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="https://harshadteli.github.io/portfolio" class="social-icon" title="Portfolio"><i class="fas fa-globe"></i></a>
                    <a href="mailto:harshadteli697@gmail.com" class="social-icon" title="Email"><i class="fas fa-at"></i></a>
                </div>
            </div>
        </div>

        <div class="dev-footer">
            <p>Designed with <i class="fas fa-heart" style="color: #ff7675;"></i> by Harshad Teli</p>
            <a href="index.php" class="btn btn-glass mt-1"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</div>

<style>
    .about-developer-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 4rem 1rem;
        min-height: 80vh;
    }

    .developer-card {
        max-width: 800px;
        width: 100%;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .dev-header {
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
    }

    .dev-avatar {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 5rem;
        color: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: 4px solid var(--glass-border);
        transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden; /* Ensure image stays within the circle */
    }

    .dev-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .dev-avatar:hover {
        transform: scale(1.1) rotate(5deg);
    }

    .dev-name {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(to right, #fff, var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .dev-role {
        font-size: 1.1rem;
        opacity: 0.9;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .dev-body {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: left;
        margin-top: 1rem;
    }

    @media (min-width: 768px) {
        .dev-body {
            grid-template-columns: 1fr 1fr;
        }
        .dev-bio {
            grid-column: span 2;
        }
    }

    .dev-body h3 {
        margin-bottom: 1rem;
        font-size: 1.3rem;
        color: var(--accent-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dev-bio p {
        line-height: 1.6;
        font-size: 1.05rem;
        opacity: 0.95;
    }

    .skill-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .skill-chip {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        border: 1px solid var(--glass-border);
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .skill-chip:hover {
        background: var(--accent-color);
        color: var(--text-color);
        transform: translateY(-3px);
    }

    .social-links-dev {
        display: flex;
        gap: 1.5rem;
    }

    .social-icon {
        font-size: 1.8rem;
        color: white;
        transition: 0.3s;
    }

    .social-icon:hover {
        color: var(--accent-color);
        transform: translateY(-5px);
    }

    .dev-footer {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid var(--glass-border);
    }

    .mt-1 { margin-top: 1rem; }

    /* Animations */
    .animate-on-load {
        animation: fadeInUp 1s ease-out forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dev-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        pointer-events: none;
        z-index: 0;
        animation: rotateBg 20s linear infinite;
    }

    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<?php include 'includes/footer.php'; ?>
