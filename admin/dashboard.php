<?php
session_start();
require 'config/connect.php';

// Cek jika admin sudah login
if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

if ($_SESSION['user']['role'] == 'rumah_tangga') {
    header("Location: page.php?mod=unaut");
    exit();
}

// Daftar peran yang tidak diizinkan
$not_allowed_roles = ['warung_mitra', 'pengelola'];
if (in_array($_SESSION['user']['role'], $not_allowed_roles)) {
    // Jika pengguna memiliki salah satu dari peran yang tidak diizinkan, redirect mereka
    header("Location: page.php?mod=unaut2");
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
        <a href="page.php?mod=admin-sampah" class="btn btn-primary mt-3">Kelola Jenis & Harga Sampah</a>
        <a href="page.php?mod=admin-user" class="btn btn-secondary mt-3">Kelola Akun Pengguna</a>
    </div>
</body>
</html>
