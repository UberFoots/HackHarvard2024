<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth_check.php';  // Ensure user is authenticated
require_once '../includes/initialize_user.php';  // Initialize user session and data

$uid = $_SESSION['user_id'];  // User ID from session

require_once '../config.php';  // Database connection

try {
    // Begin a transaction to ensure all queries execute successfully or none at all
    $pdo->beginTransaction();

    // Delete from orders table where uid matches
    $deleteOrdersQuery = $pdo->prepare("DELETE FROM orders WHERE uid = :uid");
    $deleteOrdersQuery->bindParam(':uid', $uid);
    $deleteOrdersQuery->execute();

    // Delete from favorites table where uid matches
    $deleteFavoritesQuery = $pdo->prepare("DELETE FROM favorites WHERE uid = :uid");
    $deleteFavoritesQuery->bindParam(':uid', $uid);
    $deleteFavoritesQuery->execute();

    // Delete user from users table
    $deleteUserQuery = $pdo->prepare("DELETE FROM users WHERE uid = :uid");
    $deleteUserQuery->bindParam(':uid', $uid);
    $deleteUserQuery->execute();

    // Commit the transaction
    $pdo->commit();

    // Destroy the user session after account deletion
    session_destroy();

    // Redirect to /auth after successful deletion
    echo '<script>window.location.href = "/auth";</script>';
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $pdo->rollBack();

    // Display an alert with the error message
    echo '<script>alert("Failed to delete account: ' . addslashes($e->getMessage()) . '"); window.history.back();</script>';
}
?>
