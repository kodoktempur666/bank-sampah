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



// Ambil transaksi penarikan yang pending
$query_penarikan = "SELECT tp.*, wm.nama_warung, wm.saldo AS saldo_warung
                    FROM transaksi_pencairan tp
                    JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                    WHERE tp.status = 'pending'";
$result_penarikan = mysqli_query($conn, $query_penarikan);

// Memproses penarikan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_penarikan = $_POST['id_penarikan'];
    $status = $_POST['status'];

    // Ambil data penarikan
    $query_detail = "SELECT tp.*, wm.saldo AS saldo_warung FROM transaksi_pencairan tp 
                     JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                     WHERE tp.id = '$id_penarikan'";
    $result_detail = mysqli_query($conn, $query_detail);
    $penarikan = mysqli_fetch_assoc($result_detail);

    if ($status == 'selesai' && $penarikan['jumlah'] <= $penarikan['saldo_warung']) {
        $jumlah_penarikan = $penarikan['jumlah'];

        // Kurangi saldo warung mitra
        $query_update_warung = "UPDATE warung_mitra SET saldo = saldo - $jumlah_penarikan WHERE id = '{$penarikan['id_warung_mitra']}'";
        mysqli_query($conn, $query_update_warung);


        // Ubah status penarikan menjadi selesai
        $query_update_penarikan = "UPDATE transaksi_pencairan SET status = 'selesai' WHERE id = '$id_penarikan'";
        mysqli_query($conn, $query_update_penarikan);

        // Salin data penarikan ke tabel riwayat_penarikan
        $query_insert_riwayat = "INSERT INTO riwayat_penarikan (id_warung_mitra, jumlah, status)
                                SELECT id_warung_mitra, jumlah, 'selesai'
                                FROM transaksi_pencairan
                                WHERE id = '$id_penarikan'";
        mysqli_query($conn, $query_insert_riwayat);

        // Hapus data dari tabel transaksi_pencairan
        $query_delete_penarikan = "DELETE FROM transaksi_pencairan WHERE id = '$id_penarikan'";
        mysqli_query($conn, $query_delete_penarikan);

        echo "<script>alert('Pencairan Saldo selesai'); window.location.href='page.php?mod=data-penarikan';</script>";;
    } else {
        echo "<script>alert('Saldo mitra tidak cukup'); window.location.href='page.php?mod=data-penarikan';</script>";;
    }
}

// Ambil riwayat penarikan
$query_riwayat = "SELECT rp.*, wm.nama_warung
                  FROM riwayat_penarikan rp
                  JOIN warung_mitra wm ON rp.id_warung_mitra = wm.id
                  ORDER BY rp.tanggal DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelola - Proses Penarikan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'assets/components/headerpeng.php'; ?>
    <div class="container mt-5">
        <h1>Proses Penarikan Saldo</h1>



        <h4 class="mt-4">Penarikan Pending</h4>
        <?php if (mysqli_num_rows($result_penarikan) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Jumlah (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($penarikan = mysqli_fetch_assoc($result_penarikan)): ?>
                    <tr>
                        <td><?= $penarikan['nama_warung'] ?></td>
                        <td><?= number_format($penarikan['jumlah'], 2, ',', '.') ?></td>

                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_penarikan" value="<?= $penarikan['id'] ?>">
                                <button type="submit" name="status" value="selesai" class="btn btn-success">Selesai</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada penarikan yang pending.</p>
        <?php endif; ?>

        <h4 class="mt-4">Riwayat Penarikan</h4>
        <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Jumlah (Rp)</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                    <tr>
                        <td><?= $riwayat['nama_warung'] ?></td>
                        <td><?= number_format($riwayat['jumlah'], 2, ',', '.') ?></td>
                        <td><?= $riwayat['status'] ?></td>
                        <td><?= $riwayat['tanggal'] ?></td>
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
