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


// Ambil data semua jenis sampah
$query = "SELECT * FROM jenis_sampah";
$result = mysqli_query($conn, $query);

// Tambah Jenis Sampah Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_sampah'])) {
    $nama_jenis = $_POST['nama_jenis'];
    $harga_per_kg = $_POST['harga_per_kg'];

    $query_add = "INSERT INTO jenis_sampah (nama_jenis, harga_per_kg) VALUES ('$nama_jenis', '$harga_per_kg')";
    mysqli_query($conn, $query_add);

    header("Location: page.php?mod=edit-sampah");
}

// Update Harga Sampah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_sampah'])) {
    $id_sampah = $_POST['id_sampah'];
    $harga_per_kg = $_POST['harga_per_kg'];

    $query_update = "UPDATE jenis_sampah SET harga_per_kg = '$harga_per_kg' WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_update);

    header("Location: page.php?mod=edit-sampah");
}

// Hapus Jenis Sampah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_sampah'])) {
    $id_sampah = $_POST['id_sampah'];

    $query_delete = "DELETE FROM jenis_sampah WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_delete);

    header("Location: manage_sampah.php");
}

// Update harga sampah

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_sampah'])) {
    $id_sampah = $_POST['id_sampah'];
    $harga_per_kg = $_POST['harga_per_kg'];

    $query_update = "UPDATE jenis_sampah SET harga_per_kg = '$harga_per_kg' WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_update);

    header("Location: page.php?mod=edit-sampah");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis & Harga Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Kelola Jenis & Harga Sampah</h1>

        <!-- Form Tambah Sampah -->
        <form method="POST" class="mt-4">
            <h4>Tambah Jenis Sampah</h4>
            <div class="form-group">
                <label>Nama Jenis Sampah</label>
                <input type="text" name="nama_jenis" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Harga per Kg (Rp)</label>
                <input type="number" name="harga_per_kg" class="form-control" required>
            </div>
            <button type="submit" name="add_sampah" class="btn btn-primary">Tambah Jenis Sampah</button>
        </form>

        <!-- Tabel Data Sampah -->
        <h4 class="mt-5">Jenis Sampah yang Ada</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Jenis Sampah</th>
                    <th>Harga per Kg (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($sampah = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $sampah['nama_jenis'] ?></td>
                    <td><?= number_format($sampah['harga_per_kg'], 2, ',', '.') ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                            <input type="number" name="harga_per_kg" value="<?= $sampah['harga_per_kg'] ?>" class="form-control d-inline" style="width: 120px;" required>
                            <button type="submit" name="update_sampah" class="btn btn-success">Update</button>
                        </form>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_sampah" value="<?= $sampah['id'] ?>">
                            <button type="submit" name="delete_sampah" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
