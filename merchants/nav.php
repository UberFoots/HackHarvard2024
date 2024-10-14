<nav class="bg-stylete-purple">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="/merchants/login"><span class="text-white text-xl font-bold">Stylete</span></a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="overview" class="nav-link px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">Overview</a>
                        <a href="all-orders" class="nav-link px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">All Orders</a>
                        <a href="manage-listings" class="nav-link px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">Manage Listings</a>
                        <a href="settings" class="nav-link px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">Settings</a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <a href="logout" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-stylete-purple bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-stylete-purple focus:ring-white transition duration-150 ease-in-out">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <button id="mobile-menu-button" class="bg-stylete-purple inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-stylete-purple focus:ring-white">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="overview" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">Overview</a>
            <a href="all-orders" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">All Orders</a>
            <a href="manage-listings" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">Manage Listings</a>
            <a href="settings" class="nav-link block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-purple-700 transition duration-150 ease-in-out">Settings</a>
            <a href="logout" class="block px-3 py-2 rounded-md text-base font-medium text-stylete-purple bg-white hover:bg-gray-100 transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </div>
            </a>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>