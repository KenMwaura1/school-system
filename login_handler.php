<?php
session_start();

// Include database connection
require_once('db_connection.php');

// Check if the database connection is established
if (!isset($db)) {
    die("Database connection failed.");
}

// Get form data
$phone_number = $_POST['phone_number'];
$password = $_POST['password'];

// Query to check if the user exists
$stmt = $db->prepare("SELECT * FROM users WHERE phone_number = ? AND password = ?");
if ($stmt === false) {
    die("Prepare failed: " . $db->error);
}
$stmt->bind_param("ss", $phone_number, $password);
$stmt->execute();
$result = $stmt->get_result();

$response = array();

if ($result->num_rows > 0) {
    // User found
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['user_name'];
    $response['success'] = true;
    $response['redirect'] = 'dashboard.php';
} else {
    // User not found
    $response['success'] = false;
    $response['message'] = 'Invalid phone number or password.';
}

$stmt->close();
$db->close();

echo json_encode($response);
?>  