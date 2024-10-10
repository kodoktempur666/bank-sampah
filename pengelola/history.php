<?php
session_start();
require 'config/connect.php';

// Cek apakah pengelola sudah login
// if (!isset($_SESSION['user'])) {
//     header("Location: page.php?mod=home");
//     exit();
// }

// // Periksa apakah pengguna adalah pengelola
// if ($_SESSION['user']['role'] !== 'pengelola') {
//     // Jika bukan pengelola, redirect ke halaman unauthorized
//     header("Location: page.php?mod=unaut2");
//     exit();
// }

$id_pengelola = $_SESSION['user']['id'];

// Ambil semua history sampah
$query_history = "SELECT s.*, r.nama, r.alamat, r.rw, r.kontak, js.nama_jenis AS jenis_sampah 
                  FROM sampah s 
                  JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                  JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                  WHERE s.status = 'selesai'
                  ORDER BY s.id DESC";
$result_history = mysqli_query($conn, $query_history);


?>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelola Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include 'assets/components/headerpeng.php'; ?>
    <div class="container mt-5"></div>
    <h1 class="text-center">Pengelola Sampah Desa Salem</h1>
    <!-- Bagian History Transaksi -->
    <h4 class="mt-4">History Transaksi</h4>
    <?php if (mysqli_num_rows($result_history) > 0): ?>
        <?php
        $current_household = null;
        $total_history = 0;
        while ($history = mysqli_fetch_assoc($result_history)):
            if ($current_household !== $history['nama']):
                if ($current_household !== null): ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                        <td><strong><?= number_format($total_history, 2, ',', '.') ?></strong></td>
                        <td></td>
                    </tr>
                    </tbody>
                    </table>
                <?php endif;
                $current_household = $history['nama'];
                $total_history = 0; ?>
                <h5 class="mt-4">Rumah Tangga: <?= $history['nama'] ?></h5>
                <p>Alamat: <?= $history['alamat'] ?></p>
                <p>RW: <?= $history['rw'] ?></p>
                <p>Kontak: <?= $history['kontak'] ?></p>
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
                    <?php endif; ?>
                    <tr>
                        <td><?= $history['jenis_sampah'] ?></td>
                        <td><?= number_format($history['berat'], 2, ',', '.') ?></td>
                        <td><?= number_format($history['total_harga'], 2, ',', '.') ?></td>
                        <td><?= $history['status'] ?></td>
                        <td><?= $history['created_at'] ?></td>
                    </tr>
                    <?php
                    $total_history += $history['total_harga'];
        endwhile;
        ?>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                    <td><strong><?= number_format($total_history, 2, ',', '.') ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada history transaksi.</p>
    <?php endif; ?>
</body>

</html>