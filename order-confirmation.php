<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth_check.php';  // Adjust path as necessary
require_once 'includes/initialize_user.php';
require_once 'config.php';  // Include the database connection

// Check if order ID is provided
$order_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$order_id) {
    header('Location: /home');
    exit;
}

// Function to get order details and verify ownership
function getOrderDetails($pdo, $order_id, $uid) {
    $query = "SELECT o.order_id, o.timestamp, o.price, o.address, l.name AS items
              FROM orders o
              JOIN listings l ON o.product_id = l.product_id
              WHERE o.order_id = :order_id AND o.uid = :uid
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':order_id' => $order_id, ':uid' => $uid]);

    return $stmt->fetch();
}

// Fetch the order details for the logged-in user
$order = getOrderDetails($pdo, $order_id, $_SESSION['user_id']);

if (!$order) {
    header('Location: /home');
    exit;
}

// Convert timestamp to readable date
$order_date = date('Y-m-d', intval($order['timestamp'] / 1000));  // Divide by 1000 to convert from milliseconds to seconds

// Decode the JSON address into an associative array
$address = json_decode($order['address'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="manifest.json" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">
    <title>Stylete - Order Confirmation</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <header class="bg-white p-4 flex items-center border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800 mx-auto">Order Confirmation</h1>
        </header>
        <main class="flex-grow p-4">
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Thank You for Your Order!</h2>
                    <p class="text-gray-600">Your order has been successfully placed.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">Order Summary</h2>
                    <p class="text-sm text-gray-600">Order ID: <span class="font-medium text-gray-900">#<?php echo htmlspecialchars($order['order_id']); ?></span></p>
                    <p class="text-sm text-gray-600">Order Date: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($order_date); ?></span></p>
                    <p class="text-sm text-gray-600">Total Amount: <span class="font-medium text-gray-900">$<?php echo number_format($order['price'] / 100, 2); ?></span></p>
                    <p class="text-sm text-gray-600 mt-2">Items: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($order['items']); ?></span></p>
                    <p class="text-sm text-gray-600 mt-2">Shipping Address:</p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($address['full_name']); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($address['address']); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($address['city'] . ', ' . $address['state'] . ' ' . $address['zipcode']); ?></p>
                </div>

                <div class="text-center">
                    <a href="/home" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                        Return to Home
                    </a>
                </div>
            </div>
        </main>
        <nav class="bg-white border-t border-gray-200 fixed bottom-5 left-0 right-0 max-w-md mx-auto">
            <div class="max-w-md mx-auto flex justify-around">
                <a href="/home" class="flex flex-col items-center py-2 px-3 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-xs mt-1">Home</span>
                </a>
                <a href="/search" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-xs mt-1">Search</span>
                </a>
                <a href="/favorites" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="text-xs mt-1">Favorites</span>
                </a>
                <a href="/profile" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs mt-1">Profile</span>
                </a>
            </div>
        </nav>
    </div>
</body>
</html>
