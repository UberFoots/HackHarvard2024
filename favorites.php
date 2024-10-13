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
    <title>Stylete - Favorites</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/assets/js/popup-alert.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <header class="bg-white p-4 shadow-sm">
            <h1 class="text-2xl font-semibold">Favorites</h1>
        </header>
        <main class="flex-grow p-4">
            <div id="favorites-container" class="space-y-4">
                <!-- Favorites will be loaded dynamically here -->
            </div>
            <div id="no-favorites" class="hidden text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">No favorites yet</h2>
                <p class="text-gray-500 mb-4">Start adding items to your favorites!</p>
                <a href="/search" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition-colors">
                    Explore Products
                </a>
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
                <a href="/favorites" class="flex flex-col items-center py-2 px-3 text-purple-600">
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
            const favoritesContainer = document.getElementById('favorites-container');
            const noFavorites = document.getElementById('no-favorites');

            // Function to fetch favorites from the API
            async function fetchFavorites() {
                try {
                    const response = await fetch('/api/favorites');
                    const data = await response.json();
                    return data.favorites;
                } catch (error) {
                    console.error('Error fetching favorites:', error);
                    return [];
                }
            }

            // Function to fetch listing details
            async function fetchListingDetails(productId) {
                try {
                    const response = await fetch(`/api/listing_details?id=${productId}`);
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching listing details:', error);
                    return null;
                }
            }

            // Function to render favorites
            async function renderFavorites(favoriteIds) {
                if (favoriteIds.length === 0) {
                    favoritesContainer.classList.add('hidden');
                    noFavorites.classList.remove('hidden');
                } else {
                    favoritesContainer.classList.remove('hidden');
                    noFavorites.classList.add('hidden');
                    
                    favoritesContainer.innerHTML = '';
                    for (const productId of favoriteIds) {
                        const listing = await fetchListingDetails(productId);
                        if (listing) {
                            const listingElement = document.createElement('div');
                            listingElement.className = 'bg-white border border-gray-200 rounded-lg shadow-md p-4 flex relative';
                            listingElement.innerHTML = `
                                <div class="absolute top-2 right-2 bg-purple-600 text-white px-2 py-1 rounded-md text-xs font-semibold">
                                    ${listing.stock} left
                                </div>
                                <img src="${listing.image_url}" alt="${listing.name}" class="w-24 h-24 object-cover rounded-lg mr-4">
                                <div class="flex-grow">
                                    <h3 class="font-semibold">${listing.name}</h3>
                                    <p class="text-sm text-gray-600">Store: ${listing.store_name}</p>
                                    <p class="text-purple-600 font-bold mt-1">$${(listing.price / 100).toFixed(2)}</p>
                                    <div class="flex mt-2 justify-end space-x-2">
                                        <a href="/listing?id=${listing.product_id}" class="bg-purple-600 text-white px-4 py-1 rounded-md text-sm hover:bg-purple-700 transition-colors w-20 text-center">
                                            View
                                        </a>
                                        <button class="bg-red-500 text-white px-4 py-1 rounded-md text-sm hover:bg-red-600 transition-colors w-20" onclick="removeFavorite(${listing.product_id})">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            `;
                            favoritesContainer.appendChild(listingElement);
                        }
                    }
                }
            }

            // Function to remove a favorite
            async function removeFavorite(productId) {
                try {
                    const response = await fetch(`/api/remove_favorite?id=${productId}`, { method: 'GET' });
                    const data = await response.json();
                    if (data.success) {
                        showPopupAlert('Removed from favorites', 'success');
                        const favorites = await fetchFavorites();
                        renderFavorites(favorites);
                    } else {
                        showPopupAlert('Failed to remove from favorites', 'error');
                    }
                } catch (error) {
                    console.error('Error removing favorite:', error);
                    showPopupAlert('An error occurred', 'error');
                }
            }

            // Initial load of favorites
            fetchFavorites().then(renderFavorites);

            // Make removeFavorite function globally accessible
            window.removeFavorite = removeFavorite;
        });
    </script>
</body>
</html>