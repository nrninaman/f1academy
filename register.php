<?php
session_start();
include("conn.php");

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$nationalities = [
    "American", "Australian", "Austrian", "Bahraini", "Belgian", "Brazilian", "British", 
    "Canadian", "Chinese", "Danish", "Dutch", "Egypt", "Finnish", "French", "German", "Hungary",
    "Indian", "Irish", "Italian", "Japanese", "Korean", "Luxembourg", "Malaysian", "Mexican", "Monegasque", "New Zealander", 
    "Norwegian", "Portuguese", "Russian", "Saudi Arabian", "Singaporean", "South African", 
    "Spanish", "Swedish", "Swiss", "Thai", "Turkish","Uzbekistan", "Venezuelan", "Wales"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $user = check_user_by_email($conn, $email);

    if ($user) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (insert_new_user($conn, $fullname, $email, $nationality, $age, $gender, $hashed_password)) {
        $new_user_details = check_user_by_email($conn, $email);
        if ($new_user_details) {
            $_SESSION['user_id'] = $new_user_details['id'];
            $_SESSION['fullname'] = $new_user_details['fullname'];
            $_SESSION['role'] = $new_user_details['role'];
        }

        echo "<script>alert('Registration successful! Please select your team.'); window.location.href='select_team.php';</script>";
    } else {
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
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .hover\:bg-lightpink:hover { background-color: lightpink; }
        
        .dark-select { 
            background-color: rgba(255, 255, 255, 0.1); 
            color: white;
        }
        .dark-select option {
            background-color: #1f2937;
            color: #ffffff;
        }
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
        
        <input 
            type="text" 
            name="nationality" 
            placeholder="Nationality (e.g., British)" 
            required 
            list="nationality-list"
            class="w-full p-3 my-2 border border-gray-400 rounded-md dark-select focus:outline-none focus:ring-2 focus:ring-hotpink">
        
        <datalist id="nationality-list">
            <?php foreach ($nationalities as $n): ?>
                <option value="<?php echo htmlspecialchars($n); ?>">
            <?php endforeach; ?>
        </datalist>
        <br>
        <select name="age" required class="w-full p-3 my-2 border border-gray-400 rounded-md dark-select focus:outline-none focus:ring-2 focus:ring-hotpink">
            <option value="" disabled selected>Select Age</option>
            <?php for ($i = 16; $i <= 25; $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
        </select><br>

        <select name="gender" required class="w-full p-3 my-2 border border-gray-400 rounded-md dark-select focus:outline-none focus:ring-2 focus:ring-hotpink">
            <option value="" disabled selected>Select Gender</option>
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