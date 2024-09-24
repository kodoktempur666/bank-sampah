<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Daftar peran yang tidak diizinkan
$not_allowed_roles = ['admin', 'warung_mitra', 'pengelola'];
if (in_array($_SESSION['user']['role'], $not_allowed_roles)) {
    // Jika pengguna memiliki salah satu dari peran yang tidak diizinkan, redirect mereka
    header("Location: page.php?mod=unaut2");
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
    header("Location: page.php?mod=riwayat");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        h1, h2, h3, h4 {
            font-weight: 600;
        }

        .container {
            margin-top: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .modal-content {
            border-radius: 10px;
            padding: 20px;
        }

        .no-data {
            color: #6c757d;
            text-align: center;
            margin-top: 20px;
        }

        .no-data i {
            font-size: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'assets/components/header.php'; ?>

    <div class="container">
        <h1 class="text-center">Dashboard Rumah Tangga</h1>
        <div class="card p-4">
            <h3>Saldo: Rp. <?= number_format($current_saldo, 2, ',', '.') ?></h3>
            <a href="?mod=pembayaran" class="btn btn-primary mt-4">Bayar</a>
        </div>

        <!-- Sampah Siap Pick-Up -->
        <div class="card p-4">
            <h4>Sampah Siap Pick-Up</h4>
            <?php if (mysqli_num_rows($result_sampah) > 0): ?>
                <table class="table table-striped">
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
                        mysqli_data_seek($result_sampah, 0);
                        while ($sampah = mysqli_fetch_assoc($result_sampah)): ?>
                            <tr>
                                <td><?= $sampah['nama_jenis'] ?></td>
                                <td><?= number_format($sampah['berat'], 2, ',', '.') ?></td>
                                <td><?= number_format($sampah['total_harga'], 2, ',', '.') ?></td>
                                <td><?= $sampah['status'] ?></td>
                                <td>
                                    <!-- Button to trigger modal for delete confirmation -->
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $sampah['id'] ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="2" class="text-end"><strong>Total Harga (Rp):</strong></td>
                            <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-trash-alt"></i>
                    <p>Tidak ada sampah yang siap untuk di-pickup.</p>
                </div>
            <?php endif; ?>
            <a href="page.php?mod=jual" class="btn btn-primary mt-4">Jual Sampah</a>
        </div>

        <!-- History Transaksi Sampah -->
        <div class="card p-4">
            <h4>History Transaksi Sampah</h4>
            <?php if (mysqli_num_rows($result_history) > 0): ?>
                <table class="table table-striped">
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
                        mysqli_data_seek($result_history, 0);
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
                            <td colspan="2" class="text-end"><strong>Total Penjualan (Rp):</strong></td>
                            <td><strong><?= number_format($total_history, 2, ',', '.') ?></strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-history"></i>
                    <p>Tidak ada history transaksi.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="card p-4">
            <h4>Riwayat Pembayaran</h4>
            <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Warung</th>
                            <th>Jumlah Pembayaran (Rp)</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                            <tr>
                                <td><?= $riwayat['nama_warung'] ?></td>
                                <td><?= number_format($riwayat['jumlah_pembayaran'], 2, ',', '.') ?></td>
                                <td><?= $riwayat['tanggal'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-receipt"></i>
                    <p>Tidak ada riwayat pembayaran.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Konfirmasi Penghapusan -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus sampah ini?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="page.php?mod=riwayat" method="POST">
                        <input type="hidden" name="id_sampah" id="id_sampah">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="action" value="hapus" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script for delete confirmation modal -->
    <script>
        // Function to trigger modal and set the id of sampah to be deleted
        function confirmDelete(id) {
            document.getElementById('id_sampah').value = id;  // Set the id in the hidden input
            var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));  // Initialize Bootstrap Modal
            myModal.show();  // Show the modal
        }
    </script>
</body>

</html>