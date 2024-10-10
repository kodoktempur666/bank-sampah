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


// Ambil ID pengguna dari sesi
$id_rumah_tangga = $_SESSION['user']['id'];

// Ambil data pengguna dari database
$query = "SELECT * FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Data pengguna tidak ditemukan.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .header-text {
            color: #007bff;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
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
                            <i class="icon fas fa-user"></i> Profil Pengguna
                        </h2>
                        <p class="text-center text-muted">Informasi pribadi pengguna</p>

                        <!-- Tabel Profil Pengguna -->
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama</th>
                                <td><?php echo $user['nama']; ?></td>
                            </tr>
                            <tr>
                                <th>RW</th>
                                <td><?php echo $user['rw']; ?></td>
                            </tr>
                            <tr>
                                <th>Nomor Telepon</th>
                                <td><?php echo $user['kontak']; ?></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><?php echo $user['alamat']; ?></td>
                            </tr>
                            <tr>
                                <th>Saldo</th>
                                <td>Rp<?php echo number_format($user['saldo'], 2, ',', '.'); ?></td>
                            </tr>
                        </table>

                        <!-- Tombol Edit Profil -->
                        <div class="text-center">
                            <a href="edit_profile.php" class="btn btn-primary">Edit Profil</a>
                        </div>
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
