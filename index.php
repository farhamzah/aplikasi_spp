<?php
ob_start();
session_start();

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

if (!current_user()) {
    header('Location: login.php');
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
$allowedPages = [
    'dashboard',
    'siswa',
    'kelas',
    'spp',
    'petugas',
    'pembayaran',
    'cek_pembayaran',
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

if (!page_allowed_for_level($page, current_level())) {
    redirect_with_message('dashboard', 'Akses ditolak untuk role ' . current_level() . '.', 'error');
}

$titleMap = [
    'dashboard' => 'Dashboard',
    'siswa' => 'Data Siswa',
    'kelas' => 'Data Kelas',
    'spp' => 'Data SPP',
    'petugas' => 'Data Petugas',
    'pembayaran' => 'Pembayaran',
    'cek_pembayaran' => 'Cek Pembayaran',
];

$pageTitle = $titleMap[$page];

require __DIR__ . '/includes/header.php';
require __DIR__ . '/modul/' . $page . '.php';
require __DIR__ . '/includes/footer.php';
