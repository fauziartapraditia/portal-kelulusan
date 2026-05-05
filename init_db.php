<?php
$host = 'localhost';
$user = 'smkb4789_kelulusan';
$pass = 'sAtriani&sule123';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS smkb4789_kelulusan");
    echo "Database created or already exists.\n";

    // Use database
    $pdo->exec("USE smkb4789_kelulusan");

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create settings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NOT NULL
    )");

    // Create students table
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exam_number VARCHAR(50) UNIQUE NOT NULL,
        nisn VARCHAR(20) UNIQUE NOT NULL,
        name VARCHAR(150) NOT NULL,
        major VARCHAR(100) NOT NULL,
        class_name VARCHAR(50) NOT NULL,
        status ENUM('LULUS', 'TIDAK LULUS', 'DITUNDA') DEFAULT 'LULUS',
        is_locked TINYINT(1) DEFAULT 0,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Alter students table to add is_locked column if not exists
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS is_locked TINYINT(1) DEFAULT 0");
    } catch (Exception $e) {
        try {
            $pdo->exec("ALTER TABLE students ADD COLUMN is_locked TINYINT(1) DEFAULT 0");
        } catch (Exception $e2) {}
    }

    // Insert default admin if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES ('admin', :password, 'Administrator')");
        $stmt->execute(['password' => $hashedPassword]);
        echo "Default admin created (user: admin, pass: admin123).\n";
    }

    // Insert default settings
    $defaultSettings = [
        'school_name' => 'SMK 5 AGUSTUS PEKANBARU',
        'school_address' => 'Jl. Soekarno-Hatta No. 5, Pekanbaru',
        'announcement_date' => '2026-05-02 08:00:00',
        'principal_name' => 'Drs. H. John Doe, M.Pd.',
        'principal_nip' => '19710101 199903 1 001',
        'letter_header' => 'SURAT KETERANGAN KELULUSAN (SKL)'
    ];

    foreach ($defaultSettings as $key => $val) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_key = setting_key");
        $stmt->execute(['key' => $key, 'val' => $val]);
    }
    echo "Default settings initialized.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
