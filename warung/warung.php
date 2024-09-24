<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

if ($_SESSION['user']['role'] == 'rumah_tangga') {
    header("Location: page.php?mod=unaut");
    exit();
}

// Daftar peran yang tidak diizinkan
$not_allowed_roles = ['admin', 'pengelola'];
if (in_array($_SESSION['user']['role'], $not_allowed_roles)) {
    // Jika pengguna memiliki salah satu dari peran yang tidak diizinkan, redirect mereka
    header("Location: page.php?mod=unaut2");
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
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for minimal and elegant design -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        h1, h2 {
            font-weight: bold;
            color: #222;
            margin-bottom: 20px;
        }

        h3 {
            font-weight: 500;
            color: #444;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-warning {
            background-color: #ffbb33;
            border-color: #ffbb33;
            font-weight: 600;
        }

        .btn-warning:hover {
            background-color: #ff8800;
            border-color: #ff8800;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table thead th {
            border-bottom: 2px solid #ddd;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table th {
            color: #555;
        }

        .table td {
            color: #666;
        }

        .text-center {
            margin-bottom: 30px;
        }

        footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9rem;
            color: #aaa;
        }
    </style>
</head>

<body>
    <?php include 'assets/components/headerwarung.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Warung Mitra Dashboard</h1>
        <p class="text-center">Selamat datang, warung mitra!</p>
        <h3>Saldo Anda: <span style="color: #27ae60;">Rp. <?= number_format($saldo, 2, ',', '.') ?></span></h3>
        <a href="page.php?mod=pencairan" class="btn btn-warning mt-3">Cairkan Saldo</a>
    </div>

    <div class="container mt-5">
        <h2>Riwayat Pembayaran</h2>
        <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nama Pembayar</th>
                        <th>Nama Warung</th>
                        <th>Jumlah Pembayaran (Rp)</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                        <tr>
                            <td><?= $riwayat['nama_pembayar'] ?></td>
                            <td><?= $riwayat['nama_warung'] ?></td>
                            <td><?= number_format($riwayat['jumlah_pembayaran'], 2, ',', '.') ?></td>
                            <td><?= date('d-m-Y', strtotime($riwayat['tanggal'])) ?></td>
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
            <table class="table table-striped mt-3">
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
                    <?php while ($riwayat_penarikan = mysqli_fetch_assoc($result_riwayat_penarikan)): ?>
                        <tr>
                            <td><?= $riwayat_penarikan['nama_warung'] ?></td>
                            <td><?= number_format($riwayat_penarikan['jumlah'], 2, ',', '.') ?></td>
                            <td><?= $riwayat_penarikan['tipe_penarikan'] ?></td>
                            <td><?= $riwayat_penarikan['no_rekening'] ?></td>
                            <td><?= $riwayat_penarikan['bank'] ?></td>
                            <td><?= $riwayat_penarikan['status'] ?></td>
                            <td><?= date('d-m-Y', strtotime($riwayat_penarikan['tanggal'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada riwayat penarikan.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Warung Mitra. All rights reserved.</p>
    </footer>

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/0b79c15f2d.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
</body>

</html>
