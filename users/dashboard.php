<?php 
session_start();
require 'config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Daftar peran yang tidak diizinkan
$not_allowed_roles = ['admin', 'warung_mitra', 'pengelola'];
if (in_array($_SESSION['user']['role'], $not_allowed_roles)) {
    // Jika pengguna memiliki salah satu dari peran yang tidak diizinkan, redirect mereka
    header("Location: page.php?mod=unaut2");
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
        }

        /* Animasi Fade-In */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s ease-in-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Carousel Styling */
        .carousel {
            margin-top: 70px;
        }

        .carousel-item img {
            width: 100%;
            height: 70vh;
            object-fit: cover;
            border-radius: 10px;
        }

        /* Section Styling */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            animation: slideInDown 1s ease;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .lead {
            color: #7f8c8d;
            line-height: 1.8;
            animation: fadeIn 1.2s ease-in-out;
        }

        h3 {
            color: #34495e;
            font-weight: 700;
            margin-top: 40px;
            text-align: center;
            animation: fadeIn 1.5s ease-in-out;
        }

        ol,
        ul {
            color: #7f8c8d;
            padding-left: 20px;
        }

        /* Add a card style */
        section {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            animation: fadeIn 1s ease-in-out;
        }

        /* Button Styling */
        .btn-success {
            font-size: 1.2rem;
            padding: 15px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #28a745;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transform: scale(1.05);
        }

/* Menghilangkan background dan border dari tombol prev dan next */
.carousel-control-prev,
.carousel-control-next {
    background-color: transparent !important; /* Menghapus latar belakang sepenuhnya */
    border: none; /* Menghapus border */
    width: auto; /* Ukuran tombol diatur sesuai ikon */
    height: auto; /* Ukuran tombol diatur sesuai ikon */
    top: 50%;
    transform: translateY(-50%);
}

/* Ikon sebelumnya dan berikut */
.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: transparent !important; /* Menghapus latar belakang sepenuhnya */
    border: none; /* Menghapus border */
}

/* Menampilkan ikon panah tanpa latar belakang */
.carousel-control-prev-icon::before,
.carousel-control-next-icon::before {
    content: '';
    border-style: solid;
    border-width: 2px 2px 0 0;
    display: inline-block;
    padding: 10px;
    border-color: black; /* Ubah warna ikon menjadi hitam legam */
    transform: rotate(135deg); /* Rotasi untuk ikon prev (kiri) */
}

.carousel-control-next-icon::before {
    transform: rotate(-45deg); /* Rotasi untuk ikon next (kanan) */
}

/* Menghapus hover effect dan background sepenuhnya */
.carousel-control-prev:hover,
.carousel-control-next:hover {
    background-color: transparent !important; /* Pastikan tetap transparan */
    opacity: 1; /* Tanpa perubahan opasitas saat hover */
}

/* Menyesuaikan posisi tombol prev dan next */
.carousel-control-prev,
.carousel-control-next {
    opacity: 1; /* Ikon sepenuhnya terlihat */
}




        /* Animasi tambahan untuk hover pada gambar carousel */
        .carousel-item img {
            transition: transform 0.5s ease-in-out;
        }

        .carousel-item img:hover {
            transform: scale(1.05);
        }

        /* Text Container Animation */
        .text-center a {
            animation: bounceIn 1.5s ease;
        }

        @keyframes bounceIn {

            0%,
            20%,
            40%,
            60%,
            80%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body>

    <!-- Include Header -->
    <?php include 'assets/components/header.php'; ?>

    <!-- Carousel Start -->
    <div id="carouselExampleControls" class="carousel slide container" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active fade-in">
                <img src="assets/img/slider1.jpg" class="d-block w-100" alt="Slider Image 1">
            </div>
            <div class="carousel-item fade-in">
                <img src="assets/img/slider2.jpg" class="d-block w-100" alt="Slider Image 2">
            </div>
            <div class="carousel-item fade-in">
                <img src="assets/img/slider3.jpg" class="d-block w-100" alt="Slider Image 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>

        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>

        </button>
    </div>
    <!-- Carousel End -->

    <!-- Content Section Start -->
    <section class="container my-5 fade-in">
        <h2 class="section-title">Apa itu Bank Sampah?</h2>
        <p class="lead">
            Bank Sampah adalah sebuah program pengelolaan sampah yang bertujuan untuk mendaur ulang sampah dengan
            memanfaatkan barang-barang bekas yang masih bernilai ekonomis.
        </p>
        <p class="lead">
            Program ini mendorong masyarakat untuk peduli terhadap lingkungan dan memanfaatkan sampah secara bijak demi
            menjaga kebersihan dan kelestarian lingkungan.
        </p>

        <h3>Cara Kerja Bank Sampah</h3>
        <ol class="fade-in">
            <li>Masyarakat mengumpulkan sampah yang bisa didaur ulang seperti plastik, kertas, dan logam.</li>
            <li>Sampah yang sudah dipilah disetor ke Bank Sampah terdekat.</li>
            <li>Sampah tersebut ditimbang dan dinilai berdasarkan jenis dan beratnya.</li>
            <li>Masyarakat mendapatkan buku tabungan atau aplikasi untuk mencatat saldo dari sampah yang dikumpulkan.
            </li>
            <li>Saldo tersebut dapat ditukar dengan uang, sembako, atau keperluan lainnya.</li>
        </ol>

        <h3>Manfaat Bank Sampah</h3>
        <ul class="fade-in">
            <li><strong>Mengurangi Volume Sampah:</strong> Membantu mengurangi sampah di tempat pembuangan akhir (TPA).
            </li>
            <li><strong>Meningkatkan Pendapatan:</strong> Sampah yang diolah dapat ditukar dengan uang atau barang
                kebutuhan rumah tangga.</li>
            <li><strong>Menciptakan Lapangan Kerja:</strong> Peluang kerja baru dalam pengelolaan sampah.</li>
            <li><strong>Mendorong Kesadaran Lingkungan:</strong> Memperkuat kepedulian masyarakat terhadap lingkungan.
            </li>
        </ul>

        <h3>Kisah Sukses: "Mengubah Sampah Menjadi Emas"</h3>
        <p>
            Pak Suryadi dari Jakarta Barat berhasil merasakan manfaat dari Bank Sampah. Dengan memulai dari usaha kecil,
            kini ia telah memiliki usaha daur ulang sendiri yang mendukung pendidikan anak-anaknya.
        </p>

        <div class="text-center mt-4">
            <a href="?mod=jual" class="btn btn-success btn-lg bounceIn">Mulai Menjual Sampah</a>
        </div>
    </section>
    <!-- Content Section End -->

    <!-- Include Footer -->
    <?php include 'assets/components/footer.php'; ?>

    <!-- Scripts for Bootstrap and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>