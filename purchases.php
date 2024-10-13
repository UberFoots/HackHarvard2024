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
    <title>Stylete - My Purchases</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <main class="flex-grow p-4 pt-8">
            <header class="flex items-center mb-4">
                <a href="/profile" class="text-purple-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold">My Purchases</h1>
            </header>
            
            <!-- Purchases List -->
            <div id="purchases-container" class="space-y-4">
                <!-- Purchases will be loaded dynamically here -->
            </div>

            <!-- No Purchases Message -->
            <div id="no-purchases-message" class="hidden text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <p class="text-gray-600 text-lg font-medium">No purchases yet</p>
                <p class="text-gray-500 mt-2">Start shopping to see your purchases here!</p>
                <a href="/search" class="inline-block mt-4 px-6 py-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors">
                    Browse Products
                </a>
            </div>

            <!-- Pagination Controls -->
            <div id="pagination-controls" class="flex justify-end mt-4">
                <button id="prev-btn" class="px-4 py-2 bg-gray-300 text-white rounded-md hidden">Previous</button>
                <button id="next-btn" class="px-4 py-2 bg-purple-600 text-white rounded-md">Next</button>
            </div>
        </main>

        <!-- Navigation Bar -->
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
    </div>

    <!-- JavaScript for loading purchases and pagination -->
    <script>
        let currentPage = 1;
        const purchasesPerPage = 4;  // Set the number of purchases per page
        const purchasesContainer = document.getElementById('purchases-container');
        const noPurchasesMessage = document.getElementById('no-purchases-message');
        const prevButton = document.getElementById('prev-btn');
        const nextButton = document.getElementById('next-btn');
        const paginationControls = document.getElementById('pagination-controls');

        // Function to fetch purchases from the API
        async function fetchPurchases(page = 1) {
            const response = await fetch(`/api/my_orders.php`);
            const data = await response.json();

            // Sort the orders by timestamp in descending order (newest first)
            data.orders.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

            // Pagination logic handled here
            const startIndex = (page - 1) * purchasesPerPage;
            const endIndex = page * purchasesPerPage;
            const paginatedData = data.orders.slice(startIndex, endIndex);

            return {
                orders: paginatedData,
                totalOrders: data.total_orders
            };
        }

        // Function to render purchases
        function renderPurchases(orders) {
            purchasesContainer.innerHTML = '';  // Clear the container

            if (orders.length === 0) {
                noPurchasesMessage.classList.remove('hidden');
                purchasesContainer.classList.add('hidden');
                paginationControls.classList.add('hidden');
            } else {
                noPurchasesMessage.classList.add('hidden');
                purchasesContainer.classList.remove('hidden');
                paginationControls.classList.remove('hidden');

                orders.forEach(order => {
                    const purchaseItem = `
                        <a href="/view-order?id=${order.order_id}" class="block">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4 flex">
                                <img src="${order.image_url}" alt="${order.listing_name}" class="w-24 h-24 object-cover rounded-lg mr-4">
                                <div class="flex-grow">
                                    <h3 class="font-semibold">${order.listing_name}</h3>
                                    <p class="text-sm text-gray-600">Brand: ${order.store_name}</p>
                                    <p class="text-purple-600 font-bold mt-1">$${(order.price / 100).toFixed(2)}</p>
                                    <p class="text-sm text-gray-500">Purchased: ${new Date(order.timestamp).toLocaleDateString()}</p>
                                </div>
                            </div>
                        </a>
                    `;
                    purchasesContainer.innerHTML += purchaseItem;  // Add the item to the container
                });
            }
        }

        // Load and render purchases with pagination
        async function loadPurchases(page = 1) {
            const { orders, totalOrders } = await fetchPurchases(page);

            renderPurchases(orders);

            if (totalOrders > 0) {
                // Hide "Previous" button on the first page
                if (page === 1) {
                    prevButton.style.display = 'none';
                    paginationControls.classList.remove('justify-between');  // Remove spacing between buttons
                    paginationControls.classList.add('justify-end');  // Align "Next" button to the right
                } else {
                    prevButton.style.display = 'inline-block';  // Show the "Previous" button
                    paginationControls.classList.remove('justify-end');  // Remove right alignment
                    paginationControls.classList.add('justify-between');  // Align both buttons

                    // Ensure "Previous" button is purple when enabled
                    prevButton.disabled = false;
                    prevButton.classList.remove('bg-gray-300', 'text-gray-500');
                    prevButton.classList.add('bg-purple-600', 'text-white');
                }

                // Disable or enable the "Next" button based on page and total orders
                nextButton.disabled = (page * purchasesPerPage) >= totalOrders;
                nextButton.classList.toggle('bg-gray-300', nextButton.disabled);
                nextButton.classList.toggle('text-gray-500', nextButton.disabled);
                nextButton.classList.toggle('bg-purple-600', !nextButton.disabled);
                nextButton.classList.toggle('text-white', !nextButton.disabled);
            }
        }

        // Event listeners for pagination
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                loadPurchases(currentPage);
            }
        });

        nextButton.addEventListener('click', () => {
            currentPage++;
            loadPurchases(currentPage);
        });

        // Load the initial purchases
        document.addEventListener('DOMContentLoaded', () => {
            loadPurchases();
        });
    </script>
</body>
</html>