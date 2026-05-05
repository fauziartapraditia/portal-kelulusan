<?php
global $pdo;
$school_name = get_setting('school_name');
$school_address = get_setting('school_address');
$announcement_date = get_setting('announcement_date');
$principal_name = get_setting('principal_name');
$principal_nip = get_setting('principal_nip');
$letter_header = get_setting('letter_header');

$subjects = get_setting('subjects');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_school_name = isset($_POST['school_name']) ? sanitize($_POST['school_name']) : '';
    $new_school_address = isset($_POST['school_address']) ? sanitize($_POST['school_address']) : '';
    $new_announcement_date = isset($_POST['announcement_date']) ? sanitize($_POST['announcement_date']) : '';
    $new_principal_name = isset($_POST['principal_name']) ? sanitize($_POST['principal_name']) : '';
    $new_principal_nip = isset($_POST['principal_nip']) ? sanitize($_POST['principal_nip']) : '';
    $new_letter_header = isset($_POST['letter_header']) ? sanitize($_POST['letter_header']) : '';
    $new_subjects = isset($_POST['subjects']) ? sanitize($_POST['subjects']) : '';

    if ($new_school_name !== '' && $new_announcement_date !== '') {
        set_setting('school_name', $new_school_name);
        set_setting('school_address', $new_school_address);
        set_setting('announcement_date', $new_announcement_date);
        set_setting('principal_name', $new_principal_name);
        set_setting('principal_nip', $new_principal_nip);
        set_setting('letter_header', $new_letter_header);
        set_setting('subjects', $new_subjects);

        // Update working variables
        $school_name = $new_school_name;
        $school_address = $new_school_address;
        $announcement_date = $new_announcement_date;
        $principal_name = $new_principal_name;
        $principal_nip = $new_principal_nip;
        $letter_header = $new_letter_header;
        $subjects = $new_subjects;

        $success = "Pengaturan portal berhasil disimpan!";
    } else {
        $error = "Nama Sekolah dan Tanggal Pengumuman wajib diisi.";
    }
}
$page_title = "Pengaturan Sistem";
include 'layout/header.php';
?>

    <!-- Main Content Area -->
    <main class="main-content container" style="max-width: 800px; width:100%; padding: 48px 24px;">

        <div class="mb-6">
            <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Pengaturan Sistem</h2>
            <p class="text-secondary">Ubah konfigurasi sekolah, identitas kepala sekolah, dan jadwal pengumuman.</p>
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

            <form action="/kelulusan/admin/pengaturan" method="POST">
                
                <h3 class="mb-4" style="font-size: 1.15rem; color: var(--brand-primary); border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">Identitas Sekolah & Portal</h3>
                <div class="form-group">
                    <label for="school_name" class="form-label">Nama Sekolah <span style="color: var(--error);">*</span></label>
                    <input type="text" id="school_name" name="school_name" class="form-control" value="<?php echo sanitize($school_name); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="school_address" class="form-label">Alamat Lengkap Sekolah</label>
                    <input type="text" id="school_address" name="school_address" class="form-control" value="<?php echo sanitize($school_address); ?>">
                </div>

                <div class="form-group">
                    <label for="announcement_date" class="form-label">Tanggal & Waktu Pengumuman <span style="color: var(--error);">*</span></label>
                    <input type="text" id="announcement_date" name="announcement_date" class="form-control" placeholder="Contoh: YYYY-MM-DD HH:MM:SS" value="<?php echo sanitize($announcement_date); ?>" required>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">Gunakan format baku <strong>YYYY-MM-DD HH:MM:SS</strong> (Contoh: 2026-05-02 08:00:00).</p>
                </div>

                <h3 class="mb-4 mt-4" style="font-size: 1.15rem; color: var(--brand-primary); border-bottom: 2px solid var(--border-color); padding-bottom: 8px; margin-top: 32px;">Surat Keterangan Kelulusan (SKL)</h3>
                
                <div class="form-group">
                    <label for="letter_header" class="form-label">Judul/Header Surat</label>
                    <input type="text" id="letter_header" name="letter_header" class="form-control" value="<?php echo sanitize($letter_header); ?>">
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <div class="form-group">
                        <label for="principal_name" class="form-label">Nama Kepala Sekolah</label>
                        <input type="text" id="principal_name" name="principal_name" class="form-control" value="<?php echo sanitize($principal_name); ?>">
                    </div>

                    <div class="form-group">
                        <label for="principal_nip" class="form-label">NIP Kepala Sekolah</label>
                        <input type="text" id="principal_nip" name="principal_nip" class="form-control" value="<?php echo sanitize($principal_nip); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label for="subjects" class="form-label">Daftar Mata Pelajaran (Pisahkan dengan koma)</label>
                    <textarea id="subjects" name="subjects" class="form-control" rows="4" placeholder="Misal: Agama, Matematika, Bahasa Indonesia, ..."><?php echo sanitize($subjects); ?></textarea>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">Pisahkan nama mata pelajaran dengan tanda koma.</p>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Pengaturan</button>
                    <a href="/kelulusan/admin/dashboard" class="btn btn-secondary" style="flex: 1;">Kembali ke Dashboard</a>
                </div>

            </form>
        </div>

    </main>

<?php include 'layout/footer.php'; ?>
