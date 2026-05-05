<?php
$school_name = get_setting('school_name');
if (!isset($page_title)) {
    $page_title = "Panel Admin";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo sanitize($school_name); ?></title>
    <link rel="stylesheet" href="/kelulusan/assets/css/style.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>

    <!-- Admin Top Nav -->
    <nav class="navbar" style="border-bottom: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 100;">
        <div class="container nav-container" style="display: flex; justify-content: space-between; align-items: center; height: 85px;">
            <a href="/kelulusan/admin/dashboard" class="brand" style="display: flex; align-items: center; gap: 14px; text-decoration: none;">
                <div class="brand-logo" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s ease;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="46" height="46">
                        <polygon points="250,20 480,180 390,460 110,460 20,180" fill="#ffffff" stroke="#ff0000" stroke-width="12" stroke-linejoin="round"/>
                        <polygon points="250,140 258,165 285,165 263,180 272,205 250,190 228,205 237,180 215,165 242,165" fill="#ff0000"/>
                        <path d="M 160,265 C 200,265 230,275 250,285 C 270,275 300,265 340,265 L 340,195 C 300,195 270,205 250,215 C 230,205 200,195 160,195 Z" fill="#ffffff" stroke="#0000ff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M 250,215 L 250,285" stroke="#0000ff" stroke-width="8" stroke-linecap="round"/>
                        <path d="M 130,310 C 180,300 320,300 370,310 L 390,345 C 340,335 160,335 110,345 Z" fill="#ffffff" stroke="#0000ff" stroke-width="6" stroke-linejoin="round"/>
                        <text x="250" y="333" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" font-size="22" fill="#0000ff" text-anchor="middle">PEKANBARU</text>
                        <path id="adminTextPath" d="M 70,210 C 120,90 380,90 430,210" fill="none"/>
                        <text font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" font-size="28" fill="#00b050">
                            <textPath href="#adminTextPath" startOffset="50%" text-anchor="middle">SEKOLAH MENENGAH KEJURUAN</textPath>
                        </text>
                        <text x="250" y="415" font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" font-size="36" fill="#00b050" text-anchor="middle">SMK 5 AGUSTUS</text>
                    </svg>
                </div>
                <div style="display: flex; flex-direction: column; line-height: 1.25;">
                    <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em; color: var(--text-primary);"><span style="color: var(--brand-primary);">SMK 5 AGUSTUS</span> PANEL</span>
                    <span style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); letter-spacing: 0.06em; text-transform: uppercase; margin-top: 2px;">Portal Admin Resmi</span>
                </div>
            </a>
            <div class="nav-links" style="display: flex; gap: 10px; align-items: center;">
                <a href="/kelulusan/admin/dashboard" class="btn <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Dashboard</a>
                <a href="/kelulusan/admin/siswa" class="btn <?php echo strpos($_SERVER['REQUEST_URI'], '/siswa') !== false ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Kelola Siswa</a>
                <a href="/kelulusan/admin/nilai" class="btn <?php echo strpos($_SERVER['REQUEST_URI'], '/nilai') !== false ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Kelola Nilai</a>
                <a href="/kelulusan/admin/pengaturan" class="btn <?php echo strpos($_SERVER['REQUEST_URI'], '/pengaturan') !== false ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Pengaturan</a>
                <a href="/kelulusan/admin/panduan" class="btn <?php echo strpos($_SERVER['REQUEST_URI'], '/panduan') !== false ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Panduan</a>
                <a href="/kelulusan/admin/profil" class="btn <?php echo strpos($_SERVER['REQUEST_URI'], '/profil') !== false ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Profil</a>
                <a href="/kelulusan/admin/logout" class="btn btn-danger" style="font-size: 0.88rem; font-weight: 700; padding: 10px 16px; border-radius: 10px;">Keluar</a>
            </div>
        </div>
    </nav>
