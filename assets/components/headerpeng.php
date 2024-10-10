<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <style>
            /* General Navbar Styling */
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            .navbar {
                background-color: #1a1a1a;
                padding: 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            /* Logo Styling */
            .navbar-brand img {
                width: 100px;
                height: auto;
            }

            /* Navbar Links */
            .navbar-nav {
                display: flex;
                list-style: none;
                margin: 0;
                padding: 0;
                flex-direction: row;
                /* Menu diatur horizontal */
            }

            .navbar-nav .nav-item {
                margin-left: 1rem;
            }

            .navbar-nav .nav-link {
                color: white;
                font-size: 1rem;
                padding: 0.75rem 1rem;
                text-decoration: none;
                display: flex;
                align-items: center;
                transition: color 0.3s ease;
            }

            .navbar-nav .nav-link i {
                margin-right: 8px;
            }

            /* Hover Effect */
            .navbar-nav .nav-link:hover {
                color: #63bb65;
            }

            /* Hamburger Icon for Mobile */
            .navbar-toggler {
                display: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                background: none;
                border: none;
            }

            .navbar-toggler-icon {
                width: 30px;
                height: 3px;
                background-color: #fff;
                display: block;
                margin: 5px 0;
            }

            /* Responsive Styling */
            @media (max-width: 992px) {
                .navbar-nav {
                    display: none;
                    flex-direction: column;
                    /* Menu berubah vertikal di layar kecil */
                    background-color: #404040;
                    position: absolute;
                    top: 100%;
                    left: 0;
                    width: 100%;
                    padding: 1rem;
                    text-align: center;
                }

                .navbar-nav.active {
                    display: flex;
                }

                .navbar-brand img {
                    width: 50px;
                }

                .navbar-nav .nav-link {
                    padding: 1rem 0;
                    width: 100%;
                }

                .navbar-toggler {
                    display: block;
                }
            }
        </style>
    </head>

    <body>

        <nav class="navbar">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <img src="assets/img/logo.png" alt="Logo">
            </a>

            <!-- Hamburger button for mobile -->
            <button class="navbar-toggler" onclick="toggleMenu()">
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->

        </a>
            <!-- Navigation Menu -->
            <ul class="navbar-nav" id="navbarNav">
                <li class="nav-item">
                    <a href="page.php?mod=search" class="btn btn-warning mt-3">Cari</a>
                </li>
                <li class="nav-item">
                    <a href="page.php?mod=pengelola" class="btn btn-warning mt-3">Home</a>
                <li class="nav-item">
                    <a href="page.php?mod=data-penarikan" class="btn btn-warning mt-3">Data Penarikan</a>
                </li>
                <li class="nav-item">
                    <a href="page.php?mod=edit-sampah" class="btn btn-warning mt-3">Edit Harga Sampah</a>
                </li>
                <li class="nav-item">
                    <a href="page.php?mod=verify" class="btn btn-warning mt-3">Daftar user</a>
                </li>
                <li class="nav-item">
                    <a href="page.php?mod=verify-war" class="btn btn-warning mt-3">Daftar Mitra</a>
                </li>
                <li class="nav-item">
                    <a href="page.php?mod=history" class="btn btn-warning mt-3">History</a>
                </li>
            </ul>



        </nav>

        <script>
            // Toggle the mobile menu when the hamburger button is clicked
            function toggleMenu() {
                const navbarNav = document.getElementById('navbarNav');
                navbarNav.classList.toggle('active');
            }
        </script>

        <!-- FontAwesome for icons -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    </body>

    </html>
</body>

</html>