<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = $_POST['phone_number'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        // Prepare query to check user credentials
        $query = "SELECT user_id, name, password, role FROM users WHERE phone_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $phone_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            // Send success response
            echo json_encode([
                'success' => true,
                'redirect' => 'dashboard.php'
            ]);
        } else {
            // Send error response
            echo json_encode([
                'success' => false,
                'message' => 'Invalid phone number or password'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again.'
        ]);
    }
    exit;
}
?>
