<?php
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Periksa apakah pengguna adalah pengelola
if ($_SESSION['user']['role'] !== 'rumah_tangga') {
    // Jika bukan pengelola, redirect ke halaman unauthorized
    header("Location: page.php?mod=unaut2");
    exit();
}

$id_rumah_tangga = $_SESSION['user']['id'];

// Ambil saldo saat ini dan cek apakah kueri berhasil
$query_saldo = "SELECT saldo FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
$result_saldo = mysqli_query($conn, $query_saldo);

if ($result_saldo && mysqli_num_rows($result_saldo) > 0) {
    $current_saldo = mysqli_fetch_assoc($result_saldo)['saldo'];
} else {
    $current_saldo = 0; // Tetapkan nilai default jika saldo tidak ditemukan
}

$query_riwayat = "SELECT t.*, wm.nama_warung 
                  FROM transaksi t 
                  JOIN warung_mitra wm ON t.id_warung_mitra = wm.id 
                  WHERE t.id_rumah_tangga = '$id_rumah_tangga' 
                  ORDER BY t.tanggal DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_pembayaran = floatval($_POST['jumlah_pembayaran']);
    $id_warung_mitra = $_POST['id_warung_mitra'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Periksa apakah saldo mencukupi
    if ($jumlah_pembayaran <= $current_saldo) {
        // Catat transaksi sebagai 'pending'
        $query_transaksi = "INSERT INTO transaksi (id_rumah_tangga, id_warung_mitra, jumlah_pembayaran, keterangan, status, tanggal) 
                            VALUES ('$id_rumah_tangga', '$id_warung_mitra', '$jumlah_pembayaran', '$keterangan', 'pending', NOW())";
        mysqli_query($conn, $query_transaksi);

        // Redirect ke halaman riwayat atau sukses
        header("Location: page.php?mod=riwayat");
        exit();
    } else {
        echo "Saldo tidak mencukupi.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Belanja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include 'assets/components/header.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Pembayaran Belanja</h2>
                        <h3>Saldo Anda saat ini: Rp. <?= number_format($current_saldo, 2, ',', '.') ?></h3>
                    </div>

                    <div class="card-body">
                        <form id="paymentForm" method="POST">
                            <div class="form-group">
                                <label for="id_warung_mitra">Pilih Warung Mitra</label>

                                <select name="id_warung_mitra" id="id_warung_mitra" class="form-control" required>
                                    <?php
                                    // Ambil daftar warung mitra yang terverifikasi
                                    $query_warung = "SELECT * FROM warung_mitra WHERE is_verified = 1";
                                    $result_warung = mysqli_query($conn, $query_warung);

                                    if (mysqli_num_rows($result_warung) == 0) {
                                        echo "<option>Tidak ada warung mitra yang tersedia</option>";
                                    } else {
                                        while ($row = mysqli_fetch_assoc($result_warung)) {
                                            echo "<option value='{$row['id']}'>{$row['nama_warung']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jumlah_pembayaran">Jumlah Pembayaran (Rp)</label>
                                <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran" step="0.01"
                                    class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan Pembayaran</label>
                                <textarea name="keterangan" class="form-control" required></textarea>
                            </div>

                            <button type="button" class="btn btn-primary btn-block"
                                onclick="showConfirmationModal()">Bayar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
    <!-- Riwayat Pembayaran -->
    <div class="card p-4">
            <h4>Riwayat Pembayaran</h4>
            <?php 
            $adaPembayaranGagal = false;

            // Loop pertama untuk cek status "gagal" tanpa menampilkan data
            mysqli_data_seek($result_riwayat, 0); // Kembali ke awal hasil query
            while ($riwayat = mysqli_fetch_assoc($result_riwayat)) {
                if ($riwayat['status'] === 'gagal') {
                    $adaPembayaranGagal = true;
                    break;
                }
            }
            ?>

            <?php if ($adaPembayaranGagal): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Pembayaran gagal!</strong> Dikarenakan kurangnya pembayaran pada saat transaksi di warung terkait.
                    Harap hubungi warung terkait untuk menyelesaikan pembayaran.
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
                <div class="row gy-4">
                    <?php 
                    // Loop kedua untuk menampilkan data dalam bentuk kartu
                    mysqli_data_seek($result_riwayat, 0); // Kembali ke awal hasil query
                    while ($riwayat = mysqli_fetch_assoc($result_riwayat)): 
                    ?>
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($riwayat['nama_warung']) ?></h5>
                                    <p class="card-text">
                                        <strong>Jumlah Pembayaran:</strong> Rp<?= number_format($riwayat['jumlah_pembayaran'], 2, ',', '.') ?><br>
                                        <strong>Status:</strong> <?= htmlspecialchars($riwayat['status']) ?><br>
                                        <strong>Keterangan:</strong> <?= htmlspecialchars($riwayat['keterangan']) ?><br>
                                        <strong>Tanggal:</strong> <?= htmlspecialchars($riwayat['tanggal']) ?>
                                    </p>
                                </div>
                                <?php if ($riwayat['status'] === 'gagal'): ?>
                                    <div class="card-footer text-danger">
                                        Perlu perhatian lebih
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted mt-4">
                    <i class="fas fa-receipt fa-2x"></i>
                    <p class="mt-2">Tidak ada riwayat pembayaran.</p>
                </div>
            <?php endif; ?>
        </div>        
    </div>

 

    <!-- Modal Konfirmasi Pembayaran -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirmMessage">
                    Apakah Anda yakin ingin melanjutkan pembayaran ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitPayment()">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        function showConfirmationModal() {
            // Ambil nilai dari jumlah pembayaran dan warung mitra
            var jumlahPembayaran = document.getElementById('jumlah_pembayaran').value;
            var warungMitraSelect = document.getElementById('id_warung_mitra');
            var warungMitraText = warungMitraSelect.options[warungMitraSelect.selectedIndex].text;

            // Set pesan konfirmasi dengan teks bold
            var confirmMessage = "Apakah Anda yakin ingin melanjutkan pembayaran sebesar <strong>Rp " + jumlahPembayaran + "</strong> ke warung mitra <strong>" + warungMitraText + "</strong>?";
            document.getElementById('confirmMessage').innerHTML = confirmMessage;

            // Tampilkan modal
            $('#confirmModal').modal('show');
        }

        function submitPayment() {
            $('#confirmModal').modal('hide');
            document.getElementById('paymentForm').submit();
        }
    </script>
</body>

</html>