<?php
global $url_parts, $pdo;
$school_name = get_setting('school_name');
$student_id = isset($url_parts[3]) ? intval($url_parts[3]) : 0;

if ($student_id <= 0) {
    header('Location: /kelulusan/admin/siswa');
    exit;
}

// Fetch current student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: /kelulusan/admin/siswa');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $exam_number = isset($_POST['exam_number']) ? sanitize($_POST['exam_number']) : '';
    $nisn = isset($_POST['nisn']) ? sanitize($_POST['nisn']) : '';
    $major = isset($_POST['major']) ? sanitize($_POST['major']) : '';
    $class_name = isset($_POST['class_name']) ? sanitize($_POST['class_name']) : '';
    $status = isset($_POST['status']) ? sanitize($_POST['status']) : 'LULUS';
    $notes = isset($_POST['notes']) ? sanitize($_POST['notes']) : '';
    $prank_level = isset($_POST['prank_level']) ? sanitize($_POST['prank_level']) : 'NONE';

    $scores = isset($_POST['scores']) ? $_POST['scores'] : [];
    $subject_scores_json = json_encode($scores);

    if ($name !== '' && $exam_number !== '' && $nisn !== '') {
        // Check for uniqueness of exam number and nisn
        $stmt = $pdo->prepare("SELECT id FROM students WHERE (exam_number = :ex OR nisn = :nisn) AND id != :id");
        $stmt->execute(['ex' => $exam_number, 'nisn' => $nisn, 'id' => $student_id]);
        if ($stmt->fetch()) {
            $error = "Nomor Ujian atau NISN tersebut sudah terdaftar untuk siswa lain.";
        } else {
            // If prank level changed, reset the start time so the prank resets for that student
            if (($student['prank_level'] ?? 'NONE') !== $prank_level) {
                $stmt = $pdo->prepare("UPDATE students SET prank_started_at = NULL, prank_duration = 0 WHERE id = :id");
                $stmt->execute(['id' => $student_id]);
            }

            // Update student
            $stmt = $pdo->prepare("UPDATE students SET exam_number = :ex, nisn = :nisn, name = :name, major = :major, class_name = :class_name, status = :status, notes = :notes, subject_scores = :subject_scores, prank_level = :prank_level WHERE id = :id");
            $stmt->execute([
                'ex' => $exam_number,
                'nisn' => $nisn,
                'name' => $name,
                'major' => $major,
                'class_name' => $class_name,
                'status' => $status,
                'notes' => $notes,
                'subject_scores' => $subject_scores_json,
                'prank_level' => $prank_level,
                'id' => $student_id
            ]);

            // Re-fetch to present fresh updated details in form inputs
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
            $stmt->execute(['id' => $student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            $success = "Perubahan data siswa berhasil disimpan!";
        }
    } else {
        $error = "Mohon lengkapi data Nama, Nomor Ujian, dan NISN.";
    }
}
$page_title = "Edit Siswa";
include 'layout/header.php';
?>

    <!-- Main Content Area -->
    <main class="main-content container" style="max-width: 800px; width:100%; padding: 48px 24px;">

        <div class="mb-6">
            <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Edit Data Siswa</h2>
            <p class="text-secondary">Ubah biodata dan status kelulusan siswa.</p>
        </div>

        <div class="card">
            <?php if ($error): ?>
                <div class="alert alert-danger mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="/kelulusan/admin/siswa/edit/<?php echo $student_id; ?>" method="POST">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap Siswa <span style="color: var(--error);">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo sanitize($student['name']); ?>" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="exam_number" class="form-label">Nomor Peserta Ujian <span style="color: var(--error);">*</span></label>
                        <input type="text" id="exam_number" name="exam_number" class="form-control" value="<?php echo sanitize($student['exam_number']); ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <div class="form-group">
                        <label for="nisn" class="form-label">NISN Siswa <span style="color: var(--error);">*</span></label>
                        <input type="text" id="nisn" name="nisn" class="form-control" value="<?php echo sanitize($student['nisn']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="major" class="form-label">Kompetensi Keahlian (Jurusan) <span style="color: var(--error);">*</span></label>
                        <input type="text" id="major" name="major" class="form-control" value="<?php echo sanitize($student['major']); ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <div class="form-group">
                        <label for="class_name" class="form-label">Kelas <span style="color: var(--error);">*</span></label>
                        <input type="text" id="class_name" name="class_name" class="form-control" value="<?php echo sanitize($student['class_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status Kelulusan <span style="color: var(--error);">*</span></label>
                        <select id="status" name="status" class="form-control" style="height: 50px;" required>
                            <option value="LULUS" <?php echo $student['status'] === 'LULUS' ? 'selected' : ''; ?>>LULUS</option>
                            <option value="TIDAK LULUS" <?php echo $student['status'] === 'TIDAK LULUS' ? 'selected' : ''; ?>>TIDAK LULUS</option>
                            <option value="DITUNDA" <?php echo $student['status'] === 'DITUNDA' ? 'selected' : ''; ?>>DITUNDA</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="prank_level" class="form-label">Level Prank Siswa</label>
                    <select id="prank_level" name="prank_level" class="form-control" style="height: 50px;">
                        <option value="NONE" <?php echo ($student['prank_level'] ?? 'NONE') === 'NONE' ? 'selected' : ''; ?>>Tidak Ada (Normal)</option>
                        <option value="1_MENIT" <?php echo ($student['prank_level'] ?? '') === '1_MENIT' ? 'selected' : ''; ?>>Prank 1 Menit - Notif Normal</option>
                        <option value="30_MENIT" <?php echo ($student['prank_level'] ?? '') === '30_MENIT' ? 'selected' : ''; ?>>Prank 30 Menit - Notif Sedang</option>
                        <option value="2_JAM" <?php echo ($student['prank_level'] ?? '') === '2_JAM' ? 'selected' : ''; ?>>Prank 2 Jam - Notif Tinggi</option>
                        <option value="3_JAM" <?php echo ($student['prank_level'] ?? '') === '3_JAM' ? 'selected' : ''; ?>>Prank 3 Jam - Notif Sangat Tinggi & Menakutkan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan Tambahan (Opsional)</label>
                    <textarea id="notes" name="notes" class="form-control" style="height: 100px; resize: vertical;"><?php echo sanitize($student['notes']); ?></textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Perubahan</button>
                    <a href="/kelulusan/admin/siswa" class="btn btn-secondary" style="flex: 1;">Kembali ke Daftar</a>
                </div>

            </form>
        </div>

    </main>

<?php include 'layout/footer.php'; ?>
