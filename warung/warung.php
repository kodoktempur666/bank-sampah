<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id_warung = $_SESSION['user']['id'];

// Menampilkan saldo warung mitra
$query_saldo = "SELECT saldo FROM warung_mitra WHERE id = '$id_warung'";
$result_saldo = mysqli_query($conn, $query_saldo);
$saldo = mysqli_fetch_assoc($result_saldo)['saldo'];

// Ambil riwayat pembayaran
$query_riwayat = "SELECT t.*, wm.nama_warung, r.nama AS nama_pembayar 
                  FROM transaksi t 
                  JOIN warung_mitra wm ON t.id_warung_mitra = wm.id 
                  JOIN rumah_tangga r ON t.id_rumah_tangga = r.id 
                  WHERE t.id_warung_mitra = '$id_warung' 
                  ORDER BY t.tanggal DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);

// Ambil riwayat penarikan
$query_riwayat_penarikan = "SELECT rp.*, wm.nama_warung
                           FROM riwayat_penarikan rp
                           JOIN warung_mitra wm ON rp.id_warung_mitra = wm.id
                           WHERE rp.id_warung_mitra = '$id_warung'
                           ORDER BY rp.tanggal DESC";
$result_riwayat_penarikan = mysqli_query($conn, $query_riwayat_penarikan);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Mitra Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Warung Mitra Dashboard</h1>
        <p>Selamat datang, warung mitra!</p>
        <h3>Saldo: Rp. <?= number_format($saldo, 2, ',', '.') ?></h3>
        <a href="page.php?mod=pencairan" class="btn btn-warning mt-3">Cairkan Saldo</a>
    </div>
    <div class="container mt-5">
        <h2>Riwayat Pembayaran</h2>
        <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pembayar</th>
                        <th>Nama Warung</th>
                        <th>Jumlah Pembayaran (Rp)</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                    <tr>
                        <td><?= $riwayat['nama_pembayar'] ?></td>
                        <td><?= $riwayat['nama_warung'] ?></td>
                        <td><?= number_format($riwayat['jumlah_pembayaran'], 2, ',', '.') ?></td>
                        <td><?= $riwayat['tanggal'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada riwayat pembayaran.</p>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h2>Riwayat Penarikan</h2>
        <?php if (mysqli_num_rows($result_riwayat_penarikan) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Warung</th>
                        <th>Jumlah Penarikan (Rp)</th>
                        <th>Tipe Penarikan</th>
                        <th>No Rekening</th>
                        <th>Bank</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($riwayat_penarikan = mysqli_fetch_assoc($result_riwayat_penarikan)): ?>
                    <tr>
                        <td><?= $riwayat_penarikan['nama_warung'] ?></td>
                        <td><?= number_format($riwayat_penarikan['jumlah'], 2, ',', '.') ?></td>
                        <td><?= $riwayat_penarikan['tipe_penarikan'] ?></td>
                        <td><?= $riwayat_penarikan['no_rekening'] ?></td>
                        <td><?= $riwayat_penarikan['bank'] ?></td>
                        <td><?= $riwayat_penarikan['status'] ?></td>
                        <td><?= $riwayat_penarikan['tanggal'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada riwayat penarikan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
