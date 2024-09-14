<?php
session_start();
require '../config/connect.php';

// Cek jika admin sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Dashboard Admin</h1>
        <a href="manage_sampah.php" class="btn btn-primary mt-3">Kelola Jenis & Harga Sampah</a>
        <a href="manage_users.php" class="btn btn-secondary mt-3">Kelola Akun Pengguna</a>
    </div>
</body>
</html>
