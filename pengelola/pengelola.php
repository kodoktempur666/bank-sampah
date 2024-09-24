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
$not_allowed_roles = ['admin', 'warung_mitra'];
if (in_array($_SESSION['user']['role'], $not_allowed_roles)) {
    // Jika pengguna memiliki salah satu dari peran yang tidak diizinkan, redirect mereka
    header("Location: page.php?mod=unaut2");
    exit();
}

$id_pengelola = $_SESSION['user']['id'];



// Ambil data sampah yang siap untuk di-pickup
$query_sampah = "SELECT s.*, r.nama, r.alamat, r.kontak, js.nama_jenis AS jenis_sampah 
                 FROM sampah s 
                 JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                 JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                 WHERE s.status = 'menunggu_pickup'";
$result_sampah = mysqli_query($conn, $query_sampah);

// Ambil semua history sampah
$query_history = "SELECT s.*, r.nama, r.alamat, r.kontak, js.nama_jenis AS jenis_sampah 
                  FROM sampah s 
                  JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                  JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                  WHERE s.status = 'selesai'
                  ORDER BY s.id DESC";
$result_history = mysqli_query($conn, $query_history);

// Ambil total harga per rumah tangga (baik untuk pickup maupun history)
$query_total_sampah_pickup = "SELECT r.nama, SUM(s.total_harga) AS total_harga
                              FROM sampah s
                              JOIN rumah_tangga r ON s.id_rumah_tangga = r.id
                              WHERE s.status = 'menunggu_pickup'
                              GROUP BY r.id";
$result_total_sampah_pickup = mysqli_query($conn, $query_total_sampah_pickup);
$totals_by_household = [];

$query_total_sampah_selesai = "SELECT r.nama, SUM(s.total_harga) AS total_harga
                               FROM sampah s
                               JOIN rumah_tangga r ON s.id_rumah_tangga = r.id
                               WHERE s.status = 'selesai'
                               GROUP BY r.id";
$result_total_sampah_selesai = mysqli_query($conn, $query_total_sampah_selesai);
$totals_by_household1 = [];

// Simpan total harga per rumah tangga dalam array
while ($row = mysqli_fetch_assoc($result_total_sampah_pickup)) {
    $totals_by_household[$row['nama']] = $row['total_harga'];
}

// Isi array dengan hasil query untuk sampah 'selesai'
while ($row = mysqli_fetch_assoc($result_total_sampah_selesai)) {
    $totals_by_household1[$row['nama']] = $row['total_harga'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_sampah = $_POST['id_sampah'];
    $status = $_POST['status'];

    // Ambil data sampah berdasarkan ID untuk menghitung saldo
    $query_sampah_detail = "SELECT * FROM sampah WHERE id = '$id_sampah'";
    $result_sampah_detail = mysqli_query($conn, $query_sampah_detail);
    $sampah = mysqli_fetch_assoc($result_sampah_detail);

    // Update status sampah
    $query_update_status = "UPDATE sampah SET status = '$status' WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_update_status);

    // Jika status diubah menjadi "selesai", tambahkan saldo rumah tangga
    if ($status == 'selesai') {
        $id_rumah_tangga = $sampah['id_rumah_tangga'];
        $total_harga = $sampah['total_harga'];

        // Update saldo rumah tangga
        $query_update_saldo = "UPDATE rumah_tangga SET saldo = saldo + $total_harga WHERE id = '$id_rumah_tangga'";
        mysqli_query($conn, $query_update_saldo);
    }

    header("Location: page.php?mod=pengelola");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelola Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Pengelola Sampah Desa Salem</h1>


        <a href="page.php?mod=data-penarikan" class="btn btn-warning mt-3">Data Penarikan</a>
        <a href="page.php?mod=edit-sampah" class="btn btn-warning mt-3">Edit Harga Sampah</a>
        <!-- Bagian Sampah Siap Pickup -->
        <h4 class="mt-4">Sampah Siap Pick-Up</h4>
        <?php if (mysqli_num_rows($result_sampah) > 0): ?>
            <?php 
            $current_household = null;
            $total_pickup = 0;
            while ($sampah = mysqli_fetch_assoc($result_sampah)): 
                if ($current_household !== $sampah['nama']):
                    if ($current_household !== null): ?>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                            <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                        </tbody></table>
                    <?php endif;
                    $current_household = $sampah['nama'];
                    $total_pickup = 0; ?>
                    <h5 class="mt-4">Rumah Tangga: <?= $sampah['nama'] ?></h5>
                    <p>Alamat: <?= $sampah['alamat'] ?></p>
                    <p>Kontak: <?= $sampah['kontak'] ?></p>
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
                <?php endif; ?>
                <tr>
                    <td><?= $sampah['jenis_sampah'] ?></td>
                    <td><?= number_format($sampah['berat'], 2, ',', '.') ?></td>
                    <td><?= number_format($sampah['total_harga'], 2, ',', '.') ?></td>
                    <td><?= $sampah['status'] ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                            <button value="selesai" name="status" class="btn btn-success mt-2" <?= ($sampah['status'] == 'selesai') ? 'disabled' : '' ?>>Selesai</button>
                        </form>
                        <form  method="POST" class="d-inline">
                            <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                            <button type="submit" name="action" value="hapus" class="btn btn-danger mt-2">Hapus</button>
                        </form>
                        <a href="page.php?mod=edit&id=<?= $sampah['id'] ?>" class="btn btn-warning mt-2">Hitung Sampah</a>
                    </td>
                </tr>
                <?php
                $total_pickup += $sampah['total_harga'];
            endwhile;
            ?>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                <td></td>
            </tr>
            </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada sampah yang siap untuk di-pickup.</p>
        <?php endif; ?>
        
        <!-- Bagian History Transaksi -->
        <h4 class="mt-5">History Transaksi</h4>
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
                        </tbody></table>
                    <?php endif;
                    $current_household = $history['nama'];
                    $total_history = 0; ?>
                    <h5 class="mt-4">Rumah Tangga: <?= $history['nama'] ?></h5>
                    <p>Alamat: <?= $history['alamat'] ?></p>
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
    </div>
</body>
</html>
