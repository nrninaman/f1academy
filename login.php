<?php
session_start();
// Include the database connection and query functions
include("conn.php");

// Handle POST request for login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use function to check if user exists and retrieve data, including 'role'
    $user = check_user_by_email($conn, $email);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Login successful: Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role']; // Store user role

            // Determine redirect based on role
            if ($user['role'] === 'admin') {
                echo "<script>alert('Admin login successful!'); window.location.href='admin_dashboard.php';</script>";
            } else {
                // For regular users, retrieve team/sponsor for dashboard display
                $details = get_user_by_id($conn, $user['id']);
                $_SESSION['team'] = $details['team'] ?? null;
                $_SESSION['sponsor'] = $details['sponsor'] ?? null;
                echo "<script>alert('Login successful! Welcome, {$user['fullname']}'); window.location.href='dashboard.php';</script>";
            }
            exit();
        } else {
            // Incorrect password
            echo "<script>alert('Incorrect password!'); window.history.back();</script>";
            exit();
        }
    } else {
        // Email not found
        echo "<script>alert('Email not found!'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>F1 Academy Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom colors for hotpink and lightpink */
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .hover\:bg-lightpink:hover { background-color: lightpink; }
    </style>
</head>
<body class="bg-black text-white flex flex-col min-h-screen text-center font-sans">

<header>
    <img src="image/F1AcademyLogo.png" alt="F1 Academy Logo" class="mt-8 w-48 mx-auto">
</header>

<div class="login-box w-80 p-8 m-auto border border-gray-500 rounded-xl shadow-lg shadow-white/10 text-center">
    <h2 class="mb-5 text-2xl font-bold">Login</h2>
    <form action="login.php" method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Email Address" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>
        <input type="password" name="password" placeholder="Password" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>
        <input type="submit" value="Login" class="w-full p-3 bg-hotpink text-white font-bold border-none rounded-md cursor-pointer hover:bg-lightpink transition duration-300">
    </form>

    <p class="mt-4 text-sm">Don't have an account? <a href="register.php" class="text-hotpink font-semibold hover:text-lightpink">Sign up here</a></p>
</div>

<footer class="text-center p-5 mt-auto text-sm text-gray-400">© 2025 F1 Academy. All rights reserved.</footer>

</body>
</html>
