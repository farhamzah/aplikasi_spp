<?php


/**
 * Mengamankan output sebelum ditampilkan ke HTML agar karakter khusus tidak
 * dibaca sebagai kode HTML.
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Mengarahkan pengguna ke halaman modul tertentu melalui parameter page.
 */
function redirect_to($page)
{
    header('Location: index.php?page=' . $page);
    exit;
}

/**
 * Mengarahkan pengguna sekaligus membawa pesan status untuk ditampilkan
 * setelah proses tambah, ubah, atau hapus data.
 */
function redirect_with_message($page, $message, $type = 'info')
{
    header('Location: index.php?page=' . $page . '&msg=' . urlencode($message) . '&type=' . urlencode($type));
    exit;
}

/**
 * Menampilkan pesan notifikasi dari URL dengan class alert Bootstrap yang
 * sesuai dengan jenis pesannya.
 */
function show_message()
{
    if (!isset($_GET['msg'])) {
        return;
    }

    $type = $_GET['type'] ?? 'info';
    $bootstrapType = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'info' => 'alert-info',
    ][$type] ?? 'alert-info';

    echo '<div class="alert ' . e($bootstrapType) . '" role="alert">' . e($_GET['msg']) . '</div>';
}

/**
 * Mengubah angka nominal menjadi format mata uang Rupiah.
 */
function rupiah($angka)
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

/**
 * Menghitung jumlah data pada tabel tertentu menggunakan COUNT(*) agar query
 * dashboard tetap ringan.
 */
function get_count($koneksi, $table)
{
    $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `$table`");
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}

/**
 * Menjalankan query SELECT dan mengembalikan seluruh hasilnya dalam bentuk
 * array asosiatif.
 */
function query_all($koneksi, $sql)
{
    $result = mysqli_query($koneksi, $sql);
    if (!$result) {
        die('Query gagal: ' . mysqli_error($koneksi));
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

/**
 * Membuat ID berikutnya dari nilai MAX kolom numerik pada tabel.
 */
function next_id($koneksi, $table, $column)
{
    $result = mysqli_query($koneksi, "SELECT MAX(CAST($column AS UNSIGNED)) AS max_id FROM `$table`");
    $row = mysqli_fetch_assoc($result);
    return (string) (((int) $row['max_id']) + 1);
}

/**
 * Mengambil satu nilai hitung dari query, biasanya dipakai untuk validasi
 * relasi sebelum data dihapus.
 */
function count_data($koneksi, $sql)
{
    $result = mysqli_query($koneksi, $sql);
    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_row($result);
    return (int) ($row[0] ?? 0);
}

/**
 * Mengambil data pengguna yang sedang login dari session.
 */
function current_user()
{
    return $_SESSION['user'] ?? null;
}

/**
 * Mengambil level pengguna aktif untuk kebutuhan pembatasan menu dan halaman.
 */
function current_level()
{
    return $_SESSION['user']['level'] ?? '';
}

/**
 * Mengecek apakah halaman boleh dibuka oleh level pengguna tertentu.
 */
function page_allowed_for_level($page, $level)
{
    $access = [
        'admin' => ['dashboard', 'siswa', 'kelas', 'spp', 'petugas', 'pembayaran', 'cek_pembayaran'],
        'petugas' => ['dashboard', 'pembayaran', 'cek_pembayaran'],
        'siswa' => ['dashboard', 'cek_pembayaran'],
    ];

    return in_array($page, $access[$level] ?? [], true);
}

/**
 * Menghasilkan daftar menu yang sesuai dengan level pengguna aktif.
 */
function menu_allowed_for_level($level)
{
    $menus = [
        'dashboard' => 'Dashboard',
        'siswa' => 'Siswa',
        'kelas' => 'Kelas',
        'spp' => 'SPP',
        'petugas' => 'Petugas',
        'pembayaran' => 'Pembayaran',
        'cek_pembayaran' => 'Cek Pembayaran',
    ];

    return array_filter($menus, fn($label, $page) => page_allowed_for_level($page, $level), ARRAY_FILTER_USE_BOTH);
}
