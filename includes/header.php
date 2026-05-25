<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - Aplikasi SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-mark">SPP</span>
            <div>
                <strong>Aplikasi SPP</strong>
                <small>Pengelolaan pembayaran</small>
            </div>
        </div>
        <nav class="nav">
            <?php foreach (menu_allowed_for_level(current_level()) as $menuPage => $menuLabel): ?>
                <a href="<?= $menuPage === 'dashboard' ? 'index.php' : 'index.php?page=' . e($menuPage) ?>" class="<?= $page === $menuPage ? 'active' : '' ?>"><?= e($menuLabel) ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="main container-fluid">
        <header class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h1><?= e($pageTitle) ?></h1>
                <p>Sistem informasi pembayaran SPP sekolah</p>
            </div>
            <div class="user-box text-end">
                <strong><?= e(current_user()['nama_user'] ?? current_user()['nama_petugas'] ?? '') ?></strong>
                <small class="d-block text-muted"><?= e(current_level()) ?></small>
                <a class="btn btn-sm btn-outline-danger mt-2" href="logout.php">Logout</a>
            </div>
        </header>
