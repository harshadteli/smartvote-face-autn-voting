<?php
include 'includes/config.php';
include 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified'] == 0) {
            $_SESSION['verify_email'] = $user['email'];
            $error = "Your account is not verified. Please check your email for Voter ID.";
            redirect('verify_voter.php');
        }

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role']       = $user['role'];
        
        if ($user['role'] == 'admin') {
            redirect('admin/index.php');
        } else {
            redirect('dashboard.php');
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<section class="login-section" style="padding: 60px 0; display: flex; justify-content: center;">
    <div class="glass-card" style="width: 100%; max-width: 450px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Voter <span style="color: var(--accent-color);">Login</span></h2>
        
        <?php if($error): ?>
            <div style="background: rgba(255, 118, 117, 0.2); border: 1px solid #ff7675; padding: 10px; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" onsubmit="handleFormSubmit(event, 'Authenticating...', 'Authenticating your credentials. Welcome back.', 'default')">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Login Securely</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; opacity: 0.8;">
            Don't have an account? <a href="register.php" style="color: var(--accent-color); text-decoration: none;">Register here</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
