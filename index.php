<?php
require_once 'config.php';

// Parse the route from clean URLs
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url_parts = explode('/', $url);

// Main routing switch
switch ($url_parts[0]) {
    case '':
    case 'home':
        include 'pages/public/home.php';
        break;

    case 'cek-kelulusan':
        include 'pages/public/check.php';
        break;

    case 'surat-kelulusan':
        include 'pages/public/cetak.php';
        break;

    case 'verifikasi':
        include 'pages/public/verifikasi.php';
        break;

    case 'admin':
        // Handle admin subroutes
        $subroute = isset($url_parts[1]) ? $url_parts[1] : 'dashboard';
        
        // Admin middleware: restrict access if not logged in
        if ($subroute !== 'login') {
            if (!isset($_SESSION['admin_logged_in'])) {
                header('Location: /kelulusan/admin/login');
                exit;
            }
        }

        switch ($subroute) {
            case 'login':
                include 'pages/admin/login.php';
                break;
            case 'dashboard':
                include 'pages/admin/dashboard.php';
                break;
            case 'siswa':
                // Check for deeper siswa subroutes like edit or delete
                $action = isset($url_parts[2]) ? $url_parts[2] : '';
                if ($action === 'tambah') {
                    include 'pages/admin/siswa_add.php';
                } elseif ($action === 'edit') {
                    include 'pages/admin/siswa_edit.php';
                } elseif ($action === 'hapus') {
                    include 'pages/admin/siswa_delete.php';
                } else {
                    include 'pages/admin/siswa_list.php';
                }
                break;
            case 'pengaturan':
                include 'pages/admin/settings.php';
                break;
            case 'nilai':
                include 'pages/admin/nilai_list.php';
                break;
            case 'panduan':
                include 'pages/admin/panduan.php';
                break;
            case 'profil':
                include 'pages/admin/profile.php';
                break;
            case 'logout':
                unset($_SESSION['admin_logged_in']);
                unset($_SESSION['admin_id']);
                unset($_SESSION['admin_name']);
                session_destroy();
                header('Location: /kelulusan/home');
                exit;
            default:
                include 'pages/admin/dashboard.php';
                break;
        }
        break;

    default:
        // 404 page
        http_response_code(404);
        include 'pages/public/404.php';
        break;
}
