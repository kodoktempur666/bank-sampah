<?php
session_start();
require '../config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil semua pengguna
$query_users = "SELECT * FROM rumah_tangga";
$result_users = mysqli_query($conn, $query_users);

// Hapus Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $id_user = $_POST['id_user'];

    $query_delete = "DELETE FROM rumah_tangga WHERE id = '$id_user'";
    mysqli_query($conn, $query_delete);

    header("Location: manage_users.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Kelola Akun Pengguna</h1>

        <!-- Tabel Data Pengguna -->
        <h4 class="mt-4">Akun Rumah Tangga</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Saldo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($result_users)): ?>
                <tr>
                    <td><?= $user['nama'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= number_format($user['saldo'], 2, ',', '.') ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_user" value="<?= $user['id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
