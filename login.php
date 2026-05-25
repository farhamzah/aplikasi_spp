<?php
ob_start();
session_start();

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$mode = $_GET['mode'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        $mode = 'register';
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama_user'] ?? '');
        $username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
        $passwordInput = $_POST['password'] ?? '';
        $konfirmasi = $_POST['konfirmasi_password'] ?? '';
        $level = mysqli_real_escape_string($koneksi, $_POST['level'] ?? 'siswa');
        $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn'] ?? '');

        $usernameDipakai = count_data($koneksi, "SELECT COUNT(*) FROM tb_user WHERE username = '$username'");

        if ($passwordInput !== $konfirmasi) {
            $error = 'Konfirmasi password tidak sama.';
        } elseif ($usernameDipakai > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            $id = next_id($koneksi, 'tb_user', 'id_user');
            $password = md5($passwordInput);
            $simpan = mysqli_query($koneksi, "
                INSERT INTO tb_user (id_user, username, password, nama_user, level, nisn)
                VALUES ('$id', '$username', '$password', '$nama', '$level', " . ($nisn === '' ? "NULL" : "'$nisn'") . ")
            ");

            if ($simpan) {
                $mode = 'login';
                $success = 'Registrasi berhasil. Silakan login.';
            } else {
                $error = 'Registrasi gagal.';
            }
        }
    } else {
        $username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
        $password = md5($_POST['password'] ?? '');

        $result = mysqli_query($koneksi, "
            SELECT id_user, username, nama_user, level, nisn
            FROM tb_user
            WHERE username = '$username' AND password = '$password'
            LIMIT 1
        ");

        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: index.php');
            exit;
        }

        $error = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplikasi SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="brand login-brand">
            <span class="brand-mark">SPP</span>
            <div>
                <strong>Aplikasi SPP</strong>
                <small>Masuk untuk mengelola pembayaran sekolah</small>
            </div>
        </div>

        <div class="auth-tabs">
            <a href="login.php" class="<?= $mode === 'login' ? 'active' : '' ?>">Login</a>
            <a href="login.php?mode=register" class="<?= $mode === 'register' ? 'active' : '' ?>">Registrasi</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($mode === 'register'): ?>
            <form method="post" class="login-form">
                <input type="hidden" name="action" value="register">
                <label>nama_user
                    <input type="text" name="nama_user" required autofocus placeholder="Masukkan nama lengkap">
                </label>
                <label>username
                    <input type="text" name="username" maxlength="25" required placeholder="Masukkan username">
                </label>
                <label>password
                    <input type="password" name="password" required placeholder="Masukkan password">
                </label>
                <label>konfirmasi password
                    <input type="password" name="konfirmasi_password" required placeholder="Ulangi password">
                </label>
                <label>level
                    <select name="level" required>
                        <option value="siswa">Siswa</option>
                        <option value="petugas">Petugas</option>
                        <option value="admin">Admin</option>
                    </select>
                </label>
                <label>nisn
                    <input type="text" name="nisn" maxlength="10" placeholder="Isi jika level siswa">
                </label>
                <button class="btn w-100" type="submit">Daftar</button>
            </form>
        <?php else: ?>
            <form method="post" class="login-form">
                <input type="hidden" name="action" value="login">
                <label>username
                    <input type="text" name="username" required autofocus placeholder="Masukkan username">
                </label>
                <label>password
                    <input type="password" name="password" required placeholder="Masukkan password">
                </label>
                <button class="btn w-100" type="submit">Login</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
