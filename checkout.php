<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth_check.php';  // Adjust path as necessary
require_once 'includes/initialize_user.php';

// Ensure we have a product ID
$product_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$product_id) {
    header('Location: /search');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="manifest.json" />
    <meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=0">
    <title>Stylete - Checkout</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/assets/js/popup-alert.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <header class="bg-white p-4 flex items-center border-b border-gray-200">
            <a href="/listing?id=<?php echo $product_id; ?>" class="text-purple-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Checkout</h1>
        </header>
        <main class="flex-grow p-4">
            <div class="space-y-6">
                <div id="order-summary" class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">Order Summary</h2>
                    <div id="listing-details">
                        <!-- Listing details will be populated here -->
                    </div>
                </div>
                <form id="checkout-form" class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>
                        <div class="space-y-4">
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" id="address" name="address" required value="<?php echo htmlspecialchars($address ?? ''); ?>" class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div class="flex-1">
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($city ?? ''); ?>" class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                    <select id="state" name="state" required class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select State</option>
                                        <?php
                                        $states = array(
                                            'AL'=>'Alabama', 'AK'=>'Alaska', 'AZ'=>'Arizona', 'AR'=>'Arkansas', 'CA'=>'California',
                                            'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware', 'FL'=>'Florida', 'GA'=>'Georgia',
                                            'HI'=>'Hawaii', 'ID'=>'Idaho', 'IL'=>'Illinois', 'IN'=>'Indiana', 'IA'=>'Iowa',
                                            'KS'=>'Kansas', 'KY'=>'Kentucky', 'LA'=>'Louisiana', 'ME'=>'Maine', 'MD'=>'Maryland',
                                            'MA'=>'Massachusetts', 'MI'=>'Michigan', 'MN'=>'Minnesota', 'MS'=>'Mississippi', 'MO'=>'Missouri',
                                            'MT'=>'Montana', 'NE'=>'Nebraska', 'NV'=>'Nevada', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey',
                                            'NM'=>'New Mexico', 'NY'=>'New York', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio',
                                            'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania', 'RI'=>'Rhode Island', 'SC'=>'South Carolina',
                                            'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 'UT'=>'Utah', 'VT'=>'Vermont',
                                            'VA'=>'Virginia', 'WA'=>'Washington', 'WV'=>'West Virginia', 'WI'=>'Wisconsin', 'WY'=>'Wyoming'
                                        );
                                        foreach($states as $abbr => $stateName) {
                                            $selected = ($state === $abbr) ? 'selected' : '';
                                            echo "<option value=\"$abbr\" $selected>$stateName</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                                    <input type="text" id="zipcode" name="zipcode" required value="<?php echo htmlspecialchars($zipcode ?? ''); ?>" class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Payment Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="card-number" class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                <input type="text" id="card-number" name="card-number" required class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="expiry" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div class="flex-1">
                                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" id="cvv" name="cvv" required class="mt-1 block w-full px-3 py-2 text-base rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Place Order
                    </button>
                </form>
            </div>
        </main>
        <nav class="bg-white border-t border-gray-200 fixed bottom-5 left-0 right-0 max-w-md mx-auto">
            <div class="max-w-md mx-auto flex justify-around">
                <a href="/home" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-xs mt-1">Home</span>
                </a>
                <a href="/search" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="text-xs mt-1">Search</span>
                </a>
                <a href="/favorites" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-xs mt-1">Favorites</span>
                </a>
                <a href="/profile" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-xs mt-1">Profile</span>
                </a>
            </div>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productId = <?php echo json_encode($product_id); ?>;
            const listingDetails = document.getElementById('listing-details');
            const checkoutForm = document.getElementById('checkout-form');

            // Fetch listing details
            fetch(`/api/listing_details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    listingDetails.innerHTML = `
                        <h3 class="font-semibold">${data.name}</h3>
                        <p class="text-sm text-gray-600">Store: ${data.store_name}</p>
                        <p class="text-purple-600 font-bold mt-1">$${(data.price / 100).toFixed(2)}</p>
                        <p class="text-sm text-gray-500">${data.item_range.split(',')[0]}-${data.item_range.split(',')[1]} surprise fashion items</p>
                        <p class="text-sm text-gray-500">Potential retail value: $${data.value_range.split(',')[0]}-$${data.value_range.split(',')[1]}</p>
                    `;
                })
                .catch(error => {
                    console.error('Error fetching listing details:', error);
                    showPopupAlert('Error loading product details. Please try again.', 'error');
                });

            // Handle form submission
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(checkoutForm);
                formData.append('product_id', productId);

                fetch('/api/place_order', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/order-confirmation.php?id=${data.order_id}`;
                    } else {
                        showPopupAlert('Failed to place order. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error placing order:', error);
                    showPopupAlert('An error occurred. Please try again.', 'error');
                });
            });
        });
    </script>
</body>
</html>