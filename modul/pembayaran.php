<?php
/** @var mysqli $koneksi */
require_once __DIR__ . '/../includes/classes/Pembayaran.php';

$pembayaranService = new Pembayaran($koneksi);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEdit = mysqli_real_escape_string($koneksi, $_POST['id_edit'] ?? '');

    if ($idEdit !== '') {
        $update = $pembayaranService->update($idEdit, $_POST);

        if ($update) {
            redirect_with_message('pembayaran', 'Data pembayaran berhasil diupdate.', 'success');
        }

        redirect_with_message('pembayaran', 'Data pembayaran gagal diupdate.', 'error');
    }

    $simpan = $pembayaranService->simpan($_POST);

    if ($simpan) {
        redirect_with_message('pembayaran', 'Data pembayaran berhasil disimpan.', 'success');
    }

    redirect_with_message('pembayaran', 'Data pembayaran gagal disimpan.', 'error');
}

if (isset($_GET['hapus'])) {
    $pembayaranService->hapus($_GET['hapus']);
    redirect_with_message('pembayaran', 'Data pembayaran berhasil dihapus.', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = $pembayaranService->cari($_GET['edit']);
}

$siswa = query_all($koneksi, "SELECT * FROM tb_siswa ORDER BY nama");
$spp = query_all($koneksi, "SELECT * FROM tb_spp ORDER BY tahun DESC");
$pembayaran = $pembayaranService->semua();
?>

<section class="panel">
    <?php show_message(); ?>
    <form method="post">
        <input type="hidden" name="id_edit" value="<?= e($edit['id_pembayaran'] ?? '') ?>">
        <div class="form-grid">
            <label>nisn / Siswa
                <select name="nisn" required>
                    <?php foreach ($siswa as $row): ?>
                        <option value="<?= e($row['nisn']) ?>" <?= ($edit['nisn'] ?? '') === $row['nisn'] ? 'selected' : '' ?>><?= e($row['nama']) ?> - <?= e($row['nisn']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>tgl_bayar
                <input type="date" name="tgl_bayar" value="<?= e($edit['tgl_bayar'] ?? date('Y-m-d')) ?>" required>
            </label>
            <label>tgl_terakhir_bayar
                <input type="date" name="tgl_terakhir_bayar" value="<?= e($edit['tgl_terakhir_bayar'] ?? date('Y-m-d')) ?>" required>
            </label>
            <label>batas_pembayaran
                <input type="date" name="batas_pembayaran" value="<?= e($edit['batas_pembayaran'] ?? date('Y-m-d', strtotime('+1 month'))) ?>" required>
            </label>
            <label>jumlah_bulan
                <input type="number" name="jumlah_bulan" min="1" value="<?= e($edit['jumlah_bulan'] ?? '1') ?>" required>
            </label>
            <label>id_spp / SPP
                <select name="id_spp" required>
                    <?php foreach ($spp as $row): ?>
                        <option value="<?= e($row['id_spp']) ?>" <?= ($edit['id_spp'] ?? '') === $row['id_spp'] ? 'selected' : '' ?>><?= e($row['id_spp']) ?> - <?= e($row['tahun']) ?> - <?= e(rupiah($row['nominal'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>jumlah_bayar
                <input type="number" name="jumlah_bayar" min="0" value="<?= e($edit['jumlah_bayar'] ?? '') ?>" required>
            </label>
        </div>
        <button class="btn" type="submit"><?= $edit ? 'Update Pembayaran' : 'Simpan Pembayaran' ?></button>
        <?php if ($edit): ?>
            <a class="btn secondary" href="index.php?page=pembayaran">Batal</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>id_pembayaran</th>
                    <th>id_spp</th>
                    <th>nisn</th>
                    <th>tgl_bayar</th>
                    <th>tgl_terakhir_bayar</th>
                    <th>batas_pembayaran</th>
                    <th>jumlah_bulan</th>
                    <th>status</th>
                    <th>nominal_bayar</th>
                    <th>jumlah_bayar</th>
                    <th>kembalian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembayaran as $row): ?>
                    <tr>
                        <td><?= e($row['id_pembayaran']) ?></td>
                        <td><?= e($row['id_spp']) ?></td>
                        <td><?= e($row['nisn']) ?></td>
                        <td><?= e($row['tgl_bayar']) ?></td>
                        <td><?= e($row['tgl_terakhir_bayar']) ?></td>
                        <td><?= e($row['batas_pembayaran']) ?></td>
                        <td><?= e($row['jumlah_bulan']) ?></td>
                        <td><?= e($row['status']) ?></td>
                        <td><?= e(rupiah($row['nominal_bayar'])) ?></td>
                        <td><?= e(rupiah($row['jumlah_bayar'])) ?></td>
                        <td><?= e(rupiah($row['kembalian'])) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary" href="index.php?page=pembayaran&edit=<?= e($row['id_pembayaran']) ?>">Edit</a>
                                <a class="btn danger" href="index.php?page=pembayaran&hapus=<?= e($row['id_pembayaran']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
