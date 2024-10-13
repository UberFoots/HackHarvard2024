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
    <meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=0">
    <title>Stylete - Blind Box Listing</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/assets/js/popup-alert.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <header class="bg-white p-4 flex items-center border-b border-gray-200">
            <a href="/search" class="text-purple-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Listing Details</h1>
        </header>
        <main class="flex-grow p-4">
            <div class="space-y-4">
                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <div class="relative">
                        <img id="listing-image" src="https://placehold.co/300x300/EEE/31343C" alt="Loading..." class="w-full h-64 object-cover rounded-lg mb-4">
                        <div id="stock-indicator" class="absolute top-4 right-4 bg-purple-600 text-white px-2 py-1 rounded-md text-sm font-semibold">
                            Loading...
                        </div>
                    </div>
                    <div>
                        <h3 id="listing-name" class="font-semibold text-xl">Loading...</h3>
                        <p id="store-name" class="text-sm text-gray-600">Store: Loading...</p>
                        <p id="listing-price" class="text-purple-600 font-bold mt-1 text-lg">$0.00</p>
                        <p id="item-range" class="text-sm text-gray-500 mt-2">Loading...</p>
                        <div class="flex items-center mt-2">
                            <span class="rating-stars text-yellow-500 mr-1">☆☆☆☆☆</span>
                            <span class="rating-number text-sm text-gray-600">(Loading... reviews)</span>
                        </div>
                        <p id="description" class="text-sm text-gray-700 mt-4">
                            Loading description...
                        </p>
                        <div class="flex mt-4 space-x-2">
                            <a href="#" id="purchase-button" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 transition-colors text-center">
                                Purchase
                            </a>
                            <button id="favorite-button" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm hover:bg-gray-300 transition-colors">
                                Add to Favorites
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h4 class="font-semibold mb-2">What's Inside?</h4>
                    <ul id="details-list" class="list-disc list-inside text-sm text-gray-700">
                        <li>Loading...</li>
                    </ul>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productId = new URLSearchParams(window.location.search).get('id');
            let stock = 0; // Add this line to keep track of stock
            const favoriteButton = document.getElementById('favorite-button');
            let isFavorite = false;

            if (!productId) {
                window.location.href = '/search';  // Redirect if no product ID
                return;
            }

            // Function to update favorite button
            function updateFavoriteButton() {
                if (isFavorite) {
                    favoriteButton.textContent = 'Remove from Favorites';
                    favoriteButton.classList.remove('bg-gray-200', 'text-gray-800', 'hover:bg-gray-300');
                    favoriteButton.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600');
                } else {
                    favoriteButton.textContent = 'Add to Favorites';
                    favoriteButton.classList.remove('bg-red-500', 'text-white', 'hover:bg-red-600');
                    favoriteButton.classList.add('bg-gray-200', 'text-gray-800', 'hover:bg-gray-300');
                }
            }

            // Fetch the listing details from the API
            fetch(`/api/listing_details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    // Update the listing details dynamically
                    document.getElementById('listing-image').src = data.image_url;
                    document.getElementById('listing-name').textContent = data.name;
                    document.getElementById('store-name').textContent = `Store: ${data.store_name}`;
                    document.getElementById('listing-price').textContent = `$${(data.price / 100).toFixed(2)}`;
                    document.getElementById('item-range').textContent = `${data.item_range.split(',')[0]}-${data.item_range.split(',')[1]} surprise fashion items`;
                    document.getElementById('description').textContent = data.description;
                    document.getElementById('details-list').innerHTML = `
                        <li>${data.item_range.split(',')[0]}-${data.item_range.split(',')[1]} surprise fashion items</li>
                        <li>Potential retail value: $${data.value_range.split(',')[0]}-$${data.value_range.split(',')[1]}</li>
                    `;

                    // Stock indicator
                    const stockIndicator = document.getElementById('stock-indicator');
                    stockIndicator.textContent = `${data.stock} left`;
                    if (data.stock <= 0) {
                        stockIndicator.classList.replace('bg-purple-600', 'bg-red-600');
                        stockIndicator.textContent = 'Out of Stock';
                    }

                    stock = data.stock; // Update the stock variable

                    const purchaseButton = document.getElementById('purchase-button');
                    purchaseButton.href = `/checkout?id=${productId}`;
                    if (data.stock <= 0) {
                        purchaseButton.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                        purchaseButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                        purchaseButton.removeAttribute('href');
                    } else {
                        purchaseButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        purchaseButton.classList.add('bg-purple-600', 'hover:bg-purple-700');
                    }


                    // Display average rating (if available)
                    if (data.average_rating !== null) {
                        const starRating = Math.floor(data.average_rating);  // Rounded down to nearest whole number
                        const starsHtml = '★'.repeat(starRating) + '☆'.repeat(5 - starRating);  // 5 stars max
                        document.querySelector('.rating-stars').innerHTML = starsHtml;

                        // Singular or plural "review(s)" logic
                        const reviewText = data.total_reviews === 1 
                            ? `${data.average_rating.toFixed(1)} - 1 review`
                            : `${data.average_rating.toFixed(1)} - ${data.total_reviews} reviews`;

                        document.querySelector('.rating-number').textContent = reviewText;
                    } else {
                        document.querySelector('.rating-stars').innerHTML = '☆☆☆☆☆';  // 0 stars if no reviews
                        document.querySelector('.rating-number').textContent = 'No reviews yet';
                    }

                    // Check if the product is in favorites
                    return fetch('/api/favorites');
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.favorites.includes(parseInt(productId))) {
                        isFavorite = true;
                    }
                    updateFavoriteButton();
                })
                .catch(error => {
                    console.error('Error fetching listing details:', error);
                    window.location.href = '/search';  // Redirect on error
                });

            // Favorite button functionality
            favoriteButton.addEventListener('click', function() {
                const endpoint = isFavorite ? '/api/remove_favorite' : '/api/add_favorite';
                fetch(`${endpoint}?id=${productId}`, { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            isFavorite = !isFavorite;
                            updateFavoriteButton();
                            showPopupAlert(isFavorite ? 'Added to favorites!' : 'Removed from favorites!', 'success');
                        } else {
                            showPopupAlert('Failed to update favorites.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating favorites:', error);
                        showPopupAlert('An error occurred.', 'error');
                    });
            });
        });
    </script>
</body>
</html>