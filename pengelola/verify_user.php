<?php
session_start();
require 'config/connect.php';

// Periksa apakah pengguna sudah login dan memiliki hak sebagai pengelola
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pengelola') {
    header("Location: page.php?mod=home");
    exit();
}

$id_pengelola = $_SESSION['user']['id'];

// Ambil semua pengguna rumah tangga
$query_users = "SELECT * FROM rumah_tangga";
$result_users = mysqli_query($conn, $query_users);

// Hapus Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    $id_user = $_POST['id_user'];

    $query_delete = "DELETE FROM rumah_tangga WHERE id = '$id_user'";
    mysqli_query($conn, $query_delete);

    header("Location: page.php?mod=verify");
    exit();
}

// Verifikasi Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_verify'])) {
    $id_user = $_POST['id_user'];

    $query_verify = "UPDATE rumah_tangga SET is_verified = 1 WHERE id = '$id_user'";
    mysqli_query($conn, $query_verify);

    header("Location: page.php?mod=verify");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for better mobile view */
        .table-responsive {
            overflow-x: auto;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        @media (max-width: 768px) {

            .table td,
            .table th {
                font-size: 0.875rem;
                /* Ukuran font lebih kecil */
                white-space: nowrap;
                /* Mencegah teks terpotong */
            }
        }
    </style>
</head>

<body>
    <?php include 'assets/components/headerpeng.php'; ?>
    <div class="container mt-5">
        <h1>Kelola Akun Pengguna</h1>
        <a href="page.php?mod=pengelola" class="btn btn-warning mt-3">Home</a>

        <!-- List Data Pengguna -->
        <h4 class="mt-4">Akun Rumah Tangga</h4>

        <?php while ($user = mysqli_fetch_assoc($result_users)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($user['nama']) ?></h5>
                    <p class="card-text"><strong>Alamat:</strong> <?= htmlspecialchars($user['alamat']) ?></p>
                    <p class="card-text"><strong>Kontak:</strong> <?= htmlspecialchars($user['kontak']) ?></p>
                    <p class="card-text"><strong>RW:</strong> <?= htmlspecialchars($user['rw']) ?></p>
                    <p class="card-text"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p class="card-text"><strong>Password:</strong> <?= htmlspecialchars($user['password']) ?></p>
                    <p class="card-text"><strong>Saldo:</strong> Rp <?= number_format($user['saldo'], 2, ',', '.') ?></p>
                    <p class="card-text">
                        <strong>Status Verifikasi:</strong>
                        <?php if ($user['is_verified'] == 1): ?>
                            <span class="badge badge-success">Terverifikasi</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Belum Terverifikasi</span>
                        <?php endif; ?>
                    </p>

                    <!-- Tombol Aksi -->
                    <div class="btn-group" role="group" aria-label="Aksi">
                        <a href="page.php?mod=edit-user&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Edit</a>

                        <!-- Tombol Hapus dengan Modal -->
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal"
                            data-id="<?= $user['id'] ?>">Hapus</button>

                        <!-- Tombol Verifikasi dengan Modal, hanya tampil jika user belum diverifikasi -->
                        <?php if ($user['is_verified'] == 0): ?>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#verifyModal"
                                data-id="<?= $user['id'] ?>">Verifikasi</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Hapus Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pengguna ini?
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="id_user" id="deleteUserId" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="delete_user" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi -->
    <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="verifyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyModalLabel">Verifikasi Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin memverifikasi pengguna ini?
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="id_user" id="verifyUserId" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="verify_user" class="btn btn-success">Verifikasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk mengisi ID pengguna ke dalam modal hapus dan verifikasi
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var userId = button.data('id');
            var modal = $(this);
            modal.find('#deleteUserId').val(userId);
        });

        $('#verifyModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var userId = button.data('id');
            var modal = $(this);
            modal.find('#verifyUserId').val(userId);
        });
    </script>
</body>

</html>