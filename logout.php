<?php
// public_html/api/logout.php
session_start();

// Unset all session variables except 'merchant_id'
foreach ($_SESSION as $key => $value) {
    if ($key !== 'merchant_id') {
        unset($_SESSION[$key]);
    }
}

// Optionally, you can destroy the session if no other session variables remain except 'merchant_id'
if (count($_SESSION) === 1 && isset($_SESSION['merchant_id'])) {
    // Do not destroy the session, but remove everything else
} elseif (empty($_SESSION)) {
    // If the session is completely empty, destroy it
    session_destroy();

    // Optionally, you can also clear the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Redirect to the customer login page after logout
header("Location: /auth");
exit;
