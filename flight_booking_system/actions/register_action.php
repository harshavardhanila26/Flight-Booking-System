<?php
// /actions/register_action.php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // --- Basic Validation ---
    if (empty($username) || empty($email) || empty($password)) {
        header("Location: ../register.php?error=All fields are required.");
        exit();
    }

    // --- Check if email already exists ---
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: ../register.php?error=Email already registered.");
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // --- Hash the password for security ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- Insert new user into database ---
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        // Redirect to login page with a success message
        header("Location: ../login.php?success=Registration successful. Please log in.");
    } else {
        // Redirect with a generic error
        header("Location: ../register.php?error=Something went wrong. Please try again.");
    }

    $stmt->close();
    $conn->close();
}
?>