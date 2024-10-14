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
    // Fetch the product IDs from the favorites table
    $query = "SELECT product_ids FROM favorites WHERE uid = :uid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':uid' => $uid]);
    $result = $stmt->fetch();

    // Check if favorites exist for the user
    if ($result && !empty($result['product_ids'])) {
        $product_ids = json_decode($result['product_ids'], true);

        // Ensure it is a valid JSON array before returning
        if (json_last_error() === JSON_ERROR_NONE) {
            echo json_encode(['success' => true, 'favorites' => $product_ids]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to parse favorites.']);
        }
    } else {
        // Return an empty array if no favorites found
        echo json_encode(['success' => true, 'favorites' => []]);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
