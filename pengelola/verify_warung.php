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

// Ambil semua pengguna rumah tangga
$query_users = "SELECT * FROM warung_mitra";
$result_users = mysqli_query($conn, $query_users);

// Hapus Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $id_user = $_POST['id_user'];

    $query_delete = "DELETE FROM warung_mitra WHERE id = '$id_user'";
    mysqli_query($conn, $query_delete);

    header("Location: page.php?mod=verify-war");
    exit();
}

// Verifikasi Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_user'])) {
    $id_user = $_POST['id_user'];

    $query_verify = "UPDATE warung_mitra SET is_verified = 1 WHERE id = '$id_user'";
    mysqli_query($conn, $query_verify);

    header("Location: page.php?mod=verify-war");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Mitra</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'assets/components/headerpeng.php'; ?>
    <div class="container mt-5">
        <h1>Kelola Akun Mitra</h1>
        <a href="page.php?mod=pengelola" class="btn btn-warning mt-3">Home</a>
        <!-- Tabel Data Pengguna -->
        <h4 class="mt-4">Akun Mitra</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Warung</th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Saldo</th>
                    <th>Status Verifikasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($result_users)): ?>
                <tr>
                    <td><?= htmlspecialchars($user['nama_warung']) ?></td>
                    <td><?= htmlspecialchars($user['alamat']) ?></td>
                    <td><?= htmlspecialchars($user['kontak']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['password']) ?></td>
                    <td><?= number_format($user['saldo'], 2, ',', '.') ?></td>
                    <td>
                        <?php if ($user['is_verified'] == 1): ?>
                            <span class="badge badge-success">Terverifikasi</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Belum Terverifikasi</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Tombol Edit -->
                        <a href="page.php?mod=edit-war&id=<?= $user['id'] ?>" class="btn btn-warning">Edit</a>

                        <!-- Tombol Hapus -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_user" value="<?= $user['id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus user ini?')">Hapus</button>
                        </form>

                        <!-- Tombol Verifikasi, hanya tampil jika user belum diverifikasi -->
                        <?php if ($user['is_verified'] == 0): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_user" value="<?= $user['id'] ?>">
                            <button type="submit" name="verify_user" class="btn btn-success" onclick="return confirm('Verifikasi user ini?')">Verifikasi</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
