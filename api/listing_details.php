<?php
require_once '../config.php';  // Use the config file for DB connection


// Set the Content-Type header to JSON
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Product ID not provided']);
    exit;
}

$product_id = intval($_GET['id']);

try {
    // Fetch listing details
    $stmt = $pdo->prepare('SELECT * FROM listings WHERE product_id = :product_id');
    $stmt->execute(['product_id' => $product_id]);
    $listing = $stmt->fetch();

    if (!$listing) {
        echo json_encode(['error' => 'Listing not found']);
        exit;
    }

    // Fetch and calculate the average rating for this product from the orders table
    $reviewStmt = $pdo->prepare('SELECT review FROM orders WHERE product_id = :product_id AND review IS NOT NULL');
    $reviewStmt->execute(['product_id' => $product_id]);
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_COLUMN);

    $averageRating = null;
    $totalReviews = count($reviews);
    if ($totalReviews > 0) {
        $averageRating = round(array_sum($reviews) / $totalReviews, 1);  // Average to nearest tenth
    }

    // Prepare response
    $response = [
        'product_id' => $listing['product_id'],
        'name' => $listing['name'],
        'store_name' => $pdo->query("SELECT store_name FROM merchants WHERE mid = {$listing['mid']}")->fetchColumn(),
        'image_url' => $listing['image_url'],
        'item_range' => $listing['item_range'],
        'value_range' => $listing['value_range'],
        'price' => $listing['price'],
        'stock' => $listing['stock'],
        'description' => $listing['description'],
        'average_rating' => $averageRating,
        'total_reviews' => $totalReviews
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
