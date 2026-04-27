<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_SESSION['verify_email'])) {
    header("Location: register.php");
    exit();
}

$error = '';
$success = '';
$email = $_SESSION['verify_email'];
$temp_voter_id = isset($_SESSION['temp_voter_id']) ? $_SESSION['temp_voter_id'] : '';
$is_id_verified = isset($_SESSION['id_verified']) ? $_SESSION['id_verified'] : false;

// Step 1: Verify Voter ID
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_id'])) {
    $voter_id = sanitize($_POST['voter_id']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND voter_id = ?");
    $stmt->execute([$email, $voter_id]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['id_verified'] = true;
        $_SESSION['verified_user_id'] = $user['id'];
        $is_id_verified = true;
    } else {
        $error = "Invalid Voter ID. Please check your email and try again.";
    }
}

// Step 2: Set Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['set_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_id = $_SESSION['verified_user_id'];

        $update = $conn->prepare("UPDATE users SET password = ?, is_verified = 1 WHERE id = ?");
        if ($update->execute([$hashed_password, $user_id])) {
            
            // Log the user in
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];

            unset($_SESSION['verify_email']);
            unset($_SESSION['temp_voter_id']);
            unset($_SESSION['id_verified']);
            unset($_SESSION['verified_user_id']);

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to update password. Please try again.";
        }
    }
}
?>

<section class="verify-section" style="padding: 80px 0; display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="glass-card" style="width: 100%; max-width: 500px; padding: 40px; text-align: center;">
        
        <?php if (!$is_id_verified): ?>
            <!-- Step 1: Voter ID Entry -->
            <div style="font-size: 3.5rem; color: var(--accent-color); margin-bottom: 1.5rem;">
                <i class="fas fa-id-card"></i>
            </div>
            <h2 style="margin-bottom: 1rem;">Verification <span style="color: var(--accent-color);">Step 1</span></h2>
            <p style="margin-bottom: 2rem; opacity: 0.8;">Enter the <strong>Voter ID</strong> sent to your email (<b><?php echo $email; ?></b>) to verify your identity.</p>

            <?php if($error): ?>
                <div style="background: rgba(255, 118, 117, 0.2); border: 1px solid #ff7675; padding: 10px; border-radius: 10px; margin-bottom: 1.5rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- <?php if($temp_voter_id): ?>
                <div style="background: rgba(255, 255, 255, 0.1); border: 1px dashed var(--glass-border); padding: 10px; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem;">
                    <strong>Debug:</strong> Your Voter ID is: <code style="color: var(--accent-color);"><?php echo $temp_voter_id; ?></code>
                </div>
            <?php endif; ?> -->

            <form action="verify_voter.php" method="POST" onsubmit="handleFormSubmit(event, 'Verifying ID...', 'Verifying your Voter ID. One moment please.', 'verify')">
                <input type="hidden" name="verify_id" value="1">
                <div style="margin-bottom: 1.5rem;">
                    <input type="text" name="voter_id" placeholder="Enter Voter ID" required style="width: 100%; padding: 1rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none; text-align: center; letter-spacing: 2px; font-weight: bold; font-size: 1.2rem;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: bold;">Verify Voter ID</button>
            </form>

        <?php else: ?>
            <!-- Step 2: Set Password -->
            <div style="font-size: 3.5rem; color: #55efc4; margin-bottom: 1.5rem;">
                <i class="fas fa-lock"></i>
            </div>
            <h2 style="margin-bottom: 1rem;">Verification <span style="color: #55efc4;">Complete</span></h2>
            <p style="margin-bottom: 2rem; opacity: 0.8;">Your identity is verified! Now, please <strong>set a secure password</strong> for your account.</p>

            <?php if($error): ?>
                <div style="background: rgba(255, 118, 117, 0.2); border: 1px solid #ff7675; padding: 10px; border-radius: 10px; margin-bottom: 1.5rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="verify_voter.php" method="POST" onsubmit="handleFormSubmit(event, 'Activating Account...', 'Success! Activating your account now.', 'verify')">
                <input type="hidden" name="set_password" value="1">
                <div style="margin-bottom: 1.2rem; text-align: left;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem;">New Password</label>
                    <input type="password" name="password" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
                <div style="margin-bottom: 2rem; text-align: left;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem;">Confirm Password</label>
                    <input type="password" name="confirm_password" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: bold; background: #55efc4; color: #2d3436; border: none;">Activate Account & Login</button>
            </form>
        <?php endif; ?>
        
        <p style="margin-top: 2rem; font-size: 0.9rem; opacity: 0.7;">
            Need help? <a href="#" style="color: var(--accent-color);">Contact Support</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
