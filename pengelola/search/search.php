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

// Proses pencarian rumah tangga berdasarkan nama
$search_query = "";
if (isset($_POST['search']) || isset($_GET['search'])) {
    $search_query = isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
}

// Ambil data sampah yang siap untuk di-pickup, sesuai dengan pencarian rumah tangga
$query_sampah = "SELECT s.*, r.nama, r.rw, r.alamat, r.kontak, js.nama_jenis AS jenis_sampah 
                 FROM sampah s 
                 JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                 JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                 WHERE s.status = 'siap hitung' AND r.nama LIKE '%$search_query%'";
$query_sampah .= " ORDER BY r.rw, r.nama"; // Urutkan berdasarkan RW dan nama rumah tangga
$result_sampah = mysqli_query($conn, $query_sampah);

// Handle POST request untuk update atau hapus sampah
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_sampah'])) {
    $id_sampah = $_POST['id_sampah'];

    // Jika tombol hapus diklik
    if (isset($_POST['action']) && $_POST['action'] == 'hapus') {
        // Query untuk menghapus sampah berdasarkan ID
        $query_delete = "DELETE FROM sampah WHERE id = '$id_sampah'";
        $result_delete = mysqli_query($conn, $query_delete);

        // Cek apakah query berhasil dijalankan
        if ($result_delete) {
            // Redirect ke halaman search setelah penghapusan berhasil
            header("Location: page.php?mod=search&search=" . urlencode($search_query));
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

        // Redirect ke halaman search setelah selesai
        header("Location: page.php?mod=search&search=" . urlencode($search_query));
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahan CSS agar lebih responsif dan profesional */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        h1,
        h4,
        h5 {
            color: #343a40;
        }

        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn {
            font-size: 0.9rem;
        }

        /* Tabel berformat grid saat di mode mobile */
        @media (max-width: 768px) {
            .table {
                display: block;
                width: 100%;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: flex;
                flex-direction: column;
                border: 1px solid #dee2e6;
                margin-bottom: 10px;
                padding: 10px;
                border-radius: 8px;
            }

            .table tbody tr td {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                font-size: 0.9rem;
            }

            .table tbody tr td:before {
                content: attr(data-label);
                font-weight: bold;
                color: #343a40;
            }
        }
    </style>
</head>

<body>

    <?php include 'assets/components/headerpeng.php'; ?>

    <div class="container mt-5 table-container">
        <h1 class="text-center">Pengelola Sampah Desa Salem</h1>

        <!-- Form Pencarian -->
        <form onsubmit="redirectToSearch(event)" class="mb-4">
            <div class="form-group row">
                <label for="search" class="col-sm-2 col-form-label">Cari Rumah Tangga:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="Masukkan Nama Rumah Tangga" value="<?= htmlspecialchars($search_query) ?>">
                </div>
                <div class="col-sm-2">
                    <button type="button" onclick="redirectToSearch(event)" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <script>
            function redirectToSearch(event) {
                event.preventDefault();
                const query = document.getElementById("search").value;
                if (query) {
                    window.location.href = `page.php?mod=search&search=${encodeURIComponent(query)}`;
                }
            }
        </script>

        <!-- Bagian Sampah Siap Pickup -->
        <h4 class="mt-4">Order Sampah</h4>

        <?php if (mysqli_num_rows($result_sampah) > 0): ?>
            <?php
            $current_rw = null;
            $current_household = null;
            $total_pickup = 0;

            while ($sampah = mysqli_fetch_assoc($result_sampah)):
                if ($current_rw !== $sampah['rw']): ?>
                    <?php if ($current_rw !== null): ?>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                            <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                        </tbody>
                        </table>
                    <?php endif; ?>
                    <h4 class="mt-4">RW: <?= $sampah['rw'] ?></h4>
                    <?php
                    $current_rw = $sampah['rw'];
                    $current_household = null;
                    $total_pickup = 0;
                endif;

                if ($current_household !== $sampah['nama']): ?>
                    <?php if ($current_household !== null): ?>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                            <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                        </tbody>
                        </table>
                    <?php endif;
                    $current_household = $sampah['nama'];
                    $total_pickup = 0; ?>

                    <h5 class="mt-4">Rumah Tangga: <?= $sampah['nama'] ?></h5>
                    <p>Alamat: <?= $sampah['alamat'] ?></p>
                    <p>RW: <?= $sampah['rw'] ?></p>
                    <p>Kontak: <?= $sampah['kontak'] ?></p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jenis Sampah</th>
                                <th>Berat (kg)</th>
                                <th>Total Harga (Rp)</th>
                                <th>Status</th>
                                <th>Pembayaran Pengelola</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php endif; ?>
                        <tr>
                            <td data-label="Jenis Sampah"><?= $sampah['jenis_sampah'] ?></td>
                            <td data-label="Berat (kg)"><?= number_format($sampah['berat'], 2, ',', '.') ?></td>
                            <td data-label="Total Harga (Rp)"><?= number_format($sampah['total_harga'], 2, ',', '.') ?></td>
                            <td data-label="Status"><?= $sampah['status'] ?></td>
                            <td data-label="Pembayaran Pengelola"><?= $sampah['confirmed_by_pengelola'] ?></td>
                            <td data-label="Aksi">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                                    <button type="submit" name="status" value="selesai" class="btn btn-success mt-2"
                                        <?= ($sampah['confirmed_by_pengelola'] == 'diterima') ? 'disabled' : '' ?>>Selesai</button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                                    <button type="submit" name="action" value="hapus" class="btn btn-danger mt-2">Hapus</button>
                                </form>
                                <a href="page.php?mod=edit2&id=<?= $sampah['id'] ?>&search=<?= urlencode($search_query) ?>" class="btn btn-info mt-2">Hitung Sampah</a>
                            </td>
                        </tr>
                        <?php $total_pickup += $sampah['total_harga']; ?>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total Harga (Rp):</strong></td>
                        <td><strong><?= number_format($total_pickup, 2, ',', '.') ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Tidak ada order sampah yang sesuai dengan pencarian.</p>
        <?php endif; ?>
    </div>
</body>

</html>