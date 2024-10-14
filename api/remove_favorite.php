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

// Get the product ID from the GET request
$product_id = intval($_GET['id'] ?? 0);

// Validate the product ID
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
    exit;
}

try {
    // Fetch the user's current favorites from the database
    $query = "SELECT product_ids FROM favorites WHERE uid = :uid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':uid' => $uid]);
    $result = $stmt->fetch();

    // Check if the user has any favorites
    if ($result && !empty($result['product_ids'])) {
        $product_ids = json_decode($result['product_ids'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'error' => 'Failed to parse existing favorites.']);
            exit;
        }

        // Ensure product_ids is an array
        if (!is_array($product_ids)) {
            $product_ids = [];
        }

        // Check if the product ID exists in the user's favorites
        if (!in_array($product_id, $product_ids)) {
            echo json_encode(['success' => false, 'error' => 'Product is not in your favorites.']);
            exit;
        }

        // Remove the product ID from the favorites
        $product_ids = array_filter($product_ids, function($id) use ($product_id) {
            return $id != $product_id;
        });

        // Update the database with the new list of product IDs
        $update_query = "UPDATE favorites SET product_ids = :product_ids WHERE uid = :uid";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            ':product_ids' => json_encode($product_ids),
            ':uid' => $uid
        ]);

        // Return success response in JSON format
        echo json_encode(['success' => true, 'message' => 'Product removed from favorites.']);
    } else {
        // If the user has no favorites or an empty list
        echo json_encode(['success' => false, 'error' => 'No favorites found for the user.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
