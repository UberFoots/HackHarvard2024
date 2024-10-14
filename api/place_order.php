<?php
// public_html/api/place_order.php
session_start();
header('Content-Type: application/json');  // Ensure response is in JSON format

require_once '../config.php';  // Database connection and test card array
require_once '../includes/auth_check.php';  // Ensure user is authenticated
require_once '../includes/initialize_user.php';  // Initialize user session and data

// Get user ID from session
$uid = $_SESSION['user_id'] ?? null;

if (!$uid) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get POST data
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$zipcode = trim($_POST['zipcode'] ?? '');
$card_number = trim($_POST['card-number'] ?? '');
$expiry = trim($_POST['expiry'] ?? '');
$cvv = trim($_POST['cvv'] ?? '');
$product_id = intval($_POST['product_id'] ?? 0);

// Validation checks
$errors = [];

// Address validation
if (empty($address) || empty($city) || empty($state) || empty($zipcode)) {
    $errors[] = "Address fields cannot be blank.";
}
if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $address)) {
    $errors[] = "Address contains invalid characters.";
}

// Credit card validation
if (empty($card_number) || !in_array($card_number, $test_cards)) {
    $errors[] = "Invalid or unsupported card number.";
}
if (empty($expiry) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
    $errors[] = "Invalid expiry format. Use MM/YY.";
} else {
    // Check if expiry date is in the future
    $expiry_parts = explode('/', $expiry);
    $expiry_month = intval($expiry_parts[0]);
    $expiry_year = intval('20' . $expiry_parts[1]);

    $current_year = intval(date('Y'));
    $current_month = intval(date('m'));

    if ($expiry_year < $current_year || ($expiry_year == $current_year && $expiry_month < $current_month)) {
        $errors[] = "Credit card expiry date is in the past.";
    }
}

if (empty($cvv) || !preg_match('/^\d{3}$/', $cvv)) {
    $errors[] = "CVV must be 3 digits.";
}

// Product validation
$product_query = "SELECT price, stock FROM listings WHERE product_id = :product_id";
$stmt = $pdo->prepare($product_query);
$stmt->execute([':product_id' => $product_id]);
$product = $stmt->fetch();

if (!$product) {
    $errors[] = "Product does not exist.";
} elseif ($product['stock'] <= 0) {
    $errors[] = "Product is out of stock.";
}

// If there are any errors, return them as JSON and exit
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Reduce stock by 1 for the selected product
$update_stock_query = "UPDATE listings SET stock = stock - 1 WHERE product_id = :product_id";
$stmt = $pdo->prepare($update_stock_query);
$stmt->execute([':product_id' => $product_id]);

// Prepare the address in JSON format
$address_json = json_encode([
    'full_name' => $full_name,
    'address' => $address,
    'city' => $city,
    'state' => $state,
    'zipcode' => $zipcode
]);

// Generate order details
$timestamp = round(microtime(true) * 1000);
$order_id_query = "SELECT MAX(order_id) AS max_order_id FROM orders";
$stmt = $pdo->query($order_id_query);
$row = $stmt->fetch();
$order_id = ($row['max_order_id'] ?? 0) + 1;  // Increment the max order ID by 1
$price = $product['price'];  // Price in cents

// Insert the order into the orders table
$insert_order_query = "INSERT INTO orders (order_id, timestamp, uid, product_id, price, address, review) 
                       VALUES (:order_id, :timestamp, :uid, :product_id, :price, :address, NULL)";
$stmt = $pdo->prepare($insert_order_query);
$order_success = $stmt->execute([
    ':order_id' => $order_id,
    ':timestamp' => $timestamp,
    ':uid' => $uid,
    ':product_id' => $product_id,
    ':price' => $price,
    ':address' => $address_json
]);

// Return success or failure message, and include order_id on success
if ($order_success) {
    echo json_encode(['success' => true, 'message' => 'Order placed successfully.', 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order.']);
}
exit;
