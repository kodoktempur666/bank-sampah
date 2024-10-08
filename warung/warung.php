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

// Menampilkan nama warung mitra
$query_nama = "SELECT nama_warung FROM warung_mitra WHERE id = '$id_warung'";
$result_nama = mysqli_query($conn, $query_nama);
$nama = mysqli_fetch_assoc($result_nama)['nama_warung'];

// Menampilkan saldo warung mitra
$query_saldo = "SELECT saldo FROM warung_mitra WHERE id = '$id_warung'";
$result_saldo = mysqli_query($conn, $query_saldo);
$saldo = mysqli_fetch_assoc($result_saldo)['saldo'];

// Ambil transaksi pending
$query_pending = "SELECT t.*, r.nama AS nama_pembayar 
                  FROM transaksi t 
                  JOIN rumah_tangga r ON t.id_rumah_tangga = r.id 
                  WHERE t.id_warung_mitra = '$id_warung' AND t.status = 'pending'";
$result_pending = mysqli_query($conn, $query_pending);

$query_penarikan = "SELECT tp.*, wm.nama_warung, wm.saldo AS saldo_warung
                    FROM transaksi_pencairan tp
                    JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                    WHERE tp.status = 'pending'";
$result_penarikan = mysqli_query($conn, $query_penarikan);

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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaksi_id = $_POST['transaksi_id'];

    // Ambil data transaksi
    $query_transaksi = "SELECT * FROM transaksi WHERE id = '$transaksi_id'";
    $result_transaksi = mysqli_query($conn, $query_transaksi);
    $transaksi = mysqli_fetch_assoc($result_transaksi);

    if ($transaksi) {
        $id_rumah_tangga = $transaksi['id_rumah_tangga'];
        $id_warung_mitra = $transaksi['id_warung_mitra'];
        $jumlah_pembayaran = $transaksi['jumlah_pembayaran'];

        // Mulai transaksi database
        mysqli_begin_transaction($conn);

        try {
            // Ambil saldo rumah tangga
            $query_rumah_tangga = "SELECT saldo FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
            $result_rumah_tangga = mysqli_query($conn, $query_rumah_tangga);
            $rumah_tangga = mysqli_fetch_assoc($result_rumah_tangga);

            // Perbarui saldo rumah tangga
            $saldo_baru_rumah_tangga = $rumah_tangga['saldo'] - $jumlah_pembayaran;
            $update_rumah_tangga = "UPDATE rumah_tangga SET saldo = '$saldo_baru_rumah_tangga' WHERE id = '$id_rumah_tangga'";
            mysqli_query($conn, $update_rumah_tangga);

            // Ambil saldo warung mitra
            $query_warung_mitra = "SELECT saldo FROM warung_mitra WHERE id = '$id_warung_mitra'";
            $result_warung_mitra = mysqli_query($conn, $query_warung_mitra);
            $warung_mitra = mysqli_fetch_assoc($result_warung_mitra);

            // Perbarui saldo warung mitra
            $saldo_baru_warung_mitra = $warung_mitra['saldo'] + $jumlah_pembayaran;
            $update_warung_mitra = "UPDATE warung_mitra SET saldo = '$saldo_baru_warung_mitra' WHERE id = '$id_warung_mitra'";
            mysqli_query($conn, $update_warung_mitra);

            // Ubah status transaksi menjadi selesai
            $update_transaksi = "UPDATE transaksi SET status = 'selesai' WHERE id = '$transaksi_id'";
            mysqli_query($conn, $update_transaksi);

            // Commit transaksi
            mysqli_commit($conn);
            echo "Transaksi berhasil diproses.";
            echo "<script>alert('Transaksi berhasil diproses.'); window.location.href='page.php?mod=warung';</script>";;
        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            mysqli_rollback($conn);
            echo "Terjadi kesalahan: " . $e->getMessage();
            echo "<script>alert('Terjadi kesalahan: '); window.location.href='page.php?mod=warung';</script>";$e->getMessage();
        }
    } else {
        echo "Transaksi tidak ditemukan.";
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {

         // Proses untuk menghapus transaksi
        $hapus_id = $_POST['hapus_id'];
        $query_hapus = "DELETE FROM transaksi WHERE id = '$hapus_id'";
        mysqli_query($conn, $query_hapus);
        // Redirect atau tampilkan pesan sukses
        echo "<script>alert('Transaksi berhasil dihapus.'); window.location.href='page.php?mod=warung';</script>";
    
}
      
}


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
        <p class="text-center">Selamat datang, <?=$nama?></p>
        
        <h3>Saldo Anda: <span style="color: #27ae60;">Rp. <?= number_format($saldo, 2, ',', '.') ?></span></h3>
        <a href="page.php?mod=pencairan" class="btn btn-warning mt-3">Cairkan Saldo</a>
    </div>
    <div class="container mt-5">
        <h2>Pembayaran Pending</h2>

        <?php if (mysqli_num_rows($result_pending) > 0): ?>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nama Pembayar</th>
                        <th>Jumlah Pembayaran (Rp)</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pending = mysqli_fetch_assoc($result_pending)): ?>
                        <tr>
                            <td><?= $pending['nama_pembayar'] ?></td>
                            <td><?= number_format($pending['jumlah_pembayaran'], 2, ',', '.') ?></td>
                            <td><?= $pending['keterangan'] ?></td>
                            <td><?= date('d-m-Y', strtotime($pending['tanggal'])) ?></td>
                            <td>
                                <form method="POST" > <!-- Halaman untuk memperbarui status -->
                                    <input type="hidden" name="transaksi_id" value="<?= $pending['id'] ?>">
                                    <button type="submit" class="btn btn-success">Selesai</button>
                                </form>
                                <form method="POST"> <!-- Halaman untuk menghapus transaksi -->
                                    <input type="hidden" name="hapus_id" value="<?= $pending['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada pembayaran pending.</p>
        <?php endif; ?>
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
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                        <tr>
                            <td><?= $riwayat['nama_pembayar'] ?></td>
                            <td><?= $riwayat['nama_warung'] ?></td>
                            <td><?= number_format($riwayat['jumlah_pembayaran'], 2, ',', '.') ?></td>
                            <td><?= $riwayat['keterangan']?></td>
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
    <h2 class="mt-4">Penarikan Pending</h2>
        <?php if (mysqli_num_rows($result_penarikan) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Jumlah (Rp)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($penarikan = mysqli_fetch_assoc($result_penarikan)): ?>
                    <tr>
                        <td><?= $penarikan['nama_warung'] ?></td>
                        <td><?= number_format($penarikan['jumlah'], 2, ',', '.') ?></td>
                        <td><?= $penarikan['status'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada penarikan yang pending.</p>
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
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($riwayat_penarikan = mysqli_fetch_assoc($result_riwayat_penarikan)): ?>
                        <tr>
                            <td><?= $riwayat_penarikan['nama_warung'] ?></td>
                            <td><?= number_format($riwayat_penarikan['jumlah'], 2, ',', '.') ?></td>

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
