<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html data-theme="light">
<head>
    <title>Assign Subjects to Students</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.0.0/dist/full.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="bg-base-200 min-h-screen">
    <!-- Navbar -->
    <div class="navbar bg-primary text-primary-content">
        <div class="flex-1">
            <a class="btn btn-ghost normal-case text-xl">Welcome, <?php echo htmlspecialchars($user_name ?? 'Admin'); ?></a>
        </div>
        <div class="flex-none gap-2">
            <a href="index.html" class="btn btn-ghost">Home</a>
            <a href="?logout=true" class="btn btn-ghost">Logout</a>
        </div>
    </div>
<br>
    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <!-- Form to Assign Subjects to Students -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4 text-primary">Assign Subjects to Students</h2>
            <form action="assign_subject.php" method="POST" class="bg-white p-6 rounded-lg shadow-lg">
                <div class="mb-4">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Select Student</label>
                    <select id="student_id" name="student_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                        <?php
                        // Fetch students from the database
                        $db = new mysqli('localhost', 'username', 'password', 'database');
                        if ($db->connect_error) {
                            die("Connection failed: " . $db->connect_error);
                        }
                        $students = $db->query("SELECT student_id, student_name FROM students");
                        while ($student = $students->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($student['student_id']) . '">' . htmlspecialchars($student['student_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Select Subject</label>
                    <select id="subject_id" name="subject_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                        <?php
                        // Fetch subjects from the database
                        $subjects = $db->query("SELECT subject_id, subject_name FROM subjects");
                        while ($subject = $subjects->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($subject['subject_id']) . '">' . htmlspecialchars($subject['subject_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <input type="text" id="academic_year" name="academic_year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md" placeholder="2024">
                </div>
                <div class="mb-4">
                    <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                    <input type="text" id="semester" name="semester" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md" placeholder="1">
                </div>
                <input type="hidden" name="status" value="pending">
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Request Subject Assignment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function validateForm() {
        var subject = document.getElementById('subject_id').value;
        var year = document.getElementById('academic_year').value;
        var semester = document.getElementById('semester').value;

        if (!subject || !year || !semester) {
            alert('Please fill in all required fields');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>