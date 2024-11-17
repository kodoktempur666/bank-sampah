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

// Menampilkan nama rumah tangga
$query_nama = "SELECT nama FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
$result_nama = mysqli_query($conn, $query_nama);
$nama = mysqli_fetch_assoc($result_nama)['nama'];


// Saldo terakhir
$query_saldo = "SELECT saldo FROM rumah_tangga WHERE id = '$id_rumah_tangga'";
$result_saldo = mysqli_query($conn, $query_saldo);
$current_saldo = mysqli_fetch_assoc($result_saldo)['saldo'];

// Ambil data sampah yang statusnya menunggu pickup
$query_sampah = "SELECT s.id, s.berat, s.total_harga, s.status, js.nama_jenis, s.confirmed_by_rumah_tangga, s.confirmed_by_pengelola 
                 FROM sampah s
                 JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                 WHERE s.id_rumah_tangga = '$id_rumah_tangga' AND s.status = 'siap hitung'";
$result_sampah = mysqli_query($conn, $query_sampah);

// Hitung total harga sampah yang menunggu pickup
$total_pickup = 0;
while ($row = mysqli_fetch_assoc($result_sampah)) {
    $total_pickup += $row['total_harga'];
}

// Ambil semua data sampah untuk history
$query_history = "SELECT s.berat, s.total_harga, s.status, s.created_at, js.nama_jenis 
                  FROM sampah s
                  JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                  WHERE s.id_rumah_tangga = '$id_rumah_tangga'
                  ORDER BY s.id DESC";
$result_history = mysqli_query($conn, $query_history);

// Hitung total harga untuk history
$total_history = 0;
while ($row = mysqli_fetch_assoc($result_history)) {
    $total_history += $row['total_harga'];
}

// Ambil history pembayaran
$query_riwayat = "SELECT t.*, wm.nama_warung 
                  FROM transaksi t 
                  JOIN warung_mitra wm ON t.id_warung_mitra = wm.id 
                  WHERE t.id_rumah_tangga = '$id_rumah_tangga' 
                  ORDER BY t.tanggal DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);

// Hapus sampah
if (isset($_POST['action']) && $_POST['action'] == 'hapus') {
    $id_sampah = $_POST['id_sampah'];
    $query_delete = "DELETE FROM sampah WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_delete);
    header("Location: page.php?mod=riwayat");
    exit();
}



