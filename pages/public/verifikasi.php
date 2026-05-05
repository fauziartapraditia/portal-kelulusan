<?php
global $url_parts, $pdo;
$student_id = isset($url_parts[1]) ? intval($url_parts[1]) : 0;

$school_name = get_setting('school_name');
$student = null;
$error = '';

if ($student_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->execute(['id' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student || $student['status'] !== 'LULUS') {
        $error = "Data verifikasi tidak ditemukan atau dokumen kelulusan ini tidak valid.";
    }
} else {
    $error = "ID Verifikasi tidak valid.";
}

// Generate the Verification ID/Hash matching cetak.php
$hash_salt = "PORTAL_SMK_5_AGUSTUS_SECURE_SALT_2026";
$verification_hash = $student ? strtoupper(substr(hash('sha256', $student['id'] . $student['exam_number'] . $student['nisn'] . $hash_salt), 0, 16)) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Kelulusan - <?php echo sanitize($school_name); ?></title>
    <link rel="stylesheet" href="/kelulusan/assets/css/style.css">
    <style>
        .verify-card {
            max-width: 650px;
            margin: 60px auto;
            text-align: center;
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: var(--shadow-lg);
            border-top: 6px solid var(--success);
        }
        .verify-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar" style="border-bottom: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 100;">
        <div class="container nav-container" style="display: flex; justify-content: space-between; align-items: center; height: 85px;">
            <a href="/kelulusan/" class="brand" style="display: flex; align-items: center; gap: 14px; text-decoration: none;">
                <div class="brand-logo" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s ease;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="46" height="46">
                        <polygon points="250,20 480,180 390,460 110,460 20,180" fill="#ffffff" stroke="#ff0000" stroke-width="12" stroke-linejoin="round"/>
                        <polygon points="250,140 258,165 285,165 263,180 272,205 250,190 228,205 237,180 215,165 242,165" fill="#ff0000"/>
                        <path d="M 160,265 C 200,265 230,275 250,285 C 270,275 300,265 340,265 L 340,195 C 300,195 270,205 250,215 C 230,205 200,195 160,195 Z" fill="#ffffff" stroke="#0000ff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M 250,215 L 250,285" stroke="#0000ff" stroke-width="8" stroke-linecap="round"/>
                        <path d="M 130,310 C 180,300 320,300 370,310 L 390,345 C 340,335 160,335 110,345 Z" fill="#ffffff" stroke="#0000ff" stroke-width="6" stroke-linejoin="round"/>
                        <text x="250" y="333" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" font-size="22" fill="#0000ff" text-anchor="middle">PEKANBARU</text>
                        <path id="textPath" d="M 70,210 C 120,90 380,90 430,210" fill="none"/>
                        <text font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" font-size="28" fill="#00b050">
                            <textPath href="#textPath" startOffset="50%" text-anchor="middle">SEKOLAH MENENGAH KEJURUAN</textPath>
                        </text>
                        <text x="250" y="415" font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" font-size="36" fill="#00b050" text-anchor="middle">SMK 5 AGUSTUS</text>
                    </svg>
                </div>
                <div style="display: flex; flex-direction: column; line-height: 1.25;">
                    <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em; color: var(--text-primary);"><span style="color: var(--brand-primary);">SMK 5 AGUSTUS</span> PEKANBARU</span>
                    <span style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); letter-spacing: 0.06em; text-transform: uppercase; margin-top: 2px;">Portal Kelulusan Resmi</span>
                </div>
            </a>
            <div class="nav-links" style="display: flex; gap: 16px; align-items: center;">
                <a href="/kelulusan/home" style="font-size: 0.95rem; font-weight: 600; color: var(--brand-primary); padding: 8px 16px; border-radius: 10px; background: rgba(37, 99, 235, 0.06); transition: all 0.2s ease;">Beranda</a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content container" style="padding: 48px 24px;">

        <?php if ($error): ?>
            <div class="card text-center" style="max-width: 550px; margin: 40px auto; border-top: 6px solid var(--error);">
                <div class="alert alert-danger mb-6" style="font-size: 1.1rem;">
                    <strong>Verifikasi Gagal</strong><br>
                    <?php echo $error; ?>
                </div>
                <a href="/kelulusan/" class="btn btn-primary" style="width: 100%;">Kembali ke Beranda</a>
            </div>
        <?php else: ?>

            <div class="card verify-card">
                <div class="verify-icon" style="display: inline-flex; justify-content: center; align-items: center; width: 64px; height: 64px; background: #ecfdf5; color: #10b981; border-radius: 50%; margin: 0 auto 16px auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em; color: var(--text-primary); margin-bottom: 8px;">Dokumen Terverifikasi Sah</h2>
                <p class="text-secondary mb-6" style="font-size: 1.05rem;">Dokumen ini adalah asli dan diterbitkan secara resmi oleh pihak sekolah.</p>

                <div class="table-responsive" style="margin-bottom: 24px;">
                    <table class="table" style="text-align: left; font-size: 1rem;">
                        <tr>
                            <th style="background: transparent; border-bottom: 1px solid #f1f5f9;">Nama Siswa</th>
                            <td style="border-bottom: 1px solid #f1f5f9; color: var(--text-primary);"><strong><?php echo sanitize($student['name']); ?></strong></td>
                        </tr>
                        <tr>
                            <th style="background: transparent; border-bottom: 1px solid #f1f5f9;">NISN</th>
                            <td style="border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo sanitize($student['nisn']); ?></td>
                        </tr>
                        <tr>
                            <th style="background: transparent; border-bottom: 1px solid #f1f5f9;">Nomor Peserta</th>
                            <td style="border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo sanitize($student['exam_number']); ?></td>
                        </tr>
                        <tr>
                            <th style="background: transparent; border-bottom: 1px solid #f1f5f9;">Status Kelulusan</th>
                            <td style="border-bottom: 1px solid #f1f5f9; color: var(--success); font-weight: bold;">LULUS</td>
                        </tr>
                        <tr>
                            <th style="background: transparent; border-bottom: 1px solid #f1f5f9;">Tanggal Terbit SKL</th>
                            <td style="border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo date('d F Y', strtotime($student['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <th style="background: transparent; border-bottom: 1px solid #f1f5f9;">ID Verifikasi</th>
                            <td style="border-bottom: 1px solid #f1f5f9; color: var(--brand-primary); font-family: monospace; font-weight: bold;">#<?php echo $verification_hash; ?></td>
                        </tr>
                    </table>
                </div>

                <div class="alert alert-success" style="font-size: 0.95rem; border-left: 4px solid var(--success); margin: 0;">
                    Surat Keterangan Kelulusan ini sah untuk dipergunakan sebagaimana mestinya.
                </div>
            </div>

        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2026 <?php echo sanitize($school_name); ?>. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.altKey && e.key.toLowerCase() === 's') {
                e.preventDefault();
                window.location.href = '/kelulusan/admin/login';
            }
        });
    </script>
</body>
</html>
