<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Periksa apakah pengguna adalah pengelola
if ($_SESSION['user']['role'] !== 'warung_mitra') {
    // Jika bukan pengelola, redirect ke halaman unauthorized
    header("Location: page.php?mod=unaut2");
    exit();
}

$id_warung = $_SESSION['user']['id'];

// Menampilkan saldo warung mitra dengan prepared statement
$stmt_saldo = $conn->prepare("SELECT saldo FROM warung_mitra WHERE id = ?");
$stmt_saldo->bind_param("i", $id_warung);
$stmt_saldo->execute();
$result_saldo = $stmt_saldo->get_result();
$saldo1 = $result_saldo->fetch_assoc()['saldo'];
$stmt_saldo->close();

$successMessage = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_penarikan'])) {
    $jumlah_penarikan = floatval($_POST['jumlah']);

    // Validasi jumlah penarikan
    if ($jumlah_penarikan <= 0) {
        $errorMessage = 'Jumlah penarikan harus lebih besar dari nol!';
    } elseif ($jumlah_penarikan > $saldo1) {
        $errorMessage = 'Saldo tidak mencukupi!';
    } else {
        // Masukkan transaksi penarikan
        $stmt_insert = $conn->prepare("INSERT INTO transaksi_pencairan (id_warung_mitra, jumlah, status) VALUES (?, ?, 'pending')");
        $stmt_insert->bind_param("id", $id_warung, $jumlah_penarikan);
        $stmt_insert->execute();
        $stmt_insert->close();

        // Tampilkan pesan sukses
        $successMessage = true;
    }
}

// Ambil transaksi pending
$query_penarikan = "SELECT tp.*, wm.nama_warung, wm.saldo AS saldo_warung
                    FROM transaksi_pencairan tp
                    JOIN warung_mitra wm ON tp.id_warung_mitra = wm.id
                    WHERE tp.id_warung_mitra = '$id_warung' 
                    AND (tp.status = 'gagal' OR tp.status = 'pending')";
$result_penarikan = mysqli_query($conn, $query_penarikan);

$query_riwayat_penarikan = "SELECT rp.*, wm.nama_warung
                           FROM riwayat_penarikan rp
                           JOIN warung_mitra wm ON rp.id_warung_mitra = wm.id
                           WHERE rp.id_warung_mitra = '$id_warung'
                           ORDER BY rp.tanggal DESC";
$result_riwayat_penarikan = mysqli_query($conn, $query_riwayat_penarikan);

?>

