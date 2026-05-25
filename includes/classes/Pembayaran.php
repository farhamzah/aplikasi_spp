<?php
/**
 * Class Pembayaran mengelola proses transaksi pembayaran SPP, mulai dari
 * menampilkan data, menyimpan, mengubah, menghapus, sampai menghitung status
 * lunas berdasarkan nominal SPP.
 */
class Pembayaran
{
    private mysqli $koneksi;

    /**
     * Menyimpan koneksi database agar seluruh method memakai koneksi yang sama.
     */
    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    /**
     * Mengambil seluruh transaksi pembayaran beserta nama siswa dan nominal SPP.
     */
    public function semua()
    {
        return query_all($this->koneksi, "
            SELECT p.*, s.nama, sp.nominal
            FROM tb_pembayaran p
            JOIN tb_siswa s ON s.nisn = p.nisn
            JOIN tb_spp sp ON sp.id_spp = p.id_spp
            ORDER BY p.id_pembayaran DESC
        ");
    }

    /**
     * Mencari satu transaksi berdasarkan id_pembayaran untuk proses edit.
     */
    public function cari($idPembayaran)
    {
        $id = $this->bersihkan($idPembayaran);
        $result = mysqli_query($this->koneksi, "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '$id'");
        return mysqli_fetch_assoc($result);
    }

    /**
     * Menyimpan transaksi baru setelah data dihitung dan dibersihkan.
     */
    public function simpan($data)
    {
        $id = next_id($this->koneksi, 'tb_pembayaran', 'id_pembayaran');
        $dataBayar = $this->siapkanDataPembayaran($data);

        return mysqli_query($this->koneksi, "
            INSERT INTO tb_pembayaran (id_pembayaran, id_spp, nisn, tgl_bayar, tgl_terakhir_bayar, batas_pembayaran, jumlah_bulan, status, nominal_bayar, jumlah_bayar, kembalian)
            VALUES (
                '$id',
                '{$dataBayar['id_spp']}',
                '{$dataBayar['nisn']}',
                '{$dataBayar['tgl_bayar']}',
                '{$dataBayar['tgl_terakhir_bayar']}',
                '{$dataBayar['batas_pembayaran']}',
                '{$dataBayar['jumlah_bulan']}',
                '{$dataBayar['status']}',
                '{$dataBayar['nominal']}',
                '{$dataBayar['jumlah']}',
                '{$dataBayar['kembalian']}'
            )
        ");
    }

    /**
     * Mengubah transaksi pembayaran yang sudah ada berdasarkan id_pembayaran.
     */
    public function update($idPembayaran, $data)
    {
        $id = $this->bersihkan($idPembayaran);
        $dataBayar = $this->siapkanDataPembayaran($data);

        return mysqli_query($this->koneksi, "
            UPDATE tb_pembayaran
            SET id_spp = '{$dataBayar['id_spp']}',
                nisn = '{$dataBayar['nisn']}',
                tgl_bayar = '{$dataBayar['tgl_bayar']}',
                tgl_terakhir_bayar = '{$dataBayar['tgl_terakhir_bayar']}',
                batas_pembayaran = '{$dataBayar['batas_pembayaran']}',
                jumlah_bulan = '{$dataBayar['jumlah_bulan']}',
                status = '{$dataBayar['status']}',
                nominal_bayar = '{$dataBayar['nominal']}',
                jumlah_bayar = '{$dataBayar['jumlah']}',
                kembalian = '{$dataBayar['kembalian']}'
            WHERE id_pembayaran = '$id'
        ");
    }

    /**
     * Menghapus transaksi pembayaran berdasarkan id_pembayaran.
     */
    public function hapus($idPembayaran)
    {
        $id = $this->bersihkan($idPembayaran);
        return mysqli_query($this->koneksi, "DELETE FROM tb_pembayaran WHERE id_pembayaran = '$id'");
    }

    /**
     * Menyiapkan data transaksi: membersihkan input, mengambil nominal SPP,
     * menentukan status lunas, dan menghitung kembalian.
     */
    private function siapkanDataPembayaran($data)
    {
        $idSpp = $this->bersihkan($data['id_spp'] ?? '');
        $jumlah = (float) ($data['jumlah_bayar'] ?? 0);
        $nominal = $this->ambilNominalSpp($idSpp);
        $status = $jumlah >= $nominal ? 'Sudah lunas' : 'Belum lunas';
        $kembalian = max(0, $jumlah - $nominal);

        return [
            'nisn' => $this->bersihkan($data['nisn'] ?? ''),
            'tgl_bayar' => $this->bersihkan($data['tgl_bayar'] ?? ''),
            'tgl_terakhir_bayar' => $this->bersihkan($data['tgl_terakhir_bayar'] ?? ''),
            'batas_pembayaran' => $this->bersihkan($data['batas_pembayaran'] ?? ''),
            'jumlah_bulan' => $this->bersihkan($data['jumlah_bulan'] ?? ''),
            'id_spp' => $idSpp,
            'jumlah' => (string) $jumlah,
            'nominal' => (string) $nominal,
            'status' => $status,
            'kembalian' => (string) $kembalian,
        ];
    }

    /**
     * Mengambil nominal SPP berdasarkan id_spp dari tabel tb_spp.
     */
    private function ambilNominalSpp($idSpp)
    {
        $result = mysqli_query($this->koneksi, "SELECT nominal FROM tb_spp WHERE id_spp = '$idSpp'");
        $row = mysqli_fetch_assoc($result);
        return (float) ($row['nominal'] ?? 0);
    }

    /**
     * Membersihkan input agar aman ketika dimasukkan ke query MySQL.
     */
    private function bersihkan($value)
    {
        return mysqli_real_escape_string($this->koneksi, (string) $value);
    }
}