// Proses konfirmasi transaksi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_sampah = $_POST['id_sampah'];
    $status = $_POST['status'];

    // Ambil data sampah berdasarkan ID
    $query_sampah_detail = "SELECT * FROM sampah WHERE id = '$id_sampah'";
    $result_sampah_detail = mysqli_query($conn, $query_sampah_detail);
    $sampah = mysqli_fetch_assoc($result_sampah_detail);

    // Update status konfirmasi rumah tangga
    $query_update_rumah_tangga = "UPDATE sampah SET confirmed_by_rumah_tangga = 'diterima' WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_update_rumah_tangga);

    // Cek jika sudah terkonfirmasi oleh kedua belah pihak
    $query_update_status = "UPDATE sampah SET status = '$status' WHERE id = '$id_sampah'";
    mysqli_query($conn, $query_update_status);

    // Jika status diubah menjadi "selesai", tambahkan saldo rumah tangga
    $id_rumah_tangga = $sampah['id_rumah_tangga'];  
    $total_harga = $sampah['total_harga'];

    // Update saldo rumah tangga
    $query_update_saldo = "UPDATE rumah_tangga SET saldo = saldo + $total_harga WHERE id = '$id_rumah_tangga'";
    mysqli_query($conn, $query_update_saldo);

    // if ($sampah['confirmed_by_rumah_tangga'] === 'diterima' && $sampah['confirmed_by_pengelola'] === 'diterima') {
    //     // Update status sampah
    //     if ($status === 'selesai') {
    //     }
    // }

    header("Location: page.php?mod=riwayat");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Rumah Tangga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        h1,
        h2,
        h3,
        h4 {
            font-weight: 600;
        }

        .container {
            margin-top: 30px;
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

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .modal-content {
            border-radius: 10px;
            padding: 20px;
        }

        .no-data {
            color: #6c757d;
            text-align: center;
            margin-top: 20px;
        }

        .no-data i {
            font-size: 50px;
            margin-bottom: 10px;
        }
                /* Kustomisasi untuk scrollbar pada browser berbasis Webkit (seperti Chrome, Safari) */
::-webkit-scrollbar {
    width: 12px; /* Lebar scrollbar */
}

::-webkit-scrollbar-track {
    background: #f4f6f9; /* Warna latar belakang track scrollbar */
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background-color: #70de74; /* Warna scrollbar */
    border-radius: 10px;
    border: 3px solid #f4f6f9; /* Memberi efek padding dengan latar belakang */
    transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out; /* Menambahkan transisi */
}

/* Hover effect pada scrollbar */
::-webkit-scrollbar-thumb:hover {
    background-color: #34495e; /* Warna saat hover */
    transform: scale(1.1); /* Sedikit memperbesar saat hover */
}

/* Scrollbar pada Firefox
scrollbar-color: #70de74 #f4f6f9; /* Warna thumb dan track 
scrollbar-width: thin; Menjadikan scrollbar lebih tipis; */
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'assets/components/header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center">Dashboard <?= $nama ?></h1>
        <div class="card p-4">
            <h3>Saldo: Rp. <?= number_format($current_saldo, 2, ',', '.') ?></h3>
            <a href="?mod=pembayaran" class="btn btn-primary mt-4">Bayar</a>
        </div>

        <!-- Sampah Siap Pick-Up -->
        <div class="container mt-5">
        <!-- Order Sampah -->
        <div class="card p-4">
            <h4>Order Sampah</h4>
            <?php if (mysqli_num_rows($result_sampah) > 0): ?>
                <div class="row gy-4">
                    <?php 
                    mysqli_data_seek($result_sampah, 0);
                    while ($sampah = mysqli_fetch_assoc($result_sampah)): ?>
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Jenis: <?= htmlspecialchars($sampah['nama_jenis']) ?></h5>
                                    <p class="card-text">
                                        <strong>Berat:</strong> <?= number_format($sampah['berat'], 2, ',', '.') ?> kg<br>
                                        <strong>Total Harga:</strong> Rp<?= number_format($sampah['total_harga'], 2, ',', '.') ?><br>
                                        <strong>Status:</strong> <?= htmlspecialchars($sampah['status']) ?><br>
                                        <strong>Pembayaran:</strong> <?= htmlspecialchars($sampah['confirmed_by_rumah_tangga'] ?? 'Belum Dikonfirmasi') ?>
                                    </p>
                                    <div class="d-flex flex-column mt-3">
                                        <button class="btn btn-danger btn-sm mb-2"
                                            onclick="confirmDelete(<?= $sampah['id'] ?>)">
                                            Hapus
                                        </button>
                                        <button class="btn btn-success btn-sm"
                                            onclick="openPaymentModal('<?= $sampah['nama_jenis'] ?>', '<?= $sampah['berat'] ?>', '<?= $sampah['total_harga'] ?>', '<?= $sampah['id'] ?>')"
                                            <?= ($sampah['confirmed_by_pengelola'] == 'belum diterima') ? 'disabled' : '' ?>>
                                            Setuju Nilai Transaksi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted mt-4">
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                    <p class="mt-2">Tidak ada data order sampah.</p>
                </div>
            <?php endif; ?>
            <div class="text-center mt-4">
                <a href="page.php?mod=jual" class="btn btn-primary">Jual Sampah</a>
            </div>
        </div>
    </div>


    <!-- History Transaksi Sampah -->
    <div class="card p-4">
            <h4>History Transaksi Sampah</h4>
            <div class="mt-4 text-end">
                    <h5><strong>Total Penjualan (Rp):</strong> Rp<?= number_format($total_history, 2, ',', '.') ?></h5>
            </div>
            <?php if (mysqli_num_rows($result_history) > 0): ?>
                <div class="row gy-4">
                    <?php 
                    mysqli_data_seek($result_history, 0); // Kembali ke awal hasil query
                    while ($history = mysqli_fetch_assoc($result_history)): ?>
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Jenis: <?= htmlspecialchars($history['nama_jenis']) ?></h5>
                                    <p class="card-text">
                                        <strong>Berat:</strong> <?= number_format($history['berat'], 2, ',', '.') ?> kg<br>
                                        <strong>Total Harga:</strong> Rp<?= number_format($history['total_harga'], 2, ',', '.') ?><br>
                                        <strong>Status:</strong> <?= htmlspecialchars($history['status']) ?><br>
                                        <strong>Tanggal:</strong> <?= htmlspecialchars($history['created_at']) ?>
                                    </p>
                                </div>
                                <?php if ($history['status'] === 'gagal'): ?>
                                    <div class="card-footer text-danger">
                                        Transaksi Gagal
                                    </div>
                                <?php else: ?>
                                    <div class="card-footer text-success">
                                        Transaksi Selesai
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            <?php else: ?>
                <div class="text-center text-muted mt-4">
                    <i class="fas fa-history fa-2x"></i>
                    <p class="mt-2">Tidak ada history transaksi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


 

    <!-- Modal Delete Confirmation -->
    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus sampah ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="formDelete" method="POST">
                        <input type="hidden" name="id_sampah" id="deleteId">
                        <input type="hidden" name="action" value="hapus">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- Modal pertama untuk Konfirmasi Terima Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Konfirmasi Nilai Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>Jenis Sampah:</strong> <span id="modalJenisSampah"></span></p>
                <p><strong>Berat (kg):</strong> <span id="modalBerat"></span></p>
                <p><strong>Total Harga (Rp):</strong> <span id="modalTotalHarga"></span></p>
                <p><strong>Apakah Anda menyutujui nilai transaksi ini?</strong></p>
                <form method="POST" id="paymentForm">
                    <input type="hidden" name="id_sampah" id="modalSampahId">
                    <input type="hidden" name="status" value="selesai">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-success" onclick="showFinalConfirmation()">Setuju</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal kedua untuk Konfirmasi Akhir -->
<div class="modal fade" id="finalConfirmationModal" tabindex="-1" aria-labelledby="finalConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="finalConfirmationLabel">Konfirmasi Akhir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menerima sampah dengan jenis <strong><span id="confirmJenisSampah"></span></strong>, berat <strong><span id="confirmBerat"></span></strong> kg,
                    dan total harga <strong>Rp. <span id="confirmTotalHarga"></span></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success" form="paymentForm">Ya, Terima Pembayaran</button>
            </div>
        </div>
    </div>
</div>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete(id) {
            $('#deleteId').val(id);
            $('#modalDelete').modal('show');
        }
    </script>

    <script>
    function openPaymentModal(jenisSampah, berat, totalHarga, sampahId) {
        // Set data modal pertama
        document.getElementById('modalJenisSampah').innerText = jenisSampah;
        document.getElementById('modalBerat').innerText = parseFloat(berat).toFixed(2).replace('.', ',');
        document.getElementById('modalTotalHarga').innerText = parseFloat(totalHarga).toFixed(2).replace('.', ',');
        document.getElementById('modalSampahId').value = sampahId;

        // Tampilkan modal pertama
        var paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }

    function showFinalConfirmation() {
        // Copy data dari modal pertama ke modal kedua
        document.getElementById('confirmJenisSampah').innerText = document.getElementById('modalJenisSampah').innerText;
        document.getElementById('confirmBerat').innerText = document.getElementById('modalBerat').innerText;
        document.getElementById('confirmTotalHarga').innerText = document.getElementById('modalTotalHarga').innerText;

        // Sembunyikan modal pertama dan tampilkan modal kedua
        $('#paymentModal').modal('hide');
        $('#finalConfirmationModal').modal('show');
    }
</script>
</body>