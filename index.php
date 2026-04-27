<?php
include 'includes/config.php';
include 'includes/header.php';
?>

<section class="hero-section" style="padding: 100px 0; text-align: center;">
    <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 3rem; margin-bottom: 1.5rem;">Secure Your Voice, <span
                style="color: var(--accent-color);">Shape the Future</span></h1>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9;">
            Experience the next generation of digital democracy. Our E-Voting platform provides a secure, transparent,
            and user-friendly way to participate in elections from anywhere in the world.
        </p>
        <div class="hero-btns" style="display: flex; gap: 1rem; justify-content: center;">
            <a href="register.php" class="btn btn-primary">Get Started Now</a>
            <a href="elections.php" class="btn btn-glass">View Active Elections</a>
        </div>
    </div>
</section>

<section class="features" style="padding: 50px 0;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
        <div class="glass-card" style="text-align: center;">
            <i class="fas fa-shield-alt"
                style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
            <h3>High Security</h3>
            <p>Advanced encryption and secure protocols to protect your identity and your vote.</p>
        </div>
        <div class="glass-card" style="text-align: center;">
            <i class="fas fa-bolt" style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
            <h3>Real-time Results</h3>
            <p>Watch the democratic process unfold with transparent, real-time result tracking.</p>
        </div>
        <div class="glass-card" style="text-align: center;">
            <i class="fas fa-mobile-alt"
                style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
            <h3>Fully Responsive</h3>
            <p>Vote seamlessly from your smartphone, tablet, or desktop with our optimized UI.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>