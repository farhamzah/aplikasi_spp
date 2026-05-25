<?php
/** @var mysqli $koneksi */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisnLama = mysqli_real_escape_string($koneksi, $_POST['nisn_lama'] ?? '');
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $idKelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $noTelp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $idSpp = mysqli_real_escape_string($koneksi, $_POST['id_spp']);
    $kelasRow = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_kelas FROM tb_kelas WHERE id_kelas = '$idKelas'"));
    $namaKelas = mysqli_real_escape_string($koneksi, $kelasRow['nama_kelas'] ?? '');

    if ($nisnLama !== '') {
        $update = mysqli_query($koneksi, "
            UPDATE tb_siswa
            SET nis = '$nis', nama = '$nama', id_kelas = '$idKelas', nama_kelas = '$namaKelas', alamat = '$alamat', no_telp = '$noTelp', id_spp = '$idSpp'
            WHERE nisn = '$nisnLama'
        ");

        if ($update) {
            redirect_with_message('siswa', 'Data siswa berhasil diupdate.', 'success');
        }

        redirect_with_message('siswa', 'Data siswa gagal diupdate.', 'error');
    }

    $simpan = mysqli_query($koneksi, "
        INSERT INTO tb_siswa (nisn, nis, nama, id_kelas, nama_kelas, alamat, no_telp, id_spp)
        VALUES ('$nisn', '$nis', '$nama', '$idKelas', '$namaKelas', '$alamat', '$noTelp', '$idSpp')
    ");

    if ($simpan) {
        redirect_with_message('siswa', 'Data siswa berhasil disimpan.', 'success');
    }

    redirect_with_message('siswa', 'Data siswa gagal disimpan. Periksa NISN atau NIS, mungkin sudah ada.', 'error');
}

if (isset($_GET['hapus'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $dipakaiPembayaran = count_data($koneksi, "SELECT COUNT(*) FROM tb_pembayaran WHERE nisn = '$nisn'");
    $dipakaiCek = count_data($koneksi, "SELECT COUNT(*) FROM cek_pembayaran WHERE nisn = '$nisn'");

    if (($dipakaiPembayaran + $dipakaiCek) > 0) {
        redirect_with_message('siswa', 'Siswa tidak bisa dihapus karena masih punya data pembayaran.', 'error');
    }

    mysqli_query($koneksi, "DELETE FROM tb_siswa WHERE nisn = '$nisn'");
    redirect_with_message('siswa', 'Data siswa berhasil dihapus.', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM tb_siswa WHERE nisn = '$nisn'");
    $edit = mysqli_fetch_assoc($result);
}

$kelas = query_all($koneksi, "SELECT * FROM tb_kelas ORDER BY nama_kelas");
$spp = query_all($koneksi, "SELECT * FROM tb_spp ORDER BY tahun DESC");
$siswa = query_all($koneksi, "
    SELECT s.*, k.nama_kelas, sp.tahun, sp.nominal
    FROM tb_siswa s
    JOIN tb_kelas k ON k.id_kelas = s.id_kelas
    JOIN tb_spp sp ON sp.id_spp = s.id_spp
    ORDER BY s.nama
");
?>

<section class="panel">
    <?php show_message(); ?>
    <form method="post">
        <input type="hidden" name="nisn_lama" value="<?= e($edit['nisn'] ?? '') ?>">
        <div class="form-grid">
            <label>nisn
                <input type="text" name="nisn" maxlength="10" required value="<?= e($edit['nisn'] ?? '') ?>" <?= $edit ? 'readonly' : '' ?>>
            </label>
            <label>nis
                <input type="text" name="nis" maxlength="8" required value="<?= e($edit['nis'] ?? '') ?>">
            </label>
            <label>nama
                <input type="text" name="nama" required value="<?= e($edit['nama'] ?? '') ?>">
            </label>
            <label>id_kelas / Kelas
                <select name="id_kelas" required>
                    <?php foreach ($kelas as $row): ?>
                        <option value="<?= e($row['id_kelas']) ?>" <?= ($edit['id_kelas'] ?? '') === $row['id_kelas'] ? 'selected' : '' ?>><?= e($row['id_kelas']) ?> - <?= e($row['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>no_telp
                <input type="text" name="no_telp" required value="<?= e($edit['no_telp'] ?? '') ?>">
            </label>
            <label>id_spp / SPP
                <select name="id_spp" required>
                    <?php foreach ($spp as $row): ?>
                        <option value="<?= e($row['id_spp']) ?>" <?= ($edit['id_spp'] ?? '') === $row['id_spp'] ? 'selected' : '' ?>><?= e($row['id_spp']) ?> - <?= e($row['tahun']) ?> - <?= e(rupiah($row['nominal'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>alamat
                <textarea name="alamat" required><?= e($edit['alamat'] ?? '') ?></textarea>
            </label>
        </div>
        <button class="btn" type="submit"><?= $edit ? 'Update Siswa' : 'Simpan Siswa' ?></button>
        <?php if ($edit): ?>
            <a class="btn secondary" href="index.php?page=siswa">Batal</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel" style="margin-top: 18px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>NISN</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>id_kelas</th>
                    <th>nama_kelas</th>
                    <th>alamat</th>
                    <th>No. Telepon</th>
                    <th>id_spp</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($siswa as $row): ?>
                    <tr>
                        <td><?= e($row['nisn']) ?></td>
                        <td><?= e($row['nis']) ?></td>
                        <td><?= e($row['nama']) ?></td>
                        <td><?= e($row['id_kelas']) ?></td>
                        <td><?= e($row['nama_kelas']) ?></td>
                        <td><?= e($row['alamat']) ?></td>
                        <td><?= e($row['no_telp']) ?></td>
                        <td><?= e($row['id_spp']) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary" href="index.php?page=siswa&edit=<?= e($row['nisn']) ?>">Edit</a>
                                <a class="btn danger" href="index.php?page=siswa&hapus=<?= e($row['nisn']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
