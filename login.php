<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@1.14.0/dist/full.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-100">
    <div class="navbar bg-base-200 shadow-lg">
        <div class="flex-1">
            <a href="index.html" class="text-4xl font-bold text-center text-blue-500">School Website</a>
        </div>
        <div class="flex-none gap-2">
            <a href="registration.html" class="btn btn-secondary btn-outline">Register</a>
        </div>
    </div>
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-700">Login</h2>
            <form id="loginForm" class="space-y-4" method="POST" action="login_handler.php">
                <div class="mb-4">
                    <label class="block text-gray-700">Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" class="input input-bordered w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="input input-bordered w-full" required>
                </div>    
                <div class="mb-6">
                    <button type="submit" class="btn btn-secondary btn-outline w-full">Login</button>
                </div>
                <div id="errorMessage" class="text-red-500 text-center hidden"></div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const phoneNumber = $('#phone_number').val();
                const password = $('#password').val();
                
                // Show loading state
                const submitButton = $(this).find('button[type="submit"]');
                submitButton.addClass('loading').prop('disabled', true);
                $('#errorMessage').addClass('hidden');

                $.ajax({
                    url: 'login_handler.php',
                    type: 'POST',
                    data: {
                        phone_number: phoneNumber,
                        password: password
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Redirect to dashboard
                            window.location.href = response.redirect;
                        } else {
                            // Show error message
                            $('#errorMessage')
                                .text(response.message)
                                .removeClass('hidden');
                            submitButton.removeClass('loading').prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#errorMessage')
                            .text('An error occurred: ' + xhr.responseText)
                            .removeClass('hidden');
                        submitButton.removeClass('loading').prop('disabled', false);
                    }
                });
            });
        });
    </script>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 School System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>