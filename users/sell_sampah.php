<?php
session_start();
require 'config/connect.php';

// if (!isset($_SESSION['user'])) {
//     header("Location: page.php?mod=home");
//     exit();
// }

// // Periksa apakah pengguna adalah pengelola
// if ($_SESSION['user']['role'] !== 'rumah_tangga') {
//     // Jika bukan pengelola, redirect ke halaman unauthorized
//     header("Location: page.php?mod=unaut2");
//     exit();
// }


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jenis_sampah = $_POST['id_jenis_sampah'];  // ID Jenis Sampah
    $berat_sampah = $_POST['berat_sampah'];        // Berat Sampah
    $id_rumah_tangga = $_SESSION['user']['id'];

    // Ambil harga per kg dari tabel jenis_sampah berdasarkan id_jenis_sampah
    $query = "SELECT harga_per_kg FROM jenis_sampah WHERE id = $id_jenis_sampah";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $harga_per_kg = $row['harga_per_kg'];

    // Hitung total harga berdasarkan berat sampah
    $total_harga = $berat_sampah * $harga_per_kg;

    // Simpan data ke tabel sampah
    $query_insert = "INSERT INTO sampah (id_rumah_tangga, id_jenis_sampah, berat, total_harga) 
                 VALUES ('$id_rumah_tangga', '$id_jenis_sampah', '$berat_sampah', '$total_harga')";
    mysqli_query($conn, $query_insert);


    // Redirect ke halaman sukses atau dashboard
    header("Location: page.php?mod=riwayat");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jual Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .header-text {
            color: #007bff;
            font-weight: bold;
        }
        .icon {
            font-size: 1.5rem;
            color: #007bff;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include 'assets/components/header.php'; ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center header-text">
                            <i class="icon fas fa-recycle"></i> Jual Sampah
                        </h2>
                        <p class="text-center text-muted">Jual sampah Anda dan bantu lingkungan!</p>
                        <form method="POST">
                            <div class="form-group">
                                <label for="id_jenis_sampah">Jenis Sampah</label>
                                <select name="id_jenis_sampah" class="form-control" required>
                                    <?php
                                    $query = "SELECT * FROM jenis_sampah";
                                    $result = mysqli_query($conn, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='{$row['id']}'>{$row['nama_jenis']} - Rp" . number_format($row['harga_per_kg'], 2, ',', '.') . " per Kg</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                            <div class="form-group">
                                <label  for="berat_sampah"></label>
                                <input type="hidden" name="berat_sampah" step="0.01" class="form-control" value="0" disabled>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Jual Sampah</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
