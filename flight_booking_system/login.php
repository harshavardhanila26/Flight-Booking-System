<?php
// /login.php
require_once 'includes/db_connect.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Airline Booking</title>
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
        <a href="register.php">Register</a>
    </div>
</div>

<main class="auth-wrapper">
    <div class="form-container">
        <h2>Login to your Account</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error-msg">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if (isset($_GET['success'])) {
            echo '<p class="success-msg">' . htmlspecialchars($_GET['success']) . '</p>';
        }
        ?>
        <form action="actions/login_action.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</main>

</body>
</html>