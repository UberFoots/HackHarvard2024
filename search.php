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
    <title>Stylete - Search</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensures the text is truncated with ellipsis if it overflows */
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col pb-16">
        <main class="flex-grow p-4">
            <div class="mb-4">
                <input type="text" id="search-input" placeholder="Search..." class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <!-- Styled Filter (Sort) Anchor Button -->
            <div class="mb-4 flex justify-end">
                <button id="filter-btn" class="text-purple-600 flex items-center focus:outline-none">
                    <span id="sort-text">Sort by Price</span>
                    <svg id="sort-icon" class="w-5 h-5 ml-2 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path id="sort-arrow" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
            </div>

            <div id="listings-container" class="space-y-4">
                <!-- Listings will be inserted here dynamically -->
            </div>

            <!-- Pagination Controls -->
            <div id="pagination-controls" class="flex justify-end mt-4">
                <button id="prev-page" class="px-4 py-2 bg-gray-300 text-white rounded-md hidden">Previous</button>
                <button id="next-page" class="px-4 py-2 bg-purple-600 text-white rounded-md">Next</button>
            </div>
        </main>

        <nav class="bg-white border-t border-gray-200 fixed bottom-5 left-0 right-0 max-w-md mx-auto">
            <div class="max-w-md mx-auto flex justify-around">
                <a href="/home" class="flex flex-col items-center py-2 px-3 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-xs mt-1">Home</span>
                </a>
                <a href="/search" class="flex flex-col items-center py-2 px-3 text-purple-600">
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

    <!-- JavaScript for fetching listings, filtering, and handling pagination -->
    <!-- Previous HTML code remains unchanged -->

<script>
    let currentPage = 1;
    let currentSortOrder = '';
    const listingsPerPage = 4;
    const type = new URLSearchParams(window.location.search).get('type') || '';
    const searchInput = document.getElementById('search-input');
    const listingsContainer = document.getElementById('listings-container');
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');
    const paginationControls = document.getElementById('pagination-controls');
    const filterButton = document.getElementById('filter-btn');
    const sortText = document.getElementById('sort-text');
    const sortIcon = document.getElementById('sort-icon');
    const sortArrow = document.getElementById('sort-arrow');

    async function fetchListings(query = '', sortOrder = '') {
        const url = `/api/fetch_listings.php?type=${type}&query=${query}&sortOrder=${sortOrder}`;
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Failed to fetch listings');
            }
            const listings = await response.json();
            return listings;
        } catch (error) {
            console.error(error);
            return [];
        }
    }

    function renderListings(listings) {
        listingsContainer.innerHTML = '';

        if (listings.length === 0) {
            listingsContainer.innerHTML = '<p class="text-gray-500">No listings found.</p>';
            return;
        }

        listings.forEach(listing => {
            const listingItem = `
                <a href="/listing?id=${listing.product_id}" class="block">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4 flex relative">
                        <div class="absolute top-2 right-2 bg-purple-600 text-white px-2 py-1 rounded-md text-xs font-semibold">
                            ${listing.stock} left
                        </div>
                        <img src="${listing.image_url}" alt="${listing.name}" class="w-24 h-24 object-cover rounded-lg mr-4">
                        <div>
                            <h3 class="font-semibold truncate-text">${listing.name}</h3>
                            <p class="text-sm text-gray-600">Store: ${listing.store_name}</p>
                            <p class="text-purple-600 font-bold mt-2">$${listing.price}</p>
                        </div>
                    </div>
                </a>
            `;
            listingsContainer.innerHTML += listingItem;
        });
    }

    async function loadListings(page = 1) {
        const allListings = await fetchListings(searchInput.value, currentSortOrder);
        const availableListings = allListings.filter(listing => listing.stock > 0);

        const totalPages = Math.ceil(availableListings.length / listingsPerPage);
        const startIndex = (page - 1) * listingsPerPage;
        const endIndex = startIndex + listingsPerPage;
        const listingsToRender = availableListings.slice(startIndex, endIndex);

        renderListings(listingsToRender);
        updatePaginationControls(page, totalPages);
    }

    function updatePaginationControls(currentPage, totalPages) {
        if (totalPages <= 1) {
            paginationControls.classList.add('hidden');
        } else {
            paginationControls.classList.remove('hidden');

            if (currentPage === 1) {
                prevButton.classList.add('hidden');
                paginationControls.classList.remove('justify-between');
                paginationControls.classList.add('justify-end');
            } else {
                prevButton.classList.remove('hidden');
                paginationControls.classList.remove('justify-end');
                paginationControls.classList.add('justify-between');
            }

            prevButton.disabled = currentPage === 1;
            nextButton.disabled = currentPage === totalPages;

            [prevButton, nextButton].forEach(button => {
                if (button.disabled) {
                    button.classList.add('bg-gray-300', 'text-gray-500');
                    button.classList.remove('bg-purple-600', 'text-white');
                } else {
                    button.classList.remove('bg-gray-300', 'text-gray-500');
                    button.classList.add('bg-purple-600', 'text-white');
                }
            });
        }
    }

    prevButton.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            loadListings(currentPage);
        }
    });

    nextButton.addEventListener('click', () => {
        currentPage++;
        loadListings(currentPage);
    });

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        loadListings(currentPage);
    });

    filterButton.addEventListener('click', () => {
        sortIcon.classList.remove('hidden');

        if (currentSortOrder === 'lowest') {
            currentSortOrder = 'highest';
            sortText.textContent = 'Sort by Price: High to Low';
            sortArrow.setAttribute('d', 'M5 9l7 7 7-7');
        } else {
            currentSortOrder = 'lowest';
            sortText.textContent = 'Sort by Price: Low to High';
            sortArrow.setAttribute('d', 'M5 15l7-7 7 7');
        }
        currentPage = 1;
        loadListings(currentPage);
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadListings();
    });
</script>
</body>
</html>