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
    <title>Stylete - Surplus Fashion</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensures the text is truncated with ellipsis if it overflows */
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Hide scrollbars on mobile */
        @media (max-width: 640px) {
            .overflow-x-auto {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
            .overflow-x-auto::-webkit-scrollbar {
                display: none;  /* Chrome, Safari and Opera */
            }
        }

        /* Custom scrollbar for desktop */
        @media (min-width: 641px) {
            .overflow-x-auto {
                scrollbar-width: thin;
                scrollbar-color: #D1D5DB transparent;
            }
            .overflow-x-auto::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .overflow-x-auto::-webkit-scrollbar-track {
                background: transparent;
            }
            .overflow-x-auto::-webkit-scrollbar-thumb {
                background-color: #D1D5DB;
                border-radius: 3px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col">
        <main class="flex-grow p-4">
            <!-- Search Form -->
            <div class="mb-4">
                <form action="/search" method="GET">
                    <input 
                        type="text" 
                        name="query" 
                        placeholder="Search..." 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                </form>
            </div>

            <!-- Featured Boxes Section -->
            <section class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Featured Boxes</h2>
                    <a href="/search?type=popular" class="text-purple-600 hover:underline">See all</a>
                </div>
                <div id="featured-boxes" class="flex overflow-x-auto space-x-4 pb-4">
                    <!-- Featured listings will be dynamically inserted here -->
                </div>
            </section>

            <!-- Newest Boxes Section -->
            <section class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Newest Boxes</h2>
                    <a href="/search?type=new" class="text-purple-600 hover:underline">See all</a>
                </div>
                <div id="newest-boxes" class="flex overflow-x-auto space-x-4 pb-4">
                    <!-- Newest listings will be dynamically inserted here -->
                </div>
            </section>
        </main>

        <!-- Navigation Bar -->
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
        // Function to fetch listings dynamically with stock filtering logic
async function fetchListingsWithStock(type, amount) {
    let filteredListings = [];
    let page = 1;  // Start from the first page
    let listings;

    while (filteredListings.length < amount) {
        try {
            const response = await fetch(`/api/fetch_listings?type=${type}&amount=${amount}&page=${page}`);
            listings = await response.json();

            // Filter listings with stock > 0 and add them to the filteredListings array
            const inStockListings = listings.filter(listing => listing.stock > 0);

            filteredListings = filteredListings.concat(inStockListings);

            // If there are no more listings to fetch, break the loop
            if (listings.length < amount) {
                break;
            }

            page++;  // Move to the next page if we need more listings
        } catch (error) {
            console.error(error);
            break;
        }
    }

    // Return exactly the number of listings requested (up to the amount specified)
    return filteredListings.slice(0, amount);
}

// Function to render listings in the appropriate section
function renderListings(listings, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';  // Clear the container

    listings.forEach(listing => {
        const listingItem = `
            <a href="/listing?id=${listing.product_id}" class="block">
                <div class="flex-shrink-0 w-48 bg-white border border-gray-200 rounded-lg shadow-md">
                    <div class="relative">
                        <img src="${listing.image_url}" alt="${listing.name}" class="w-full h-32 object-cover rounded-t-lg">
                        <div class="absolute top-2 left-2 bg-purple-600 text-white px-2 py-1 text-xs font-semibold rounded">${listing.stock} left</div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold truncate-text">${listing.name}</h3>
                        <p class="text-sm text-gray-600">Store: ${listing.store_name}</p>
                        <p class="text-purple-600 font-bold mt-2">$${listing.price}</p>
                    </div>
                </div>
            </a>
        `;
        container.innerHTML += listingItem;  // Append listing to the container
    });
}

// Fetch and display the most popular listings for the "Featured Boxes" section
async function loadFeaturedBoxes() {
    const popularListings = await fetchListingsWithStock('popular', 5);
    renderListings(popularListings, 'featured-boxes');
}

// Fetch and display the newest listings for the "Newest Boxes" section
async function loadNewestBoxes() {
    const newestListings = await fetchListingsWithStock('new', 5);
    renderListings(newestListings, 'newest-boxes');
}

// Load listings when the page is loaded
document.addEventListener('DOMContentLoaded', () => {
    loadFeaturedBoxes();
    loadNewestBoxes();
});

    </script>
</body>
</html>
