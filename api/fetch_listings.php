<?php
require_once '../config.php';  // Include your database connection

// Get parameters from the GET request
$type = isset($_GET['type']) ? $_GET['type'] : 'new';  // Default to 'new' if type is empty
$amount = isset($_GET['amount']) && is_numeric($_GET['amount']) ? intval($_GET['amount']) : 0;  // Default to 0 (all listings) if empty
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;  // Default to page 1 if empty
$query = isset($_GET['query']) ? $_GET['query'] : '';  // Optional search query
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : '';  // Optional sort order for price

// Determine the offset for pagination
$offset = ($page - 1) * $amount;

// Base query for fetching listings with store names
$queryString = "
    SELECT l.product_id, l.timestamp, l.mid, l.name, l.description, l.image_url, l.item_range, l.value_range, l.price, l.stock, m.store_name
    FROM listings l
    JOIN merchants m ON l.mid = m.mid
";

// Add filtering based on search query if provided
if (!empty($query)) {
    $queryString .= " WHERE (l.name LIKE :query OR l.description LIKE :query OR m.store_name LIKE :query)";
}

// Sorting and ordering based on 'type' and 'sortOrder'
if (!empty($sortOrder)) {
    // Sort by price if 'sortOrder' is set
    if ($sortOrder === 'lowest') {
        $queryString .= " ORDER BY l.price ASC";
    } elseif ($sortOrder === 'highest') {
        $queryString .= " ORDER BY l.price DESC";
    }
} else {
    // Default ordering if 'sortOrder' is not set
    if ($type === 'popular') {
        // Sort by most popular (most orders)
        $queryString .= " LEFT JOIN (SELECT product_id, COUNT(*) as order_count FROM orders GROUP BY product_id) o 
                          ON l.product_id = o.product_id 
                          ORDER BY o.order_count DESC, l.timestamp DESC";
    } else {
        // Default to 'newest' listings
        $queryString .= " ORDER BY l.timestamp DESC";
    }
}

// Limit and offset for pagination
if ($amount > 0) {
    $queryString .= " LIMIT :limit OFFSET :offset";
}

$stmt = $pdo->prepare($queryString);

// Bind the search query if provided
if (!empty($query)) {
    $searchTerm = '%' . $query . '%';
    $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
}

// Bind limit and offset if amount is specified
if ($amount > 0) {
    $stmt->bindParam(':limit', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
}

$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the response data
$response = [];

foreach ($listings as $listing) {
    $response[] = [
        'product_id'   => $listing['product_id'],
        'timestamp'    => $listing['timestamp'],
        'mid'          => $listing['mid'],
        'name'         => $listing['name'],
        'image_url'    => $listing['image_url'],
        'item_range'   => $listing['item_range'],
        'value_range'  => $listing['value_range'],
        'price'        => number_format($listing['price'] / 100, 2),  // Convert cents to dollars
        'stock'        => $listing['stock'],
        'store_name'   => $listing['store_name'],
        'description'  => $listing['description'],
    ];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
