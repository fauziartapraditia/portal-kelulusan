<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - SMK 5 AGUSTUS PEKANBARU</title>
    <link rel="stylesheet" href="/kelulusan/assets/css/style.css">
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

    <!-- Main Content -->
    <main class="main-content container" style="display: flex; align-items: center; justify-content: center; flex: 1;">
        <div class="card text-center" style="max-width: 450px; width:100%;">
            <h1 style="font-size: 5rem; color: var(--brand-primary); margin-bottom: 8px;">404</h1>
            <h2 class="mb-4">Halaman Tidak Ditemukan</h2>
            <p class="mb-6 text-secondary">Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
            <a href="/kelulusan/" class="btn btn-primary" style="width: 100%;">Kembali ke Beranda</a>
        </div>
    </main>

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
