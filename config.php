<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
$db_host = 'localhost';
$db_user = 'smkb4789_kelulusan';
$db_pass = 'sAtriani&sule123';
$db_name = 'smkb4789_kelulusan';

try {
    // Attempt to connect to MySQL without selecting DB first
    try {
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    } catch (PDOException $e) {
        // Fallback to default XAMPP root without password
        $pdo = new PDO("mysql:host=$db_host", "root", "");
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create DB if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Select DB
    $pdo->exec("USE `$db_name`");

    // Initialize tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NOT NULL
    )");

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
        } catch (Exception $e2) {
        }
    }

    // Alter students table to add subject_scores column if not exists
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS subject_scores TEXT");
    } catch (Exception $e) {
        try {
            $pdo->exec("ALTER TABLE students ADD COLUMN subject_scores TEXT");
        } catch (Exception $e2) {
        }
    }

    // Alter students table to add prank_level column if not exists
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS prank_level VARCHAR(20) DEFAULT 'NONE'");
    } catch (Exception $e) {
        try {
            $pdo->exec("ALTER TABLE students ADD COLUMN prank_level VARCHAR(20) DEFAULT 'NONE'");
        } catch (Exception $e2) {
        }
    }

    // Alter students table to add prank_started_at and prank_duration columns if not exists
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS prank_started_at TIMESTAMP NULL DEFAULT NULL");
    } catch (Exception $e) {
        try {
            $pdo->exec("ALTER TABLE students ADD COLUMN prank_started_at TIMESTAMP NULL DEFAULT NULL");
        } catch (Exception $e2) {
        }
    }

    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS prank_duration INT DEFAULT 0");
    } catch (Exception $e) {
        try {
            $pdo->exec("ALTER TABLE students ADD COLUMN prank_duration INT DEFAULT 0");
        } catch (Exception $e2) {
        }
    }

    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS is_viewed TINYINT(1) DEFAULT 0");
    } catch (Exception $e) {
        try {
            $pdo->exec("ALTER TABLE students ADD COLUMN is_viewed TINYINT(1) DEFAULT 0");
        } catch (Exception $e2) {
        }
    }

    // Insert default admin if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES ('admin', :password, 'Administrator')");
        $stmt->execute(['password' => $hashedPassword]);
    }

    // Insert default settings
    $defaultSettings = [
        'school_name' => 'SMK 5 AGUSTUS PEKANBARU',
        'school_address' => 'Jl. Soekarno-Hatta No. 5, Pekanbaru',
        'announcement_date' => '2026-05-02 08:00:00',
        'principal_name' => 'Drs. H. John Doe, M.Pd.',
        'principal_nip' => '19710101 199903 1 001',
        'letter_header' => 'SURAT KETERANGAN KELULUSAN (SKL)',
        'subjects' => 'Pendidikan Agama dan Budi Pekerti,Pendidikan Pancasila dan Kewarganegaraan,Bahasa Indonesia,Matematika,Sejarah Indonesia,Bahasa Inggris,Seni Budaya,Pendidikan Jasmani Olahraga dan Kesehatan,Prakarya dan Kewirausahaan,Kompetensi Keahlian'
    ];

    foreach ($defaultSettings as $key => $val) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_key = setting_key");
        $stmt->execute(['key' => $key, 'val' => $val]);
    }

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Global functions
function get_setting($key)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
    $stmt->execute(['key' => $key]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res ? $res['setting_value'] : '';
}

function set_setting($key, $value)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_value = :val2");
    $stmt->execute(['key' => $key, 'val' => $value, 'val2' => $value]);
}

function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
