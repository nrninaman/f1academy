<?php
session_start();
include("conn.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Team list standardized to use "Red Bull Racing"
$f1_teams = [
    "Ferrari",
    "Mercedes",
    "Red Bull Racing",
    "McLaren",
    "Aston Martin",
    "Alpine",
    "Williams",
    "Kick Sauber",
    "RB Cash App"
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    
    $new_team = trim($_POST['team']);
    $new_nationality = trim($_POST['nationality']);
    
    if (empty($new_team) || empty($new_nationality)) {
        $message = "<div class='text-red-500 font-bold'>Error: Team and Nationality fields cannot be empty.</div>";
    } elseif (!in_array($new_team, $f1_teams)) {
        $message = "<div class='text-red-500 font-bold'>Error: Invalid team selected.</div>";
    } else {
        // Use function to update user profile
        if (update_user_profile($conn, $new_team, $new_nationality, $user_id)) {
            // Update session for immediate team change display
            $_SESSION['team'] = $new_team;
            $message = "<div class='text-green-500 font-bold'>Profile updated successfully!</div>";
        } else {
            $message = "<div class='text-red-500 font-bold'>Database Error: Unable to update profile.</div>";
        }
    }
}

// Retrieve current user details
$user = get_user_by_id($conn, $user_id);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$team = $user['team'] ?? "Not Selected";
$sponsor = $user['sponsor'] ?? "Not Assigned";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Profile | F1 Academy</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .bg-hotpink { background-color: hotpink; }
    .text-hotpink { color: hotpink; }
    .hover\:bg-lightpink:hover { background-color: lightpink; }
</style>
</head>
<body class="bg-black text-white font-sans flex flex-col min-h-screen">

<nav class="bg-gray-900 flex justify-between items-center px-8 py-4 border-b-2 border-hotpink">
    <img src="image/F1AcademyLogo.png" alt="F1 Academy" class="w-32 h-auto">
    <ul class="flex space-x-6 m-0 p-0 list-none">
        <li><a href="dashboard.php" class="text-white font-bold transition duration-300 hover:text-hotpink">🏠 Home</a></li>
        <li><a href="profile.php" class="text-hotpink font-bold">👤 Profile</a></li>
        <li><a href="logout.php" class="text-white font-bold transition duration-300 hover:text-hotpink">🚪 Logout</a></li>
    </ul>
</nav>

<div class="container text-center flex-grow py-16 px-5">
    <h1 class="text-3xl font-bold mb-8">Your Full F1 Academy Profile</h1>

    <?php echo $message; ?>

    <div class="profile-card bg-gray-900 border-2 border-hotpink rounded-xl w-full max-w-lg mx-auto p-6 shadow-xl shadow-pink-500/40 text-left">
        <h2 class="text-center text-hotpink text-2xl font-semibold mb-5 border-b border-dashed border-gray-700 pb-3">
            <?php echo htmlspecialchars($user['fullname']); ?>'s Details
        </h2>
        
        <label class="block font-bold mt-4 text-gray-300">Email Address</label>
        <div class="display-value p-2 text-lg border-b border-gray-800 mb-4">
            <?php echo htmlspecialchars($user['email']); ?>
        </div>
        
        <label class="block font-bold mt-4 text-gray-300">Age</label>
        <div class="display-value p-2 text-lg border-b border-gray-800 mb-4">
            <?php echo htmlspecialchars($user['age']); ?>
        </div>
        
        <label class="block font-bold mt-4 text-gray-300">Gender</label>
        <div class="display-value p-2 text-lg border-b border-gray-800 mb-4">
            <?php echo htmlspecialchars(ucfirst($user['gender'])); ?>
        </div>

        <label class="block font-bold mt-4 text-gray-300">Assigned Sponsor</label>
        <div class="display-value p-2 text-lg border-b border-gray-800 mb-4">
            <?php echo htmlspecialchars($sponsor); ?>
        </div>

        <form method="POST" action="profile.php">
            <label for="nationality" class="block font-bold mt-4 text-gray-300">Nationality</label>
            <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($user['nationality']); ?>" required 
                   class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">

            <label for="team" class="block font-bold mt-4 text-gray-300">Assigned Team</label>
            <select id="team" name="team" required 
                    class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                <option value="">-- Select or Re-Select Team --</option>
                <?php foreach ($f1_teams as $t): ?>
                    <option value="<?php echo $t; ?>" 
                        <?php echo ($team == $t) ? 'selected' : ''; ?>>
                        <?php echo $t; ?>
                    </option>
                <?php endforeach; ?>
                <option value="Not Selected" disabled <?php echo ($team == "Not Selected") ? 'selected' : ''; ?>>
                    Not Selected (Choose a team above)
                </option>
            </select>
            
            <button type="submit" name="update_profile" 
                    class="update-btn block w-full p-3 mt-6 bg-hotpink text-white border-none rounded-lg font-bold cursor-pointer transition duration-300 hover:bg-lightpink">
                Save Changes
            </button>
        </form>
    </div>
</div>

<footer class="text-center p-5 text-sm text-gray-400 border-t border-gray-800 mt-auto">
    © 2025 F1 Academy. All rights reserved.
</footer>

</body>
</html>