<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "students";

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset('utf8mb4');

try {
    // Start transaction
    $conn->begin_transaction();

    // Insert Users (Mix of teachers and students)
    $users_sql = $conn->prepare("INSERT INTO users (service_number, rank, name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Sample data for users
    $users = [
        // Teachers
        ['114852', 'WOII', 'Analo', 'analo@school.com', '1234567890', password_hash('password123', PASSWORD_DEFAULT), 'teacher'],
        ['114584', 'WOII', 'Musa', 'musa@school.com', '1234567891', password_hash('password123', PASSWORD_DEFAULT), 'teacher'],
        // Students
        ['417521', 'PTE', 'Walter Kiptoo', 'walt.k@school.com', '1234567892', password_hash('password123', PASSWORD_DEFAULT), 'student'],
        ['417522', 'PTE', 'Hilla Kaunda', 'hill.k@school.com', '1234567893', password_hash('password123', PASSWORD_DEFAULT), 'student'],
        ['417523', 'PTE', 'Gift Tembu', 'gift.t@school.com', '1234567894', password_hash('password123', PASSWORD_DEFAULT), 'student']
    ];

    foreach ($users as $user) {
        $users_sql->bind_param('sssssss', $user[0], $user[1], $user[2], $user[3], $user[4], $user[5], $user[6]);
        $users_sql->execute();
        $user_id = $conn->insert_id;

        // Insert into teachers or students table based on role
        if ($user[6] === 'teacher') {
            $teacher_sql = $conn->prepare("INSERT INTO teachers (teacher_id, department) VALUES (?, ?)");
            $department = 'Information Technology';
            $teacher_sql->bind_param('is', $user_id, $department);
            $teacher_sql->execute();
        } else {
            $student_sql = $conn->prepare("INSERT INTO students (student_id, enrollment_date, graduation_year, status) VALUES (?, ?, ?, ?)");
            $enrollment_date = '2024-09-01';
            $graduation_year = 2027;
            $status = 'active';
            $student_sql->bind_param('isis', $user_id, $enrollment_date, $graduation_year, $status);
            $student_sql->execute();
        }
    }

    // Insert Subjects
    $subjects_sql = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, description, credits) VALUES (?, ?, ?, ?)");
    
    $subjects = [
        ['W101', 'Windows 10', 'Introduction to Windows 10 ', 3],
        ['MYSQL101', 'MYSQL', 'MYSQL Databases', 3],
        ['IT101', 'IT Essentials', 'IT Essentials', 4],
        ['WS101', 'Windows Server', 'Windows Server', 3]
    ];

    foreach ($subjects as $subject) {
        $subjects_sql->bind_param('sssi', $subject[0], $subject[1], $subject[2], $subject[3]);
        $subjects_sql->execute();
    }

    // Assign teachers to subjects
    $subject_teachers_sql = $conn->prepare("INSERT INTO subject_teachers (subject_id, teacher_id, academic_year, semester) VALUES (?, ?, ?, ?)");
    
    // Get teacher IDs (assuming first two users are teachers)
    $teacher_ids = [];
    $teacher_result = $conn->query("SELECT teacher_id FROM teachers LIMIT 2");
    while ($row = $teacher_result->fetch_assoc()) {
        $teacher_ids[] = $row['teacher_id'];
    }

    // Assign subjects to teachers
    for ($i = 1; $i <= 4; $i++) {
        $subject_id = $i;
        $teacher_id = $teacher_ids[($i - 1) % 2]; // Alternate between teachers
        $academic_year = 2024;
        $semester = '1';
        $subject_teachers_sql->bind_param('iiis', $subject_id, $teacher_id, $academic_year, $semester);
        $subject_teachers_sql->execute();
    }

    // Create assignments
    $assignments_sql = $conn->prepare("INSERT INTO assignments (subject_id, teacher_id, title, description, due_date, max_score) VALUES (?, ?, ?, ?, ?, ?)");
    
    $assignments = [
        [1, $teacher_ids[0], 'Math Quiz 1', 'Complete chapters 1-3', '2024-02-15 23:59:59', 100],
        [2, $teacher_ids[1], 'English Essay', 'Write a 1000-word essay', '2024-02-20 23:59:59', 100],
        [3, $teacher_ids[0], 'Programming Task', 'Create a simple calculator', '2024-02-25 23:59:59', 100],
        [4, $teacher_ids[1], 'Physics Lab Report', 'Document your lab findings', '2024-03-01 23:59:59', 100]
    ];

    foreach ($assignments as $assignment) {
        $assignments_sql->bind_param('iisssd', $assignment[0], $assignment[1], $assignment[2], $assignment[3], $assignment[4], $assignment[5]);
        $assignments_sql->execute();
    }

    // Enroll students in subjects
    $student_subjects_sql = $conn->prepare("INSERT INTO student_subjects (student_id, subject_id, teacher_id, academic_year, semester, status) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Get student IDs
    $student_ids = [];
    $student_result = $conn->query("SELECT student_id FROM students");
    while ($row = $student_result->fetch_assoc()) {
        $student_ids[] = $row['student_id'];
    }

    // Enroll each student in all subjects
    foreach ($student_ids as $student_id) {
        for ($subject_id = 1; $subject_id <= 4; $subject_id++) {
            $teacher_id = $teacher_ids[($subject_id - 1) % 2];
            $academic_year = 2024;
            $semester = '1';
            $status = 'enrolled';
            $student_subjects_sql->bind_param('iiiiss', $student_id, $subject_id, $teacher_id, $academic_year, $semester, $status);
            $student_subjects_sql->execute();
        }
    }

    // Create some sample notifications
    $notifications_sql = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    
    foreach ($student_ids as $student_id) {
        $title = "Welcome to the new semester!";
        $message = "Welcome to the 2024 academic year. Please check your course schedule.";
        $type = "announcement";
        $notifications_sql->bind_param('isss', $student_id, $title, $message, $type);
        $notifications_sql->execute();
    }

    // Commit transaction
    $conn->commit();
    echo "Sample data inserted successfully!";

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
