<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth_check.php';  // Adjust path as necessary
require_once 'includes/initialize_user.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="manifest.json" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">
    <title>Stylete - View Order</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <header class="bg-white p-4 flex items-center border-b border-gray-200">
            <a href="/purchases" class="text-purple-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-xl font-semibold text-gray-800">View Order</h1>
        </header>
        <main class="flex-grow p-4">
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Order Information</h2>
                            <p id="order-id" class="text-sm text-gray-600">Order ID: <span class="font-medium text-gray-900"></span></p>
                            <p id="order-date" class="text-sm text-gray-600">Order Date: <span class="font-medium text-gray-900"></span></p>
                            <p id="order-total" class="text-sm text-gray-600">Total Amount: <span class="font-medium text-gray-900"></span></p>
                        </div>
                        <a id="review-link"><button id="review-btn" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            Review Order
                        </button></a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">Product Details</h2>
                    <div class="flex items-start">
                        <img id="product-image" src="https://placehold.co/100x100/EEE/31343C" alt="Eco-Friendly Mystery Box" class="w-24 h-24 object-cover rounded-lg mr-4">
                        <div>
                            <h3 id="listing-name" class="font-semibold"></h3>
                            <p id="store-name" class="text-sm text-gray-600"></p>
                            <p id="listing-price" class="text-purple-600 font-bold mt-1"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">Shipping Address</h2>
                    <p id="address" class="text-sm text-gray-600"></p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">Delivery Status</h2>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p id="delivery-status" class="font-semibold text-green-500">Delivered</p>
                            </div>
                        </div>
                        <a href="#"><button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            View Tracking
                        </button></a>
                    </div>
                </div>

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
                <a href="/profile" class="flex flex-col items-center py-2 px-3 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-xs mt-1">Profile</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- JavaScript for loading order details -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const orderId = new URLSearchParams(window.location.search).get('id');
    const orderIdElement = document.getElementById('order-id').querySelector('span');
    const orderDateElement = document.getElementById('order-date').querySelector('span');
    const orderTotalElement = document.getElementById('order-total').querySelector('span');
    const reviewButton = document.getElementById('review-btn');
    const reviewLink = document.getElementById('review-link');

    const productImage = document.getElementById('product-image');
    const listingName = document.getElementById('listing-name');
    const storeName = document.getElementById('store-name');
    const listingPrice = document.getElementById('listing-price');

    const addressElement = document.getElementById('address');

    const deliveryStatus = document.getElementById('delivery-status');

    // Fetch order details from the API
    async function fetchOrderDetails() {
        try {
            const response = await fetch(`/api/my_orders.php`);
            const data = await response.json();

            // Find the specific order by order_id and check if it belongs to the user
            const order = data.orders.find(o => o.order_id == orderId);

            if (order) {
                // Update the HTML elements with the fetched data
                orderIdElement.textContent = `#${order.order_id}`;
                orderDateElement.textContent = new Date(order.timestamp).toLocaleDateString();
                orderTotalElement.textContent = `$${(order.price / 100).toFixed(2)}`;
                productImage.src = order.image_url;
                listingName.textContent = order.listing_name;
                storeName.textContent = `Store: ${order.store_name}`;
                listingPrice.textContent = `$${(order.price / 100).toFixed(2)}`;
                
                // Parse the address string
                const addressObj = JSON.parse(order.address);

                // Destructure address object into individual variables
                const { full_name, address, city, state, zipcode } = addressObj;

                // Update the address section with parsed address information
                addressElement.innerHTML = `
                    <strong>${full_name}</strong><br>
                    ${address}<br>
                    ${city}, ${state} ${zipcode}
                `;

                // Review button logic
                if (order.review !== null) {
                    reviewButton.disabled = true;
                    reviewButton.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    reviewButton.classList.remove('bg-purple-600', 'text-white', 'hover:bg-purple-700');
                    reviewButton.textContent = 'Reviewed';
                    reviewLink.removeAttribute('href');
                } else {
                    reviewLink.href = `/review?id=${order.order_id}`;
                }
            } else {
                // Redirect if order does not exist or does not belong to the user
                window.location.href = '/purchases';
            }
        } catch (error) {
            console.error('Error fetching order details:', error);
            window.location.href = '/purchases';  // Redirect on error
        }
    }

    // Load the order details when the page is loaded
    fetchOrderDetails();
});

    </script>
</body>
</html>
