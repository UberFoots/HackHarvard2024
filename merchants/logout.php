<?php
// public_html/merchants/logout.php
session_start();

// Unset only the 'merchant_id' session variable
if (isset($_SESSION['merchant_id'])) {
    unset($_SESSION['merchant_id']);
}

// Check if the session is now empty and destroy the session if necessary
if (empty($_SESSION)) {
    session_destroy();

    // Optionally, clear the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Redirect to the merchant login page after logout
header("Location: /merchants/login");
exit;
