<?php
// /includes/functions.php

// This function can be called at the top of any page that requires a user to be logged in.
function require_login() {
    // If the session variable for user_id is not set, redirect to login page
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>