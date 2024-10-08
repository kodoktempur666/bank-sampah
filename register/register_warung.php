<?php
session_start();
require 'config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_warung = mysqli_real_escape_string($conn, $_POST['nama_warung']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Hash password
    $role = 'warung_mitra'; // Role is rumah_tangga
    $is_verified = 0; // Akun belum diverifikasi, pengelola yang akan memverifikasi

    // Cek apakah ada data dengan email yang sama dan belum terverifikasi (is_verified = 0)
    $check_query = "SELECT * FROM warung_mitra WHERE nama_warung = '$nama_warung' AND is_verified = 0";
    $check_result = mysqli_query($conn, $check_query);

    // Jika ditemukan data dengan is_verified = 0, hapus data tersebut
    if (mysqli_num_rows($check_result) > 0) {
        $delete_query = "DELETE FROM warung_mitra WHERE nama_warung = '$nama_warung' AND is_verified = 0";
        mysqli_query($conn, $delete_query);
    }

    // Cek apakah nama, username, atau email sudah ada dengan is_verified = 1
    $check_query_verified = "SELECT * FROM warung_mitra WHERE (nama_warung = '$nama_warung' OR username = '$username') AND is_verified = 1";
    $check_result_verified = mysqli_query($conn, $check_query_verified);

    if (mysqli_num_rows($check_result_verified) > 0) {
        // Jika ditemukan pengguna dengan nama, username, atau email yang sama
        $existing_data = mysqli_fetch_assoc($check_result_verified);
        if ($existing_data['nama_warung'] == $nama_warung) {
            echo "<script>alert('Nama sudah terdaftar.');</script>";
        } elseif ($existing_data['username'] == $username) {
            echo "<script>alert('Username sudah terdaftar.');</script>";
        }
    } else {
        // Jika tidak ada data yang sama, lanjutkan proses registrasi
        $query = "INSERT INTO warung_mitra (nama_warung, alamat, kontak, username, password, is_verified) 
                  VALUES ('$nama_warung', '$alamat', '$kontak', '$username', '$password', '$is_verified')";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('Registrasi berhasil. Akun Anda akan diverifikasi oleh pengelola.');
                window.location.href = 'page.php?mod=home';
            </script>";
            exit();
        } else {
            // Gagal menyimpan data
            $delete_query = "DELETE FROM warung_mitra WHERE nama_warung = '$nama_warung'";
            mysqli_query($conn, $delete_query);
            echo "<script>alert('Gagal menyimpan data.');</script>" . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Rumah Tangga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #7ab987;
            --background-color: #1a1a1a;
            --form-bg-color: #f5f5f5;
            --text-color: #333;
            --button-success: #28a745;
            --button-warning: #ffc107;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: var(--form-bg-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }

        .form-container h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 1.8rem;
        }

        .form-container label {
            font-weight: bold;
            color: var(--text-color);
            font-size: 1rem;
        }

        .form-control {
            margin-bottom: 15px;
            border: 1px solid var(--primary-color);
            padding: 10px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 5px rgba(0, 119, 182, 0.5);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            font-size: 1rem;
            padding: 12px;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-success {
            background-color: var(--button-warning);
            border: none;
            font-size: 1rem;
            padding: 12px;
            margin-top: 10px;
            width: 100%;
        }

        .btn-success:hover {
            background-color: var(--button-success);
        }

        /* Responsive styling for mobile devices */
        @media (max-width: 768px) {
            .form-container {
                padding: 30px;
                max-width: 90%;
            }

            .form-container h2 {
                font-size: 1.6rem;
                margin-bottom: 20px;
            }

            .form-control {
                font-size: 0.9rem;
                padding: 10px;
            }

            .btn-primary,
            .btn-success {
                font-size: 0.9rem;
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 20px;
                max-width: 100%;
            }

            .form-container h2 {
                font-size: 1.4rem;
                margin-bottom: 15px;
            }

            .form-control {
                font-size: 0.85rem;
                padding: 8px;
            }

            .btn-primary,
            .btn-success {
                font-size: 0.85rem;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Register Warung Mitra</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nama_warung">Nama Warung:</label>
                <input type="text" class="form-control" id="nama_warung" name="nama_warung" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <input type="text" class="form-control" id="alamat" name="alamat" required>
            </div>

            <div class="form-group">
                <label for="kontak">Kontak (No WA):</label>
                <input type="text" class="form-control" id="kontak" name="kontak" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>


            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            <button type="button" class="btn btn-success" onclick="location.href='?mod=home'">Sudah punya akun?
                Login</button>
        </form>
    </div>
</body>

</html>
