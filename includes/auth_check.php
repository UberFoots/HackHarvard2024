<?php
session_start();

// Debugging: Check if session variables are set
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: /auth");
    exit;
}

// Optional: Debugging output to ensure the user is logged in correctly
// Uncomment the following lines if you want to check session values during testing
/*
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "User Name: " . $_SESSION['user_name'] . "<br>";
echo "User Email: " . $_SESSION['user_email'] . "<br>";
*/
?>
