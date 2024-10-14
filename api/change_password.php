<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth_check.php';  // Ensure user is authenticated
require_once '../includes/initialize_user.php';  // Initialize user session and data

$uid = $_SESSION['user_id'];  // User ID from session

require_once '../config.php';  // Database connection

// Set the Content-Type header to JSON
header('Content-Type: application/json');

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Get POSTed parameters
$current_password = sanitize_input($_POST['current-password'] ?? '');
$new_password = sanitize_input($_POST['new-password'] ?? '');
$confirm_password = sanitize_input($_POST['confirm-password'] ?? '');

// Validate passwords
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['success' => false, 'error' => 'All password fields are required.']);
    exit();
}

// Check if the new password matches the confirmation password
if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'error' => 'New password and confirmation password do not match.']);
    exit();
}

// Check password length (minimum 8 characters)
if (strlen($new_password) < 8) {
    echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters long.']);
    exit();
}

// Fetch current password hash from the database
$query = $pdo->prepare("SELECT password FROM users WHERE uid = :user_id");
$query->bindParam(':user_id', $uid);
$query->execute();
$user_data = $query->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    echo json_encode(['success' => false, 'error' => 'User not found.']);
    exit();
}

// Verify current password using bcrypt
if (!password_verify($current_password, $user_data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
    exit();
}

// Hash the new password using bcrypt
$new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

// Update the password in the database
$update_query = $pdo->prepare("UPDATE users SET password = :new_password WHERE uid = :user_id");
$update_query->bindParam(':new_password', $new_password_hashed);
$update_query->bindParam(':user_id', $uid);

if ($update_query->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update password.']);
}
?>
