<?php
/** @var mysqli $koneksi */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEdit = mysqli_real_escape_string($koneksi, $_POST['id_edit'] ?? '');
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_petugas']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $passwordInput = $_POST['password'] ?? '';
    $level = mysqli_real_escape_string($koneksi, $_POST['level']);

    if ($idEdit !== '') {
        $passwordSql = '';
        if ($passwordInput !== '') {
            $password = md5($passwordInput);
            $passwordSql = ", password = '$password'";
        }

        $update = mysqli_query($koneksi, "
            UPDATE tb_petugas
            SET username = '$username', nama_petugas = '$nama', level = '$level' $passwordSql
            WHERE id_petugas = '$idEdit'
        ");

        if ($update) {
            redirect_with_message('petugas', 'Data petugas berhasil diupdate.', 'success');
        }

        redirect_with_message('petugas', 'Data petugas gagal diupdate.', 'error');
    }

    $id = next_id($koneksi, 'tb_petugas', 'id_petugas');
    $password = md5($passwordInput);
    $simpan = mysqli_query($koneksi, "
        INSERT INTO tb_petugas (id_petugas, username, password, nama_petugas, level)
        VALUES ('$id', '$username', '$password', '$nama', '$level')
    ");

    if ($simpan) {
        redirect_with_message('petugas', 'Data petugas berhasil disimpan.', 'success');
    }

    redirect_with_message('petugas', 'Data petugas gagal disimpan.', 'error');
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM tb_petugas WHERE id_petugas = '$id'");
    redirect_with_message('petugas', 'Data petugas berhasil dihapus.', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM tb_petugas WHERE id_petugas = '$id'");
    $edit = mysqli_fetch_assoc($result);
}

$petugas = query_all($koneksi, "SELECT * FROM tb_petugas ORDER BY CAST(id_petugas AS UNSIGNED) DESC");
?>

<section class="panel">
    <?php show_message(); ?>
    <form method="post">
        <input type="hidden" name="id_edit" value="<?= e($edit['id_petugas'] ?? '') ?>">
        <div class="form-grid">
            <label>nama_petugas
                <input type="text" name="nama_petugas" required value="<?= e($edit['nama_petugas'] ?? '') ?>">
            </label>
            <label>username
                <input type="text" name="username" required value="<?= e($edit['username'] ?? '') ?>">
            </label>
            <label>password
                <input type="password" name="password" <?= $edit ? '' : 'required' ?> placeholder="<?= $edit ? 'Kosongkan jika tidak diganti' : '' ?>">
            </label>
            <label>level
                <select name="level" required>
                    <?php foreach (['admin' => 'Admin', 'petugas' => 'Petugas', 'siswa' => 'Siswa'] as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= ($edit['level'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <button class="btn" type="submit"><?= $edit ? 'Update Petugas' : 'Simpan Petugas' ?></button>
        <?php if ($edit): ?>
            <a class="btn secondary" href="index.php?page=petugas">Batal</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>id_petugas</th>
                    <th>username</th>
                    <th>password</th>
                    <th>nama_petugas</th>
                    <th>level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($petugas as $row): ?>
                    <tr>
                        <td><?= e($row['id_petugas']) ?></td>
                        <td><?= e($row['username']) ?></td>
                        <td><?= e($row['password']) ?></td>
                        <td><?= e($row['nama_petugas']) ?></td>
                        <td><?= e($row['level']) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary" href="index.php?page=petugas&edit=<?= e($row['id_petugas']) ?>">Edit</a>
                                <a class="btn danger" href="index.php?page=petugas&hapus=<?= e($row['id_petugas']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
