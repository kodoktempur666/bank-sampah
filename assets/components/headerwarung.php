<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Header with Bootstrap 4.6</title>
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Navbar Styling */
        .navbar {
            background-color: #1a1a1a;
            padding: 1rem;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        /* Logo Styling */
        .navbar-brand img {
            width: 100px;
            height: auto;
        }

        /* Navbar Links */
        .navbar-nav .nav-link {
            color: white;
            font-size: 1rem;
            padding-left: 15px;
            padding-right: 15px;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link i {
            margin-right: 8px;
        }

        /* Hover Effect */
        .navbar-nav .nav-link:hover {
            color: #63bb65;
        }

        /* Hamburger Icon */
        .navbar-toggler {
            border: none;
            color: #63bb65;
            font-size: 1.5rem;
            outline: none;
            width: 50px;
        }

        /* Fix alignment of navbar elements */
        .navbar-nav {
            align-items: center;
        }

        /* Responsive Styling */
        @media (max-width: 992px) {
            .navbar-collapse {
                background-color: #1a1a1a;
            }

            .navbar-brand img {
                width: 50px;
            }

            .navbar-nav {
                flex-direction: column;
                text-align: center;
            }

            .navbar-nav .nav-link {
                padding: 1rem 0;
                width: 100%;
            }

            /* Position hamburger icon on the right */
            .navbar-toggler {
                position: absolute;
                right: 20px;
                top: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Added sticky-top class to make the navbar sticky -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="assets\img\logo.png" alt="Logo">
        </a>

        <!-- Hamburger button for mobile -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-history"></i> History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-tags"></i> Pencairan saldo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-user"></i>Profile anda</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Bootstrap & JQuery dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
