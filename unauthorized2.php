<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff9966, #ff5e62);
            height: 100vh;
            margin: 0;
        }

        .wrapper {
            height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 50%;
            text-align: center;
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: #ff5e62;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #ff9966;
        }

        .icon {
            font-size: 3rem;
            color: #ff5e62;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Jangan masukkan header.php jika tidak ingin navbar -->
    <?php include 'assets/components/header.php'; ?>
    <!-- Jika header.php harus dimasukkan, tambahkan logika di dalamnya untuk tidak menampilkan navbar pada halaman ini. -->
    
    <div class="wrapper">
        <div class="container">
            <div class="icon">🚫</div>
            <h1>Akses Ditolak</h1>
            <p>Maaf, Anda tidak diizinkan masuk</p>
            <a href="logout.php" class="btn">LOGOUT</a>           
        </div>
    </div>

</body>

</html>
