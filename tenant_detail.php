<?php
include 'includes/connection.php';

$id_penyewa = isset($_GET['id']) ? $_GET['id'] : 0;

// Query diperbarui untuk mengambil harga harian, mingguan, dan bulanan
$query = "SELECT p.*, k.nomor_kamar, k.harga_harian, k.harga_mingguan, k.harga_bulanan 
          FROM penyewa p 
          JOIN kamar k ON p.id_kamar = k.id_kamar 
          WHERE p.id_penyewa = '$id_penyewa' AND p.status_sewa = 'Aktif'";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if(!$data) {
    echo "<script>alert('Data tidak ditemukan atau anak kos sudah pindah.'); window.location.href='index.php';</script>";
    exit;
}

$tgl_masuk = date('d M Y', strtotime($data['tanggal_masuk']));
$tgl_jatuh_tempo = date('d M Y', strtotime($data['tanggal_keluar']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Anak Kos</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; padding: 20px; color: #333; }
        .container { background: white; padding: 30px; border-radius: 12px; max-width: 600px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-detail { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .header-detail h2 { margin: 0 0 10px 0; color: #2c3e50; }
        .badge-kamar { background-color: #3498db; color: white; padding: 8px 15px; border-radius: 8px; font-size: 18px; font-weight: bold; }
        
        .info-box { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 5px solid #3498db; font-size: 18px; }
        .info-box p { margin: 8px 0; }
        .jatuh-tempo { color: #e74c3c; font-weight: bold; font-size: 20px; }

        .form-perpanjang { border: 2px dashed #2ecc71; padding: 20px; border-radius: 8px; background-color: #f4fdf7; }
        .form-perpanjang h3 { margin-top: 0; color: #27ae60; }
        label { display: block; margin-bottom: 8px; font-weight: bold; font-size: 18px; }
        
        /* Layout Fleksibel untuk Input Durasi */
        .flex-input { display: flex; gap: 10px; margin-bottom: 15px; }
        .flex-input input { flex: 1; text-align: center; font-weight: bold; }
        .flex-input select { flex: 2; }
        
        select, input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 18px; margin-bottom: 15px; }
        input[readonly] { background-color: #e9ecef; color: #555; cursor: not-allowed; font-weight: bold; font-size: 20px; }
        
        .btn-perpanjang { width: 100%; background-color: #2ecc71; color: white; border: none; padding: 15px; font-size: 18px; font-weight: bold; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 6px rgba(46, 204, 113, 0.3); }
        .btn-perpanjang:hover { background-color: #27ae60; }
        
        .btn-keluar { display: block; text-align: center; background-color: #e74c3c; color: white; text-decoration: none; padding: 15px; border-radius: 8px; font-weight: bold; font-size: 18px; margin-top: 20px; }
        .btn-back { display: block; text-align: center; margin-bottom: 20px; text-decoration: none; color: #7f8c8d; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>

    <a href="index.php" class="btn-back">⬅ Kembali ke Dashboard Utama</a>

    <div class="container">
        <div class="header-detail">
            <h2><?php echo $data['nama_lengkap']; ?></h2>
            <span class="badge-kamar">Kamar <?php echo $data['nomor_kamar']; ?></span>
        </div>

        <div class="info-box">
            <p>📋 Tipe Sewa Saat Ini: <strong><?php echo $data['tipe_sewa']; ?></strong></p>
            <p>🗓️ Tanggal Masuk: <strong><?php echo $tgl_masuk; ?></strong></p>
            <p class="jatuh-tempo">🚨 Jatuh Tempo: <?php echo $tgl_jatuh_tempo; ?></p>
        </div>

        <div class="form-perpanjang">
            <h3>Perpanjang Sewa (Bebas Pilih)</h3>
            <form action="process_extend.php" method="POST">
                <input type="hidden" name="id_penyewa" value="<?php echo $id_penyewa; ?>">
                <input type="hidden" name="tanggal_keluar_lama" value="<?php echo $data['tanggal_keluar']; ?>">
                
                <label>Mau tambah durasi berapa lama?</label>
                <div class="flex-input">
                    <!-- Input Angka -->
                    <input type="number" name="durasi_angka" id="durasi_angka" value="1" min="1" required>
                    
                    <!-- Input Satuan Waktu -->
                    <select name="durasi_satuan" id="durasi_satuan" required>
                        <option value="Hari" <?php if($data['tipe_sewa'] == 'Harian') echo 'selected'; ?>>Hari</option>
                        <option value="Minggu" <?php if($data['tipe_sewa'] == 'Mingguan') echo 'selected'; ?>>Minggu</option>
                        <option value="Bulan" <?php if($data['tipe_sewa'] == 'Bulanan') echo 'selected'; ?>>Bulan</option>
                    </select>
                </div>

                <label>Jumlah Uang (Dihitung Otomatis)</label>
                <!-- Dibuat readonly agar Mama tidak usah mengetik manual -->
                <input type="text" name="jumlah_bayar" id="jumlah_bayar" required>
                <button type="submit" name="submit" class="btn-perpanjang">✅ Bayar & Perpanjang</button>
            </form>
        </div>

        <a href="checkout.php?id=<?php echo $id_penyewa; ?>" class="btn-keluar" onclick="return confirm('Apakah Anda yakin anak kos ini sudah pindah/keluar?');">🛑 Anak Kos Keluar (Check-Out)</a>

    </div>

   <!-- KALKULATOR PINTAR JAVASCRIPT (KAMUS HARGA FINAL + FORMAT RUPIAH) -->
    <script>
        const hargaBulanKamarIni = <?php echo $data['harga_bulanan']; ?>;

        // KAMUS 1: Kamar Standar (600.000 / Bulan)
        const hargaHari600 = {
            1: 60000, 2: 110000, 3: 150000, 4: 190000, 5: 225000, 6: 250000, 7: 280000, 
            8: 310000, 9: 335000, 10: 360000, 11: 385000, 12: 410000, 13: 435000, 14: 460000, 
            // Lanjutan hari 15-30 (naik 10rb/hari) agar mentok di 600k
            15: 470000, 16: 480000, 17: 490000, 18: 500000, 19: 510000, 20: 520000, 21: 530000, 
            22: 540000, 23: 550000, 24: 560000, 25: 570000, 26: 580000, 27: 590000, 28: 600000, 29: 600000, 30: 600000
        };

        // KAMUS 2: Kamar Premium (700.000 / Bulan) 
        const hargaHari700 = {
            1: 70000, 2: 130000, 3: 180000, 4: 225000, 5: 265000, 6: 300000, 7: 330000, 
            8: 360000, 9: 390000, 10: 420000, 11: 450000, 12: 480000, 13: 510000, 14: 540000, 
            // Lanjutan hari 15-30 (naik bertahap) agar mentok di 700k
            15: 555000, 16: 570000, 17: 585000, 18: 600000, 19: 615000, 20: 630000, 21: 645000, 
            22: 660000, 23: 670000, 24: 680000, 25: 690000, 26: 695000, 27: 700000, 28: 700000, 29: 700000, 30: 700000
        };

        let kamusAktif = hargaHari600; 
        if (hargaBulanKamarIni == 700000) { kamusAktif = hargaHari700; }

        const inputAngka = document.getElementById('durasi_angka');
        const inputSatuan = document.getElementById('durasi_satuan');
        const inputBayar = document.getElementById('jumlah_bayar');

        // Fungsi perapih angka dengan Titik (Format Rupiah)
        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, '');
            let sisa = number_string.length % 3;
            let rupiah = number_string.substr(0, sisa);
            let ribuan = number_string.substr(sisa).match(/\d{3}/g);
            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }

        function hitungOtomatis() {
            let angka = parseInt(inputAngka.value) || 0; 
            let satuan = inputSatuan.value;
            let total = 0;

            if (satuan === 'Hari') {
                if (angka >= 30) {
                    let jumlahBulan = Math.floor(angka / 30);
                    let sisaHari = angka % 30;
                    total = (jumlahBulan * hargaBulanKamarIni) + (sisaHari > 0 ? kamusAktif[sisaHari] : 0);
                } else {
                    total = kamusAktif[angka];
                }
            } else if (satuan === 'Minggu') {
                let totalHari = angka * 7;
                if (totalHari >= 30) {
                    let jumlahBulan = Math.floor(totalHari / 30);
                    let sisaHari = totalHari % 30;
                    total = (jumlahBulan * hargaBulanKamarIni) + (sisaHari > 0 ? kamusAktif[sisaHari] : 0);
                } else {
                    total = kamusAktif[totalHari];
                }
            } else if (satuan === 'Bulan') {
                total = angka * hargaBulanKamarIni;
            }

            // Terapkan format Rupiah ke hasil perhitungan otomatis
            inputBayar.value = formatRupiah(total);
        }

        // Kalau mamamu ngetik manual bayar DP/nyicil, langsung dikasih titik
        inputBayar.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });

        // Jalankan saat durasi / satuan diubah
        inputAngka.addEventListener('input', hitungOtomatis);
        inputSatuan.addEventListener('change', hitungOtomatis);
        
        // Panggil saat halaman pertama terbuka
        hitungOtomatis();
    </script>
</body>
</html>