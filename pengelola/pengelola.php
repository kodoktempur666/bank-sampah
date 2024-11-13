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

// Ambil data sampah yang siap untuk di-pickup
$query_sampah = "SELECT s.*, r.nama, r.rw, r.alamat, r.kontak, js.nama_jenis AS jenis_sampah 
                 FROM sampah s 
                 JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                 JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                 WHERE s.status = 'siap hitung'";
$query_sampah .= " ORDER BY r.rw, r.nama"; // Urutkan berdasarkan RW dan nama rumah tangga
$result_sampah = mysqli_query($conn, $query_sampah);

// Ambil semua history sampah
$query_history = "SELECT s.*, r.nama, r.alamat, r.kontak, js.nama_jenis AS jenis_sampah 
                  FROM sampah s 
                  JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                  JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                  WHERE s.status = 'selesai'
                  ORDER BY s.id DESC";
$result_history = mysqli_query($conn, $query_history);

// Ambil total harga per rumah tangga
$query_total_sampah_pickup = "SELECT r.nama, SUM(s.total_harga) AS total_harga
                              FROM sampah s
                              JOIN rumah_tangga r ON s.id_rumah_tangga = r.id
                              WHERE s.status = 'menunggu_pickup'
                              GROUP BY r.id";
$result_total_sampah_pickup = mysqli_query($conn, $query_total_sampah_pickup);

// Query untuk total harga sampah selesai
$query_total_sampah_selesai = "SELECT r.nama, SUM(s.total_harga) AS total_harga
                               FROM sampah s
                               JOIN rumah_tangga r ON s.id_rumah_tangga = r.id
                               WHERE s.status = 'selesai'
                               GROUP BY r.id";
$result_total_sampah_selesai = mysqli_query($conn, $query_total_sampah_selesai);

// Simpan total harga per rumah tangga dalam array
$totals_by_household = [];
while ($row = mysqli_fetch_assoc($result_total_sampah_pickup)) {
    $totals_by_household[$row['nama']] = $row['total_harga'];
}

$totals_by_household1 = [];
while ($row = mysqli_fetch_assoc($result_total_sampah_selesai)) {
    $totals_by_household1[$row['nama']] = $row['total_harga'];
}

// Handle POST request untuk update atau hapus sampah
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_sampah = $_POST['id_sampah'];

    // Jika tombol hapus diklik
    if (isset($_POST['action']) && $_POST['action'] == 'hapus') {
        // Query untuk menghapus sampah berdasarkan ID
        $query_delete = "DELETE FROM sampah WHERE id = '$id_sampah'";
        $result_delete = mysqli_query($conn, $query_delete);

        // Cek apakah query berhasil dijalankan
        if ($result_delete) {
            // Redirect ke halaman pengelola setelah penghapusan berhasil
            header("Location: page.php?mod=pengelola");
            exit();
        } else {
            echo "Gagal menghapus sampah. Silakan coba lagi.";
        }
    }

    // Jika tombol selesai diklik
    if (isset($_POST['status']) && $_POST['status'] == 'selesai') {
        $confirmed_by_pengelola = $_POST['confirmed_by_pengelola'];

        // Update status sampah
        $query_update_pengelola = "UPDATE sampah SET confirmed_by_pengelola = 'diterima' WHERE id = '$id_sampah'";
        mysqli_query($conn, $query_update_pengelola);

        // Redirect ke halaman pengelola setelah selesai
        header("Location: page.php?mod=pengelola");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelola Sampah</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include 'assets/components/headerpeng.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Pengelola Sampah Desa Salem</h1>

        <?php if (mysqli_num_rows($result_sampah) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php while ($sampah = mysqli_fetch_assoc($result_sampah)): ?>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">RW: <?= $sampah['rw'] ?></h5>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2"><i class="fas fa-home"></i> Rumah Tangga: <?= $sampah['nama'] ?></h6>
                                <p class="card-text"><i class="fas fa-map-marker-alt"></i> Alamat: <?= $sampah['alamat'] ?></p>
                                <p class="card-text"><i class="fas fa-phone-alt"></i> Kontak: <?= $sampah['kontak'] ?></p>
                                <hr>
                                <p><strong>Jenis Sampah:</strong> <?= $sampah['jenis_sampah'] ?></p>
                                <p><strong>Berat:</strong> <?= number_format($sampah['berat'], 2, ',', '.') ?> kg</p>
                                <p><strong>Total Harga:</strong> Rp <?= number_format($sampah['total_harga'], 2, ',', '.') ?></p>
                                <p><strong>Status:</strong> <?= $sampah['status'] ?></p>
                                <p><strong>Pembayaran Pengelola:</strong> <?= $sampah['confirmed_by_pengelola'] ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <!-- Button to trigger 'Selesai' modal -->
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#selesaiModal<?= $sampah['id'] ?>">
                                    <i class="fas fa-check"></i> Selesai
                                </button>
                                
                                <!-- Button to trigger 'Hapus' modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $sampah['id'] ?>">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>

                                <!-- Link to edit waste details -->
                                <a href="page.php?mod=edit&id=<?= $sampah['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i> Hitung Sampah
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for 'Selesai' Confirmation -->
                    <div class="modal fade" id="selesaiModal<?= $sampah['id'] ?>" tabindex="-1" aria-labelledby="selesaiModalLabel<?= $sampah['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="selesaiModalLabel<?= $sampah['id'] ?>">Konfirmasi Selesai</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menandai sampah ini sebagai selesai?
                                </div>
                                <div class="modal-footer">
                                    <form method="POST">
                                        <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                                        <button type="submit" name="status" value="selesai" class="btn btn-success">Ya, Selesai</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for 'Hapus' Confirmation -->
                    <div class="modal fade" id="hapusModal<?= $sampah['id'] ?>" tabindex="-1" aria-labelledby="hapusModalLabel<?= $sampah['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="hapusModalLabel<?= $sampah['id'] ?>">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus sampah ini?
                                </div>
                                <div class="modal-footer">
                                    <form method="POST">
                                        <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                                        <button type="submit" name="action" value="hapus" class="btn btn-danger">Ya, Hapus</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">Tidak ada order sampah yang siap diproses.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>