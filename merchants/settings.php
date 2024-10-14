<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if merchant is logged in, if not redirect to login page
if (!isset($_SESSION['merchant_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config.php';  // Database connection

$merchant_id = $_SESSION['merchant_id'];
$successMessage = '';
$errorMessage = '';

// Fetch current merchant data
function fetchMerchantData($pdo, $merchant_id) {
    $query = "SELECT store_name, email FROM merchants WHERE mid = :mid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':mid', $merchant_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if store_name is unique
function isStoreNameUnique($pdo, $store_name, $merchant_id) {
    $query = "SELECT COUNT(*) FROM merchants WHERE store_name = :store_name AND mid != :mid";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':store_name' => $store_name, ':mid' => $merchant_id]);
    return $stmt->fetchColumn() == 0;
}

// Check if email is unique
function isEmailUnique($pdo, $email, $merchant_id) {
    $query = "SELECT COUNT(*) FROM merchants WHERE email = :email AND mid != :mid";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':email' => $email, ':mid' => $merchant_id]);
    return $stmt->fetchColumn() == 0;
}

// Update merchant data
function updateMerchantData($pdo, $merchant_id, $store_name, $email, $password = null) {
    if ($password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE merchants SET store_name = :store_name, email = :email, password = :password WHERE mid = :mid";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':store_name' => $store_name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':mid' => $merchant_id
        ]);
    } else {
        $query = "UPDATE merchants SET store_name = :store_name, email = :email WHERE mid = :mid";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':store_name' => $store_name,
            ':email' => $email,
            ':mid' => $merchant_id
        ]);
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_name = trim($_POST['store_name']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation: Check for empty fields
    if (empty($store_name) || empty($email)) {
        $errorMessage = "Store name and email cannot be empty.";
    } 
    // Validation: Check for valid email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    }
    // Validation: Check for password match and length
    elseif (!empty($new_password) && (strlen($new_password) < 8)) {
        $errorMessage = "Password must be at least 8 characters long.";
    }
    elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $errorMessage = "Passwords do not match.";
    } 
    // Validation: Check if store_name is unique
    elseif (!isStoreNameUnique($pdo, $store_name, $merchant_id)) {
        $errorMessage = "Store name is already taken.";
    } 
    // Validation: Check if email is unique
    elseif (!isEmailUnique($pdo, $email, $merchant_id)) {
        $errorMessage = "Email is already in use.";
    } else {
        // Update the merchant details
        $updateSuccess = updateMerchantData($pdo, $merchant_id, $store_name, $email, $new_password ?: null);

        if ($updateSuccess) {
            $successMessage = "Settings updated successfully.";
            $_SESSION['store_name'] = $store_name;  // Update session with new store name
        } else {
            $errorMessage = "Failed to update settings.";
        }
    }
}

// Fetch the merchant's current data
$merchantData = fetchMerchantData($pdo, $merchant_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylete - Settings</title>
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
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Settings</h1>

                <?php if ($successMessage): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p><?php echo htmlspecialchars($successMessage); ?></p>
                    </div>
                <?php elseif ($errorMessage): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p><?php echo htmlspecialchars($errorMessage); ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="store_name">Store Name</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="store_name" name="store_name" type="text" value="<?php echo htmlspecialchars($merchantData['store_name']); ?>" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="email">Email Address</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="email" name="email" type="email" value="<?php echo htmlspecialchars($merchantData['email']); ?>" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="new_password">New Password (optional, must be 8 characters or more)</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="new_password" name="new_password" type="password">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="confirm_password">Confirm New Password</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="confirm_password" name="confirm_password" type="password">
                    </div>

                    <div>
                        <button type="submit" class="w-full px-6 py-3 bg-stylete-purple text-white font-semibold rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:-translate-y-1">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
