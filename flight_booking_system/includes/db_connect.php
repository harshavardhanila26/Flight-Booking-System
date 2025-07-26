<?php
// /includes/db_connect.php

// --- Database Credentials ---
$servername = "localhost"; // Or your server IP
$username = "root";        // Default XAMPP username
$password = "";            // Default XAMPP password
$dbname = "airline_db";    // The database name you created

// --- Create Connection ---
$conn = new mysqli($servername, $username, $password, $dbname);

// --- Check Connection ---
if ($conn->connect_error) {
    // Stop the script and display connection error
    die("Connection failed: " . $conn->connect_error);
}

// --- Start Session ---
// This is needed on every page to track logged-in users
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>