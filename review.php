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
    <title>Stylete - Write a Review</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <header class="bg-white p-4 flex items-center border-b border-gray-200">
            <a href="/order-details" class="text-purple-600 mr-4" id="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Leave a Review</h1>
        </header>
        <main class="flex-grow p-4">
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h2 id="product-name" class="text-lg font-semibold mb-2"></h2>
                    <p id="brand-name" class="text-sm text-gray-600"></p>
                    <p id="product-price" class="text-purple-600 font-bold mt-1"></p>
                    <p id="order-id" class="text-sm text-gray-600 mt-2">Order ID: <span class="font-medium text-gray-900"></span></p>
                </div>

                <form id="reviewForm" class="space-y-8" action="/api/review.php" method="POST">
                    <div class="text-center">
                        <label for="rating" class="block text-lg font-medium text-gray-700 mb-4">Your Rating</label>
                        <div class="flex justify-center items-center" id="starRating">
                            <i class="fas fa-star text-5xl text-gray-300 cursor-pointer hover:text-purple-600 mx-1" data-rating="1"></i>
                            <i class="fas fa-star text-5xl text-gray-300 cursor-pointer hover:text-purple-600 mx-1" data-rating="2"></i>
                            <i class="fas fa-star text-5xl text-gray-300 cursor-pointer hover:text-purple-600 mx-1" data-rating="3"></i>
                            <i class="fas fa-star text-5xl text-gray-300 cursor-pointer hover:text-purple-600 mx-1" data-rating="4"></i>
                            <i class="fas fa-star text-5xl text-gray-300 cursor-pointer hover:text-purple-600 mx-1" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="">
                        <input type="hidden" name="order_id" id="orderIdInput" value="">
                    </div>

                    <button type="submit" id="submitReview" class="w-full bg-purple-600 text-white py-3 px-4 rounded-md hover:bg-purple-700 transition-colors opacity-50 cursor-not-allowed" disabled>
                        Submit Review
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
            const starRating = document.getElementById('starRating');
            const ratingInput = document.getElementById('ratingInput');
            const orderIdInput = document.getElementById('orderIdInput');  // Get the hidden order_id input
            const submitButton = document.getElementById('submitReview');
            
            const orderIdElement = document.getElementById('order-id');
            const productNameElement = document.getElementById('product-name');
            const brandNameElement = document.getElementById('brand-name');
            const productPriceElement = document.getElementById('product-price');
            const backLink = document.getElementById('back-link');

            // Fetch review data for dynamic content
            async function fetchReviewDetails() {
                const orderId = new URLSearchParams(window.location.search).get('id');
                orderIdInput.value = orderId;  // Set order_id in the hidden input

                try {
                    const response = await fetch(`/api/my_orders.php`);
                    const data = await response.json();
                    const order = data.orders.find(o => o.order_id == orderId);

                    if (order) {
                        orderIdElement.innerHTML = `Order ID: <span class="font-medium text-gray-900">#${order.order_id}</span>`;
                        productNameElement.textContent = order.listing_name;
                        brandNameElement.textContent = `Brand: ${order.store_name}`;
                        productPriceElement.textContent = `$${(order.price / 100).toFixed(2)}`;
                        backLink.href = `/view-order?id=${order.order_id}`;
                    } else {
                        window.location.href = '/purchases';
                    }
                } catch (error) {
                    console.error('Error fetching order details:', error);
                    window.location.href = '/purchases';
                }
            }

            fetchReviewDetails();

            starRating.addEventListener('click', function(e) {
                if (e.target.classList.contains('fa-star')) {
                    const rating = e.target.getAttribute('data-rating');
                    ratingInput.value = rating;
                    updateStars(rating);
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });

            function updateStars(rating) {
                const stars = starRating.querySelectorAll('.fa-star');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-purple-600');
                    } else {
                        star.classList.remove('text-purple-600');
                        star.classList.add('text-gray-300');
                    }
                });
            }
        });
    </script>
</body>
</html>
