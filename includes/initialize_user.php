<?php
// public_html/includes/initialize_user.php

// Only start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Correct the path to config.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php'; 

// Check if the user is logged in by verifying if the session variable user_id is set
if (isset($_SESSION['user_id'])) {
    // Fetch user information from the database based on the user_id in the session
    $query = "SELECT full_name, email, address, city, state, zipcode FROM users WHERE uid = :uid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':uid' => $_SESSION['user_id']]);
    
    $user = $stmt->fetch();

    // If the user exists, initialize variables
    if ($user) {
        $full_name = $user['full_name'];
        $email = $user['email'];
        $address = $user['address'];
        $city = $user['city'];
        $state = $user['state'];
        $zipcode = $user['zipcode'];
    } else {
        // If no user found, clear session and redirect to login
        session_unset();
        session_destroy();
        header("Location: /auth");
        exit;
    }

    // Fetch user's orders from the orders table based on user_id
    $order_query = "SELECT order_id, timestamp, product_id, price, address, review FROM orders WHERE uid = :uid";
    $order_stmt = $pdo->prepare($order_query);
    $order_stmt->execute([':uid' => $_SESSION['user_id']]);
    $orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count the number of orders
    $order_count = count($orders);

} else {
    // If not logged in, redirect to the login page
    header("Location: /auth");
    exit;
}
?>