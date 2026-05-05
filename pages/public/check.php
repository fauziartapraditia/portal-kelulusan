<?php
$school_name = get_setting('school_name');
$announcement_date = get_setting('announcement_date');
if (time() < strtotime($announcement_date)) {
    header('Location: /kelulusan/home');
    exit;
}

$identifier = isset($_POST['identifier']) ? sanitize($_POST['identifier']) : '';
$student = null;
$error = '';

if ($identifier !== '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE exam_number = :id OR nisn = :id2");
    $stmt->execute(['id' => $identifier, 'id2' => $identifier]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $error = "Data tidak ditemukan. Pastikan NISN atau Nomor Peserta Ujian yang Anda masukkan sudah benar.";
    } else {
        // Mark as viewed
        $stmt2 = $pdo->prepare("UPDATE students SET is_viewed = 1 WHERE id = :id");
        $stmt2->execute(['id' => $student['id']]);
        $student['is_viewed'] = 1;

        if (($student['prank_level'] ?? 'NONE') !== 'NONE') {
            if (empty($student['prank_started_at'])) {
                $started_at = date('Y-m-d H:i:s');
                $duration = 0;
                if ($student['prank_level'] === '1_MENIT' || $student['prank_level'] === 'MENENGAH') {
                    $duration = 60; // 1 minute
                } elseif ($student['prank_level'] === '30_MENIT') {
                    $duration = 1800; // 30 minutes
                } elseif ($student['prank_level'] === '2_JAM' || $student['prank_level'] === 'TINGGI') {
                    $duration = 7200; // 2 hours
                } elseif ($student['prank_level'] === '3_JAM') {
                    $duration = 10800; // 3 hours
                }

                $stmt = $pdo->prepare("UPDATE students SET prank_started_at = :start, prank_duration = :dur WHERE id = :id");
                $stmt->execute(['start' => $started_at, 'dur' => $duration, 'id' => $student['id']]);

                $student['prank_started_at'] = $started_at;
                $student['prank_duration'] = $duration;
            }

            // Calculate current progress based on the time elapsed since started
            $started_time = strtotime($student['prank_started_at']);
            $elapsed = time() - $started_time;
            $prank_duration = intval($student['prank_duration']);

            // Calculate base starting percentage deterministically based on NISN (between 5% and 15%)
            $base_percentage = (intval(substr($student['nisn'], -2)) % 11) + 5;

            if ($prank_duration > 0) {
                $time_pct = min(100, max(0, intval(($elapsed / $prank_duration) * (100 - $base_percentage))));
                $progress_percentage = $base_percentage + $time_pct;
                $progress_percentage = min(100, $progress_percentage);
            } else {
                $progress_percentage = 100;
            }

            // Store calculated progress
            $student['server_calculated_progress'] = $progress_percentage;
        }
    }
} else {
    // If accessed directly without post data, redirect to home
    header('Location: /kelulusan/home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kelulusan - <?php echo sanitize($school_name); ?></title>
    <link rel="stylesheet" href="/kelulusan/assets/css/style.css">
    <style>
        .result-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .result-status-card {
            border-radius: var(--radius-lg);
            padding: 32px;
            margin-bottom: 32px;
            text-align: center;
            border: 1px solid transparent;
            box-shadow: var(--shadow-md);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .status-lulus {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #a7f3d0;
            color: #065f46;
        }
        .status-tidak-lulus {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fecaca;
            color: #991b1b;
        }
        .status-ditunda {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: #fcd34d;
            color: #92400e;
        }
        .info-steps {
            text-align: left;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
            margin-top: 32px;
        }
        .info-steps h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-steps ul {
            padding-left: 20px;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }
        .info-steps li {
            margin-bottom: 8px;
        }
        .info-steps li:last-child {
            margin-bottom: 0;
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
                <a href="/kelulusan/home" class="btn btn-secondary" style="font-size: 0.9rem; padding: 10px 18px; font-weight: 700; gap: 8px; border-radius: 10px; display: flex; align-items: center;">Kembali</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content container" style="max-width: 800px; width:100%; padding: 48px 24px;">
        
        <?php if ($error): ?>
            <div class="card text-center" style="max-width: 550px; margin: 40px auto;">
                <div class="alert alert-danger mb-6" style="font-size: 1.05rem;">
                    <strong>Pencarian Gagal</strong><br>
                    <?php echo $error; ?>
                </div>
                <a href="/kelulusan/home" class="btn btn-primary" style="width: 100%; height: 48px;">Coba Lagi</a>
            </div>
        <?php else: ?>

            <div class="result-header">
                <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 8px; color: var(--text-primary);">Detail Pengumuman Kelulusan</h2>
                <p style="font-size: 1.05rem; color: var(--text-secondary);">Hasil verifikasi data kelulusan peserta didik tahun ajaran 2025/2026</p>
            </div>

            <!-- Custom status-based styling card -->
            <?php 
                $status_class = 'status-lulus';
                if ($student['status'] === 'TIDAK LULUS') {
                    $status_class = 'status-tidak-lulus';
                } elseif ($student['status'] === 'DITUNDA') {
                    $status_class = 'status-ditunda';
                }
            ?>
            <div class="result-status-card <?php echo $status_class; ?>">
                <span style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 6px;">STATUS KELULUSAN ANDA:</span>
                <h1 style="font-size: 3rem; font-weight: 900; letter-spacing: -0.015em; margin: 0; line-height: 1.1; text-transform: uppercase;"><?php echo $student['status']; ?></h1>
            </div>

            <!-- Detailed Table & Action Steps -->
            <div class="card" style="padding: 32px;">
                <h3 class="mb-4" style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">Data Biodata Siswa</h3>

                <div class="table-responsive" style="border: none; margin-bottom: 32px;">
                    <table class="table" style="font-size: 1rem; width: 100%;">
                        <tr>
                            <th style="width: 35%; padding: 12px 0; background: transparent; border-bottom: 1px solid #f1f5f9;">Nama Lengkap</th>
                            <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: var(--text-primary);">
                                <strong><?php echo sanitize($student['name']); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding: 12px 0; background: transparent; border-bottom: 1px solid #f1f5f9;">Nomor Peserta Ujian</th>
                            <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo sanitize($student['exam_number']); ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px 0; background: transparent; border-bottom: 1px solid #f1f5f9;">NISN</th>
                            <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo sanitize($student['nisn']); ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px 0; background: transparent; border-bottom: 1px solid #f1f5f9;">Kompetensi Keahlian</th>
                            <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo sanitize($student['major']); ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px 0; background: transparent; border-bottom: 1px solid #f1f5f9;">Kelas</th>
                            <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: var(--text-secondary);"><?php echo sanitize($student['class_name']); ?></td>
                        </tr>
                        <?php if ($student['notes']): ?>
                        <tr>
                            <th style="padding: 12px 0; background: transparent; border-bottom: 1px solid #f1f5f9;">Catatan Tambahan</th>
                            <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: var(--text-primary); font-weight: 500;"><?php echo sanitize($student['notes']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- Guidance & Actions -->
                <?php if ($student['status'] === 'LULUS'): ?>
                    <div class="alert alert-success" style="font-size: 1rem; border-left: 4px solid var(--success); border-radius: var(--radius-md);">
                        <strong>Selamat!</strong> Anda dinyatakan <strong>LULUS</strong> dari SMK 5 AGUSTUS PEKANBARU. Terima kasih atas kerja keras dan dedikasi Anda selama ini.
                    </div>

                    <?php if (isset($student['is_locked']) && $student['is_locked'] == 1): ?>
                        <div class="alert alert-warning" style="font-size: 1rem; border-left: 4px solid var(--warning); border-radius: var(--radius-md); margin-top: 16px;">
                            <strong>Pencetakan SKL Ditangguhkan:</strong> Mohon maaf, pencetakan Surat Keterangan Kelulusan (SKL) Anda dikunci. Silakan hubungi bagian Administrasi Sekolah untuk informasi lebih lanjut.
                        </div>
                    <?php else: ?>
                        <a href="/kelulusan/surat-kelulusan/<?php echo $student['id']; ?>" class="btn btn-primary" style="width: 100%; height: 54px; margin-top: 16px; font-size: 1.05rem;" target="_blank">
                            Cetak Surat Keterangan Kelulusan (SKL)
                        </a>
                    <?php endif; ?>

                    <!-- Informative Next Steps -->
                    <div class="info-steps">
                        <h4>Langkah Selanjutnya</h4>
                        <ul>
                            <li><strong>Unduh dan cetak</strong> Surat Keterangan Kelulusan (SKL) di atas sebagai bukti sementara kelulusan Anda.</li>
                            <li>Bawa hasil cetak SKL ke bagian tata usaha sekolah untuk proses legalisir jika diperlukan.</li>
                            <li>Pantau informasi resmi sekolah mengenai jadwal pengambilan Ijazah asli dan berkas kelulusan lainnya.</li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" style="border-left: 4px solid var(--warning); border-radius: var(--radius-md); font-size: 1rem;">
                        <strong>Informasi:</strong> Status kelulusan Anda adalah <strong><?php echo $student['status']; ?></strong>. Harap segera menghubungi wali kelas atau berkonsultasi langsung dengan pihak sekolah.
                    </div>
                    <a href="/kelulusan/home" class="btn btn-secondary" style="width: 100%; height: 50px; margin-top: 16px;">Kembali ke Beranda</a>
                <?php endif; ?>
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

    <?php if ($student && ($student['prank_level'] ?? 'NONE') !== 'NONE'): ?>
        <!-- Prank Loading Overlay -->
        <div id="prank-loading-overlay" style="position: fixed; top:0; left:0; width:100%; height:100%; background: #0f172a; z-index: 99999; display: flex; align-items: center; justify-content: center; color: #fff; font-family: sans-serif; overflow: hidden; padding: 20px;">
            <div style="max-width: 600px; width: 100%; background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 32px; box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4); backdrop-filter: blur(20px); text-align: left;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 10px #ef4444;" id="status-pulse"></div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #f8fafc; margin: 0; letter-spacing: -0.02em;" id="prank-title">PENGALUHAN SINKRONISASI DATA</h3>
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #94a3b8; background: rgba(255,255,255,0.05); padding: 4px 12px; border-radius: 20px;" id="prank-pct">0%</div>
                </div>

                <!-- Custom loading progress bar -->
                <div style="width: 100%; height: 8px; background: rgba(255, 255, 255, 0.06); border-radius: 10px; margin-bottom: 24px; overflow: hidden;">
                    <div id="prank-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3b82f6 0%, #06b6d4 100%); transition: width 0.3s ease; box-shadow: 0 0 8px rgba(59, 130, 246, 0.6);"></div>
                </div>

                <!-- Unique scary messages container -->
                <div id="prank-console" style="background: #090d16; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 18px; height: 220px; overflow-y: auto; font-family: 'Courier New', Courier, monospace; font-size: 0.88rem; color: #38bdf8; line-height: 1.6; display: flex; flex-direction: column-reverse;">
                    <!-- Output will append here from newest to oldest -->
                </div>

                <div style="margin-top: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #64748b;">
                    <span style="font-size: 1rem;">⚠️</span>
                    <span id="prank-warning-text">Mohon jangan menutup atau memuat ulang halaman ini. Sedang melakukan sinkronisasi data kritis dengan server dinas.</span>
                </div>

            </div>
        </div>

        <script>
        (function() {
            const studentId = "<?= $student['id'] ?>";
            const studentName = "<?= addslashes(sanitize($student['name'])) ?>";
            const prankLevel = "<?= $student['prank_level'] ?>";
            
            let logs = [];
            if (prankLevel === '1_MENIT') {
                logs = [
                    { p: 1, t: "Melakukan verifikasi identitas untuk siswa <?= addslashes(sanitize($student['name'])) ?>... [OK]" },
                    { p: 1, t: "Membaca berkas kelulusan lokal kelas <?= addslashes(sanitize($student['class_name'])) ?>..." },
                    { p: 1, t: "Sinkronisasi tanda tangan elektronik Kepala Sekolah..." },
                    { p: 1, t: "Memvalidasi sertifikat keamanan server untuk NISN <?= addslashes(sanitize($student['nisn'])) ?>..." },
                    { p: 100, t: "Verifikasi berhasil. Membuka halaman..." }
                ];
                document.getElementById('prank-title').innerText = "VERIFIKASI DATA";
                document.getElementById('prank-warning-text').innerText = "Sedang memproses data kelulusan Anda secara aman.";
            } else if (prankLevel === '30_MENIT' || prankLevel === 'MENENGAH') {
                logs = [
                    { p: 1, t: "Menghubungkan ke database server Dinas Pendidikan... [OK]" },
                    { p: 1, t: "Mengunduh berkas transkrip nilai untuk jurusan <?= addslashes(sanitize($student['major'])) ?>..." },
                    { p: 1, t: "Ditemukan perbedaan data sinkronisasi pada file student_<?= $student['id'] ?>.json!" },
                    { p: 1, t: "Sistem mendeteksi kelas <?= addslashes(sanitize($student['class_name'])) ?> belum tervalidasi sepenuhnya oleh pengawas." },
                    { p: 1, t: "Meminta validasi ulang nilai rapor Semester 1 s/d 5 atas nama <?= addslashes(sanitize($student['name'])) ?>..." },
                    { p: 1, t: "Sedang mencoba memulihkan status kelulusan secara otomatis..." },
                    { p: 1, t: "Berhasil memvalidasi nilai siswa." },
                    { p: 100, t: "Pengalihan data selesai sepenuhnya." }
                ];
                document.getElementById('prank-title').innerText = "SINKRONISASI DATA";
                document.getElementById('prank-warning-text').innerText = "Sedang mensinkronisasi data nilai ke Pusat Data Nasional.";
            } else if (prankLevel === '2_JAM' || prankLevel === 'TINGGI') {
                logs = [
                    { p: 1, t: "Mengakses API Pusat Penilaian Pendidikan... [OK]" },
                    { p: 1, t: "PERINGATAN: Terdeteksi upaya manipulasi nilai ujian pada siswa <?= addslashes(sanitize($student['name'])) ?>!" },
                    { p: 1, t: "Status Validasi: TIDAK VALID (Error 401: Data Integrity Compromised for NISN <?= addslashes(sanitize($student['nisn'])) ?>)" },
                    { p: 1, t: "Mengecek riwayat pelanggaran tata tertib sekolah dari Database Kesiswaan kelas <?= addslashes(sanitize($student['class_name'])) ?>..." },
                    { p: 1, t: "Menghubungi server Dinas Pendidikan Provinsi Riau untuk pelaporan pelanggaran..." },
                    { p: 1, t: "Nilai dibekukan sementara. Menunggu keputusan rapat dewan guru..." },
                    { p: 1, t: "Penyelidikan internal selesai. Sistem mengabaikan kesalahan data." },
                    { p: 100, t: "Data berhasil dipulihkan secara penuh. Memuat halaman kelulusan..." }
                ];
                document.getElementById('prank-title').innerText = "PERINGATAN VALIDASI DATA";
                document.getElementById('prank-title').style.color = "#ef4444";
                document.getElementById('prank-progress-bar').style.background = "linear-gradient(90deg, #ef4444 0%, #f97316 100%)";
                document.getElementById('prank-console').style.color = "#f43f5e";
                document.getElementById('prank-warning-text').innerText = "PERINGATAN KRITIS: Terjadi kegagalan validasi. Hubungi Admin Sistem.";
            } else if (prankLevel === '3_JAM') {
                logs = [
                    { p: 1, t: "Mengakses pangkalan data terenkripsi Pusat Penilaian Pendidikan... [OK]" },
                    { p: 12, t: "Mengecek berkas kelulusan untuk siswa: <?= addslashes(sanitize($student['name'])) ?> [NISN: <?= addslashes(sanitize($student['nisn'])) ?>]..." },
                    { p: 28, t: "PERINGATAN KRITIS: Terdeteksi ketidakcocokan tanda tangan digital pada data nilai semester 1-5." },
                    { p: 45, t: "Sistem mendeteksi indikasi modifikasi nilai yang tidak sah pada server lokal untuk jurusan <?= addslashes(sanitize($student['major'])) ?>." },
                    { p: 62, t: "Status Akses: DIBEKUKAN (Error code: 0x80070005 - Access Denied for student_<?= $student['id'] ?>)" },
                    { p: 78, t: "Menghubungi server pengawas Dinas Pendidikan Provinsi untuk audit forensik data..." },
                    { p: 90, t: "Berkas ditangguhkan. Menunggu klarifikasi dan verifikasi manual oleh admin sistem sekolah." },
                    { p: 100, t: "Audit forensik selesai. Sistem memulihkan akses secara darurat. Memuat laporan..." }
                ];
                document.getElementById('prank-title').innerText = "PROSES VALIDASI DATA";
                document.getElementById('prank-title').style.color = "#ef4444";
                document.getElementById('status-pulse').style.background = "#ef4444";
                document.getElementById('status-pulse').style.boxShadow = "0 0 12px #ef4444";
                document.getElementById('prank-progress-bar').style.background = "linear-gradient(90deg, #ef4444 0%, #7f1d1d 100%)";
                document.getElementById('prank-console').style.color = "#fca5a5";
                document.getElementById('prank-warning-text').innerText = "PROSES SINKRONISASI: Terjadi kesalahan data";
            } else {
                logs = [
                    { p: 1, t: "Memulai verifikasi data untuk <?= addslashes(sanitize($student['name'])) ?>..." },
                    { p: 100, t: "Berhasil." }
                ];
            }

            let startedTime = parseInt("<?= strtotime($student['prank_started_at']) ?>") || 0;
            let duration = parseInt("<?= $student['prank_duration'] ?>") || 1;

            function getRealtimeProgress() {
                let now = Math.floor(Date.now() / 1000);
                let elapsed = now - startedTime;
                if (duration > 0) {
                    return Math.min(100, Math.max(0, Math.floor((elapsed / duration) * 100)));
                }
                return 100;
            }

            let currentProgress = getRealtimeProgress();
            
            if (currentProgress >= 100) {
                document.getElementById('prank-loading-overlay').style.display = 'none';
                return;
            }

            const pctEl = document.getElementById('prank-pct');
            const barEl = document.getElementById('prank-progress-bar');
            const consoleEl = document.getElementById('prank-console');

            function updateUI(pct) {
                pctEl.innerText = pct + "%";
                barEl.style.width = pct + "%";
                
                // Generate visible console logs based on percentage
                let logHtml = "";
                logs.forEach(function(item) {
                    if (pct >= item.p) {
                        logHtml = `<div style="margin-bottom: 6px; color: ${item.p === 100 ? '#10b981' : ''}">${item.t}</div>` + logHtml;
                    }
                });
                consoleEl.innerHTML = logHtml;
            }

            updateUI(currentProgress);

            let interval = setInterval(function() {
                currentProgress = getRealtimeProgress();
                updateUI(currentProgress);

                if (currentProgress >= 100) {
                    clearInterval(interval);
                    setTimeout(function() {
                        // Smooth fadeout
                        const overlay = document.getElementById('prank-loading-overlay');
                        overlay.style.transition = "opacity 0.5s ease";
                        overlay.style.opacity = 0;
                        setTimeout(function() {
                            overlay.style.display = 'none';
                        }, 500);
                    }, 2000);
                }
            }, 1000);

        })();
        </script>
    <?php endif; ?>
</body>
</html>
