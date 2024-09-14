<?php
require 'config/connect.php';

if (isset($_GET['id'])) {
    $jenis_sampah_id = $_GET['id'];

    $query = "SELECT harga_per_kg FROM jenis_sampah WHERE id = '$jenis_sampah_id'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['harga_per_kg' => $row['harga_per_kg']]);
    } else {
        echo json_encode(['harga_per_kg' => 0]);
    }
} else {
    echo json_encode(['harga_per_kg' => 0]);
}
