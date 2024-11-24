<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "students";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("An error occurred. Please try again later or contact support.");
}

// Fetch student details
$student_query = "
    SELECT u.name, u.rank, u.phone_number, s.enrollment_date, s.graduation_year, s.status
    FROM students s
    INNER JOIN users u ON s.student_id = u.user_id
    WHERE s.student_id = ?";
$stmt = $conn->prepare($student_query);
if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student_details = $result->fetch_assoc();

// Fetch subjects
$subjects_query = "
    SELECT sub.subject_code, sub.subject_name, sub.credits, u.name AS teacher_name
    FROM student_subjects ss
    INNER JOIN subjects sub ON ss.subject_id = sub.subject_id
    INNER JOIN teachers t ON ss.teacher_id = t.teacher_id
    INNER JOIN users u ON t.teacher_id = u.user_id
    WHERE ss.student_id = ?";
$stmt = $conn->prepare($subjects_query);
if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$subjects_result = $stmt->get_result();

// Fetch assignments
$assignments_query = "
    SELECT a.title, sa.submission_date, sa.score, sa.status, sa.feedback
    FROM student_assignments sa
    INNER JOIN assignments a ON sa.assignment_id = a.assignment_id
    WHERE sa.student_id = ?";
$stmt = $conn->prepare($assignments_query);
if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$assignments_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@1.14.0/dist/full.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-100">
    <!-- Navbar -->
    <div class="navbar bg-primary text-primary-content">
        <div class="flex-1">
            <a class="btn btn-ghost normal-case text-xl">Welcome, <?php echo htmlspecialchars($user_name ?? 'Student'); ?></a>
        </div>
        <div class="flex-none gap-2">
            <a href="index.html" class="btn btn-ghost">Home</a>
            <a href="?logout=true" class="btn btn-ghost">Logout</a>
            <a href="dashboard.php" class="btn btn-ghost">Dashboard</a>
        </div>
    </div>
    <div class="container mx-auto p-4">
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-100">Student Details</h2>
            <div class="mb-4">
                <h3 class="text-xl font-semibold mb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student_details['name']); ?></p>
                    <p><strong>Rank:</strong> <?php echo htmlspecialchars($student_details['rank']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($student_details['phone_number']); ?></p>
                    <p><strong>Enrollment Date:</strong> <?php echo htmlspecialchars($student_details['enrollment_date']); ?></p>
                    <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($student_details['graduation_year']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($student_details['status']); ?></p>
                </div>
            </div>
            <div class="mb-4">
                <h3 class="text-xl font-semibold mb-2">Subjects</h3>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="bg-gray-700 text-gray-100">
                                <th class="px-4 py-2">Subject Code</th>
                                <th class="px-4 py-2">Subject Name</th>
                                <th class="px-4 py-2">Credits</th>
                                <th class="px-4 py-2">Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($subject = $subjects_result->fetch_assoc()) { ?>
                                <tr class="hover:bg-gray-600">
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($subject['credits']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($subject['teacher_name']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mb-4">
                <h3 class="text-xl font-semibold mb-2">Assignments</h3>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="bg-gray-700 text-gray-100">
                                <th class="px-4 py-2">Title</th>
                                <th class="px-4 py-2">Submission Date</th>
                                <th class="px-4 py-2">Score</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($assignment = $assignments_result->fetch_assoc()) { ?>
                                <tr class="hover:bg-gray-600">
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($assignment['title']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($assignment['submission_date']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($assignment['score']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($assignment['status']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($assignment['feedback']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>