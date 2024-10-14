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
    // Check if the product ID exists in the listings table
    $product_check_query = "SELECT COUNT(*) FROM listings WHERE product_id = :product_id";
    $stmt = $pdo->prepare($product_check_query);
    $stmt->execute([':product_id' => $product_id]);
    $product_exists = $stmt->fetchColumn();

    if (!$product_exists) {
        echo json_encode(['success' => false, 'error' => 'Product does not exist.']);
        exit;
    }

    // Fetch the user's current favorites from the database
    $query = "SELECT product_ids FROM favorites WHERE uid = :uid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':uid' => $uid]);
    $result = $stmt->fetch();

    // Initialize an empty array for product IDs
    $product_ids = [];

    if ($result) {
        // Decode existing product IDs if they exist
        $product_ids = json_decode($result['product_ids'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'error' => 'Failed to parse existing favorites.']);
            exit;
        }

        // Ensure product_ids is an array
        if (!is_array($product_ids)) {
            $product_ids = [];
        }

        // Check if the product ID is already in the list
        if (in_array($product_id, $product_ids)) {
            echo json_encode(['success' => false, 'error' => 'Product is already in your favorites.']);
            exit;
        }

        // Add the new product ID
        $product_ids[] = $product_id;

        // Update the database with the new list of product IDs as a JSON array
        $update_query = "UPDATE favorites SET product_ids = :product_ids WHERE uid = :uid";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            ':product_ids' => json_encode($product_ids, JSON_NUMERIC_CHECK),  // Store as a JSON array with numbers
            ':uid' => $uid
        ]);
    } else {
        // If no favorites exist, create a new entry
        $product_ids[] = $product_id;
        $insert_query = "INSERT INTO favorites (uid, product_ids) VALUES (:uid, :product_ids)";
        $stmt = $pdo->prepare($insert_query);
        $stmt->execute([
            ':uid' => $uid,
            ':product_ids' => json_encode($product_ids, JSON_NUMERIC_CHECK)  // Store as a JSON array with numbers
        ]);
    }

    // Return success response in JSON format
    echo json_encode(['success' => true, 'message' => 'Product added to favorites successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
