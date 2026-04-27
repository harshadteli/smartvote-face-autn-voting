<?php
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    
    // Validate email uniqueness if changed
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $error = "Email address is already in use by another account.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $user_id])) {
            $_SESSION['user_name'] = $full_name;
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include 'includes/header.php';
?>

<div style="padding: 2rem 0; display: flex; justify-content: center;">
    <div class="glass-card" style="width: 100%; max-width: 600px;">
        <h2 style="margin-bottom: 2rem; text-align: center;">My <span style="color: var(--accent-color);">Profile</span></h2>

        <?php if($error): ?>
            <div style="background: rgba(255, 118, 117, 0.2); border: 1px solid #ff7675; padding: 10px; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div style="background: rgba(85, 239, 196, 0.2); border: 1px solid #55efc4; padding: 10px; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="POST">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Role</label>
                <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.05); color: rgba(255, 255, 255, 0.5); outline: none; cursor: not-allowed;">
                <p style="font-size: 0.8rem; opacity: 0.6; margin-top: 0.5rem;">Contact administrator to change your role.</p>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 2;">Update Profile</button>
                <a href="dashboard.php" class="btn btn-glass" style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
        
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
            <h4 style="margin-bottom: 1rem; color: #ff7675;">Security</h4>
            <p style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 1.5rem;">To change your password, please click the button below.</p>
            <a href="change-password.php" class="btn btn-glass" style="border-color: rgba(255, 118, 117, 0.3); color: #ff7675;">Change Password</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
