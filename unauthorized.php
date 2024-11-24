<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@1.14.0/dist/full.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md mx-auto mt-20">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-700">Unauthorized Access</h2>
            <p class="text-center text-gray-600 mb-4">Sorry, <?php echo htmlspecialchars($user_name); ?>. You do not have permission to access this page.</p>
            <div class="text-center">
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="login.php?logout=true" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>