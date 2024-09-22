<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <!-- Google Fonts for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
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
    <!-- Header -->
    <?php include 'assets/components/header.php'; ?>

    <!-- Payment Form Section -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Pembayaran</h2>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm" method="POST">
                            <!-- Warung Mitra Selection -->
                            <div class="form-group">
                                <label for="id_warung_mitra">Pilih Warung Mitra</label>
                                <select name="id_warung_mitra" class="form-control" required>
                                    <?php
                                    // Ambil daftar warung mitra
                                    $query_warung = "SELECT * FROM warung_mitra";
                                    $result_warung = mysqli_query($conn, $query_warung);
                                    while ($row = mysqli_fetch_assoc($result_warung)) {
                                        echo "<option value='{$row['id']}'>{$row['nama_warung']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="jumlah_pembayaran">Jumlah Pembayaran (Rp)</label>
                                <input type="number" name="jumlah_pembayaran" step="0.01" class="form-control" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#confirmModal">Bayar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Pembayaran -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin melanjutkan pembayaran?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmPayment">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // When the user clicks on 'Konfirmasi' in the modal, submit the form
        document.getElementById('confirmPayment').addEventListener('click', function () {
            document.getElementById('paymentForm').submit();
        });
    </script>
</body>

</html>
