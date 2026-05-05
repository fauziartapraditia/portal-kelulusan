<?php
global $pdo;

$school_name = get_setting('school_name');
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Toggle lock feature logic
if (isset($_GET['action']) && $_GET['action'] === 'toggle_lock') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $set_lock = isset($_GET['is_locked']) ? intval($_GET['is_locked']) : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE students SET is_locked = :lock WHERE id = :id");
        $stmt->execute(['lock' => $set_lock, 'id' => $id]);
    }
    header('Location: /kelulusan/admin/siswa');
    exit;
}

// Reset prank action
if (isset($_GET['action']) && $_GET['action'] === 'reset_prank') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE students SET prank_started_at = NULL, prank_duration = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    header('Location: /kelulusan/admin/siswa');
    exit;
}

// Retrieve matching students
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
    
    $page_title = "Kelola Siswa";
    include 'layout/header.php';
    ?>

    <!-- Main Content -->
    <main class="main-content container" style="padding: 48px 24px;">

        <div class="justify-between align-center mb-6" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div>
                <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Data Siswa Kelas XII</h2>
                <p class="text-secondary">Kelola daftar seluruh siswa beserta status kelulusannya.</p>
            </div>
            <a href="/kelulusan/admin/siswa/tambah" class="btn btn-primary">Tambah Siswa Baru</a>
        </div>

        <div class="card" style="padding: 24px;">
            <!-- Simple Search Bar -->
            <form action="/kelulusan/admin/siswa" method="GET" class="mb-6" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama, NISN atau No. Ujian..." value="<?php echo $search; ?>" style="flex: 1; min-width: 200px;">
                <button type="submit" class="btn btn-secondary">Cari Data</button>
                <?php if ($search !== ''): ?>
                    <a href="/kelulusan/admin/siswa" class="btn btn-secondary" style="background-color: var(--border-color);">Reset</a>
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
                                <th>Status</th>
                                <th>Prank</th>
                                <th>Status Cek</th>
                                <th>Akses SKL</th>
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
                                         <?php if ($s['status'] === 'LULUS'): ?>
                                             <span class="badge badge-success">LULUS</span>
                                         <?php elseif ($s['status'] === 'TIDAK LULUS'): ?>
                                             <span class="badge badge-danger">TIDAK LULUS</span>
                                         <?php else: ?>
                                             <span class="badge badge-warning">DITUNDA</span>
                                         <?php endif; ?>
                                    </td>
                                    <td>
                                         <?php if (($s['prank_level'] ?? '') === '1_MENIT'): ?>
                                             <span class="badge" style="background-color: #38bdf8; color: #0f172a;">1 Menit</span>
                                         <?php elseif (($s['prank_level'] ?? '') === '30_MENIT' || ($s['prank_level'] ?? '') === 'MENENGAH'): ?>
                                             <span class="badge" style="background-color: var(--warning); color: #1e1e1e;">30 Menit</span>
                                         <?php elseif (($s['prank_level'] ?? '') === '2_JAM' || ($s['prank_level'] ?? '') === 'TINGGI'): ?>
                                             <span class="badge" style="background-color: var(--error); color: white;">2 Jam</span>
                                         <?php elseif (($s['prank_level'] ?? '') === '3_JAM'): ?>
                                             <span class="badge" style="background-color: #881337; color: white;">3 Jam</span>
                                         <?php else: ?>
                                             <span class="badge" style="background-color: var(--secondary); color: white;">Normal</span>
                                         <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($s['is_viewed']) && $s['is_viewed'] == 1): ?>
                                            <span class="badge badge-success">👀 Sudah Cek</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">⏳ Belum Melihat</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($s['is_locked']) && $s['is_locked'] == 1): ?>
                                            <span class="badge badge-danger">🔒 Terkunci</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">🔓 Terbuka</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <?php if (isset($s['is_locked']) && $s['is_locked'] == 1): ?>
                                                <a href="/kelulusan/admin/siswa?action=toggle_lock&id=<?php echo $s['id']; ?>&is_locked=0" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; background-color: var(--success); color: white;">Buka Kunci</a>
                                            <?php else: ?>
                                                <a href="/kelulusan/admin/siswa?action=toggle_lock&id=<?php echo $s['id']; ?>&is_locked=1" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem;">Kunci SKL</a>
                                            <?php endif; ?>
                                            <?php if (($s['prank_level'] ?? 'NONE') !== 'NONE'): ?>
                                                <a href="/kelulusan/admin/siswa?action=reset_prank&id=<?php echo $s['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; background-color: var(--warning); color: #1e1e1e;" onclick="return confirm('Apakah Anda yakin ingin me-reset progres prank untuk siswa ini?');">Reset Prank</a>
                                            <?php endif; ?>
                                            <a href="/kelulusan/admin/siswa/edit/<?php echo $s['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem;">Edit</a>
                                            <a href="/kelulusan/admin/siswa/hapus/<?php echo $s['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </main>

<?php include 'layout/footer.php'; ?>
