<?php
session_start();
require 'config/connect.php';

$message = []; // Initialize message array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = ($_POST['password']); 

    // Cek dari tabel rumah_tangga
    $query1 = "SELECT * FROM rumah_tangga WHERE username='$username' AND password='$password' AND is_verified=1";
    $result1 = mysqli_query($conn, $query1);

    // Cek dari tabel pengelola_sampah
    $query2 = "SELECT * FROM pengelola_sampah WHERE username='$username' AND password='$password' AND is_verified=1";
    $result2 = mysqli_query($conn, $query2);

    // Cek dari tabel warung_mitra
    $query3 = "SELECT * FROM warung_mitra WHERE username='$username' AND password='$password' AND is_verified=1";
    $result3 = mysqli_query($conn, $query3);

    // Cek dari tabel admin
    $query4 = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result4 = mysqli_query($conn, $query4);

    if (mysqli_num_rows($result1) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result1);
        header("Location: page.php?mod=users");
    } elseif (mysqli_num_rows($result2) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result2);
        header("Location: page.php?mod=pengelola");
    } elseif (mysqli_num_rows($result3) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result3);
        header("Location: page.php?mod=warung");
    } elseif (mysqli_num_rows($result4) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result4);
        header("Location: page.php?mod=admin");
    } else {
        $message[] = 'Username atau password salah';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">
                <img src="assets/img/logo.png" alt="logo">
            </div>
        </div>

        <section class="box">
            <div class="form-container">
                <h2>Selamat Datang di Bank Sampah Desa Salem</h2>
                <form action="" method="POST">
                    <div class="input-group mb-3">
                        <label for="username">Username : </label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="input-group mb-3">
                        <label for="password">Password : </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <!-- Display Error Messages -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($message as $msg): ?>
                                <p><?php echo $msg; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success btn-block">Login</button>
                    <p class="text-center text-light mt-3">Belum punya akun?</p>
                    <button type="button" class="btn btn-warning btn-block"
                        onclick="location.href='?mod=register'">Register</button>
                </form>
            </div>
        </section>
    </div>

    <script>
        function createRandomGlowingBall() {
            const ball = document.createElement('div');
            ball.classList.add('glow-ball');

            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight;

            ball.style.left = `${x}px`;
            ball.style.top = `${y}px`;

            document.body.appendChild(ball);

            setTimeout(() => {
                ball.remove();
            }, 5000);
        }

        setInterval(createRandomGlowingBall, 1000);
    </script>

</body>
</html>
