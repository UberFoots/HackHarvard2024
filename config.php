<?php
// public_html/config.php

define('DB_HOST', 'localhost');  // host the page is running on
define('DB_NAME', 'styletea_db');
define('DB_USER', 'styletea_db');
define('DB_PASS', '');

// Array of test card numbers
$test_cards = [
    '4242424242424242', 
    '4000056655665556', 
    '5555555555554444',
    '2223003122003222',
    '5200828282828210',
    '5105105105105100'
];

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}
?>
