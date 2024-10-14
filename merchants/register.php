<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config.php';  // Adjusted the path to point one level up from the merchants folder
$registrationError = '';

// Check if the merchant is already logged in
if (isset($_SESSION['merchant_id'])) {
    // Redirect to the merchant dashboard if already logged in
    header('Location: /merchants/overview');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch the POST request data
    $store_name = trim($_POST['store_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation checks
    if (empty($store_name)) {
        $registrationError = 'Store name cannot be blank.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registrationError = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $registrationError = 'Password must be at least 8 characters long.';
    } elseif (empty($password)) {
        $registrationError = 'Password cannot be blank.';
    } elseif ($password !== $password_confirm) {
        $registrationError = 'Passwords do not match.';
    } else {
        // Check if the store name already exists
        $query = "SELECT * FROM merchants WHERE store_name = :store_name LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':store_name' => $store_name]);
        $existingStore = $stmt->fetch();

        if ($existingStore) {
            $registrationError = "The store name is already taken.";
        } else {
            // Check if the email already exists
            $query = "SELECT * FROM merchants WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':email' => strtolower($email)]);
            $existingMerchant = $stmt->fetch();

            if ($existingMerchant) {
                $registrationError = "An account with this email already exists.";
            } else {
                // Hash the password before storing it
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Insert the new merchant into the database
                $query = "INSERT INTO merchants (store_name, email, password, timestamp) 
          VALUES (:store_name, :email, :password, :timestamp)";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':store_name' => $store_name,
    ':email' => strtolower($email),
    ':password' => $hashedPassword,
    ':timestamp' => time(),
]);

// Log in the newly registered merchant
$_SESSION['merchant_id'] = $pdo->lastInsertId();  // Automatically get the inserted mid
$_SESSION['store_name'] = $store_name;


                // Redirect to the merchant dashboard
                header('Location: /merchants/overview');
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylete - Merchant Registration</title>
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
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <img src="/assets/img/icon.png" alt="Stylete Logo" class="h-10 w-10 mr-2 rounded-2xl">
                <h1 class="text-2xl font-bold text-stylete-purple">Stylete</h1>
            </div>
            <nav>
                <a href="/merchants/login" class="text-gray-600 hover:text-stylete-purple transition duration-300">Login</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white shadow-lg rounded-3xl overflow-hidden">
                <div class="bg-stylete-purple text-white py-8 px-6 sm:px-10">
                    <h2 class="text-3xl font-extrabold text-center">Merchant Registration</h2>
                    <p class="mt-2 text-center">Create a new merchant account</p>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="p-6 sm:p-10 space-y-6">
                    <?php if ($registrationError): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                            <p><?php echo htmlspecialchars($registrationError); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="store_name">Store Name</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="store_name" name="store_name" type="text" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email Address</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="email" name="email" type="email" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="password" name="password" type="password" required>
                        <p class="text-sm text-gray-600">Password must be at least 8 characters long.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="password_confirm">Confirm Password</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="password_confirm" name="password_confirm" type="password" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-6 py-3 bg-stylete-purple text-white font-semibold rounded-full hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:-translate-y-1">
                            Register
                        </button>
                    </div>
                </form>
                
                <div class="px-6 sm:px-10 py-4 bg-gray-50 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">
                        Already have a merchant account? 
                        <a href="/merchants/login" class="font-medium text-stylete-purple hover:text-purple-700">
                            Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
