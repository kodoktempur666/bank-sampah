<?php
session_start();
require 'config/connect.php';

// Cek apakah pengelola sudah login
if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Periksa apakah pengguna adalah pengelola
if ($_SESSION['user']['role'] !== 'pengelola') {
    header("Location: page.php?mod=unaut2");
    exit();
}

$id_pengelola = $_SESSION['user']['id'];

// Ambil transaksi penarikan yang pending
$query_penarikan = "SELECT tp.*, wm.nama_warung, wm.saldo AS saldo_warung
                    FROM transaksi_pencairan tp
                    JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                    WHERE tp.status = 'pending'";
$result_penarikan = mysqli_query($conn, $query_penarikan);

// Ambil transaksi penarikan yang gagal
$query_penarikan_gagal = "SELECT tp.*, wm.nama_warung
                          FROM transaksi_pencairan tp
                          JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                          WHERE tp.status = 'gagal'";
$result_penarikan_gagal = mysqli_query($conn, $query_penarikan_gagal);

// Memproses penarikan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_penarikan']) && $_POST['status'] == 'selesai') {
        $id_penarikan = $_POST['id_penarikan'];

        // Ambil data penarikan
        $query_detail = "SELECT tp.*, wm.saldo AS saldo_warung FROM transaksi_pencairan tp 
                         JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                         WHERE tp.id = '$id_penarikan'";
        $result_detail = mysqli_query($conn, $query_detail);
        $penarikan = mysqli_fetch_assoc($result_detail);

        if ($penarikan['jumlah'] <= $penarikan['saldo_warung']) {
            $jumlah_penarikan = $penarikan['jumlah'];

            // Kurangi saldo warung mitra
            $query_update_warung = "UPDATE warung_mitra SET saldo = saldo - $jumlah_penarikan WHERE id = '{$penarikan['id_warung_mitra']}'";
            mysqli_query($conn, $query_update_warung);

            // Ubah status penarikan menjadi selesai
            $query_update_penarikan = "UPDATE transaksi_pencairan SET status = 'selesai' WHERE id = '$id_penarikan'";
            mysqli_query($conn, $query_update_penarikan);

            // Salin data penarikan ke tabel riwayat_penarikan
            $query_insert_riwayat = "INSERT INTO riwayat_penarikan (id_warung_mitra, jumlah, status)
                                    SELECT id_warung_mitra, jumlah, 'selesai'
                                    FROM transaksi_pencairan
                                    WHERE id = '$id_penarikan'";
            mysqli_query($conn, $query_insert_riwayat);

            // Hapus data dari tabel transaksi_pencairan
            $query_delete_penarikan = "DELETE FROM transaksi_pencairan WHERE id = '$id_penarikan'";
            mysqli_query($conn, $query_delete_penarikan);
            header("Location: page.php?mod=data-penarikan&status=success");
            exit();
        } else {
            header("Location: page.php?mod=data-penarikan&status=insufficient");
            exit();
        }
    } elseif (isset($_POST['id_gagal']) && $_POST['status'] == 'gagal') {
        $id_gagal = $_POST['id_gagal'];

        // Proses untuk menandai transaksi sebagai gagal
        $query_gagal = "UPDATE transaksi_pencairan SET status = 'gagal' WHERE id = '$id_gagal'";
        mysqli_query($conn, $query_gagal);

        header("Location: page.php?mod=data-penarikan&status=failed");
        exit();
    }
}

