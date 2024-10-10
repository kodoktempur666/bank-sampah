<?php
// Pastikan session sudah dimulai dan koneksi ke database sudah di-include
include "config/connect.php";
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: page.php?mod=home");
    exit();
}

// Periksa apakah user adalah pengelola
if ($_SESSION['user']['role'] !== 'pengelola') {
    header("Location: page.php?mod=unaut2");
    exit();
}

// Inisialisasi variabel untuk pencarian
$search_query = '';
$search_results = [];

if (isset($_POST['search_box'])) {
    $search_query = $_POST['search_box'];

    // Query untuk mencari berdasarkan nama rumah tangga
    $query_search = $conn->prepare("SELECT s.*, r.nama, r.rw, r.alamat, r.kontak, js.nama_jenis AS jenis_sampah
                                    FROM sampah s
                                    JOIN rumah_tangga r ON s.id_rumah_tangga = r.id
                                    JOIN jenis_sampah js ON s.id_jenis_sampah = js.id
                                    WHERE r.nama LIKE ? ORDER BY r.nama ASC");

    // Bind parameter dan eksekusi query
    $search_param = "%{$search_query}%";
    $query_search->bind_param('s', $search_param);
    $query_search->execute();
    $result_search = $query_search->get_result();

    // Jika data ditemukan
    if ($result_search->num_rows > 0) {
        while ($row = $result_search->fetch_assoc()) {
            $search_results[] = $row;
        }
    } else {
        $error_message = 'Tidak ada data yang ditemukan untuk pencarian tersebut.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Rumah Tangga</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 300px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        table thead {
            background-color: #28a745;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }


        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        p {
            text-align: center;
            font-size: 18px;
            color: #ff0000;
        }
    </style>
</head>
<body>
    <h1>Pencarian Data Rumah Tangga</h1>

    <!-- Form pencarian -->
    <form method="POST" action="">
        <input type="text" name="search_box" placeholder="Cari nama rumah tangga" value="<?= htmlspecialchars($search_query); ?>" required>
        <button type="submit" name="search_btn">Cari</button>
    </form>

    <!-- Tampilkan hasil pencarian -->
    <?php if (!empty($search_results)): ?>
        <h2>Hasil Pencarian: <?= htmlspecialchars($search_query); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>RW</th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>Jenis Sampah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($search_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['rw']); ?></td>
                        <td><?= htmlspecialchars($row['alamat']); ?></td>
                        <td><?= htmlspecialchars($row['kontak']); ?></td>
                        <td><?= htmlspecialchars($row['jenis_sampah']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($error_message)): ?>
        <p><?= $error_message; ?></p>
    <?php endif; ?>
</body>
</html>
