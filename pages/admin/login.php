<?php
$school_name = get_setting('school_name');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username !== '' && $password !== '') {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :user");
        $stmt->execute(['user' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_username'] = $user['username'];

            header('Location: /kelulusan/admin/dashboard');
            exit;
        } else {
            $error = "Username atau Password salah.";
        }
    } else {
        $error = "Username dan Password wajib diisi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?php echo sanitize($school_name); ?></title>
    <link rel="stylesheet" href="/kelulusan/assets/css/style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="/kelulusan/" class="brand">
                <span class="brand-accent">SMK 5 AGUSTUS</span> PEKANBARU
            </a>
            <div class="nav-links">
                <a href="/kelulusan/" class="btn btn-secondary">Beranda Public</a>
            </div>
        </div>
    </nav>

    <!-- Login Area -->
    <main class="auth-wrapper">
        <div class="card auth-card">
            <h3 class="mb-4 text-center">Login Administrator</h3>
            <p class="text-center text-secondary mb-6">Kelola portal pengumuman kelulusan siswa</p>

            <?php if ($error): ?>
                <div class="alert alert-danger mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="/kelulusan/admin/login" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="admin" required autofocus autocomplete="off">
                </div>
                <div class="form-group mb-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; height: 50px;">Masuk Sekarang</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2026 <?php echo sanitize($school_name); ?>. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>
