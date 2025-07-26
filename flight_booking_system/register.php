<?php
// /register.php
require_once 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Airline Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-image: url('assets/images/airplane_background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 70px);
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .navbar {
            background-color: #76717128; /* Changed background color as requested */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo img {
            height: 45px;
        }
        .logo span {
            font-size: 24px;
            font-weight: bold;
            color: #fff; /* Changed text to white for better contrast */
        }
        .nav-links a {
            color: #fff; /* Changed link to white for better contrast */
            text-decoration: none;
            font-size: 18px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <img src="assets/images/logo.jpg" alt="SkyBooker Logo">
        <span>SkyBooker</span>
    </div>
    <div class="nav-links">
        <a href="login.php">Login</a>
    </div>
</div>

<main class="auth-wrapper">
    <div class="form-container">
        <h2>Create an Account</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error-msg">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>
        <form action="actions/register_action.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
    </div>
</main>

</body>
</html>