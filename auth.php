<?php
// public_html/includes/auth_check_redirect.php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If logged in, redirect to the home page
    header("Location: /home");
    exit;
}
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="/assets/js/popup-alert.js"></script>
    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col">
        <main id="main-content" class="flex-grow flex flex-col justify-center items-center px-6">
            <img src="/assets/img/icon.png?height=100&width=100" alt="Stylete Logo" class="w-24 h-24 absolute top-12 rounded-2xl">
            <h1 class="text-3xl font-bold text-purple-700 text-center mb-8">Start saving with Stylete.</h1>
            <div class="w-full max-w-sm space-y-4">
                <button id="login-btn" class="w-full bg-purple-600 text-white py-3 rounded-full flex items-center justify-center">
                    Login
                </button>
                <button id="register-btn" class="w-full bg-purple-600 text-white py-3 rounded-full flex items-center justify-center">
                    Register
                </button>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mainContent = document.getElementById('main-content');
            const loginBtn = document.getElementById('login-btn');
            const registerBtn = document.getElementById('register-btn');

            function showLoginForm() {
                mainContent.innerHTML = `
                    <h2 class="text-2xl font-bold text-purple-700 text-center mb-6">Login to Stylete</h2>
                    <form id="login-form" class="w-full max-w-sm space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required autocomplete="off" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 px-3 py-2 text-base">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="password" name="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 px-3 py-2 text-base">
                        </div>
                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-full flex items-center justify-center text-base">
                            Login
                        </button>
                    </form>
                    <button id="back-btn" class="mt-4 text-purple-600 hover:underline">Back to main</button>
                `;
                document.getElementById('back-btn').addEventListener('click', showMainContent);
                document.getElementById('login-form').addEventListener('submit', handleLogin);
            }

            function showRegisterForm() {
                mainContent.innerHTML = `
                    <h2 class="text-2xl font-bold text-purple-700 text-center mb-6">Register for Stylete</h2>
                    <form id="register-form" class="w-full max-w-sm space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name" required autocomplete="off" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 px-3 py-2 text-base">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required autocomplete="off" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 px-3 py-2 text-base">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="password" name="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 px-3 py-2 text-base">
                        </div>
                        <div>
                            <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm-password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50 px-3 py-2 text-base">
                        </div>
                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-full flex items-center justify-center text-base">
                            Register
                        </button>
                    </form>
                    <button id="back-btn" class="mt-4 text-purple-600 hover:underline">Back to main</button>
                `;
                document.getElementById('back-btn').addEventListener('click', showMainContent);
                document.getElementById('register-form').addEventListener('submit', handleRegister);
            }

            function showMainContent() {
                mainContent.innerHTML = `
                    <img src="/assets/img/icon.png?height=100&width=100" alt="Stylete Logo" class="w-24 h-24 absolute top-12 rounded-xl">
                    <h1 class="text-3xl font-bold text-purple-700 text-center mb-8">Start saving with Stylete.</h1>
                    <div class="w-full max-w-sm space-y-4">
                        <button id="login-btn" class="w-full bg-purple-600 text-white py-3 rounded-full flex items-center justify-center text-base">
                            Login
                        </button>
                        <button id="register-btn" class="w-full bg-purple-600 text-white py-3 rounded-full flex items-center justify-center text-base">
                            Register
                        </button>
                    </div>
                `;
                document.getElementById('login-btn').addEventListener('click', showLoginForm);
                document.getElementById('register-btn').addEventListener('click', showRegisterForm);
            }

            async function handleLogin(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch('/api/login', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        showPopupAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '/home';
                        }, ALERT_DURATION_MS);
                    } else {
                        if (Array.isArray(data.errors)) {
                            data.errors.forEach((error, index) => {
                                setTimeout(() => {
                                    showPopupAlert(error, 'error');
                                }, index * (ALERT_DURATION_MS + 100));
                            });
                        } else {
                            showPopupAlert('An error occurred. Please try again.', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showPopupAlert('An error occurred. Please try again.', 'error');
                }
            }

            async function handleRegister(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch('/api/register', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        showPopupAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '/home';
                        }, ALERT_DURATION_MS);
                    } else {
                        if (Array.isArray(data.errors)) {
                            data.errors.forEach((error, index) => {
                                setTimeout(() => {
                                    showPopupAlert(error, 'error');
                                }, index * (ALERT_DURATION_MS + 100));
                            });
                        } else {
                            showPopupAlert('An error occurred. Please try again.', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showPopupAlert('An error occurred. Please try again.', 'error');
                }
            }

            loginBtn.addEventListener('click', showLoginForm);
            registerBtn.addEventListener('click', showRegisterForm);
        });
    </script>
</body>
</html>