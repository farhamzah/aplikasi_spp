<?php
/**
 * Class CekPembayaran mengelola data verifikasi atau pengecekan pembayaran
 * siswa, termasuk menyimpan status pembayaran dan mengambil identitas siswa.
 */
class CekPembayaran
{
    private mysqli $koneksi;

    /**
     * Menyimpan koneksi database untuk dipakai oleh seluruh method class.
     */
    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    /**
     * Mengambil seluruh data cek pembayaran dan mengurutkannya dari tanggal
     * terbaru.
     */
    public function semua()
    {
        return query_all($this->koneksi, "SELECT * FROM cek_pembayaran ORDER BY tgl_sekarang DESC, nisn");
    }

    /**
     * Mencari satu data cek pembayaran berdasarkan NISN dan tanggal pengecekan.
     */
    public function cari($nisn, $tglSekarang)
    {
        $nisn = $this->bersihkan($nisn);
        $tgl = $this->bersihkan($tglSekarang);
        $result = mysqli_query($this->koneksi, "SELECT * FROM cek_pembayaran WHERE nisn = '$nisn' AND tgl_sekarang = '$tgl'");
        return mysqli_fetch_assoc($result);
    }

    /**
     * Menyimpan data cek pembayaran setelah identitas siswa dilengkapi otomatis.
     */
    public function simpan($data)
    {
        $dataCek = $this->siapkanDataCek($data);

        return mysqli_query($this->koneksi, "
            INSERT INTO cek_pembayaran (nisn, tgl_terakhir_bayar, tgl_sekarang, status_pembayaran, jumlah_bulan, nama, no_telp)
            VALUES (
                '{$dataCek['nisn']}',
                '{$dataCek['tgl_terakhir_bayar']}',
                '{$dataCek['tgl_sekarang']}',
                '{$dataCek['status_pembayaran']}',
                '{$dataCek['jumlah_bulan']}',
                '{$dataCek['nama']}',
                '{$dataCek['no_telp']}'
            )
        ");
    }

    /**
     * Mengubah data cek pembayaran berdasarkan NISN dan tanggal lama.
     */
    public function update(string $nisnLama, string $tglSekarangLama, array $data)
    {
        $nisnLama = $this->bersihkan($nisnLama);
        $tglLama = $this->bersihkan($tglSekarangLama);
        $dataCek = $this->siapkanDataCek($data);

        return mysqli_query($this->koneksi, "
            UPDATE cek_pembayaran
            SET nisn = '{$dataCek['nisn']}',
                tgl_terakhir_bayar = '{$dataCek['tgl_terakhir_bayar']}',
                tgl_sekarang = '{$dataCek['tgl_sekarang']}',
                status_pembayaran = '{$dataCek['status_pembayaran']}',
                jumlah_bulan = '{$dataCek['jumlah_bulan']}',
                nama = '{$dataCek['nama']}',
                no_telp = '{$dataCek['no_telp']}'
            WHERE nisn = '$nisnLama' AND tgl_sekarang = '$tglLama'
        ");
    }

    /**
     * Menghapus data cek pembayaran berdasarkan NISN.
     */
    public function hapus(string $nisn)
    {
        $nisn = $this->bersihkan($nisn);
        return mysqli_query($this->koneksi, "DELETE FROM cek_pembayaran WHERE nisn = '$nisn'");
    }

    /**
     * Menyiapkan data cek pembayaran dengan mengambil nama dan nomor telepon
     * siswa dari tabel tb_siswa.
     */
    private function siapkanDataCek($data)
    {
        $nisn = $this->bersihkan($data['nisn'] ?? '');
        $siswa = $this->ambilSiswa($nisn);

        return [
            'nisn' => $nisn,
            'tgl_terakhir_bayar' => $this->bersihkan($data['tgl_terakhir_bayar'] ?? ''),
            'tgl_sekarang' => $this->bersihkan($data['tgl_sekarang'] ?? ''),
            'status_pembayaran' => $this->bersihkan($data['status_pembayaran'] ?? ''),
            'jumlah_bulan' => $this->bersihkan($data['jumlah_bulan'] ?? ''),
            'nama' => $this->bersihkan($siswa['nama'] ?? ''),
            'no_telp' => $this->bersihkan($siswa['no_telp'] ?? ''),
        ];
    }

    /**
     * Mengambil data siswa yang diperlukan untuk disimpan ke cek_pembayaran.
     */
    private function ambilSiswa($nisn)
    {
        $result = mysqli_query($this->koneksi, "SELECT nama, no_telp FROM tb_siswa WHERE nisn = '$nisn'");
        return mysqli_fetch_assoc($result) ?: [];
    }

    /**
     * Membersihkan input agar aman ketika dimasukkan ke query MySQL.
     */
    private function bersihkan($value)
    {
        return mysqli_real_escape_string($this->koneksi, (string) $value);
    }
}
