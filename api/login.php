<?php
// public_html/api/login.php
session_start();
header('Content-Type: application/json');  // Ensure the response is in JSON format
require_once '../config.php';  // Database connection

// Get the POST request data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation checks
$errors = [];

// Check if email and password are provided
if (empty($email)) {
    $errors[] = "Email cannot be blank.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}
if (empty($password)) {
    $errors[] = "Password cannot be blank.";
}

// If there are any validation errors, stop the process and return them in JSON
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Fetch the user by email from the database
$query = "SELECT * FROM users WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// Verify the password and check if the user exists
if ($user && password_verify($password, $user['password'])) {
    // Password matches, set session variables
    $_SESSION['user_id'] = $user['uid'];

    // Set session to expire after 30 days of inactivity
    ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);  // 30 days in seconds
    session_set_cookie_params(30 * 24 * 60 * 60);  // Cookie expiration time

    // Return success response in JSON format
    echo json_encode(['success' => true, 'message' => 'Login successful. Redirecting...']);
    exit;
} else {
    // Invalid login credentials, return error in JSON
    echo json_encode(['success' => false, 'errors' => ["Invalid email or password."]]);
    exit;
}
