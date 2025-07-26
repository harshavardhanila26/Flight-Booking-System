<?php
// /index.php
require_once 'includes/db_connect.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // If logged in, redirect to the dashboard
    header("Location: dashboard.php");
} else {
    // If not logged in, redirect to the login page
    header("Location: login.php");
}
exit();
?>