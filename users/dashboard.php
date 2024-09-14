<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id_rumah_tangga = $_SESSION['user']['id'];

// Saldo terakhir
$query_saldo = "SELECT saldo FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
$result_saldo = mysqli_query($conn, $query_saldo);
$current_saldo = mysqli_fetch_assoc($result_saldo)['saldo'];

// Ambil data sampah yang statusnya menunggu pickup
$query_sampah = "SELECT s.id, s.berat, s.total_harga, s.status, js.nama_jenis 
                 FROM sampah s
                 JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                 WHERE s.id_rumah_tangga = '$id_rumah_tangga' AND s.status = 'menunggu_pickup'";
$result_sampah = mysqli_query($conn, $query_sampah);

// Hitung total harga sampah yang menunggu pickup
$total_pickup = 0;
while ($row = mysqli_fetch_assoc($result_sampah)) {
    $total_pickup += $row['total_harga'];
}

// Ambil semua data sampah untuk history
$query_history = "SELECT s.berat, s.total_harga, s.status, s.created_at, js.nama_jenis 
                  FROM sampah s
                  JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                  WHERE s.id_rumah_tangga = '$id_rumah_tangga'
                  ORDER BY s.id DESC";
$result_history = mysqli_query($conn, $query_history);

// Hitung total harga untuk history
$total_history = 0;
while ($row = mysqli_fetch_assoc($result_history)) {
    $total_history += $row['total_harga'];
}

// Ambil history pembayaran
$query_riwayat = "SELECT t.*, wm.nama_warung 
                  FROM transaksi t 
                  JOIN warung_mitra wm ON t.id_warung_mitra = wm.id 
                  WHERE t.id_rumah_tangga = '$id_rumah_tangga' 
                  ORDER BY t.tanggal DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);

// Hapus sampah
if (isset($_POST['action']) && $_POST['action'] == 'hapus') {
    $id_sampah = $_POST['id_sampah'];
    $query_delete = "DELETE FROM sampah WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_delete);
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Rumah Tangga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Dashboard Rumah Tangga</h1>
        <h3>Saldo: Rp. <?= number_format($current_saldo, 2, ',', '.') ?></h3>
        <a href="pembayaran.php" class="btn btn-primary mt-4">Bayar</a>
        
        <!-- Sampah Siap Pick-Up -->
        <h4 class="mt-4">Sampah Siap Pick-Up</h4>
        <?php if (mysqli_num_rows($result_sampah) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Jenis Sampah</th>
                        <th>Berat (kg)</th>
                        <th>Total Harga (Rp)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($result_sampah, 0); // Reset pointer to loop again for display
                    while ($sampah = mysqli_fetch_assoc($result_sampah)): ?>
                    <tr>
                        <td><?= $sampah['nama_jenis'] ?></td>
                        <td><?= number_format($sampah['berat'], 2, ',', '.') ?></td>
                        <td><?= number_format($sampah['total_harga'], 2, ',', '.') ?></td>
                        <td><?= $sampah['status'] ?></td>
                        <td>
                            <form action="dashboard.php" method="POST" class="d-inline">
                                <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                                <button type="submit" name="action" value="hapus" class="btn btn-danger mt-2">Hapus</button>
                            </form>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="2" class="text-right"><strong>Total Harga (Rp):</strong></td>
                        <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada sampah yang siap untuk di-pickup.</p>
        <?php endif; ?>

        <a href="page.php?mod=jual" class="btn btn-primary mt-4">Jual Sampah</a>

        <!-- History Transaksi Sampah -->
        <h4 class="mt-5">History Transaksi Sampah</h4>
        <?php if (mysqli_num_rows($result_history) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Jenis Sampah</th>
                        <th>Berat (kg)</th>
                        <th>Total Harga (Rp)</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($result_history, 0); // Reset pointer to loop again for display
                    while ($history = mysqli_fetch_assoc($result_history)): ?>
                    <tr>
                        <td><?= $history['nama_jenis'] ?></td>
                        <td><?= number_format($history['berat'], 2, ',', '.') ?></td>
                        <td><?= number_format($history['total_harga'], 2, ',', '.') ?></td>
                        <td><?= $history['status'] ?></td>
                        <td><?= $history['created_at'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="2" class="text-right"><strong>Total Penjualan (Rp):</strong></td>
                        <td><strong><?= number_format($total_history, 2, ',', '.') ?></strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>           
        <?php else: ?>
            <p>Tidak ada history transaksi.</p>
        <?php endif; ?>

        <!-- Riwayat Pembayaran -->
        <div class="container mt-5">
            <h2>Riwayat Pembayaran</h2>
            <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Warung</th>
                            <th>Jumlah Pembayaran (Rp)</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                        <tr>
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
    </div>
</body>
</html>
