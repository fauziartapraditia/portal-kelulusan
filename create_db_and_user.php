<?php
try {
    // 1. Connect as root (full privileges)
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected as root.<br>";

    // 2. Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `smkb4789_kelulusan` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created successfully.<br>";

    // Use database
    $pdo->exec("USE `smkb4789_kelulusan`");
    echo "Database selected.<br>";

    // 3. Create schema tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table created.<br>";

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NOT NULL
    )");
    echo "Settings table created.<br>";

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
    echo "Students table created.<br>";

    // Add columns if not exists
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN is_locked TINYINT(1) DEFAULT 0");
    } catch (Exception $e) {}
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN subject_scores TEXT");
    } catch (Exception $e) {}
    echo "Altered tables.<br>";

    // Insert default admin if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES ('admin', :password, 'Administrator')");
        $stmt->execute(['password' => $hashedPassword]);
        echo "Admin created.<br>";
    }

    // Insert default settings
    $defaultSettings = [
        'school_name' => 'SMK 5 AGUSTUS PEKANBARU',
        'school_address' => 'Jl. Soekarno-Hatta No. 5, Pekanbaru',
        'announcement_date' => '2026-05-02 08:00:00',
        'principal_name' => 'Doni Rahman, S.E, M.Si',
        'principal_nip' => '19710101 199903 1 001',
        'letter_header' => 'SURAT KETERANGAN KELULUSAN (SKL)',
        'subjects' => 'Pendidikan Agama and Budi Pekerti,Pendidikan Pancasila and Kewarganegaraan,Bahasa Indonesia,Matematika,Sejarah Indonesia,Bahasa Inggris,Seni Budaya,Pendidikan Jasmani Olahraga and Kesehatan,Prakarya and Kewirausahaan,Kompetensi Keahlian'
    ];

    foreach ($defaultSettings as $key => $val) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_key = setting_key");
        $stmt->execute(['key' => $key, 'val' => $val]);
    }
    echo "Settings populated.<br>";

    // 4. Also try to create the new database user and grant privileges
    try {
        $pdo->exec("GRANT ALL PRIVILEGES ON `smkb4789_kelulusan`.* TO 'smkb4789_kelulusan'@'localhost' IDENTIFIED BY 'sAtriani&sule123'");
        $pdo->exec("FLUSH PRIVILEGES");
        echo "New user created/updated and privileges granted.<br>";
    } catch (Exception $e) {
        try {
            $pdo->exec("DROP USER IF EXISTS 'smkb4789_kelulusan'@'localhost'");
            $pdo->exec("CREATE USER 'smkb4789_kelulusan'@'localhost' IDENTIFIED BY 'sAtriani&sule123'");
            $pdo->exec("GRANT ALL PRIVILEGES ON `smkb4789_kelulusan`.* TO 'smkb4789_kelulusan'@'localhost'");
            $pdo->exec("FLUSH PRIVILEGES");
            echo "New user created via ALTER/CREATE and privileges granted.<br>";
        } catch (Exception $e2) {
            echo "Warning about user creation: " . $e2->getMessage() . "<br>";
        }
    }
    echo "Setup finished successfully!<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
