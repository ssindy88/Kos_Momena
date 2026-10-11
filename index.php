<?php
include 'includes/connection.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pembukuan Kos Mama</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; color: #333; }
        .header { background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 28px; }
        
        /* Tombol Navigasi Tab Lurus & Presisi */
        .tab-container { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 20px; }
        .tab-btn { flex: 1; padding: 15px; text-align: center; font-size: 18px; font-weight: bold; border-radius: 8px; cursor: pointer; border: 2px solid #3498db; background-color: white; color: #3498db; transition: all 0.3s ease; }
        .tab-btn.active { background-color: #3498db; color: white; box-shadow: 0 4px 6px rgba(52, 152, 219, 0.3); }
        
        /* Konten Tab */
        .tab-content { display: none; }
        .tab-content.active { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        
        /* Desain Kartu (Sama seperti sebelumnya) */
        .kamar-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-left: 8px solid #ccc; transition: transform 0.2s; }
        .kamar-card:hover { transform: translateY(-5px); }
        .kamar-card h2 { margin: 0 0 10px 0; font-size: 24px; }
        .kamar-card p { margin: 5px 0; font-size: 16px; }
        .kamar-card .harga { font-weight: bold; color: #555; }
        
        .status-kosong { border-left-color: #2ecc71; } 
        .status-kosong .badge { background-color: #2ecc71; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .status-terisi { border-left-color: #3498db; } 
        .status-terisi .badge { background-color: #3498db; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .status-warning { border-left-color: #f1c40f; background-color: #fffdf0; } 
        .status-warning .badge { background-color: #f1c40f; color: #333; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .status-lewat { border-left-color: #e74c3c; background-color: #fff0f0; } 
        .status-lewat .badge { background-color: #e74c3c; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        
        .btn-action { display: block; text-align: center; background-color: #2c3e50; color: white; text-decoration: none; padding: 12px; border-radius: 6px; margin-top: 15px; font-weight: bold; font-size: 16px; }
        .btn-action:hover { background-color: #1a252f; }
        .btn-laporan { display:inline-block; margin-top:15px; padding:12px 25px; background-color:#2ecc71; color:white; text-decoration:none; border-radius:8px; font-weight:bold; font-size: 18px; box-shadow: 0 4px 6px rgba(46, 204, 113, 0.3); }
        .btn-pengeluaran { display:inline-block; margin-top:15px; margin-left:10px; padding:12px 25px; background-color:#e74c3c; color:white; text-decoration:none; border-radius:8px; font-weight:bold; font-size: 18px; box-shadow: 0 4px 6px rgba(231, 76, 60, 0.3); }
    </style>
</head>
<body>

<div class="header">
    <h1>🏠 Dashboard Kos Mama</h1>
    <p>Pantau status kamar dan jadwal pembayaran anak kos dengan mudah.</p>
    <a href="report.php" class="btn-laporan">📊 Buku Kas</a>
    <a href="record_pengeluaran.php" class="btn-pengeluaran">💸 Catat Pengeluaran</a>
</div>

<!-- Tombol Navigasi Lantai -->
<div class="tab-container">
    <div class="tab-btn active" onclick="bukaTab('bawah', this)">Lantai Bawah (A)</div>
    <div class="tab-btn" onclick="bukaTab('atas', this)">Lantai Atas (B)</div>
</div>

<?php
$query = "
    SELECT 
        k.id_kamar, k.nomor_kamar, k.harga_bulanan,
        p.id_penyewa, p.nama_lengkap, p.tanggal_keluar, p.status_sewa, p.tipe_sewa
    FROM kamar k
    LEFT JOIN penyewa p ON k.id_kamar = p.id_kamar AND p.status_sewa = 'Aktif'
    ORDER BY k.nomor_kamar ASC
";

$result = mysqli_query($koneksi, $query);
$tanggal_sekarang = date('Y-m-d');

$html_bawah = "";
$html_atas = "";

if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        
        if (empty($row['id_penyewa'])) {
            $class_kartu = "status-kosong";
            $teks_badge = "Kosong - Siap Huni";
            $info_penghuni = "<p>Tidak ada penghuni.</p>";
            $tombol_aksi = "<a href='checkin.php?id_kamar=".$row['id_kamar']."' class='btn-action'>+ Isi Penghuni Baru</a>";
        } else {
            $tanggal_keluar = $row['tanggal_keluar'];
            $diff = strtotime($tanggal_keluar) - strtotime($tanggal_sekarang);
            $sisa_hari = round($diff / (60 * 60 * 24));
            
            // LOGIKA STATUS PEMBAYARAN BARU
            if ($sisa_hari < 0) {
                $class_kartu = "status-lewat";
                $teks_badge = "Lewat Tempo (" . abs($sisa_hari) . " Hari)";
                $status_bayar = "<span style='color: white; font-weight: bold; background: #e74c3c; padding: 4px 8px; border-radius: 5px;'>❌ BELUM BAYAR</span>";
            } elseif ($sisa_hari == 0) {
                $class_kartu = "status-warning";
                $teks_badge = "Jatuh Tempo Hari Ini!";
                $status_bayar = "<span style='color: white; font-weight: bold; background: #d35400; padding: 4px 8px; border-radius: 5px;'>⚠️ WAKTUNYA BAYAR</span>";
            } elseif ($sisa_hari <= 3) {
                $class_kartu = "status-warning";
                $teks_badge = "Mendekati Tempo (Sisa $sisa_hari Hari)";
                $status_bayar = "<span style='color: #8a6d3b; font-weight: bold; background: #fcf8e3; padding: 4px 8px; border-radius: 5px;'>⏳ Akan Jatuh Tempo</span>";
            } else {
                $class_kartu = "status-terisi";
                $teks_badge = "Terisi - Aman";
                $status_bayar = "<span style='color: white; font-weight: bold; background: #2ecc71; padding: 4px 8px; border-radius: 5px;'>✅ SUDAH BAYAR</span>";
            }

            $info_penghuni = "<p>🧑 Penghuni: <strong>" . $row['nama_lengkap'] . "</strong></p>";
            $info_penghuni .= "<p>⏳ Tipe Sewa: <strong>" . $row['tipe_sewa'] . "</strong></p>";
            $info_penghuni .= "<p>📅 Jatuh Tempo: <strong>" . date('d M Y', strtotime($tanggal_keluar)) . "</strong></p>";
            // MENAMPILKAN STATUS PEMBAYARAN KE KARTU
            $info_penghuni .= "<p>💰 Status: " . $status_bayar . "</p>";
            
            $tombol_aksi = "<a href='tenant_detail.php?id=".$row['id_penyewa']."' class='btn-action'>Lihat Detail & Perpanjang</a>";
        }

        $kartu = "
        <div class='kamar-card $class_kartu'>
            <h2>Kamar " . $row['nomor_kamar'] . "</h2>
            <span class='badge'>$teks_badge</span>
            <p class='harga' style='margin-top: 15px;'>Rp " . number_format($row['harga_bulanan'], 0, ',', '.') . " / bln</p>
            <hr style='border: 0; border-top: 1px solid #eee; margin: 15px 0;'>
            $info_penghuni
            $tombol_aksi
        </div>
        ";

        // Filter Otomatis: Awalan 'B' atau 'b' masuk ke atas, sisanya ke bawah
        if (strtoupper(substr($row['nomor_kamar'], 0, 1)) === 'B') {
            $html_atas .= $kartu;
        } else {
            $html_bawah .= $kartu;
        }
    }
}
?>

<!-- Kontainer Kamar Bawah -->
<div id="bawah" class="tab-content active">
    <?php echo $html_bawah ?: "<p>Belum ada data kamar bawah.</p>"; ?>
</div>

<!-- Kontainer Kamar Atas -->
<div id="atas" class="tab-content">
    <?php echo $html_atas ?: "<p>Belum ada data kamar atas.</p>"; ?>
</div>

<!-- Script Sederhana untuk Ganti Tab -->
<script>
function bukaTab(targetId, elemenTombol) {
    // Sembunyikan semua konten tab
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    // Hapus warna biru dari semua tombol
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Tampilkan tab yang diklik dan beri warna biru
    document.getElementById(targetId).classList.add('active');
    elemenTombol.classList.add('active');
}
</script>

</body>
</html>