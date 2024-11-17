<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <style>
        /* General Styling */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #1a1a1a;
            /* Dark navbar color */
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
        }

        .nav-item {
            margin-left: 1rem;
        }

        .nav-link {
            color: #ffffff;
            font-size: 1rem;
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .nav-link i {
            margin-right: 8px;
        }

        /* Hover Effect */
        .nav-link:hover {
            color: #63bb65;
        }

        /* Logout Button */
        .logout-link {
            color: #ffffff;
            font-size: 1rem;
            padding: 0.75rem 1rem;
            text-decoration: none;
            background-color: #ff4c4c;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-link:hover {
            background-color: #ff1a1a;
            color: #ffffff;
        }

        /* Hamburger Icon for Mobile */
        .navbar-toggler {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .navbar-toggler-icon {
            width: 30px;
            height: 3px;
            background-color: #fff;
            margin: 8px 0;
            display: block;

        }

        /* Responsive Styling */
        @media (max-width: 992px) {
            .navbar-nav {
                display: none;
                flex-direction: column;
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
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a href="page.php?mod=search" class="nav-link"><i class="fas fa-search"></i> Cari Penjualan</a>
            </li>
            <li class="nav-item">
                <a href="page.php?mod=pengelola" class="nav-link"><i class="fas fa-home"></i> Daftar Penjualan</a>
            </li>
            <li class="nav-item">
                <a href="page.php?mod=data-penarikan" class="nav-link"><i class="fas fa-database"></i> Data
                    Penarikan</a>
            </li>
            <li class="nav-item">
                <a href="page.php?mod=edit-sampah" class="nav-link"><i class="fas fa-edit"></i> Edit Harga Sampah</a>
            </li>
            <li class="nav-item">
                <a href="page.php?mod=verify" class="nav-link"><i class="fas fa-user-check"></i> Daftar User</a>
            </li>
            <li class="nav-item">
                <a href="page.php?mod=verify-war" class="nav-link"><i class="fas fa-users"></i> Daftar Mitra</a>
            </li>
            <li class="nav-item">
                <a href="page.php?mod=history" class="nav-link"><i class="fas fa-history"></i> History</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>

        <!-- Logout Button -->

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