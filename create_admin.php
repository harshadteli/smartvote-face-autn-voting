<?php
include 'includes/config.php';

// New Admin Details
$admin_name = "Admin User";
$admin_email = "admin@example.com";
$admin_pass = "admin123"; // CHANGE THIS LATER
$hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status, is_verified) VALUES (?, ?, ?, 'admin', 'active', 1)");
    if ($stmt->execute([$admin_name, $admin_email, $hashed_pass])) {
        echo "<h1>Admin Created Successfully!</h1>";
        echo "<p><strong>Email:</strong> $admin_email</p>";
        echo "<p><strong>Password:</strong> $admin_pass</p>";
        echo "<br><a href='login.php' style='padding: 10px 20px; background: #4a90e2; color: white; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "<h1>Admin already exists!</h1>";
        echo "<p>User with email $admin_email is already in the database.</p>";
    } else {
        echo "<h1>Error:</h1> " . $e->getMessage();
    }
    echo "<br><a href='login.php'>Go to Login</a>";
}
?>
