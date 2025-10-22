<?php
// Start the session
session_start();
// Include the database connection and query functions
include("conn.php");

// Run only if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    $age = (int)$_POST['age']; // Cast age to integer
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password match (Functional check)
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Use function to check if email already exists
    $user = check_user_by_email($conn, $email);

    if ($user) {
        // Email already registered
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit();
    }

    // Hash password before insertion
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Use function to insert new user into database
    if (insert_new_user($conn, $fullname, $email, $nationality, $age, $gender, $hashed_password)) {
        // FUNCTIONAL FIX: Redirect to team selection without email parameter
        echo "<script>alert('Registration successful! Please select your team.'); window.location.href='select_team.php';</script>";
    } else {
        // Database insertion failed
        echo "<script>alert('Error: Unable to register'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>F1 Academy Sign Up</title>
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

<div class="signup-box w-80 p-8 m-auto border border-gray-500 rounded-xl shadow-lg shadow-white/10 text-center">
    <h2 class="mb-5 text-2xl font-bold">Sign Up</h2>
    <form action="register.php" method="post" class="space-y-4">
        <input type="text" name="fullname" placeholder="Full Name" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>
        <input type="email" name="email" placeholder="Email Address" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>
        <input type="text" name="nationality" placeholder="Nationality" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>

        <select name="age" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink">
            <option value="">Select Age</option>
            <script>
                for (let i = 16; i <= 25; i++) {
                    document.write(`<option value="${i}">${i}</option>`);
                }
            </script>
        </select><br>

        <select name="gender" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink">
            <option value="">Select Gender</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
        </select><br>

        <input type="password" name="password" placeholder="Password" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required class="w-full p-3 my-2 border border-gray-400 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-hotpink"><br>

        <input type="submit" value="Register" class="w-full p-3 bg-hotpink text-white font-bold border-none rounded-md cursor-pointer hover:bg-lightpink transition duration-300">
    </form>

    <p class="mt-4 text-sm">Already have an account? <a href="login.php" class="text-hotpink font-semibold hover:text-lightpink">Login here</a></p>
</div>

<footer class="text-center p-5 mt-auto text-sm text-gray-400">© 2025 F1 Academy. All rights reserved.</footer>
</body>
</html>