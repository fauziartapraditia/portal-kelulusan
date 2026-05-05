<?php
$school_name = get_setting('school_name');
$announcement_date = get_setting('announcement_date');
$announcement_timestamp = strtotime($announcement_date);
$current_timestamp = time();

$is_announced = $current_timestamp >= $announcement_timestamp;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kelulusan - <?php echo sanitize($school_name); ?></title>
    <link rel="stylesheet" href="/kelulusan/assets/css/style.css">
    <style>
        body {
            background-image: linear-gradient(rgba(244, 247, 254, 0.94), rgba(255, 255, 255, 0.96)), url('/kelulusan/assets/images/premium_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .premium-hero {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.04) 0%, rgba(6, 182, 212, 0.02) 100%);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(8px);
            padding: 80px 0 60px 0;
            text-align: center;
        }
        .premium-hero h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.025em;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .premium-hero p {
            font-size: 1.15rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
        }
        .lock-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
        }
        .status-badge-portal {
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }
        .portal-live {
            background: #ecfdf5;
            color: #10b981;
            border-color: #a7f3d0;
        }
        .portal-locked {
            background: #fffbeb;
            color: #f59e0b;
            border-color: #fef3c7;
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
                <?php if (isset($_SESSION['admin_logged_in'])): ?>
                    <a href="/kelulusan/admin/dashboard" class="btn btn-secondary" style="font-size: 0.9rem; padding: 10px 18px; font-weight: 700; border-radius: 10px;">
                        Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        
        <section class="premium-hero">
            <div class="container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;">
                <div class="status-badge-portal <?php echo $is_announced ? 'portal-live' : 'portal-locked'; ?>">
                    <span style="height: 8px; width: 8px; border-radius: 50%; background-color: currentColor; display: inline-block;"></span>
                    <?php echo $is_announced ? 'PORTAL AKTIF' : 'PORTAL DIKUNCI'; ?>
                </div>
                <h1>Portal Pengumuman Kelulusan</h1>
                <p>Selamat datang di portal informasi resmi pengumuman kelulusan siswa/siswi kelas XII <?php echo sanitize($school_name); ?> Tahun Pelajaran 2025/2026.</p>
            </div>
        </section>

        <section class="container" style="max-width: 650px; width: 100%; padding: 48px 24px;">
            <div class="card text-center" style="padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
                <?php if (!$is_announced): ?>
                    <div class="lock-container">
                        <span>🔒</span> Portal Masih Terkunci
                    </div>
                    <p class="mb-6 text-secondary" style="font-size: 1.05rem;">Sistem akan terbuka secara otomatis pada waktu rilis resmi di bawah ini:</p>

                    <div class="alert alert-warning" style="font-size: 1.1rem; border-left: 4px solid var(--warning); border-radius: var(--radius-md); margin-bottom: 32px;">
                        <strong><?php echo date('d F Y', $announcement_timestamp); ?></strong> pukul <strong><?php echo date('H:i', $announcement_timestamp); ?> WIB</strong>
                    </div>

                    <!-- Enhanced Countdown Timer UI -->
                    <div class="countdown-box" id="countdown" style="margin-bottom: 12px;">
                        <div class="countdown-item" style="flex: 1; border-color: var(--warning);">
                            <div class="countdown-number" id="days" style="color: var(--warning);">00</div>
                            <div class="countdown-label">Hari</div>
                        </div>
                        <div class="countdown-item" style="flex: 1; border-color: var(--warning);">
                            <div class="countdown-number" id="hours" style="color: var(--warning);">00</div>
                            <div class="countdown-label">Jam</div>
                        </div>
                        <div class="countdown-item" style="flex: 1; border-color: var(--warning);">
                            <div class="countdown-number" id="minutes" style="color: var(--warning);">00</div>
                            <div class="countdown-label">Menit</div>
                        </div>
                        <div class="countdown-item" style="flex: 1; border-color: var(--warning);">
                            <div class="countdown-number" id="seconds" style="color: var(--warning);">00</div>
                            <div class="countdown-label">Detik</div>
                        </div>
                    </div>

                    <!-- Script Countdown -->
                    <script>
                        const announceDate = new Date("<?php echo date('Y-m-d H:i:s', $announcement_timestamp); ?>").getTime();
                        
                        const timer = setInterval(function() {
                            const now = new Date().getTime();
                            const distance = announceDate - now;

                            if (distance < 0) {
                                clearInterval(timer);
                                window.location.reload();
                                return;
                            }

                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            document.getElementById("days").innerText = days.toString().padStart(2, '0');
                            document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
                            document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
                            document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');
                        }, 1000);
                    </script>
                <?php else: ?>
                    <h3 class="mb-4" style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">Periksa Status Kelulusan Anda</h3>
                    <p class="mb-6 text-secondary" style="font-size: 1rem;">Silakan masukkan NISN atau Nomor Peserta Ujian Anda secara lengkap.</p>
                    
                    <form action="/kelulusan/cek-kelulusan" method="POST">
                        <div class="form-group">
                            <label for="identifier" class="form-label" style="font-size: 0.95rem; font-weight: 600;">NISN atau Nomor Peserta</label>
                            <input type="text" id="identifier" name="identifier" class="form-control" style="height: 52px; font-size: 1.05rem;" placeholder="Contoh: 0012345678 atau 12-345-678-9" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1.05rem; margin-top: 8px;">Periksa Sekarang</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>

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
