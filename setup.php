<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Connect without database first
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $conn->exec("CREATE DATABASE IF NOT EXISTS evoting_db");
    $conn->exec("USE evoting_db");

    // Create Users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('voter', 'admin') DEFAULT 'voter',
        status ENUM('pending', 'active', 'blocked') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create Elections table
    $conn->exec("CREATE TABLE IF NOT EXISTS elections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status ENUM('upcoming', 'active', 'completed') DEFAULT 'upcoming',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create Candidates table
    $conn->exec("CREATE TABLE IF NOT EXISTS candidates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        election_id INT,
        name VARCHAR(100) NOT NULL,
        party VARCHAR(100),
        bio TEXT,
        photo_url VARCHAR(255),
        FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE
    )");

    // Create Votes table
    $conn->exec("CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        election_id INT,
        candidate_id INT,
        voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_vote (user_id, election_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
        FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE
    )");

    echo "<h1>Database Setup Successful!</h1>";
    echo "<p>The 'evoting_db' and all required tables have been created.</p>";
    echo "<a href='index.php'>Go to Home Page</a>";

} catch(PDOException $e) {
    echo "<h1>Database Setup Failed!</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Note:</strong> Please ensure MySQL is running in your XAMPP Control Panel.</p>";
}
?>
