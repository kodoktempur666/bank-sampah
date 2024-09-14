<?php
session_start();
require 'config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = ($_POST['password']); 

    // Cek dari tabel rumah_tangga
    $query1 = "SELECT * FROM rumah_tangga WHERE username='$username' AND password='$password' AND is_verified=1";
    $result1 = mysqli_query($conn, $query1);

    // Cek dari tabel pengelola_sampah
    $query2 = "SELECT * FROM pengelola_sampah WHERE username='$username' AND password='$password' AND is_verified=1";
    $result2 = mysqli_query($conn, $query2);

    // Cek dari tabel warung_mitra
    $query3 = "SELECT * FROM warung_mitra WHERE username='$username' AND password='$password' AND is_verified=1";
    $result3 = mysqli_query($conn, $query3);

    // Cek dari tabel admin
    $query4 = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result4 = mysqli_query($conn, $query4);

    if (mysqli_num_rows($result1) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result1);
        header("Location: page.php?mod=users");
    } elseif (mysqli_num_rows($result2) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result2);
        header("Location: page.php?mod=pengelola");
    } elseif (mysqli_num_rows($result3) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result3);
        header("Location: page.php?mod=warung");
    } elseif (mysqli_num_rows($result4) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result4);
        header("Location: page.php?mod=admin");
    } else {
        echo "Login gagal. Username atau password salah.";
    }
}
?>
