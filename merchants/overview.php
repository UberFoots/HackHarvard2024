<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if merchant is logged in, if not redirect to login page
if (!isset($_SESSION['merchant_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config.php';  // Database connection

$merchant_id = $_SESSION['merchant_id'];

// Function to fetch merchant statistics
function fetchMerchantStats($pdo, $merchant_id) {
    // Step 1: Get all product IDs owned by this merchant
    $query = "SELECT product_id FROM listings WHERE mid = :mid";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->execute();
    $productIds = $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch as array of product IDs

    if (empty($productIds)) {
        error_log("No products found for merchant ID: $merchant_id");
        return [
            'total_orders' => 0,
            'total_revenue' => 0,
            'active_listings' => 0
        ];
    }

    // Step 2: Get the total number of orders and revenue for the merchant's products
    $placeholders = implode(',', array_fill(0, count($productIds), '?')); // Prepare placeholders for IN clause
    $query = "
        SELECT COUNT(*) as total_orders, COALESCE(SUM(price), 0) as total_revenue
        FROM orders
        WHERE product_id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($productIds); // Pass product IDs as parameters
    $orderStats = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($orderStats === false) {
        error_log("Error fetching order stats for merchant ID: $merchant_id");
        $orderStats = ['total_orders' => 0, 'total_revenue' => 0];
    }

    // Step 3: Get the number of active listings for this merchant
    $query = "SELECT COUNT(*) as active_listings FROM listings WHERE mid = :mid AND stock > 0";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->execute();
    $listingStats = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($listingStats === false) {
        error_log("Error fetching listing stats for merchant ID: $merchant_id");
        $listingStats = ['active_listings' => 0];
    }

    return [
        'total_orders' => $orderStats['total_orders'] ?? 0,
        'total_revenue' => $orderStats['total_revenue'] ?? 0,
        'active_listings' => $listingStats['active_listings'] ?? 0
    ];
}

// Function to fetch recent orders
function fetchRecentOrders($pdo, $merchant_id, $limit = 5) {
    $query = "
        SELECT o.order_id, o.timestamp, o.price, l.name AS product_name, u.full_name AS customer_name
        FROM orders o
        JOIN listings l ON o.product_id = l.product_id
        JOIN users u ON o.uid = u.uid
        WHERE l.mid = :mid
        ORDER BY o.timestamp DESC
        LIMIT :limit
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch the stats
$merchantStats = fetchMerchantStats($pdo, $merchant_id);

// Fetch recent orders
$recentOrders = fetchRecentOrders($pdo, $merchant_id);

// Debug output
error_log("Merchant ID: $merchant_id");
error_log("Merchant Stats: " . print_r($merchantStats, true));
error_log("Recent Orders: " . print_r($recentOrders, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylete - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'stylete-purple': '#8B5CF6',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Include the navigation bar -->
        <?php include 'nav.php'; ?>

        <!-- Main content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Overview</h1>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
                    <!-- Total Orders -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-stylete-purple rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                        <dd class="text-lg font-semibold text-gray-900"><?php echo $merchantStats['total_orders']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-stylete-purple rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Revenue</dt>
                                        <dd class="text-lg font-semibold text-gray-900">$<?php echo number_format($merchantStats['total_revenue'] / 100, 2); ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Listings -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-stylete-purple rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Listings</dt>
                                        <dd class="text-lg font-semibold text-gray-900"><?php echo $merchantStats['active_listings']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Widget -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg leading-6 font-medium text-gray-900">Recent Orders</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Your 5 most recent orders.</p>
                    </div>
                    <div class="border-t border-gray-200">
                        <?php if (empty($recentOrders)): ?>
                            <p class="px-6 py-4 text-sm text-gray-500">No recent orders found.</p>
                        <?php else: ?>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', (int)($order['timestamp'] / 1000)); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['product_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo number_format($order['price'] / 100, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>