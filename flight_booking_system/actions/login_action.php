<?php
// /actions/login_action.php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=Email and password are required.");
        exit();
    }

    // --- Fetch user from database ---
    $sql = "SELECT user_id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // --- Verify the password ---
        if (password_verify($password, $user['password'])) {
            // Password is correct, start the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the main dashboard
            header("Location: ../dashboard.php");
            exit();
        } else {
            // Incorrect password
            header("Location: ../login.php?error=Invalid email or password.");
            exit();
        }
    } else {
        // No user found with that email
        header("Location: ../login.php?error=Invalid email or password.");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>