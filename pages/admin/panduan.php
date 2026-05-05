<?php
$school_name = get_setting('school_name');
$page_title = "Panduan Penggunaan";
$extra_css = '
    <style>
        .guide-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            margin: 20px 0;
            box-shadow: var(--shadow-md);
        }
        .guide-section {
            margin-bottom: 60px;
        }
        .guide-section h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .guide-section h3 span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--brand-primary);
            color: white;
            border-radius: 50%;
            font-size: 1rem;
        }
        .guide-content p {
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }
        .shortcut-box {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: var(--radius-md);
            margin: 20px 0;
        }
        kbd {
            background: #e2e8f0;
            border-radius: 4px;
            padding: 2px 6px;
            font-family: monospace;
            font-weight: 700;
            box-shadow: 0 2px 0 #cbd5e1;
        }
    </style>
';

include 'layout/header.php';
?>

    <!-- Main Content -->
    <main class="main-content container" style="padding: 48px 24px; max-width: 900px;">
        
        <div class="mb-8">
            <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 8px;">Panduan Penggunaan Aplikasi</h2>
            <p class="text-secondary">Pelajari cara mengoperasikan sistem pengumuman kelulusan SMK 5 AGUSTUS dengan benar di sini.</p>
        </div>

        <div class="card guide-content" style="padding: 40px;">
            
            <!-- Section 1: Beranda -->
            <div class="guide-section">
                <h3><span>1</span> Portal Siswa (Beranda)</h3>
                <p>Halaman ini adalah pintu utama bagi siswa untuk mengecek kelulusan. Dilengkapi dengan hitung mundur otomatis yang sinkron dengan jadwal pengumuman di panel admin.</p>
                <img src="/kelulusan/assets/docs/home.png" alt="Halaman Beranda">
                <p><strong>Fitur Utama:</strong> Indikator status portal (Aktif/Terkunci), Hitung Mundur, dan Form Pengecekan status menggunakan NISN.</p>
            </div>

            <!-- Section 2: Akses Admin -->
            <div class="guide-section">
                <h3><span>2</span> Cara Mengakses Panel Admin</h3>
                <p>Untuk alasan keamanan, tombol login admin disembunyikan. Anda dapat membukanya dengan menggunakan kombinasi shortcut keyboard di halaman utama.</p>
                <div class="shortcut-box">
                    Tekan bersamaan: <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>S</kbd>
                </div>
                <img src="/kelulusan/assets/docs/login.png" alt="Halaman Login">
                <p>Gunakan kredensial admin yang telah diberikan (Username: <code>admin</code>, Password: <code>admin123</code>).</p>
            </div>

            <!-- Section 3: Dashboard -->
            <div class="guide-section">
                <h3><span>3</span> Dashboard Statistik</h3>
                <p>Dashboard memberikan ringkasan cepat mengenai status kelulusan seluruh siswa secara real-time.</p>
                <img src="/kelulusan/assets/docs/dashboard.png" alt="Dashboard">
                <p>Anda dapat melihat total siswa yang lulus, tidak lulus, dan yang statusnya masih ditunda.</p>
            </div>

            <!-- Section 4: Kelola Siswa -->
            <div class="guide-section">
                <h3><span>4</span> Manajemen Data Siswa</h3>
                <p>Gunakan menu ini untuk menambah, mengedit, atau menghapus data siswa kelas XII. Anda juga dapat mengatur status kelulusan di sini.</p>
                <img src="/kelulusan/assets/docs/siswa.png" alt="Kelola Siswa">
                <p><strong>Penting:</strong> Pastikan Nomor Ujian dan NISN diinput dengan benar agar siswa tidak kesulitan saat mengecek status.</p>
            </div>

            <!-- Section 5: Kelola Nilai -->
            <div class="guide-section">
                <h3><span>5</span> Input Nilai SKL</h3>
                <p>Menu ini digunakan untuk mengisi nilai mata pelajaran yang akan muncul di Surat Keterangan Lulus (SKL) digital siswa.</p>
                <img src="/kelulusan/assets/docs/nilai.png" alt="Input Nilai">
                <p>Nilai harus diinput lengkap agar tampilan SKL saat dicetak oleh siswa terlihat profesional dan valid.</p>
            </div>

            <!-- Section 6: Pengaturan -->
            <div class="guide-section">
                <h3><span>6</span> Pengaturan Sistem</h3>
                <p>Konfigurasikan identitas sekolah, jadwal pengumuman, dan data kepala sekolah.</p>
                <img src="/kelulusan/assets/docs/pengaturan.png" alt="Pengaturan">
                <p>Pastikan jadwal pengumuman sesuai dengan instruksi dinas/sekolah agar portal terbuka tepat waktu.</p>
            </div>

        </div>

    </main>

<?php include 'layout/footer.php'; ?>
