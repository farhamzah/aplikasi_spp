<?php
$user = current_user();
$level = current_level();
$siswaLogin = null;

if ($level === 'siswa') {
    $username = mysqli_real_escape_string($koneksi, $user['username'] ?? '');
    $namaUser = mysqli_real_escape_string($koneksi, $user['nama_user'] ?? $user['nama_petugas'] ?? '');
    $nisnUser = mysqli_real_escape_string($koneksi, $user['nisn'] ?? '');
    $result = mysqli_query($koneksi, "
        SELECT *
        FROM tb_siswa
        WHERE nisn = '$nisnUser' OR nisn = '$username' OR nis = '$username' OR nama = '$namaUser'
        LIMIT 1
    ");
    $siswaLogin = mysqli_fetch_assoc($result);
}

if ($level === 'siswa' && $siswaLogin) {
    $nisn = mysqli_real_escape_string($koneksi, $siswaLogin['nisn']);
    $totals = [
        'Pembayaran Saya' => count_data($koneksi, "SELECT COUNT(*) FROM tb_pembayaran WHERE nisn = '$nisn'"),
        'Cek Pembayaran' => count_data($koneksi, "SELECT COUNT(*) FROM cek_pembayaran WHERE nisn = '$nisn'"),
    ];

    $recentPayments = query_all($koneksi, "
        SELECT p.*, s.nama
        FROM tb_pembayaran p
        JOIN tb_siswa s ON s.nisn = p.nisn
        WHERE p.nisn = '$nisn'
        ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC
        LIMIT 10
    ");
} elseif ($level === 'siswa') {
    $totals = [
        'Pembayaran Saya' => 0,
        'Cek Pembayaran' => 0,
    ];
    $recentPayments = [];
} else {
    $totals = [
        'Siswa' => get_count($koneksi, 'tb_siswa'),
        'Kelas' => get_count($koneksi, 'tb_kelas'),
        'SPP' => get_count($koneksi, 'tb_spp'),
        'Petugas' => get_count($koneksi, 'tb_petugas'),
        'Pembayaran' => get_count($koneksi, 'tb_pembayaran'),
        'Cek Pembayaran' => get_count($koneksi, 'cek_pembayaran'),
    ];

    $recentPayments = query_all($koneksi, "
        SELECT p.*, s.nama
        FROM tb_pembayaran p
        JOIN tb_siswa s ON s.nisn = p.nisn
        ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC
        LIMIT 5
    ");
}
?>

<?php if ($level === 'siswa' && !$siswaLogin): ?>
    <div class="alert alert-warning" role="alert">
        Akun siswa belum terhubung dengan data di tb_siswa. Isi nisn pada tb_user, gunakan username berupa NISN/NIS siswa, atau samakan nama_user dengan nama siswa.
    </div>
<?php endif; ?>

<section class="grid">
    <?php foreach ($totals as $label => $total): ?>
        <article class="card stat">
            <span><?= e($label) ?></span>
            <strong><?= e($total) ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="toolbar">
        <h2><?= $level === 'siswa' ? 'Pembayaran Saya' : 'Transaksi Terbaru' ?></h2>
        <?php if ($level !== 'siswa'): ?>
            <a class="btn" href="index.php?page=pembayaran">Tambah Pembayaran</a>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Siswa</th>
                    <th>Jumlah Bulan</th>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentPayments as $payment): ?>
                    <tr>
                        <td><?= e($payment['tgl_bayar']) ?></td>
                        <td><?= e($payment['nama']) ?></td>
                        <td><?= e($payment['jumlah_bulan']) ?> bulan</td>
                        <td><?= e($payment['status']) ?></td>
                        <td><?= e(rupiah($payment['jumlah_bayar'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentPayments): ?>
                    <tr><td colspan="5" class="muted">Belum ada transaksi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
