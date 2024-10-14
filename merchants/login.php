<?php
session_start();
require_once '../config.php';  // Adjusted the path to point one level up from the merchants folder
$loginError = '';

// Check if the merchant is already logged in
if (isset($_SESSION['merchant_id'])) {
    // Redirect to the merchant dashboard if already logged in
    header('Location: /merchants/overview');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch the POST request data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation checks
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loginError = 'Invalid email format.';
    } elseif (empty($password)) {
        $loginError = 'Password cannot be blank.';
    } else {
        // Fetch the merchant by email from the database
        $query = "SELECT * FROM merchants WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => strtolower($email)]);
        $merchant = $stmt->fetch();

        if ($merchant && password_verify($password, $merchant['password'])) {
            // Password matches, set session variables
            $_SESSION['merchant_id'] = $merchant['mid'];
            $_SESSION['store_name'] = $merchant['store_name'];

            // Set session to expire after 30 days of inactivity
            ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);  // 30 days in seconds
            session_set_cookie_params(30 * 24 * 60 * 60);  // Cookie expiration time

            // Redirect to merchant dashboard
            header('Location: /merchants/overview');
            exit;
        } else {
            // Invalid login credentials
            $loginError = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylete - Merchant Login</title>
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
                <a href="/merchants/register" class="text-gray-600 hover:text-stylete-purple transition duration-300">Register</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white shadow-lg rounded-3xl overflow-hidden">
                <div class="bg-stylete-purple text-white py-8 px-6 sm:px-10">
                    <h2 class="text-3xl font-extrabold text-center">Merchant Login</h2>
                    <p class="mt-2 text-center">Access your merchant account</p>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="p-6 sm:p-10 space-y-6">
                    <?php if ($loginError): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                            <p><?php echo htmlspecialchars($loginError); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email Address</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="email" name="email" type="email" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stylete-purple focus:border-transparent" id="password" name="password" type="password" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-6 py-3 bg-stylete-purple text-white font-semibold rounded-full hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:-translate-y-1">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div class="px-6 sm:px-10 py-4 bg-gray-50 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have a merchant account? 
                        <a href="/merchants/register" class="font-medium text-stylete-purple hover:text-purple-700">
                            Register
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
