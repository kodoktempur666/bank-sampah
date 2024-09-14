<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jenis_sampah = $_POST['id_jenis_sampah'];  // ID Jenis Sampah
    $berat_sampah = $_POST['berat_sampah'];        // Berat Sampah
    $id_rumah_tangga = $_SESSION['user']['id'];

    // Ambil harga per kg dari tabel jenis_sampah berdasarkan id_jenis_sampah
    $query = "SELECT harga_per_kg FROM jenis_sampah WHERE id = $id_jenis_sampah";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $harga_per_kg = $row['harga_per_kg'];

    // Hitung total harga berdasarkan berat sampah
    $total_harga = $berat_sampah * $harga_per_kg;

    // Simpan data ke tabel sampah
    $query_insert = "INSERT INTO sampah (id_rumah_tangga, id_jenis_sampah, berat, total_harga) 
                 VALUES ('$id_rumah_tangga', '$id_jenis_sampah', '$berat_sampah', '$total_harga')";
    mysqli_query($conn, $query_insert);


    // Redirect ke halaman sukses atau dashboard
    header("Location: page.php?mod=users");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jual Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Jual Sampah</h2>
        <form method="POST">
            <div class="form-group">
                <label for="id_jenis_sampah">Jenis Sampah</label>
                <select name="id_jenis_sampah" class="form-control" required>
                    <?php
                    $query = "SELECT * FROM jenis_sampah";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['id']}'>{$row['nama_jenis']} - Rp" . number_format($row['harga_per_kg'], 2, ',', '.') . " per Kg</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label  for="berat_sampah"></label>
                <input type="hidden" name="berat_sampah" step="0.01" class="form-control" value="0" disabled>
            </div>
            <button type="submit" class="btn btn-primary">Jual Sampah</button>
        </form>
    </div>
</body>
</html>