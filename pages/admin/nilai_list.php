<?php
global $pdo;

$school_name = get_setting('school_name');
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$student = null;
$error = '';
$success = '';

// Mode 2: Handling specific student score edits
if ($student_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->execute(['id' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        header('Location: /kelulusan/admin/nilai');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $scores = isset($_POST['scores']) ? $_POST['scores'] : [];
        $subject_scores_json = json_encode($scores);

        $stmt = $pdo->prepare("UPDATE students SET subject_scores = :scores WHERE id = :id");
        $stmt->execute(['scores' => $subject_scores_json, 'id' => $student_id]);

        $success = "Nilai mata pelajaran untuk " . sanitize($student['name']) . " berhasil disimpan!";
        // Refetch fresh details
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Mode 1: Fetching students list for inputting scores
$students = [];
if ($student_id === 0) {
    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE :search OR exam_number LIKE :search2 OR nisn LIKE :search3 ORDER BY name ASC");
        $stmt->execute([
            'search' => "%$search%",
            'search2' => "%$search%",
            'search3' => "%$search%"
        ]);
    } else {
        $stmt = $pdo->query("SELECT * FROM students ORDER BY name ASC");
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$page_title = "Kelola Nilai Siswa";
include 'layout/header.php';
?>

    <!-- Main Content Area -->
    <main class="main-content container" style="padding: 48px 24px;">

        <?php if ($student_id > 0 && $student): ?>
            <!-- Specific Student Scoring View (Mode 2) -->
            <div class="mb-6">
                <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Input Nilai Siswa</h2>
                <p class="text-secondary">Kelola nilai mata pelajaran untuk <strong><?php echo sanitize($student['name']); ?></strong> (NISN: <?php echo sanitize($student['nisn']); ?>)</p>
            </div>

            <div class="card">
                <?php if ($success): ?>
                    <div class="alert alert-success mb-4">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form action="/kelulusan/admin/nilai?id=<?php echo $student['id']; ?>" method="POST">
                    
                    <h3 class="mb-4" style="font-size: 1.15rem; color: var(--brand-primary); border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">Nilai Mata Pelajaran Siswa</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px;">
                        <?php
                        $subjects_setting = get_setting('subjects');
                        $subject_array = array_filter(array_map('trim', explode(',', $subjects_setting)));
                        $existing_scores = !empty($student['subject_scores']) ? json_decode($student['subject_scores'], true) : [];
                        if (!is_array($existing_scores)) { $existing_scores = []; }

                        foreach ($subject_array as $sub):
                            $score_val = isset($existing_scores[$sub]) ? $existing_scores[$sub] : '';
                        ?>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;" title="<?php echo sanitize($sub); ?>"><?php echo sanitize($sub); ?></label>
                                <input type="number" step="0.01" min="0" max="100" name="scores[<?php echo sanitize($sub); ?>]" class="form-control" style="height: 46px;" placeholder="Nilai" value="<?php echo sanitize($score_val); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; height: 48px;">Simpan Nilai Siswa</button>
                        <a href="/kelulusan/admin/nilai" class="btn btn-secondary" style="flex: 1; height: 48px; display: inline-flex; align-items: center; justify-content: center;">Kembali ke Daftar</a>
                    </div>

                </form>
            </div>

        <?php else: ?>
            <!-- Student Selection List View (Mode 1) -->
            <div class="mb-6">
                <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Kelola Nilai Siswa</h2>
                <p class="text-secondary">Pilih siswa untuk menginput atau memperbarui nilai mata pelajaran SKL.</p>
            </div>

            <div class="card" style="padding: 24px;">
                <!-- Simple Search Bar -->
                <form action="/kelulusan/admin/nilai" method="GET" class="mb-6" style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama, NISN atau No. Ujian..." value="<?php echo $search; ?>" style="flex: 1; min-width: 200px;">
                    <button type="submit" class="btn btn-secondary">Cari Data</button>
                    <?php if ($search !== ''): ?>
                        <a href="/kelulusan/admin/nilai" class="btn btn-secondary" style="background-color: var(--border-color);">Reset</a>
                    <?php endif; ?>
                </form>

                <?php if (count($students) === 0): ?>
                    <div class="alert alert-warning text-center" style="margin: 0;">
                        Tidak ada data siswa ditemukan.
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="margin: 0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. Ujian</th>
                                    <th>NISN</th>
                                    <th>Jurusan</th>
                                    <th>Kelas</th>
                                    <th>Status Nilai</th>
                                    <th style="text-align: center;">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($students as $s): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo sanitize($s['name']); ?></strong></td>
                                        <td><?php echo sanitize($s['exam_number']); ?></td>
                                        <td><?php echo sanitize($s['nisn']); ?></td>
                                        <td><?php echo sanitize($s['major']); ?></td>
                                        <td><?php echo sanitize($s['class_name']); ?></td>
                                        <td>
                                            <?php if (!empty($s['subject_scores']) && $s['subject_scores'] !== '[]'): ?>
                                                <span class="badge badge-success">Sudah Diinput</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Belum Diinput</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="/kelulusan/admin/nilai?id=<?php echo $s['id']; ?>" class="btn btn-primary" style="padding: 6px 14px; font-size: 0.85rem;">Input Nilai</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>

<?php include 'layout/footer.php'; ?>