<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Mitra - Penarikan Saldo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
        }
        .modal-footer .btn {
            min-width: 100px;
        }
        .saldo-section, .form-group, .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<?php include 'assets/components/headerwarung.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Penarikan Saldo</h1>
        <div class="saldo-section p-3 mb-4 bg-light text-center">
            <p>Saldo Anda saat ini: <strong>Rp. <?= number_format($saldo1, 2, ',', '.') ?></strong></p>
        </div>

        <form id="withdrawalForm" method="POST" onsubmit="return validateAmount()">
            <div class="form-group">
                <label for="jumlah">Jumlah Penarikan (Rp)</label>
                <input type="number" class="form-control" name="jumlah" id="jumlah" required>
            </div>

            <button type="button" class="btn btn-primary btn-block" onclick="showConfirmationModal()">Ajukan Penarikan</button>
            <input type="hidden" name="confirm_penarikan" value="1">
        </form>
    </div>

    <style>
        .status-pending {
            background-color: yellow;
            color: black;
        }

        .status-gagal {
            background-color: crimson;
            color: white;
        }

        .status-berhasil,
        .status-selesai {
            background-color: green;
            color: white;
        }

        .button-container {
            margin-top: 10px;
        }

        .btn-chat,
        .btn-delete {
            display: inline-block;
            margin-right: 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .btn-chat {
            background-color: #25D366;
            /* WhatsApp color */
        }

        .btn-delete {
            background-color: #DC3545;
            /* Delete button color */
        }
    </style>

    <div class="container mt-5">
        <h2>Status penarikan</h2>
        <?php if (mysqli_num_rows($result_penarikan) > 0): ?>
            <?php while ($riwayat_penarikan = mysqli_fetch_assoc($result_penarikan)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="data-item">
                            <span class="data-label">Nama Warung:</span>
                            <span class="data-value"><?= $riwayat_penarikan['nama_warung'] ?></span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Status:</span>
                            <span class="data-value">
                                <div class="status <?= 'status-' . strtolower($riwayat_penarikan['status']) ?>">
                                    <?= $riwayat_penarikan['status'] ?>
                                </div>
                            </span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Jumlah Penarikan (Rp):</span>
                            <span class="data-value"><?= number_format($riwayat_penarikan['jumlah'], 2, ',', '.') ?></span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Tanggal:</span>
                            <span class="data-value"><?= date('d-m-Y', strtotime($riwayat_penarikan['tanggal'])) ?></span>
                        </div>
                        <?php if (strtolower($riwayat_penarikan['status']) == 'gagal'): ?>
                            <div class="button-container d-flex justify-content-center">
                                <a href="https://wa.me/62xxxxxxxxx?text=Halo%20Pengelola,%20saya%20ingin%20menanyakan%20tentang%20transaksi%20gagal%20pada%20penarikan%20ini."
                                    class="btn-chat">Chat Pengelola</a>
                                <form method="POST">
                                    <input type="hidden" name="hapus_riwayat_penarikan_id" value="<?= $riwayat_penarikan['id'] ?>">
                                    <div class="btn-group mt-2 justify-content-center d-flex align-items-center">
    <button type="button" class="btn-delete" onclick="showConfirmModal(<?= $riwayat_penarikan['id'] ?>)">Hapus</button>
</div>

                                </form>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Tidak ada riwayat penarikan.</p>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h2>Riwayat penarikan</h2>
        <?php if (mysqli_num_rows($result_riwayat_penarikan) > 0): ?>
            <?php while ($riwayat_riwayat_penarikan = mysqli_fetch_assoc($result_riwayat_penarikan)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="data-item">
                            <span class="data-label">Nama Warung:</span>
                            <span class="data-value"><?= $riwayat_riwayat_penarikan['nama_warung'] ?></span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Status:</span>
                            <span class="data-value">
                                <div class="status <?= 'status-' . strtolower($riwayat_riwayat_penarikan['status']) ?>">
                                    <?= $riwayat_riwayat_penarikan['status'] ?>
                                </div>
                            </span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Jumlah Penarikan (Rp):</span>
                            <span
                                class="data-value"><?= number_format($riwayat_riwayat_penarikan['jumlah'], 2, ',', '.') ?></span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Tanggal:</span>
                            <span
                                class="data-value"><?= date('d-m-Y', strtotime($riwayat_riwayat_penarikan['tanggal'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Tidak ada riwayat penarikan.</p>
        <?php endif; ?>
    </div>
    

    <!-- Modal Konfirmasi Penarikan -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Penarikan Saldo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="confirmMessage">
                    Apakah Anda yakin ingin melakukan penarikan saldo sebesar <strong id="amount"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitWithdrawal()">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Modal Sukses -->
     <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5>Permintaan pencairan saldo berhasil!</h5>
                    <p>Menunggu konfirmasi dari pengelola.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function showConfirmationModal() {
            var jumlahPenarikan = document.getElementById('jumlah').value;
            if (jumlahPenarikan <= 0) {
                alert('Jumlah penarikan harus lebih besar dari nol!');
                return;
            }
            document.getElementById('amount').textContent = "Rp " + new Intl.NumberFormat('id-ID').format(jumlahPenarikan);
            $('#confirmModal').modal('show');
        }

        function submitWithdrawal() {
            $('#confirmModal').modal('hide');
            document.getElementById('withdrawalForm').submit();
        }

        <?php if ($successMessage): ?>
        $(document).ready(function() {
            $('#successModal').modal('show');
            setTimeout(function() {
                window.location.href = 'page.php?mod=pencairan';
            }, 3000);
        });
        <?php endif; ?>
    </script>
</body>
</html>
