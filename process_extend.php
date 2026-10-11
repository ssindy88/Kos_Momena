<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(empty($_POST)) {
    die("Form kosong. Pastikan tombol submit ditekan.");
}

include 'includes/connection.php';

if(isset($_POST['submit'])) {
    
    $id_penyewa = $_POST['id_penyewa'];
    $tanggal_keluar_lama = $_POST['tanggal_keluar_lama'];
    $jumlah_bayar = (int)str_replace('.', '', $_POST['jumlah_bayar']);
    $tanggal_hari_ini = date('Y-m-d');
    
    // TANGKAP FORMAT DURASI BARU
    $durasi_angka = (int)$_POST['durasi_angka'];
    $durasi_satuan = $_POST['durasi_satuan']; 

    $tanggal_keluar_baru = "";
    $tipe_sewa_baru = ""; // Untuk memperbarui status anak kos di depan (Harian/Mingguan/Bulanan)
    
    // HITUNG KALENDER & UPDATE TIPE SEWA TERBARU
    if ($durasi_satuan == "Hari") {
        $tanggal_keluar_baru = date('Y-m-d', strtotime($tanggal_keluar_lama . ' + ' . $durasi_angka . ' days'));
        $keterangan = "Perpanjang " . $durasi_angka . " Hari";
        $tipe_sewa_baru = "Harian";
    } 
    elseif ($durasi_satuan == "Minggu") {
        $tanggal_keluar_baru = date('Y-m-d', strtotime($tanggal_keluar_lama . ' + ' . $durasi_angka . ' weeks'));
        $keterangan = "Perpanjang " . $durasi_angka . " Minggu";
        $tipe_sewa_baru = "Mingguan";
    } 
    elseif ($durasi_satuan == "Bulan") {
        $tanggal_keluar_baru = date('Y-m-d', strtotime($tanggal_keluar_lama . ' + ' . $durasi_angka . ' months'));
        $keterangan = "Perpanjang " . $durasi_angka . " Bulan";
        $tipe_sewa_baru = "Bulanan";
    }

    // UPDATE DATABASE (Tanggal keluar DAN Tipe sewa terbarunya)
    $query_update = "UPDATE penyewa 
                     SET tanggal_keluar = '$tanggal_keluar_baru', tipe_sewa = '$tipe_sewa_baru' 
                     WHERE id_penyewa = '$id_penyewa'";
                     
    $query_bayar = "INSERT INTO pembayaran (id_penyewa, tanggal_bayar, jumlah_bayar, keterangan) 
                    VALUES ('$id_penyewa', '$tanggal_hari_ini', '$jumlah_bayar', '$keterangan')";

    if(mysqli_query($koneksi, $query_update) && mysqli_query($koneksi, $query_bayar)) {
        $tanggal_tampil = date('d-m-Y', strtotime($tanggal_keluar_baru));
        echo "<script>
                alert('Berhasil perpanjang!\\n\\nKeterangan: $keterangan\\nJatuh Tempo Baru: $tanggal_tampil');
                window.location.href = 'index.php';
              </script>";
    } else {
        die("Error Database: " . mysqli_error($koneksi));
    }

} else {
    echo "Tombol submit tidak terbaca sistem.";
}
?>