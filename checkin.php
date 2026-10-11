<?php
// Gunakan nama file sesuai di folder includes milikmu
include 'includes/connection.php'; 

$id_kamar = isset($_GET['id_kamar']) ? $_GET['id_kamar'] : 0;

$query_kamar = "SELECT nomor_kamar, harga_bulanan FROM kamar WHERE id_kamar = '$id_kamar'";
$result_kamar = mysqli_query($koneksi, $query_kamar);
$data_kamar = mysqli_fetch_assoc($result_kamar);

if(!$data_kamar) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Penghuni Baru</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; padding: 20px; color: #333; }
        .form-container { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 600px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-form { text-align: center; margin-bottom: 25px; }
        .header-form h2 { margin: 0; color: #2c3e50; }
        .badge-kamar { display: inline-block; background-color: #2ecc71; color: white; padding: 8px 15px; border-radius: 8px; font-size: 20px; font-weight: bold; margin-top: 10px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 18px; }
        input[type="text"], input[type="date"], select { 
            width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 18px;
        }
        input:focus, select:focus { border-color: #3498db; outline: none; }
        
        .btn-submit { width: 100%; background-color: #2ecc71; color: white; border: none; padding: 18px; font-size: 20px; font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 10px; box-shadow: 0 4px 6px rgba(46, 204, 113, 0.3); }
        .btn-submit:hover { background-color: #27ae60; }
        .btn-back { display: block; text-align: center; margin-bottom: 20px; text-decoration: none; color: #7f8c8d; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>

    <a href="index.php" class="btn-back">⬅ Kembali ke Dashboard Utama</a>

    <div class="form-container">
        <div class="header-form">
            <h2>Pendaftaran Penghuni Baru</h2>
            <div class="badge-kamar">Kamar <?php echo $data_kamar['nomor_kamar']; ?></div>
        </div>
        
        <form action="process_checkin.php" method="POST">
            
            <input type="hidden" name="id_kamar" value="<?php echo $id_kamar; ?>">

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap Anak Kos</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Contoh: Budi Santoso">
            </div>

            <div class="form-group">
                <label for="tipe_sewa">Mau Sewa Berapa Lama?</label>
                <select id="tipe_sewa" name="tipe_sewa" required>
                    <option value="Bulanan">Bulanan</option>
                    <option value="Mingguan">Mingguan</option>
                    <option value="Harian">Harian</option>
                </select>
            </div>

            <div class="form-group">
                <label for="durasi_sewa">Jumlah (Bulan/Minggu/Hari)</label>
                <select id="durasi_sewa" name="durasi_sewa" required>
                    <!-- Opsi dari 1 sampai 12 agar fleksibel untuk harian/mingguan/bulanan -->
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_masuk">Mulai Tanggal Berapa?</label>
                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="jumlah_deposit">Uang Deposit</label>
                <select id="jumlah_deposit" name="jumlah_deposit" required>
                    <option value="0">Tidak Ada Deposit (Rp 0)</option>
                    <option value="200000">Rp 200.000</option>
                    <option value="300000">Rp 300.000</option>
                </select>
            </div>

            <button type="submit" name="submit" class="btn-submit">Simpan Data!</button>

        </form>
    </div>

</body>
</html>