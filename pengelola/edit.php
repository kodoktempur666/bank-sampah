<?php
session_start();
require 'config/connect.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

if ($_SESSION['user']['role'] == 'rumah_tangga') {
    header("Location: page.php?mod=unaut");
    exit();
}

// Daftar peran yang tidak diizinkan
$not_allowed_roles = ['admin', 'warung_mitra'];
if (in_array($_SESSION['user']['role'], $not_allowed_roles)) {
    // Jika pengguna memiliki salah satu dari peran yang tidak diizinkan, redirect mereka
    header("Location: page.php?mod=unaut2");
    exit();
}

// Periksa apakah ada ID sampah yang diberikan
if (!isset($_GET['id'])) {
    header("Location: page.php?mod=pengelola");
    exit();
}

$id_sampah = $_GET['id'];

// Ambil data sampah berdasarkan ID untuk ditampilkan dalam form
$query_sampah = "SELECT s.*, js.harga_per_kg FROM sampah s
                 JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                 WHERE s.id = '$id_sampah'";
$result_sampah = mysqli_query($conn, $query_sampah);

// Jika tidak ada data sampah ditemukan, kembali ke halaman pengelola
if (mysqli_num_rows($result_sampah) == 0) {
    header("Location: page.php?mod=pengelola");
    exit();
}

$sampah = mysqli_fetch_assoc($result_sampah);

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $berat = $_POST['berat'];

    // Ambil harga per kg berdasarkan jenis sampah yang sudah ada
    $harga_per_kg = $sampah['harga_per_kg'];

    // Hitung total harga
    $total_harga = $berat * $harga_per_kg;

    // Update data sampah
    $query_update = "UPDATE sampah SET 
                        berat = '$berat',
                        total_harga = '$total_harga'
                    WHERE id = '$id_sampah'";
    if (mysqli_query($conn, $query_update)) {
        header("Location: page.php?mod=pengelola");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data jenis sampah untuk dropdown
$query_jenis_sampah = "SELECT * FROM jenis_sampah";
$result_jenis_sampah = mysqli_query($conn, $query_jenis_sampah);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hitung Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Hitung Sampah</h1>

        <form method="POST">
            <div class="form-group">
                <label for="jenis_sampah">Jenis Sampah</label>
                <select name="jenis_sampah" id="jenis_sampah" class="form-control" disabled>
                    <?php while ($jenis = mysqli_fetch_assoc($result_jenis_sampah)): ?>
                        <option value="<?= $jenis['id'] ?>" <?= ($jenis['id'] == $sampah['id_jenis_sampah']) ? 'selected' : '' ?>>
                            <?= $jenis['nama_jenis'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="berat">Berat (kg)</label>
                <input type="number" step="0.01" name="berat" id="berat" value="<?= $sampah['berat'] ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="harga_per_kg">Harga per Kg (Rp)</label>
                <input type="number" step="0.01" name="harga_per_kg" id="harga_per_kg" value="<?= $sampah['harga_per_kg'] ?>" class="form-control" readonly>
            </div>

            <!-- <div class="form-group">
                <label for="total_harga">Total Harga (Rp)</label>
                <input type="number" step="0.01" name="total_harga" id="total_harga" value="<?= $sampah['total_harga'] ?>" class="form-control" readonly>
            </div> -->

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="page.php?mod=pengelola" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script>
    document.getElementById('berat').addEventListener('input', function() {
        const berat = parseFloat(this.value) || 0;
        const hargaPerKg = parseFloat(document.getElementById('harga_per_kg').value) || 0;
        document.getElementById('total_harga').value = (berat * hargaPerKg).toFixed(2);
    });
    </script>
</body>
</html>
