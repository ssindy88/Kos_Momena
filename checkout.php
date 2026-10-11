<?php
// Panggil koneksi database
include 'includes/connection.php';

// Tangkap ID penyewa dari URL
$id_penyewa = isset($_GET['id']) ? $_GET['id'] : 0;

if($id_penyewa > 0) {
    // Update status sewa menjadi Selesai. 
    // Otomatis, sistem di Dashboard tidak akan membaca penghuni ini lagi, 
    // dan kamar akan kembali berstatus "Kosong".
    $query = "UPDATE penyewa SET status_sewa = 'Selesai' WHERE id_penyewa = '$id_penyewa'";
    
    if(mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Check-out berhasil!\\n\\nKamar sekarang sudah kosong dan siap disewakan kembali.');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "Error Database: " . mysqli_error($koneksi);
    }
} else {
    // Lempar kembali ke dashboard jika ID tidak valid
    header("Location: index.php");
}
?>