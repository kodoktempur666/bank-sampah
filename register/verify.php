<?php
session_start();
require 'config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $entered_code = mysqli_real_escape_string($conn, $_POST['verification_code']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // The role of the user (rumah_tangga, warung_mitra, pengelola)

    // Determine which table to query based on the user's role
    $table = '';
    if ($role == 'rumah_tangga') {
        $table = 'rumah_tangga';
    } elseif ($role == 'warung_mitra') {
        $table = 'warung_mitra';
    } elseif ($role == 'pengelola') {
        $table = 'pengelola_sampah';
    }

    // Get the verification code from the database for the given email
    $query = "SELECT verification_code FROM $table WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    // Check if a record was found
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stored_code = $row['verification_code'];

        // Check if the entered code matches the stored code
        if ($entered_code == $stored_code) {
            // Update the user’s status to verified
            $query_update = "UPDATE $table SET is_verified = 1 WHERE email = '$email'";
            if (mysqli_query($conn, $query_update)) {
                // Redirect to the home page
                echo "<script>
                alert('Akun berhasil dibuat.');
                window.location.href = 'page.php?mod=home';
                </script>";
                exit();

            } else {
                 echo "Error updating verification status: " . mysqli_error($conn);
                // Anda juga bisa menggunakan alert dan redirect jika terjadi error
                echo "<script>
                alert('Error updating verification status.');
                window.location.href = 'page.php?mod=verify&email=$email&role=$role';
              </script>";
            }
        } else {
            
            //kode verifikasi salah

            echo "<script>alert('kode verifikasi salah. ');</script>";

        }
    } else {
        echo "<script>alert('Email tidak valid. ');</script>";

    }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account</title>
</head>
<body>
    <h1>Verify Your Account</h1>
    <form method="POST">
        <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']); ?>">
        <input type="hidden" name="role" value="<?= htmlspecialchars($_GET['role']); ?>">

        <label>Enter Verification Code:</label>
        <input type="text" name="verification_code" required><br>

        <button type="submit">Verify</button>
    </form>
</body>
</html>
