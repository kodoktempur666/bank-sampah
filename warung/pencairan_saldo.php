<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// // Periksa apakah pengguna adalah pengelola
// if ($_SESSION['user']['role'] !== 'warung_mitra') {
//     // Jika bukan pengelola, redirect ke halaman unauthorized
//     header("Location: page.php?mod=unaut2");
//     exit();
// }

$id_warung = $_SESSION['user']['id'];

// Menampilkan saldo warung mitra
$query_saldo = "SELECT saldo FROM warung_mitra WHERE id = '$id_warung'";
$result_saldo = mysqli_query($conn, $query_saldo);
$saldo1 = mysqli_fetch_assoc($result_saldo)['saldo'];

// Mengajukan Penarikan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah_penarikan = $_POST['jumlah'];


    // Pastikan saldo mencukupi
    if ($jumlah_penarikan > $saldo1) {
        echo "<script>alert('Saldo tidak mencukupi!');</script>";
    } else {
        // Ambil no_rekening dan bank dari warung_mitra


        // Masukkan transaksi penarikan
        $query_insert = "INSERT INTO transaksi_pencairan (id_warung_mitra, jumlah, status) 
                         VALUES ('$id_warung', '$jumlah_penarikan', 'pending')";
        mysqli_query($conn, $query_insert);

        echo "<script>alert('Permintaan pencairan saldo berhasil, menunggu konfirmasi pengelola!'); window.location.href='page.php?mod=warung';</script>";;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Mitra - Penarikan Saldo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Penarikan Saldo</h1>
        <p>Saldo Anda saat ini: Rp. <?= number_format($saldo1, 2, ',', '.') ?></p>

        <form method="POST">
            <div class="form-group">
                <label for="jumlah">Jumlah Penarikan (Rp)</label>
                <input type="number" class="form-control" name="jumlah" required>
            </div>

            <button type="submit" class="btn btn-primary">Ajukan Penarikan</button>
        </form>
    </div>
</body>
</html>
