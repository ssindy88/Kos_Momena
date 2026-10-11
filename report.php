<?php
include 'includes/connection.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Kos</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; padding: 20px; color: #333; }
        .container { background: white; padding: 30px; border-radius: 12px; max-width: 900px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-laporan { text-align: center; margin-bottom: 30px; }
        .header-laporan h2 { margin: 0; color: #2c3e50; font-size: 28px; }
        
        /* Gaya untuk Tabel */
        table { width: 100%; border-collapse: collapse; font-size: 16px; margin-bottom: 30px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        tr:hover { background-color: #f5f5f5; }
        
        /* Warna Header Tabel */
        .th-masuk { background-color: #2ecc71; color: white; }
        .th-keluar { background-color: #e74c3c; color: white; }
        
        /* Warna Angka Uang */
        .uang-masuk { font-weight: bold; color: #27ae60; }
        .uang-keluar { font-weight: bold; color: #c0392b; }
        
        /* Kotak Ringkasan (Laba Bersih) */
        .summary-container { display: flex; justify-content: space-between; gap: 20px; margin-top: 20px; }
        .summary-box { flex: 1; padding: 20px; border-radius: 10px; text-align: center; border: 2px dashed #ccc; }
        .box-masuk { background-color: #eafaf1; border-color: #2ecc71; color: #27ae60; }
        .box-keluar { background-color: #fdf0ed; border-color: #e74c3c; color: #c0392b; }
        .box-laba { background-color: #eef7fd; border-color: #3498db; color: #2980b9; box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2); border-style: solid; }
        
        .summary-box h3 { margin: 0 0 10px 0; font-size: 18px; }
        .summary-box .angka { font-size: 24px; font-weight: bold; }
        
        .btn-back { display: block; text-align: center; margin-bottom: 20px; text-decoration: none; color: #7f8c8d; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>

    <a href="index.php" class="btn-back">⬅ Kembali ke Dashboard Utama</a>

    <div class="container">
        <div class="header-laporan">
            <h2>📊 Buku Kas Kos Mama</h2>
            <p>Rekapitulasi seluruh uang masuk dan uang keluar.</p>
        </div>

        <!-- BAGIAN 1: TABEL UANG MASUK -->
        <h3 style="color: #27ae60;">📥 Riwayat Uang Masuk (Pendapatan)</h3>
        <?php
        $query_masuk = "SELECT pb.tanggal_bayar, pb.jumlah_bayar, pb.keterangan, py.nama_lengkap 
                        FROM pembayaran pb 
                        LEFT JOIN penyewa py ON pb.id_penyewa = py.id_penyewa 
                        ORDER BY pb.tanggal_bayar DESC";
        $result_masuk = mysqli_query($koneksi, $query_masuk);
        $total_pemasukan = 0;
        ?>
        <table>
            <thead>
                <tr>
                    <th class="th-masuk">Tanggal</th>
                    <th class="th-masuk">Nama Anak Kos</th>
                    <th class="th-masuk">Keterangan</th>
                    <th class="th-masuk">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($result_masuk) > 0) {
                    while($row = mysqli_fetch_assoc($result_masuk)) {
                        $total_pemasukan += $row['jumlah_bayar'];
                        echo "<tr>";
                        echo "<td>" . date('d M Y', strtotime($row['tanggal_bayar'])) . "</td>";
                        echo "<td><strong>" . ($row['nama_lengkap'] ? $row['nama_lengkap'] : 'Tidak diketahui') . "</strong></td>";
                        echo "<td>" . $row['keterangan'] . "</td>";
                        echo "<td class='uang-masuk'>+ Rp " . number_format($row['jumlah_bayar'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>Belum ada pemasukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- BAGIAN 2: TABEL UANG KELUAR -->
        <h3 style="color: #c0392b; margin-top: 40px;">📤 Riwayat Uang Keluar (Pengeluaran)</h3>
        <?php
        $query_keluar = "SELECT * FROM pengeluaran ORDER BY tanggal DESC";
        $result_keluar = mysqli_query($koneksi, $query_keluar);
        $total_pengeluaran = 0;
        ?>
        <table>
            <thead>
                <tr>
                    <th class="th-keluar">Tanggal</th>
                    <th class="th-keluar">Kategori</th>
                    <th class="th-keluar">Keterangan</th>
                    <th class="th-keluar">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($result_keluar) > 0) {
                    while($row = mysqli_fetch_assoc($result_keluar)) {
                        $total_pengeluaran += $row['jumlah'];
                        echo "<tr>";
                        echo "<td>" . date('d M Y', strtotime($row['tanggal'])) . "</td>";
                        echo "<td><strong>" . $row['kategori'] . "</strong></td>";
                        echo "<td>" . $row['keterangan'] . "</td>";
                        echo "<td class='uang-keluar'>- Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>Belum ada pengeluaran.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- BAGIAN 3: RINGKASAN LABA BERSIH -->
        <?php 
        // Rumus sakti Laba Bersih
        $laba_bersih = $total_pemasukan - $total_pengeluaran; 
        ?>
        <h3 style="text-align: center; margin-top: 50px;">📈 Ringkasan Keuangan</h3>
        <div class="summary-container">
            <div class="summary-box box-masuk">
                <h3>Total Pemasukan</h3>
                <div class="angka">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></div>
            </div>
            
            <div class="summary-box box-keluar">
                <h3>Total Pengeluaran</h3>
                <div class="angka">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></div>
            </div>

            <div class="summary-box box-laba">
                <h3>Laba Bersih</h3>
                <div class="angka">Rp <?php echo number_format($laba_bersih, 0, ',', '.'); ?></div>
            </div>
        </div>

    </div>

</body>
</html>