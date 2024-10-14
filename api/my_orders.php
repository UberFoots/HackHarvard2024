<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth_check.php';  // Ensure user is authenticated
require_once '../includes/initialize_user.php';  // Initialize user session and data

$uid = $_SESSION['user_id'];  // User ID from session

require_once '../config.php';  // Ensure the database is connected

// Set the Content-Type header to JSON
header('Content-Type: application/json');

try {
    // Get all the orders for the user
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.timestamp, o.product_id, o.price, o.address, o.review,
               l.name AS listing_name, l.image_url, m.store_name
        FROM orders o
        JOIN listings l ON o.product_id = l.product_id
        JOIN merchants m ON l.mid = m.mid
        WHERE o.uid = :uid
        ORDER BY o.timestamp ASC
    ");
    $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the orders as JSON
    echo json_encode([
        'orders' => $orders,
        'total_orders' => count($orders)
    ]);

} catch (PDOException $e) {
    http_response_code(500);  // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
