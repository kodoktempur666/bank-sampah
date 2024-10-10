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


// Cek apakah ada ID pengguna yang dikirimkan melalui URL
if (!isset($_GET['id'])) {
    header("Location: page.php?mod=verify-war");
    exit();
}

$id_user = $_GET['id'];

// Ambil data pengguna berdasarkan ID
$query_user = "SELECT * FROM warung_mitra WHERE id = '$id_user'";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Jika pengguna tidak ditemukan
if (!$user) {
    header("Location: page.php?mod=edit-user");
    exit();
}

// Proses update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_warung = $_POST['nama_warung'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $saldo = $_POST['saldo'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    // Update data pengguna
    $query_update = "
        UPDATE warung_mitra
        SET nama_warung = '$nama_warung', alamat = '$alamat', kontak = '$kontak', 
            username = '$username', password = '$password', saldo = '$saldo', is_verified = '$is_verified'
        WHERE id = '$id_user'
    ";
    
    if (mysqli_query($conn, $query_update)) {
        
        header("Location: page.php?mod=verify-war");
        
        exit();
    } else {
        $error_message = "Terjadi kesalahan saat mengupdate data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'assets/components/headerpeng.php'; ?>
    <div class="container mt-5">
        <h1>Edit Pengguna</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Warung</label>
                <input type="text" name="nama_warung" class="form-control" value="<?= htmlspecialchars($user['nama_warung']) ?>" required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($user['alamat']) ?>">
            </div>

            <div class="form-group">
                <label>Kontak</label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($user['kontak']) ?>">
            </div>


            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($user['password']) ?>" required>
            </div>

            <div class="form-group">
                <label>Saldo</label>
                <input type="number" step="0.01" name="saldo" class="form-control" value="<?= number_format($user['saldo'], 2, '.', '') ?>" required>
            </div>

            <div class="form-group">
                <label>Status Verifikasi</label>
                <div class="form-check">
                    <input type="checkbox" name="is_verified" class="form-check-input" id="is_verified" <?= $user['is_verified'] == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_verified">Terverifikasi</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="page.php?mod=verify-war" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