// Ambil riwayat penarikan
$query_riwayat = "SELECT rp.*, wm.nama_warung
                  FROM riwayat_penarikan rp
                  JOIN warung_mitra wm ON rp.id_warung_mitra = wm.id
                  ORDER BY rp.tanggal DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelola - Proses Penarikan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'assets/components/headerpeng.php'; ?>
    <div class="container mt-5">
        <h1>Proses Penarikan Saldo</h1>

        <h4 class="mt-4">Penarikan Pending</h4>
        <?php if (mysqli_num_rows($result_penarikan) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Jumlah (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($penarikan = mysqli_fetch_assoc($result_penarikan)): ?>
                        <tr>
                            <td><?= $penarikan['nama_warung'] ?></td>
                            <td><?= number_format($penarikan['jumlah'], 2, ',', '.') ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_penarikan" value="<?= $penarikan['id'] ?>">
                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#modalSelesai" data-id="<?= $penarikan['id'] ?>"
                                        data-jumlah="<?= $penarikan['jumlah'] ?>" data-saldo="<?= $penarikan['saldo_warung'] ?>"
                                        data-nama="<?= $penarikan['nama_warung'] ?>">
                                        Disetujui
                                    </button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_gagal" value="<?= $penarikan['id'] ?>">
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalGagal"
                                        data-id="<?= $penarikan['id'] ?>" data-nama="<?= $penarikan['nama_warung'] ?>">
                                        Ditolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada penarikan yang pending.</p>
        <?php endif; ?>

        <h4 class="mt-4">Riwayat Penarikan</h4>
        <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Jumlah (Rp)</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($riwayat = mysqli_fetch_assoc($result_riwayat)): ?>
                        <tr>
                            <td><?= $riwayat['nama_warung'] ?></td>
                            <td><?= number_format($riwayat['jumlah'], 2, ',', '.') ?></td>
                            <td><?= $riwayat['status'] ?></td>
                            <td><?= $riwayat['tanggal'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada riwayat penarikan.</p>
        <?php endif; ?>

        <h4 class="mt-4">Penarikan Ditolak</h4>
        <?php if (mysqli_num_rows($result_penarikan_gagal) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Jumlah (Rp)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($penarikan_gagal = mysqli_fetch_assoc($result_penarikan_gagal)): ?>
                        <tr>
                            <td><?= $penarikan_gagal['nama_warung'] ?></td>
                            <td><?= number_format($penarikan_gagal['jumlah'], 2, ',', '.') ?></td>
                            <td><?= $penarikan_gagal['status'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada penarikan gagal.</p>
        <?php endif; ?>
    </div>
    <!-- Modal Konfirmasi Selesai -->
    <div class="modal fade" id="modalSelesai" tabindex="-1" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSelesaiLabel">Konfirmasi Penarikan Selesai</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin untuk mencairkan saldo untuk warung: <strong id="namaWarung"></strong>?</p>
                        <p>Jumlah Penarikan: <strong id="jumlahPenarikan"></strong></p>
                        <p>Saldo sebelum penarikan: <strong id="saldoWarung"></strong></p>
                        <p>Saldo setelah penarikan: <strong id="saldoSisa"></strong></p>
                        <input type="hidden" name="id_penarikan" id="idPenarikanSelesai">
                        <input type="hidden" name="status" value="selesai">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal Konfirmasi Gagal -->
    <div class="modal fade" id="modalGagal" tabindex="-1" aria-labelledby="modalGagalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGagalLabel">Konfirmasi Penarikan Gagal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin untuk menandai penarikan untuk warung <strong
                                id="namaWarungGagal"></strong> sebagai gagal?</p>
                        <input type="hidden" name="id_gagal" id="idPenarikanGagal">
                        <input type="hidden" name="status" value="gagal">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#modalSelesai').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var id = button.data('id');
                var jumlah = parseFloat(button.data('jumlah'));
                var saldo = parseFloat(button.data('saldo'));
                var namaWarung = button.data('nama');
                var saldoSisa = saldo - jumlah;

                $('#idPenarikanSelesai').val(id);
                $('#namaWarung').text(namaWarung);
                $('#jumlahPenarikan').text(jumlah.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }));
                $('#saldoWarung').text(saldo.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }));
                $('#saldoSisa').text(saldoSisa.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }));
            });

            $('#modalGagal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var id = button.data('id');
                var namaWarung = button.data('nama');

                $('#idPenarikanGagal').val(id);
                $('#namaWarungGagal').text(namaWarung);
            });
        });
    </script>



</body>

</html>