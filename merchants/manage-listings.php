<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if merchant is logged in, if not redirect to login page
if (!isset($_SESSION['merchant_id'])) {
    header('Location: login');
    exit();
}

require_once '../config.php';  // Database connection

$merchant_id = $_SESSION['merchant_id'];

// Function to fetch merchant's listings
function fetchMerchantListings($pdo, $merchant_id) {
    $query = "SELECT product_id, name, description, price, stock, image_url, timestamp FROM listings WHERE mid = :mid ORDER BY timestamp DESC";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get the next available product_id
function getNextProductId($pdo) {
    $query = "SELECT MAX(product_id) as max_id FROM listings";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['max_id'] + 1;
}

// Handle form submission for creating new listing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $item_range = $_POST['item_range'];
    $value_range = $_POST['value_range'];
    $price = intval($_POST['price'] * 100); // Convert to cents
    $stock = $_POST['stock'];

    // Get the next available product_id
    $next_product_id = getNextProductId($pdo);

    // Get current timestamp (13-digit epoch) and explicitly cast to integer
    $timestamp = (int)round(microtime(true) * 1000);

    // Include product_id and timestamp in the INSERT statement
    $query = "INSERT INTO listings (product_id, mid, name, description, image_url, item_range, value_range, price, stock, timestamp) 
              VALUES (:product_id, :mid, :name, :description, :image_url, :item_range, :value_range, :price, :stock, :timestamp)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $next_product_id, PDO::PARAM_INT);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':image_url', $image_url, PDO::PARAM_STR);
    $stmt->bindParam(':item_range', $item_range, PDO::PARAM_STR);
    $stmt->bindParam(':value_range', $value_range, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $_SESSION['message'] = "Listing created successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error creating listing: " . $e->getMessage();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission for editing listing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $price = intval($_POST['price'] * 100); // Convert to cents
    $stock = $_POST['stock'];

    $query = "UPDATE listings SET name = :name, description = :description, image_url = :image_url, price = :price, stock = :stock WHERE product_id = :product_id AND mid = :mid";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':image_url', $image_url, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $_SESSION['message'] = "Listing updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating listing: " . $e->getMessage();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deactivate/reactivate action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'deactivate' || $_POST['action'] === 'reactivate')) {
    $product_id = $_POST['product_id'];
    $stock = ($_POST['action'] === 'deactivate') ? 0 : 1;

    $query = "UPDATE listings SET stock = :stock WHERE product_id = :product_id AND mid = :mid";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $_SESSION['message'] = ($_POST['action'] === 'deactivate') ? "Listing deactivated successfully!" : "Listing reactivated successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating listing: " . $e->getMessage();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $product_id = $_POST['product_id'];

    $query = "DELETE FROM listings WHERE product_id = :product_id AND mid = :mid";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $_SESSION['message'] = "Listing deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting listing: " . $e->getMessage();
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Check for message in session
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch the merchant's listings
$listings = fetchMerchantListings($pdo, $merchant_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylete - Manage Listings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'stylete-purple': '#8B5CF6',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Include the navigation bar -->
        <?php include 'nav.php'; ?>

        <!-- Main content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Manage Listings</h1>

                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-stylete-purple hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stylete-purple">
                        Create New Listing
                    </button>
                </div>

                <?php if (empty($listings)): ?>
                    <p class="text-gray-600">You have no active listings. Click "Create New Listing" to add one.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($listings as $listing): ?>
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4 relative">
                                <img src="<?php echo htmlspecialchars($listing['image_url']); ?>" alt="<?php echo htmlspecialchars($listing['name']); ?>" class="w-full h-48 object-cover rounded-md mb-4">
                                <button onclick='openDeleteModal(<?php echo json_encode($listing); ?>)' class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-2 shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">Delete</span>
                                </button>
                                <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($listing['name']); ?></h3>
                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($listing['description']); ?></p>
                                <p class="text-sm text-gray-500 mt-2">Price: $<?php echo number_format($listing['price'] / 100, 2); ?></p>
                                <p class="text-sm text-gray-500">Stock: <?php echo htmlspecialchars($listing['stock']); ?></p>
                                <p class="text-sm text-gray-500">Created: <?php echo date('M j, Y', (int)($listing['timestamp'] / 1000)); ?></p>
                                
                                <div class="mt-4 flex justify-between">
                                    <button onclick='openEditModal(<?php echo json_encode($listing); ?>)' class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-stylete-purple hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stylete-purple">
                                        Edit
                                    </button>
                                    <form method="POST" action="">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($listing['product_id']); ?>">
                                        <input type="hidden" name="action" value="<?php echo $listing['stock'] > 0 ? 'deactivate' : 'reactivate'; ?>">
                                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white <?php echo $listing['stock'] > 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'; ?> focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-<?php echo $listing['stock'] > 0 ? 'red' : 'green'; ?>-500">
                                            <?php echo $listing['stock'] > 0 ? 'Deactivate' : 'Reactivate'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Create Listing Modal -->
    <div id="createListingModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle  sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Create New Listing
                    </h3>
                    <div class="mt-2">
                        <form id="createListingForm" method="POST" action="">
                            <input type="hidden" name="action" value="create">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium  text-gray-700">Name</label>
                                <input type="text" name="name" id="name" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL</label>
                                <input type="url" name="image_url" id="image_url" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="item_range" class="block text-sm font-medium text-gray-700">Item Range (e.g., 1,5)</label>
                                <input type="text" name="item_range" id="item_range" required pattern="\d+,\d+" class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="value_range" class="block text-sm font-medium text-gray-700">Value Range (e.g., 50,100)</label>
                                <input type="text" name="value_range" id="value_range" required pattern="\d+,\d+" class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="price" class="block text-sm font-medium text-gray-700">Price (in dollars)</label>
                                <input type="number" name="price" id="price" required step="0.01" class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="stock" id="stock" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2  sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-stylete-purple text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stylete-purple sm:col-start-2 sm:text-sm">
                                    Create Listing
                                </button>
                                <button type="button" onclick="closeCreateModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Listing Modal -->
    <div id="editListingModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Edit Listing
                    </h3>
                    <div class="mt-2">
                        <form id="editListingForm" method="POST" action="">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="product_id" id="edit_product_id">
                            <div class="mb-4">
                                <label for="edit_name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="edit_name" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="edit_description" rows="3" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="edit_image_url" class="block text-sm font-medium text-gray-700">Image URL</label>
                                <input type="url" name="image_url" id="edit_image_url" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="edit_price" class="block text-sm font-medium text-gray-700">Price (in dollars)</label>
                                <input type="number" name="price" id="edit_price" required step="0.01" class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label for="edit_stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="stock" id="edit_stock" required class="mt-1 focus:ring-stylete-purple focus:border-stylete-purple block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-stylete-purple text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stylete-purple sm:col-start-2 sm:text-sm">
                                    Update Listing
                                </button>
                                <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Delete Listing
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this listing? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form id="deleteListingForm" method="POST" action="">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" id="delete_product_id">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createListingModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createListingModal').classList.add('hidden');
        }

        function openEditModal(listing) {
            document.getElementById('edit_product_id').value = listing.product_id;
            document.getElementById('edit_name').value = listing.name;
            document.getElementById('edit_description').value = listing.description;
            document.getElementById('edit_image_url').value = listing.image_url;
            document.getElementById('edit_price').value = (listing.price / 100).toFixed(2);
            document.getElementById('edit_stock').value = listing.stock;
            document.getElementById('editListingModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editListingModal').classList.add('hidden');
        }

        function openDeleteModal(listing) {
            document.getElementById('delete_product_id').value = listing.product_id;
            document.getElementById('deleteConfirmationModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmationModal').classList.add('hidden');
        }
    </script>
</body>
</html>