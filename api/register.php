<?php
// public_html/api/register.php
session_start();
header('Content-Type: application/json');  // Ensure the response is in JSON format
require_once '../config.php';  // Database connection

// Get the POST request data
$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));  // Convert email to lowercase
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm-password'] ?? '';

// Validation checks
$errors = [];

// Check if any field is blank
if (empty($name)) {
    $errors[] = "Name cannot be blank.";
}
if (empty($email)) {
    $errors[] = "Email cannot be blank.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}
if (empty($password) || strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

// If there are any validation errors, stop the process and return them in JSON
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Hash the password using bcrypt
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Generate 13-digit epoch timestamp
$timestamp = round(microtime(true) * 1000);

// Check if the email already exists in the database
$email_check_query = "SELECT COUNT(*) FROM users WHERE email = :email";
$stmt = $pdo->prepare($email_check_query);
$stmt->execute([':email' => $email]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'errors' => ["Email is already in use."]]);
    exit;
}

// Get the latest UID from the database
$query = "SELECT MAX(uid) AS max_uid FROM users";
$stmt = $pdo->query($query);
$row = $stmt->fetch();
$uid = ($row['max_uid'] ?? 0) + 1;  // Increment the max UID by 1

// Insert the new user into the database with state and zipcode as NULL
$insert_query = "INSERT INTO users (uid, timestamp, full_name, address, email, password, city, state, zipcode) 
                 VALUES (:uid, :timestamp, :full_name, NULL, :email, :password, NULL, NULL, NULL)";
$stmt = $pdo->prepare($insert_query);
$insert_success = $stmt->execute([
    ':uid' => $uid,
    ':timestamp' => $timestamp,
    ':full_name' => $name,
    ':email' => $email,
    ':password' => $hashed_password
]);

// If insert failed, return an error
if (!$insert_success) {
    echo json_encode(['success' => false, 'errors' => ['Failed to register. Please try again later.']]);
    exit;
}

// Set proper session variables after registration
$_SESSION['user_id'] = $uid;

// Set session to expire after 30 days of inactivity
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);  // 30 days in seconds
session_set_cookie_params(30 * 24 * 60 * 60);  // Cookie expiration time

// Return success response in JSON format
echo json_encode(['success' => true, 'message' => 'Registration successful. Redirecting...']);
exit;
