<?php
/** @var mysqli $koneksi */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEdit = mysqli_real_escape_string($koneksi, $_POST['id_edit'] ?? '');
    $tahun = (int) $_POST['tahun'];
    $nominal = (float) $_POST['nominal'];

    if ($idEdit !== '') {
        $update = mysqli_query($koneksi, "UPDATE tb_spp SET tahun = $tahun, nominal = '$nominal' WHERE id_spp = '$idEdit'");

        if ($update) {
            redirect_with_message('spp', 'Data SPP berhasil diupdate.', 'success');
        }

        redirect_with_message('spp', 'Data SPP gagal diupdate.', 'error');
    }

    $id = next_id($koneksi, 'tb_spp', 'id_spp');
    $simpan = mysqli_query($koneksi, "INSERT INTO tb_spp (id_spp, tahun, nominal) VALUES ('$id', $tahun, '$nominal')");

    if ($simpan) {
        redirect_with_message('spp', 'Data SPP berhasil disimpan.', 'success');
    }

    redirect_with_message('spp', 'Data SPP gagal disimpan.', 'error');
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $dipakaiSiswa = count_data($koneksi, "SELECT COUNT(*) FROM tb_siswa WHERE id_spp = '$id'");
    $dipakaiPembayaran = count_data($koneksi, "SELECT COUNT(*) FROM tb_pembayaran WHERE id_spp = '$id'");

    if (($dipakaiSiswa + $dipakaiPembayaran) > 0) {
        redirect_with_message('spp', 'SPP tidak bisa dihapus karena masih dipakai data siswa atau pembayaran.', 'error');
    }

    mysqli_query($koneksi, "DELETE FROM tb_spp WHERE id_spp = '$id'");
    redirect_with_message('spp', 'Data SPP berhasil dihapus.', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM tb_spp WHERE id_spp = '$id'");
    $edit = mysqli_fetch_assoc($result);
}

$spp = query_all($koneksi, "SELECT * FROM tb_spp ORDER BY tahun DESC");
?>

<section class="panel">
    <?php show_message(); ?>
    <form method="post">
        <input type="hidden" name="id_edit" value="<?= e($edit['id_spp'] ?? '') ?>">
        <div class="form-grid">
            <label>tahun
                <input type="number" name="tahun" min="2000" max="2099" required placeholder="2026" value="<?= e($edit['tahun'] ?? '') ?>">
            </label>
            <label>nominal
                <input type="number" name="nominal" min="0" required placeholder="250000" value="<?= e($edit['nominal'] ?? '') ?>">
            </label>
        </div>
        <button class="btn" type="submit"><?= $edit ? 'Update SPP' : 'Simpan SPP' ?></button>
        <?php if ($edit): ?>
            <a class="btn secondary" href="index.php?page=spp">Batal</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>id_spp</th>
                    <th>tahun</th>
                    <th>nominal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($spp as $row): ?>
                    <tr>
                        <td><?= e($row['id_spp']) ?></td>
                        <td><?= e($row['tahun']) ?></td>
                        <td><?= e(rupiah($row['nominal'])) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary" href="index.php?page=spp&edit=<?= e($row['id_spp']) ?>">Edit</a>
                                <a class="btn danger" href="index.php?page=spp&hapus=<?= e($row['id_spp']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
