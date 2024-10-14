<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth_check.php';  // Adjust path as necessary
require_once '../includes/initialize_user.php';
require_once '../config.php';  // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted rating and order_id
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : null;
    $order_id = isset($_POST['order_id']) ? (int) $_POST['order_id'] : null;

    if ($rating && $order_id) {
        try {
            // Check if the order belongs to the authenticated user and if review is still NULL
            $stmt = $pdo->prepare("SELECT uid, review FROM orders WHERE order_id = :order_id");
            $stmt->execute([':order_id' => $order_id]);
            $order = $stmt->fetch();

            if ($order && $order['uid'] == $_SESSION['user_id']) {
                if (is_null($order['review'])) {
                    // The order belongs to the user and has not been reviewed yet, so update the review
                    $updateStmt = $pdo->prepare("UPDATE orders SET review = :review WHERE order_id = :order_id AND uid = :uid");
                    $updateStmt->execute([
                        ':review' => $rating,
                        ':order_id' => $order_id,
                        ':uid' => $_SESSION['user_id']
                    ]);

                    // Redirect back to the order details page
                    header("Location: /view-order?id=" . $order_id);
                    exit;
                } else {
                    // The order has already been reviewed, deny access
                    header("Location: /purchases?error=already_reviewed");
                    exit;
                }
            } else {
                // Order does not belong to the user, deny access
                header("Location: /purchases");
                exit;
            }

        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        // Redirect back to the review page if there was an issue
        header("Location: /review?id=" . $order_id);
        exit;
    }
} else {
    // Redirect to home if the request is not POST
    header("Location: /home");
    exit;
}
