<?php
session_start();
require '../../config/connect.php';

// Periksa apakah request adalah POST dan ID sampah ada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_sampah'])) {
    $id_sampah = $_POST['id_sampah'];

    // Validasi apakah user yang login berhak menghapus sampah ini
    $id_rumah_tangga = $_SESSION['user']['id'];
    
    // Query untuk mengecek apakah sampah milik user yang sedang login
    $query_check = "SELECT * FROM sampah WHERE id = '$id_sampah' AND id_rumah_tangga = '$id_rumah_tangga'";
    $result_check = mysqli_query($conn, $query_check);

    // Jika sampah ditemukan dan milik user, hapus sampah tersebut
    if (mysqli_num_rows($result_check) > 0) {
        $query_delete = "DELETE FROM sampah WHERE id = '$id_sampah'";
        if (mysqli_query($conn, $query_delete)) {
            // Redirect ke halaman jual dengan pesan sukses
            header("Location: ../page.php?mod=jual");
            exit();
        } else {
            // Redirect ke halaman jual dengan pesan gagal
        }
    }
}