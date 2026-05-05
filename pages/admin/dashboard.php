<?php
global $pdo;

// Fetch Quick statistics
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM students");
$total_students = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM students WHERE status = 'LULUS'");
$total_lulus = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM students WHERE status = 'TIDAK LULUS'");
$total_tidak_lulus = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM students WHERE status = 'DITUNDA'");
$total_ditunda = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$school_name = get_setting('school_name');
$announcement_date = get_setting('announcement_date');

$page_title = "Dashboard Admin";
$extra_css = '
    <style>
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .admin-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .admin-card .stat-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-secondary);
        }
        .admin-card .stat-val {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }
        .admin-card .stat-bg {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
';

include 'layout/header.php';
?>

    <!-- Admin Content Area -->
    <main class="main-content container" style="padding: 48px 24px;">

        <div class="mb-6">
            <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Selamat Datang, <?php echo sanitize($_SESSION['admin_name']); ?>!</h2>
            <p class="text-secondary">Kelola data kelulusan siswa dan pengaturan portal pengumuman secara lengkap di sini.</p>
        </div>

        <!-- Metric Grid -->
        <div class="admin-grid">
            <div class="admin-card">
                <div class="stat-bg">
                    <span class="stat-title">Total Siswa</span>
                    <span class="badge" style="background-color: var(--brand-light); color: var(--brand-primary); font-size: 0.75rem;">Siswa</span>
                </div>
                <div class="stat-val"><?php echo $total_students; ?></div>
            </div>

            <div class="admin-card">
                <div class="stat-bg">
                    <span class="stat-title">Dinyatakan Lulus</span>
                    <span class="badge badge-success" style="font-size: 0.75rem;">LULUS</span>
                </div>
                <div class="stat-val"><?php echo $total_lulus; ?></div>
            </div>

            <div class="admin-card">
                <div class="stat-bg">
                    <span class="stat-title">Tidak Lulus</span>
                    <span class="badge badge-danger" style="font-size: 0.75rem;">TIDAK LULUS</span>
                </div>
                <div class="stat-val"><?php echo $total_tidak_lulus; ?></div>
            </div>

            <div class="admin-card">
                <div class="stat-bg">
                    <span class="stat-title">Ditunda</span>
                    <span class="badge badge-warning" style="font-size: 0.75rem;">DITUNDA</span>
                </div>
                <div class="stat-val"><?php echo $total_ditunda; ?></div>
            </div>
        </div>

        <!-- Quick Info & Action Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px;">
            <div class="card" style="padding: 28px;">
                <h3 class="mb-4">Informasi Rilis Pengumuman</h3>
                <p class="mb-4 text-secondary">Status portal aktif dan rilis pengumuman kelulusan disinkronkan dengan:</p>
                <div class="alert alert-warning mb-4" style="font-size: 1.05rem;">
                    <strong><?php echo date('d F Y \p\u\k\u\l H:i \W\I\B', strtotime($announcement_date)); ?></strong>
                </div>
                <a href="/kelulusan/admin/pengaturan" class="btn btn-secondary" style="width: 100%;">Ubah Tanggal Pengumuman</a>
            </div>

            <div class="card" style="padding: 28px;">
                <h3 class="mb-4">Kelola Data Siswa</h3>
                <p class="mb-4 text-secondary">Tambahkan atau perbarui data siswa kelas XII beserta status kelulusannya.</p>
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <a href="/kelulusan/admin/siswa/tambah" class="btn btn-primary" style="flex: 1;">Tambah Siswa</a>
                    <a href="/kelulusan/admin/siswa" class="btn btn-secondary" style="flex: 1;">Daftar Siswa</a>
                </div>
            </div>

            <div class="card" style="padding: 28px;">
                <h3 class="mb-4">Input Nilai Mata Pelajaran</h3>
                <p class="mb-4 text-secondary">Masukkan nilai mata pelajaran siswa secara dinamis untuk dicantumkan di SKL.</p>
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <a href="/kelulusan/admin/nilai" class="btn btn-primary" style="flex: 1; height: 46px; display: inline-flex; align-items: center; justify-content: center;">Buka Input Nilai</a>
                </div>
            </div>
        </div>

    </main>

<?php include 'layout/footer.php'; ?>

