<?php
session_start();
require 'config/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Verify if the user is an admin (pengelola)
if ($_SESSION['user']['role'] !== 'pengelola') {
    header("Location: page.php?mod=unaut2");
    exit();
}

$id_pengelola = $_SESSION['user']['id'];

// Check for search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query to include search functionality
$query_history = "SELECT s.*, r.nama, r.alamat, r.rw, r.kontak, js.nama_jenis AS jenis_sampah 
                  FROM sampah s 
                  JOIN rumah_tangga r ON s.id_rumah_tangga = r.id 
                  JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                  WHERE s.status = 'selesai'";

if ($search_query) {
    $query_history .= " AND r.nama LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%'";
}

$query_history .= " ORDER BY s.id DESC";
$result_history = mysqli_query($conn, $query_history);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelola Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        h1, h4 {
            color: #343a40;
        }

        .card {
            margin-top: 1.5rem;
        }

        .table thead th {
            background-color: #00984a;
            color: #ffffff;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .table .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .section-title {
            color: #6c757d;
            font-weight: bold;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>

<?php include 'assets/components/headerpeng.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Pengelola Sampah Desa Salem</h1>

    <!-- Search Form -->
    <form class="form-inline my-4" method="GET" action="">
        <input type="hidden" name="mod" value="history">
        <input type="text" class="form-control mr-2" name="search" placeholder="Cari Rumah Tangga" value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <!-- Transaction History Section -->
    <h4 class="mt-5 section-title">History Transaksi</h4>

    <?php if (mysqli_num_rows($result_history) > 0): ?>
        <?php
        $current_household = null;
        $total_history = 0;
        while ($history = mysqli_fetch_assoc($result_history)):
            // Check if it's a new household section
            if ($current_household !== $history['nama']):
                // If we're closing the previous household's table, display the total row
                if ($current_household !== null): ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-right">Total Harga (Rp):</td>
                        <td><?= number_format($total_history, 2, ',', '.') ?></td>
                        <td colspan="2"></td>
                    </tr>
                    </tbody>
                    </table>
                <?php endif;

                // Reset for new household
                $current_household = $history['nama'];
                $total_history = 0; ?>

                <!-- Household Information -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Rumah Tangga: <?= $history['nama'] ?></h5>
                        <p class="card-text">Alamat: <?= $history['alamat'] ?></p>
                        <p class="card-text">RW: <?= $history['rw'] ?></p>
                        <p class="card-text">Kontak: <?= $history['kontak'] ?></p>
                    </div>
                </div>

                <!-- Transaction Table for Household -->
                <table class="table mt-3">
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

            <!-- Transaction Row -->
            <tr>
                <td><?= $history['jenis_sampah'] ?></td>
                <td><?= number_format($history['berat'], 2, ',', '.') ?></td>
                <td><?= number_format($history['total_harga'], 2, ',', '.') ?></td>
                <td><?= ucfirst($history['status']) ?></td>
                <td><?= date('d-m-Y', strtotime($history['created_at'])) ?></td>
            </tr>

            <?php
            // Accumulate total price for current household
            $total_history += $history['total_harga'];
        endwhile;
        ?>

        <!-- Final Total Row for Last Household -->
        <tr class="total-row">
            <td colspan="2" class="text-right">Total Harga (Rp):</td>
            <td><?= number_format($total_history, 2, ',', '.') ?></td>
            <td colspan="2"></td>
        </tr>
        </tbody>
        </table>

    <?php else: ?>
        <p class="text-muted text-center mt-4">Tidak ada history transaksi yang sesuai dengan pencarian.</p>
    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
