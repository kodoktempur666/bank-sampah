<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Defining color variables */
        :root {
            --primary-color: #0077b6;
            --secondary-color: #7ab987;
            --background-color: #1a1a1a;
            --form-bg-color: #f5f5f5;
            --text-color: #333;
            --button-success: #28a745;
            --button-warning: #ffc107;
        }

        /* Body styling */
        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Roboto', sans-serif;
        }

        /* Container styling */
        .container {
            background-color: var(--form-bg-color);
            border-radius: 8px;
            padding: 40px;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
        }

        h2 {
            color: var(--primary-color);
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .btn {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border-radius: 50px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        /* Button hover effect */
        button:focus {
            outline: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Register</h2>
        <form method="POST">
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="location.href='?mod=reg-rumah'">Register Rumah Tangga</button>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="location.href='?mod=reg-warung'">Register Warung Mitra</button>
            </div>
        </form>
    </div>
</body>
</html>
