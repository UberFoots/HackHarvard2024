<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth_check.php';  // Adjust path as necessary
require_once 'includes/initialize_user.php';  // Initialize user session and data
require_once 'config.php';  // Ensure the database is connected

$uid = $_SESSION['user_id'] ?? null;

// Initialize saved amount
$amount_saved = 0;

try {
    // Fetch all orders related to the logged-in user, including the price paid
    $query = "
        SELECT o.product_id, o.price, l.value_range 
        FROM orders o
        JOIN listings l ON o.product_id = l.product_id 
        WHERE o.uid = :uid";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':uid' => $uid]);

    $orders = $stmt->fetchAll();

    // Sum up the minimum value from each value_range, and subtract the price paid
    foreach ($orders as $order) {
        $value_range = explode(',', $order['value_range']);  // Assuming the value range is like '50,100'
        $min_value = (int)$value_range[0];  // Get the minimum value (first part of the range)

        // Calculate the difference between the minimum value and the price paid, rounding to the nearest dollar
        $amount_spent = (int) round($order['price'] / 100);  // Convert cents to dollars
        $amount_saved += ($min_value - $amount_spent);  // Add the difference to the total saved amount
    }

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="manifest.json" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">
    <title>Stylete - Surplus Fashion</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col">
        <div class="p-4">
            <div class="text-center mb-6">
                <img src="/assets/img/avatar.png?height=150&width=150" alt="Profile Picture" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                <h2 class="text-2xl font-semibold"><?php echo htmlspecialchars($full_name); ?></h2>
            </div>
            <div class="flex justify-around mb-6">
                <div class="text-center">
                    <p class="text-2xl font-bold"><?php echo $order_count; ?></p>
                    <p class="text-sm text-gray-500">Purchases</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold">$<?php echo $amount_saved; ?>+</p> <!-- Display the calculated amount saved -->
                    <p class="text-sm text-gray-500">Saved</p>
                </div>
            </div>
            <div class="space-y-3">
                <a href="/settings" class="block w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors text-center" style="margin-top:10px">Manage Profile</a>
                <a href="/purchases" class="block w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors text-center" style="margin-top:10px">My Purchases</a>
                <a href="/logout" class="block w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition-colors text-center" style="margin-top:10px">Log Out</a>
            </div>
        </div>
    </div>
    <nav class="bg-white border-t border-gray-200 fixed bottom-5 left-0 right-0 max-w-md mx-auto">
        <div class="max-w-md mx-auto flex justify-around">
            <a href="/home" class="flex flex-col items-center py-2 px-3 text-gray-500">
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
            <a href="/profile" class="flex flex-col items-center py-2 px-3 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-xs mt-1">Profile</span>
            </a>
        </div>
    </nav>
</body>
</html>
