<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'username');
define('DB_PASS', '');
define('DB_NAME', 'your_database_name');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Global functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($path) {
    header("Location: " . $path);
    exit();
}

// Google Apps Script Web App URL for sending emails
define('GAS_WEBAPP_URL', 'https://script.google.com/macros/s/AKfycbwlcaIBFJ_eBOeU2yYtjWeLnk7rDPeGKSpFKwDUrXzlQUnv08nPx6N62LcQAnG-2kknzQ/exec');

session_start();
ob_start(); // Enable output buffering to prevent "headers already sent" errors during redirects
?>
