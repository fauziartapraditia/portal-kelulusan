<?php
global $url_parts, $pdo;
$student_id = isset($url_parts[3]) ? intval($url_parts[3]) : 0;

if ($student_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $stmt->execute(['id' => $student_id]);
}

header('Location: /kelulusan/admin/siswa');
exit;
