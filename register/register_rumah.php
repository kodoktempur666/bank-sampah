<?php
session_start();
require 'config/connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Hash password
    $role = 'rumah_tangga'; // Role is rumah_tangga
    $verification_code = rand(100000, 999999); // Generate verification code

    // Cek apakah ada data dengan email yang sama dan belum terverifikasi (is_verified = 0)
    $check_query = "SELECT * FROM rumah_tangga WHERE email = '$email' AND is_verified = 0";
    $check_result = mysqli_query($conn, $check_query);

    // Jika ditemukan data dengan is_verified = 0, hapus data tersebut
    if (mysqli_num_rows($check_result) > 0) {
        $delete_query = "DELETE FROM rumah_tangga WHERE email = '$email' AND is_verified = 0";
        mysqli_query($conn, $delete_query);
    }

    // Cek apakah nama, username, atau email sudah ada dengan is_verified = 1
    $check_query_verified = "SELECT * FROM rumah_tangga WHERE (nama = '$nama' OR username = '$username' OR email = '$email') AND is_verified = 1";
    $check_result_verified = mysqli_query($conn, $check_query_verified);

    if (mysqli_num_rows($check_result_verified) > 0) {
        // Jika ditemukan pengguna dengan nama, username, atau email yang sama
        $existing_data = mysqli_fetch_assoc($check_result_verified);
        if ($existing_data['nama'] == $nama) {
            echo "<script>alert('Nama sudah terdaftar.');</script>";
        } elseif ($existing_data['username'] == $username) {
            echo "<script>alert('Username sudah terdaftar.');</script>";
        } elseif ($existing_data['email'] == $email) {
            echo "<script>alert('Email sudah terdaftar.');</script>";
        }
    } else {
        // Jika tidak ada data yang sama, lanjutkan proses registrasi
        $query = "INSERT INTO rumah_tangga (nama, alamat, kontak, username, email, password, verification_code) 
                  VALUES ('$nama', '$alamat', '$kontak', '$username', '$email', '$password', '$verification_code')";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Send verification code via email
            $mail = new PHPMailer();
            
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lontopcx12@gmail.com'; // Your SMTP username
            $mail->Password   = 'xyltfgzrxvprmwvt'; // Your SMTP password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('your_email@example.com', 'Bank Sampah');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Verification Code';
            $mail->Body    = 'Your verification code is: ' . $verification_code;

            // Check if email was sent successfully
            if (!$mail->send()) {
                // If email failed to send, handle error first
                $error_message = $mail->ErrorInfo;

                // Hapus data dari tabel
                $delete_query = "DELETE FROM rumah_tangga WHERE email = '$email'";
                mysqli_query($conn, $delete_query);

                // Tambahkan kondisi untuk registrasi gagal jika alamat email tidak ditemukan
                if (strpos($error_message, 'Recipient address rejected') !== false || strpos($error_message, 'Invalid address') !== false) {
                    echo "<script>alert('Registrasi gagal: Alamat email tidak ditemukan atau tidak valid. Data registrasi telah dihapus.');</script>";
                } else {
                    echo "<script>alert('Registrasi gagal: Gagal mengirim email. Data registrasi telah dihapus.');</script>";
                }
            } else {
                // If email sent successfully, proceed with success handling
                echo "<script>
                    alert('Kode telah dikirim ke email yang didaftarkan.');
                    window.location.href = 'page.php?mod=verify&email=$email&role=$role';
                </script>";
                exit();
            }
        } else {
            // Gagal menyimpan data
            $delete_query = "DELETE FROM rumah_tangga WHERE email = '$email'";
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
</head>
<body>
    <form method="POST">
        <label>Nama:</label>
        <input type="text" name="nama" required><br>

        <label>Alamat:</label>
        <input type="text" name="alamat" required><br>

        <label>Kontak(No WA):</label>
        <input type="text" name="kontak" required><br>

        <label>Username:</label>
        <input type="text" name="username" required><br>
        
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
