<?php
session_start();
require '../config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$id_rumah_tangga = $_SESSION['user']['id'];

// Proses pembayaran jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_pembayaran = floatval($_POST['jumlah_pembayaran']);
    
    // Ambil saldo saat ini
    $query_saldo = "SELECT saldo FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
    $result_saldo = mysqli_query($conn, $query_saldo);
    $current_saldo = mysqli_fetch_assoc($result_saldo)['saldo'];
    
    // Periksa apakah saldo mencukupi
    if ($jumlah_pembayaran <= $current_saldo) {
        // Kurangi saldo rumah tangga
        $query_update_saldo = "UPDATE rumah_tangga SET saldo = saldo - '$jumlah_pembayaran' WHERE id = '$id_rumah_tangga'";
        mysqli_query($conn, $query_update_saldo);
        
        // Tambah saldo ke warung mitra
        $id_warung_mitra = $_POST['id_warung_mitra'];
        $query_update_warung = "UPDATE warung_mitra SET saldo = saldo + '$jumlah_pembayaran' WHERE id = '$id_warung_mitra'";
        mysqli_query($conn, $query_update_warung);
        
        // Catat transaksi
        $query_transaksi = "INSERT INTO transaksi (id_rumah_tangga, id_warung_mitra, jumlah_pembayaran, tanggal) 
                            VALUES ('$id_rumah_tangga', '$id_warung_mitra', '$jumlah_pembayaran', NOW())";
        mysqli_query($conn, $query_transaksi);
        
        // Redirect ke dashboard atau halaman sukses
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Saldo tidak mencukupi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Pembayaran</h2>
        <form method="POST">
            <div class="form-group">
                <label for="id_warung_mitra">Pilih Warung Mitra</label>
                <select name="id_warung_mitra" class="form-control" required>
                    <?php
                    // Ambil daftar warung mitra
                    $query_warung = "SELECT * FROM warung_mitra";
                    $result_warung = mysqli_query($conn, $query_warung);
                    while ($row = mysqli_fetch_assoc($result_warung)) {
                        echo "<option value='{$row['id']}'>{$row['nama_warung']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jumlah_pembayaran">Jumlah Pembayaran (Rp)</label>
                <input type="number" name="jumlah_pembayaran" step="0.01" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Bayar</button>
        </form>
    </div>
</body>
</html>
