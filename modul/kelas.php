<?php
/** @var mysqli $koneksi */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEdit = mysqli_real_escape_string($koneksi, $_POST['id_edit'] ?? '');
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kelas']);
    $kompetensi = mysqli_real_escape_string($koneksi, $_POST['kompetensi_keahlian']);

    if ($idEdit !== '') {
        $update = mysqli_query($koneksi, "UPDATE tb_kelas SET nama_kelas = '$nama', komp_keahlian = '$kompetensi' WHERE id_kelas = '$idEdit'");

        if ($update) {
            redirect_with_message('kelas', 'Data kelas berhasil diupdate.', 'success');
        }

        redirect_with_message('kelas', 'Data kelas gagal diupdate.', 'error');
    }

    $id = next_id($koneksi, 'tb_kelas', 'id_kelas');
    $simpan = mysqli_query($koneksi, "INSERT INTO tb_kelas (id_kelas, nama_kelas, komp_keahlian) VALUES ('$id', '$nama', '$kompetensi')");

    if ($simpan) {
        redirect_with_message('kelas', 'Data kelas berhasil disimpan.', 'success');
    }

    redirect_with_message('kelas', 'Data kelas gagal disimpan.', 'error');
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $dipakai = count_data($koneksi, "SELECT COUNT(*) FROM tb_siswa WHERE id_kelas = '$id'");

    if ($dipakai > 0) {
        redirect_with_message('kelas', 'Kelas tidak bisa dihapus karena masih dipakai data siswa.', 'error');
    }

    mysqli_query($koneksi, "DELETE FROM tb_kelas WHERE id_kelas = '$id'");
    redirect_with_message('kelas', 'Data kelas berhasil dihapus.', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM tb_kelas WHERE id_kelas = '$id'");
    $edit = mysqli_fetch_assoc($result);
}

$kelas = query_all($koneksi, "SELECT * FROM tb_kelas ORDER BY CAST(id_kelas AS UNSIGNED) DESC");
?>

<section class="panel">
    <?php show_message(); ?>
    <form method="post">
        <input type="hidden" name="id_edit" value="<?= e($edit['id_kelas'] ?? '') ?>">
        <div class="form-grid">
            <label>nama_kelas
                <input type="text" name="nama_kelas" required placeholder="Contoh: X RPL 1" value="<?= e($edit['nama_kelas'] ?? '') ?>">
            </label>
            <label>komp_keahlian
                <input type="text" name="kompetensi_keahlian" required placeholder="Contoh: Rekayasa Perangkat Lunak" value="<?= e($edit['komp_keahlian'] ?? '') ?>">
            </label>
        </div>
        <button class="btn" type="submit"><?= $edit ? 'Update Kelas' : 'Simpan Kelas' ?></button>
        <?php if ($edit): ?>
            <a class="btn secondary" href="index.php?page=kelas">Batal</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>id_kelas</th>
                    <th>nama_kelas</th>
                    <th>komp_keahlian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kelas as $row): ?>
                    <tr>
                        <td><?= e($row['id_kelas']) ?></td>
                        <td><?= e($row['nama_kelas']) ?></td>
                        <td><?= e($row['komp_keahlian']) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary" href="index.php?page=kelas&edit=<?= e($row['id_kelas']) ?>">Edit</a>
                                <a class="btn danger" href="index.php?page=kelas&hapus=<?= e($row['id_kelas']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
