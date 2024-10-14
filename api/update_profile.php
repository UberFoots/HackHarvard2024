<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth_check.php';  // Ensure user is authenticated
require_once '../includes/initialize_user.php';  // Initialize user session and data

$uid = $_SESSION['user_id'];  // User ID from session

require_once '../config.php';  // Ensure the database is connected

// Set the Content-Type header to JSON
header('Content-Type: application/json');

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Function to check if any fields are empty
function validate_required_fields($fields) {
    foreach ($fields as $field) {
        if (empty($field)) {
            return false;
        }
    }
    return true;
}

// Get POSTed parameters
$full_name = sanitize_input($_POST['name'] ?? '');  // Adjusted to map 'name' to 'full_name'
$email = sanitize_input($_POST['email'] ?? '');
$address = sanitize_input($_POST['address'] ?? '');
$city = sanitize_input($_POST['city'] ?? '');
$state = sanitize_input($_POST['state'] ?? '');
$zipcode = sanitize_input($_POST['zipcode'] ?? '');

// Validate required fields
if (!validate_required_fields([$full_name, $email, $address, $city, $state, $zipcode])) {
    echo json_encode(['error' => 'All fields are required.']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format.']);
    exit();
}

// Ensure address has valid characters
if (!preg_match('/^[A-Za-z0-9\s\-]+$/', $address)) {
    echo json_encode(['error' => 'Invalid characters in address.']);
    exit();
}
if (!preg_match('/^[A-Za-z\s]+$/', $city)) {
    echo json_encode(['error' => 'Invalid characters in city.']);
    exit();
}
if (!preg_match('/^[A-Za-z]{2}$/', $state)) {
    echo json_encode(['error' => 'Invalid state format.']);
    exit();
}
if (!preg_match('/^\d{5}$/', $zipcode)) {
    echo json_encode(['error' => 'Invalid zipcode format.']);
    exit();
}

// Lowercase email if it's different from the current one
$email = strtolower($email);

// Fetch current user data from the database
$query = $pdo->prepare("SELECT full_name, email, address, city, state, zipcode FROM users WHERE uid = :uid");
$query->bindParam(':uid', $uid);
$query->execute();
$current_user_data = $query->fetch(PDO::FETCH_ASSOC);

if (!$current_user_data) {
    echo json_encode(['error' => 'User not found.']);
    exit();
}

// Build the update query dynamically based on changed fields
$update_fields = [];
$params = [];

if ($full_name !== $current_user_data['full_name']) {
    $update_fields[] = 'full_name = :full_name';
    $params[':full_name'] = $full_name;
}
if ($email !== $current_user_data['email']) {
    $update_fields[] = 'email = :email';
    $params[':email'] = $email;
}
if ($address !== $current_user_data['address']) {
    $update_fields[] = 'address = :address';
    $params[':address'] = $address;
}
if ($city !== $current_user_data['city']) {
    $update_fields[] = 'city = :city';
    $params[':city'] = $city;
}
if ($state !== $current_user_data['state']) {
    $update_fields[] = 'state = :state';
    $params[':state'] = $state;
}
if ($zipcode !== $current_user_data['zipcode']) {
    $update_fields[] = 'zipcode = :zipcode';
    $params[':zipcode'] = $zipcode;
}

// If no fields are different, return success without updating
if (empty($update_fields)) {
    echo json_encode(['success' => 'No changes detected.']);
    exit();
}

// Prepare the update query
$update_query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE uid = :uid";
$params[':uid'] = $uid;

$update_stmt = $pdo->prepare($update_query);

// Execute the update query
if ($update_stmt->execute($params)) {
    echo json_encode(['success' => 'Profile updated successfully.']);
} else {
    echo json_encode(['error' => 'Failed to update profile.']);
}
?>
