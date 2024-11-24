<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

// Check if user is a student
if ($_SESSION['user_role'] !== 'student') {
    header('Location: unauthorized.php');
    exit;
}

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.html');
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "students";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    die("An error occurred. Please try again later or contact support.");
}

// Fetch user name
$user_query = "SELECT name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_name = $user['name'];
} else {
    $user_name = 'Student';
}

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // First verify this is a valid student user
    $student_query = "
        SELECT s.student_id 
        FROM students s 
        INNER JOIN users u ON s.student_id = u.user_id 
        WHERE u.user_id = ? AND u.role = 'student'";
    
    $stmt = $conn->prepare($student_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No student found with this ID");
    }

    $student = $result->fetch_assoc();
    $student_id = $student['student_id'];

    // Now fetch subjects
    $subjects_query = "
        SELECT s.*, st.teacher_id, u.name as teacher_name 
        FROM subjects s
        INNER JOIN student_subjects ss ON s.subject_id = ss.subject_id
        INNER JOIN subject_teachers st ON s.subject_id = st.subject_id
        INNER JOIN teachers t ON st.teacher_id = t.teacher_id
        INNER JOIN users u ON t.teacher_id = u.user_id
        WHERE ss.student_id = ?
        AND ss.academic_year = YEAR(CURRENT_DATE)";

    $stmt = $conn->prepare($subjects_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $subjects = $stmt->get_result();

    // Fetch assignments
    $assignments_query = "
        SELECT a.assignment_id, a.title, a.description, a.due_date, s.subject_name, sa.status
        FROM assignments a
        INNER JOIN subjects s ON a.subject_id = s.subject_id
        INNER JOIN student_subjects ss ON s.subject_id = ss.subject_id
        LEFT JOIN student_assignments sa ON a.assignment_id = sa.assignment_id 
            AND sa.student_id = ?
        WHERE ss.student_id = ?
        ORDER BY a.due_date ASC";
    
    $stmt = $conn->prepare($assignments_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $student_id, $student_id);
    $stmt->execute();
    $assignments = $stmt->get_result();

    // Get counts for stats cards
    $stats_query = "
        SELECT 
            (SELECT COUNT(*) FROM student_subjects WHERE student_id = ?) as total_subjects,
            (SELECT COUNT(*) FROM assignments a
             INNER JOIN student_subjects ss ON a.subject_id = ss.subject_id
             LEFT JOIN student_assignments sa ON a.assignment_id = sa.assignment_id 
                AND sa.student_id = ?
             WHERE ss.student_id = ? AND (sa.status IS NULL OR sa.status = 'pending')) as pending_assignments,
            (SELECT COALESCE(AVG(grade), 0) FROM student_subjects WHERE student_id = ?) as average_grade";
    
    $stmt = $conn->prepare($stats_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("iiii", $student_id, $student_id, $student_id, $student_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();

} catch (Exception $e) {
    error_log($e->getMessage());
    die("An error occurred. Please try again later or contact support.");
}
?>


<!DOCTYPE html>
<html data-theme="light">
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.0.0/dist/full.css" rel="stylesheet">
</head>
<body class="bg-base-200 min-h-screen">
    <!-- Navbar -->
    <div class="navbar bg-primary text-primary-content">
        <div class="flex-1">
            <a class="btn btn-ghost normal-case text-xl">Welcome, <?php echo htmlspecialchars($user_name ?? 'Student'); ?></a>
        </div>
        <div class="flex-none gap-2">
            <a href="index.php" class="btn btn-ghost">Home</a>
            <a href="?logout=true" class="btn btn-ghost">Logout</a>
            <button class="btn btn-ghost btn-circle">
                <div class="indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="badge badge-xs badge-primary indicator-item"></span>
                </div>
            </button>
        </div>
    </div>
<br>
    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stats shadow bg-primary text-primary-content">
                <div class="stat">
                    <div class="stat-title text-primary-content/80">Total Subjects</div>
                    <div class="stat-value"><?php echo $stats['total_subjects'] ?? 0; ?></div>
                </div>
            </div>

            <div class="stats shadow bg-secondary text-secondary-content">
                <div class="stat">
                    <div class="stat-title text-secondary-content/80">Pending Assignments</div>
                    <div class="stat-value"><?php echo $stats['pending_assignments'] ?? 0; ?></div>
                </div>
            </div>

            <div class="stats shadow bg-accent text-accent-content">
                <div class="stat">
                    <div class="stat-title text-accent-content/80">Average Grade</div>
                    <div class="stat-value"><?php echo number_format($stats['average_grade'] ?? 0, 1); ?>%</div>
                </div>
            </div>
        </div>
    </div>
<br>
        <!-- Subjects Table -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4 text-primary">My Subjects</h2>
            <div class="overflow-x-auto rounded-lg shadow-lg">
                <table class="table w-full bg-white">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="px-6 py-4">Subject Code</th>
                            <th class="px-6 py-4">Subject Name</th> 
                            <th class="px-6 py-4">Teacher</th>
                            <th class="px-6 py-4">Credits</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($subject = $subjects->fetch_assoc()) { ?>
                            <tr class="hover:bg-gray-100 transition-colors">
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td class="px-6 py-4 border-b font-medium"><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($subject['teacher_name']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($subject['credits']); ?></td>
                                <td class="px-6 py-4 border-b">
                                    <div class="flex gap-2">
                                        <button onclick="window.location.href='download_handler.php?type=subject&id=<?php echo $subject['subject_id']; ?>'" 
                                                class="btn btn-outline btn-info btn-xs hover:scale-105 transition-transform">
                                            <span class="text-green-500">Download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assignments Table -->
        <div>
            <h2 class="text-2xl font-bold mb-4 text-primary">My Assignments</h2>
            <div class="overflow-x-auto rounded-lg shadow-lg">
                <?php if ($assignments->num_rows > 0) { ?>
                <table class="table w-full bg-white">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="px-6 py-4">Title</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Due Date</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($assignment = $assignments->fetch_assoc()) { ?>
                            <tr class="hover:bg-gray-100 transition-colors">
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($assignment['status'] ?? 'pending'); ?></td>
                                <td class="px-6 py-4 border-b">
                                    <div class="flex gap-2">
                                        <button onclick="window.location.href='download_handler.php?type=assignment&id=<?php echo $assignment['assignment_id']; ?>'" 
                                                class="btn btn-outline btn-info btn-xs hover:scale-105 transition-transform">
                                            <span class="text-green-500">Download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                    <p class="text-center text-gray-500">No assignments found.</p>
                <?php } ?>
            </div>
        </div>
