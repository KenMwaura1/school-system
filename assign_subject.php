<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db = new mysqli('localhost', 'username', 'password', 'database');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get form data
$student_id = $_POST['student_id'];
$subject_id = $_POST['subject_id'];
$academic_year = $_POST['academic_year'];
$semester = $_POST['semester'];
$status = $_POST['status'];

// Insert data into student_subject table
$stmt = $db->prepare("INSERT INTO student_subject (student_id, subject_id, academic_year, semester, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $student_id, $subject_id, $academic_year, $semester, $status);

if ($stmt->execute()) {
    echo "Subject assigned successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$db->close();
?>