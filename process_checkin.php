<?php
// Panggil koneksi database
include 'includes/connection.php';

// Pastikan kode ini hanya jalan kalau tombol submit ditekan
if(isset($_POST['submit'])) {
    
    // 1. Tangkap semua data dari form
    $id_kamar = $_POST['id_kamar'];
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $tipe_sewa = $_POST['tipe_sewa'];
    $durasi_sewa = (int)$_POST['durasi_sewa'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $jumlah_deposit = (int)$_POST['jumlah_deposit'];
    
    // Karena nomor HP & Kontak Darurat kita hapus dari form agar mama tidak repot,
    // kita isi otomatis dengan tanda strip (-) agar database tidak error.
    $no_hp = "-";
    $kontak_darurat = "-";

    // 2. MESIN PENGHITUNG JATUH TEMPO OTOMATIS
    $tanggal_keluar = "";
    
    if ($tipe_sewa == "Harian") {
        // Jika harian, tambah X hari
        $tanggal_keluar = date('Y-m-d', strtotime($tanggal_masuk . ' + ' . $durasi_sewa . ' days'));
    } 
    elseif ($tipe_sewa == "Mingguan") {
        // Jika mingguan, tambah X minggu
        $tanggal_keluar = date('Y-m-d', strtotime($tanggal_masuk . ' + ' . $durasi_sewa . ' weeks'));
    } 
    elseif ($tipe_sewa == "Bulanan") {
        // Jika bulanan, tambah X bulan
        $tanggal_keluar = date('Y-m-d', strtotime($tanggal_masuk . ' + ' . $durasi_sewa . ' months'));
    }

    $status_sewa = 'Aktif';

    // 3. Masukkan data ke tabel penyewa
    $query = "INSERT INTO penyewa 
              (id_kamar, nama_lengkap, no_hp, kontak_darurat, tipe_sewa, durasi_sewa, tanggal_masuk, tanggal_keluar, jumlah_deposit, status_sewa) 
              VALUES 
              ('$id_kamar', '$nama_lengkap', '$no_hp', '$kontak_darurat', '$tipe_sewa', '$durasi_sewa', '$tanggal_masuk', '$tanggal_keluar', '$jumlah_deposit', '$status_sewa')";

    // 4. Eksekusi dan berikan pesan ke Mama
    if(mysqli_query($koneksi, $query)) {
        // Format tanggal kalender Indonesia untuk pesan sukses
        $tanggal_tampil = date('d-m-Y', strtotime($tanggal_keluar));
        
        echo "<script>
                alert('Berhasil! Anak kos sudah masuk.\\n\\nJatuh tempo otomatis diatur ke: $tanggal_tampil');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Menampilkan error jika ada masalah query
        echo "Waduh, ada error sistem: " . mysqli_error($koneksi);
    }
} else {
    // Kalau ada yang iseng buka file ini langsung tanpa lewat form, tendang balik ke depan
    header("Location: index.php");
}
?>