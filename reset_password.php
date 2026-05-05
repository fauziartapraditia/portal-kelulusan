<?php
require_once 'config.php';
try {
    global $pdo;
    $username = 'admin';
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // First, delete if exists to ensure we don't have multiple or conflicts
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = :user");
    $stmt->execute(['user' => $username]);
    
    // Insert new
    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (:user, :pass, 'Administrator')");
    $stmt->execute(['user' => $username, 'pass' => $hashedPassword]);
    
    echo "<h1>Password Reset Successful</h1>";
    echo "<p>Username: <strong>admin</strong></p>";
    echo "<p>Password: <strong>admin123</strong></p>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
