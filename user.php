<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        // Get form data
        $service_number = $_POST['service_number'];
        $rank = $_POST['rank'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        
        // Validate password match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Passwords do not match");
        }
        
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Begin transaction
        $conn->begin_transaction();

        // Insert into users table
        $user_query = "INSERT INTO users (service_number, rank, name, email, phone_number, password, role) 
                      VALUES (?, ?, ?, ?, ?, ?, 'student')";
        
        $stmt = $conn->prepare($user_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssssss", $service_number, $rank, $name, $email, $phone_number, $password);
        $stmt->execute();
        
        $user_id = $conn->insert_id;

        // Insert into students table
        $student_query = "INSERT INTO students (student_id, enrollment_date, graduation_year, status) 
                         VALUES (?, CURRENT_DATE, YEAR(CURRENT_DATE) + 4, 'active')";
        
        $stmt = $conn->prepare($student_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Send success response
        $response['success'] = true;
        $response['message'] = "Welcome " . $name . "! Registration successful.";
        
        // Add login redirect button
        $response['html'] = '<button onclick="window.location.href=\'login.php\'" class="btn btn-primary">Go to Login</button>';
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $response['message'] = $e->getMessage() ?: "Registration failed. Please try again.";
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
