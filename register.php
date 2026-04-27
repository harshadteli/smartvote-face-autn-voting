<?php
include 'includes/config.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $dob = sanitize($_POST['dob']);
    $address = sanitize($_POST['address']);
    
    // Generate Unique Voter ID
    $voter_id = 'EV-' . strtoupper(bin2hex(random_bytes(4)));

    // Handle Profile Picture Upload
    $profile_pic = '';
    $base64_image = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . '_' . $voter_id . '.' . $file_ext;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_pic = $file_name;
            // Convert to base64 for GAS
            $imageData = file_get_contents($target_file);
            $base64_image = base64_encode($imageData);
        } else {
            $error = "Failed to upload profile picture.";
        }
    } else {
        $error = "Profile picture is required.";
    }

    if (empty($error)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered!";
        } else {
            try {
                // Insert user into database with a temporary password
                $temp_password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, dob, address, profile_picture, voter_id, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
                
                if ($stmt->execute([$full_name, $email, $temp_password, $dob, $address, $profile_pic, $voter_id])) {
                    
                    // Trigger Google Apps Script to send email
                    if (GAS_WEBAPP_URL !== 'YOUR_GOOGLE_APPS_SCRIPT_WEBAPP_URL_HERE') {
                        $post_data = [
                            'action' => 'sendVoterCard',
                            'email' => $email,
                            'name' => $full_name,
                            'voter_id' => $voter_id,
                            'dob' => $dob,
                            'address' => $address,
                            'image' => $base64_image
                        ];

                        $ch = curl_init(GAS_WEBAPP_URL);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                        // Removed CURLOPT_FOLLOWLOCATION as it causes fatal errors on shared hosts (open_basedir restriction)
                        @curl_exec($ch);
                        curl_close($ch);
                    }

                    $_SESSION['verify_email'] = $email;
                    $_SESSION['temp_voter_id'] = $voter_id; 
                    unset($_SESSION['id_verified']);
                    unset($_SESSION['verified_user_id']);
                    
                    header("Location: verify_voter.php");
                    exit();
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            } catch (Exception $e) {
                $error = "System Error: " . $e->getMessage();
            }
        }
    }
}
?>

<section class="register-section" style="padding: 60px 0; display: flex; justify-content: center;">
    <div class="glass-card" style="width: 100%; max-width: 600px; padding: 30px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Voter <span style="color: var(--accent-color);">Registration</span></h2>
        
        <?php if($error): ?>
            <div style="background: rgba(255, 118, 117, 0.2); border: 1px solid #ff7675; padding: 10px; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(event, 'Sending Email...', 'Generating your Voter ID. Please check your email shortly.', 'register')">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Full Name</label>
                    <input type="text" name="full_name" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Date of Birth</label>
                    <input type="date" name="dob" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*" required style="width: 100%; padding: 0.6rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Residential Address</label>
                <textarea name="address" required rows="3" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: bold; font-size: 1.1rem;">Register & Generate Voter ID</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; opacity: 0.8;">
            Already have an account? <a href="login.php" style="color: var(--accent-color); text-decoration: none;">Login here</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
