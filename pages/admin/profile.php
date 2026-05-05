<?php
global $pdo;
$school_name = get_setting('school_name');
$error = '';
$success = '';

// Re-fetch current user profile details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '';
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($full_name !== '' && $username !== '') {
        // Check username availability
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :user AND id != :id");
        $stmt->execute(['user' => $username, 'id' => $_SESSION['admin_id']]);
        if ($stmt->fetch()) {
            $error = "Username tersebut sudah digunakan oleh akun lain.";
        } else {
            // Update base details
            $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, username = :user WHERE id = :id");
            $stmt->execute(['full_name' => $full_name, 'user' => $username, 'id' => $_SESSION['admin_id']]);

            // Update session values
            $_SESSION['admin_name'] = $full_name;
            $_SESSION['admin_username'] = $username;

            // If a new password is set, hash and update it
            if ($password !== '') {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET password = :pass WHERE id = :id");
                $stmt->execute(['pass' => $hashedPassword, 'id' => $_SESSION['admin_id']]);
            }

            // Re-fetch fresh user info
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['admin_id']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            $success = "Akun administrator berhasil diperbarui!";
        }
    } else {
        $error = "Nama Lengkap dan Username wajib diisi.";
    }
}
$page_title = "Akun Admin";
include 'layout/header.php';
?>

    <!-- Main Content Area -->
    <main class="main-content container" style="max-width: 600px; width:100%; padding: 48px 24px;">

        <div class="mb-6">
            <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 4px;">Akun Administrator</h2>
            <p class="text-secondary">Ubah informasi nama, username, dan kata sandi akun Anda.</p>
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

            <form action="/kelulusan/admin/profil" method="POST">
                <div class="form-group">
                    <label for="full_name" class="form-label">Nama Lengkap Admin <span style="color: var(--error);">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo sanitize($admin['full_name']); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username <span style="color: var(--error);">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo sanitize($admin['username']); ?>" required autocomplete="off">
                </div>

                <div class="form-group mb-6">
                    <label for="password" class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Perubahan</button>
                    <a href="/kelulusan/admin/dashboard" class="btn btn-secondary" style="flex: 1;">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>

    </main>

<?php include 'layout/footer.php'; ?>
