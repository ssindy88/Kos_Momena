<?php
include 'includes/connection.php';

if(isset($_POST['submit'])) {
    
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    // Gunakan real_escape_string untuk mencegah error jika ada tanda kutip di keterangan
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    // Hapus semua titik sebelum disimpan ke database
$jumlah = (int)str_replace('.', '', $_POST['jumlah']);

    // Query untuk menyimpan ke database
    $query = "INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah) 
              VALUES ('$tanggal', '$kategori', '$keterangan', '$jumlah')";

    if(mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Pengeluaran berhasil dicatat ke buku kas!');
                window.location.href = 'report.php'; 
              </script>";
              // Perhatikan: Setelah simpan, kita langsung arahkan Mama ke halaman Laporan (laporan.php)
    } else {
        echo "Error Database: " . mysqli_error($koneksi);
    }

} else {
    header("Location: index.php");
}
?>