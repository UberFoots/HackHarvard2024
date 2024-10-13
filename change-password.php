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
    <title>Stylete - Change Password</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/assets/js/popup-alert.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-white min-h-screen flex flex-col">
        <header class="bg-white p-4 flex items-center">
            <a href="/settings" class="text-purple-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-800 ml-4">Change Password</h1>
        </header>
        <div class="p-4">
            <section class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form id="change-password-form">
                    <div class="space-y-4">
                        <div>
                            <label for="current-password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" id="current-password" name="current-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" id="new-password" name="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" id="confirm-password" name="confirm-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 transition-colors">Update Password</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
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
            <a href="/profile" class="flex flex-col items-center py-2 px-3 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-xs mt-1">Profile</span>
            </a>
        </div>
    </nav>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('change-password-form');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch('/api/change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPopupAlert('Password changed successfully', 'success');
                    form.reset();
                } else {
                    showPopupAlert('Failed to change password: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPopupAlert('An error occurred while changing the password', 'error');
            });
        });
    });
    </script>
</body>
</html>