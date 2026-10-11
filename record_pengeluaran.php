<?php
include 'includes/connection.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pengeluaran</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; padding: 20px; color: #333; }
        .form-container { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 600px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-form { text-align: center; margin-bottom: 25px; }
        .header-form h2 { margin: 0; color: #e74c3c; } /* Warna merah untuk pengeluaran */
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 18px; }
        input[type="text"], input[type="number"], input[type="date"], select { 
            width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 18px;
        }
        input:focus, select:focus { border-color: #e74c3c; outline: none; }
        
        .btn-submit { width: 100%; background-color: #e74c3c; color: white; border: none; padding: 18px; font-size: 20px; font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 10px; box-shadow: 0 4px 6px rgba(231, 76, 60, 0.3); }
        .btn-submit:hover { background-color: #c0392b; }
        .btn-back { display: block; text-align: center; margin-bottom: 20px; text-decoration: none; color: #7f8c8d; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>

    <a href="index.php" class="btn-back">⬅ Kembali ke Dashboard Utama</a>

    <div class="form-container">
        <div class="header-form">
            <h2>💸 Catat Pengeluaran Kos</h2>
            <p>Masukkan biaya operasional yang keluar hari ini.</p>
        </div>
        
        <form action="process_pengeluaran.php" method="POST">

            <div class="form-group">
                <label for="tanggal">Tanggal Pengeluaran</label>
                <input type="date" id="tanggal" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori Pengeluaran</label>
                <select id="kategori" name="kategori" required>
                    <option value="Listrik / Token">Listrik / Token</option>
                    <option value="Air">Air PAM / Sumur</option>
                    <option value="Kebersihan / Keamanan">Iuran Kebersihan / Keamanan</option>
                    <option value="Perbaikan">Perbaikan (Tukang, Genteng, dll)</option>
                    <option value="Lainnya">Lainnya...</option>
                </select>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan Detail (Opsional)</label>
                <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Beli token listrik untuk bulan September">
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Uang Keluar (Rp)</label>
                <input type="text" id="jumlah" name="jumlah" required placeholder="Contoh: 150.000">
            </div>

            <button type="submit" name="submit" class="btn-submit">Catat Pengeluaran!</button>

        </form>
    </div>
<script>
        var inputJumlah = document.getElementById('jumlah');
        inputJumlah.addEventListener('keyup', function(e) {
            inputJumlah.value = formatRupiah(this.value);
        });

        // Fungsi ajaib untuk menambah titik otomatis
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa  = split[0].length % 3,
                rupiah  = split[0].substr(0, sisa),
                ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }
    </script>
</body>
</html>