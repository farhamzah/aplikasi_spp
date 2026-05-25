<?php
/** @var mysqli $koneksi */
require_once __DIR__ . '/../includes/classes/CekPembayaran.php';

$cekPembayaranService = new CekPembayaran($koneksi);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisnLama = mysqli_real_escape_string($koneksi, $_POST['nisn_lama'] ?? '');
    $tglSekarangLama = mysqli_real_escape_string($koneksi, $_POST['tgl_sekarang_lama'] ?? '');

    if ($nisnLama !== '') {
        $update = $cekPembayaranService->update($nisnLama, $tglSekarangLama, $_POST);

        if ($update) {
            redirect_with_message('cek_pembayaran', 'Status pembayaran berhasil diupdate.', 'success');
        }

        redirect_with_message('cek_pembayaran', 'Status pembayaran gagal diupdate.', 'error');
    }

    $simpan = $cekPembayaranService->simpan($_POST);

    if ($simpan) {
        redirect_with_message('cek_pembayaran', 'Status pembayaran berhasil disimpan.', 'success');
    }

    redirect_with_message('cek_pembayaran', 'Status pembayaran gagal disimpan.', 'error');
}

if (isset($_GET['hapus'])) {
    $cekPembayaranService->hapus($_GET['hapus']);
    redirect_with_message('cek_pembayaran', 'Status pembayaran berhasil dihapus.', 'success');
}

$edit = null;
if (isset($_GET['edit']) && isset($_GET['tgl'])) {
    $edit = $cekPembayaranService->cari($_GET['edit'], $_GET['tgl']);
}

$siswa = query_all($koneksi, "SELECT * FROM tb_siswa ORDER BY nama");
$cek = $cekPembayaranService->semua();
?>

<section class="panel">
    <?php show_message(); ?>
    <form method="post">
        <input type="hidden" name="nisn_lama" value="<?= e($edit['nisn'] ?? '') ?>">
        <input type="hidden" name="tgl_sekarang_lama" value="<?= e($edit['tgl_sekarang'] ?? '') ?>">
        <div class="form-grid">
            <label>nisn / Siswa
                <select name="nisn" required>
                    <?php foreach ($siswa as $row): ?>
                        <option value="<?= e($row['nisn']) ?>" <?= ($edit['nisn'] ?? '') === $row['nisn'] ? 'selected' : '' ?>><?= e($row['nama']) ?> - <?= e($row['nisn']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>status_pembayaran
                <select name="status_pembayaran" required>
                    <option value="Sudah Lunas" <?= ($edit['status_pembayaran'] ?? '') === 'Sudah Lunas' ? 'selected' : '' ?>>Sudah Lunas</option>
                    <option value="Belum Lunas" <?= ($edit['status_pembayaran'] ?? '') === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                </select>
            </label>
            <label>tgl_terakhir_bayar
                <input type="date" name="tgl_terakhir_bayar" value="<?= e($edit['tgl_terakhir_bayar'] ?? date('Y-m-d')) ?>" required>
            </label>
            <label>tgl_sekarang
                <input type="date" name="tgl_sekarang" value="<?= e($edit['tgl_sekarang'] ?? date('Y-m-d')) ?>" required>
            </label>
            <label>jumlah_bulan
                <input type="number" name="jumlah_bulan" min="1" value="<?= e($edit['jumlah_bulan'] ?? '1') ?>" required>
            </label>
        </div>
        <button class="btn" type="submit"><?= $edit ? 'Update Status' : 'Simpan Status' ?></button>
        <?php if ($edit): ?>
            <a class="btn secondary" href="index.php?page=cek_pembayaran">Batal</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>nisn</th>
                    <th>tgl_terakhir_bayar</th>
                    <th>tgl_sekarang</th>
                    <th>status_pembayaran</th>
                    <th>jumlah_bulan</th>
                    <th>nama</th>
                    <th>no_telp</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cek as $row): ?>
                    <tr>
                        <td><?= e($row['nisn']) ?></td>
                        <td><?= e($row['tgl_terakhir_bayar']) ?></td>
                        <td><?= e($row['tgl_sekarang']) ?></td>
                        <td>
                            <span class="badge <?= $row['status_pembayaran'] === 'Sudah Lunas' ? 'paid' : 'unpaid' ?>">
                                <?= e($row['status_pembayaran']) ?>
                            </span>
                        </td>
                        <td><?= e($row['jumlah_bulan']) ?></td>
                        <td><?= e($row['nama']) ?></td>
                        <td><?= e($row['no_telp']) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary" href="index.php?page=cek_pembayaran&edit=<?= e($row['nisn']) ?>&tgl=<?= e($row['tgl_sekarang']) ?>">Edit</a>
                                <a class="btn danger" href="index.php?page=cek_pembayaran&hapus=<?= e($row['nisn']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
