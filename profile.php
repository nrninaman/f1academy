<?php
session_start();
include("conn.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

$f1_teams = [
    "Ferrari", "Mercedes", "Red Bull Racing", "McLaren", "Aston Martin", "Alpine", "Williams", "Kick Sauber", "RB Cash App"
];

$f1_sponsors = [
    'Oracle',
    'HP (Hewlett-Packard)',
    'PETRONAS',
    'Visa',
    'Cash App',
    'Stake',
    'Kick',
    'MoneyGram',
    'BWT',
    'Aramco',
    'Mastercard',
    'Shell',
    'Ineos',
    'Red Bull GmbH',
    'Cognizant'
];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    
    $new_fullname = trim($_POST['fullname']);
    $new_email = trim($_POST['email']);
    $new_nationality = trim($_POST['nationality']);
    $new_team = trim($_POST['team']);
    $new_sponsor = trim($_POST['sponsor']);

    $team_request = null;
    $sponsor_request = null;

    // Validation
    if (empty($new_fullname) || empty($new_email) || empty($new_nationality)) {
        $message = "<div class='text-red-500 font-bold'>Error: Full Name, Email, and Nationality fields cannot be empty.</div>";
    } else {
        // Handle Team Request (Logic remains correct)
        if (!empty($new_team) && in_array($new_team, $f1_teams)) {
            $team_request = $new_team;
        }

        // Handle Sponsor Request (Logic remains correct, now using new $f1_sponsors list)
        if (!empty($new_sponsor) && in_array($new_sponsor, $f1_sponsors)) {
            $sponsor_request = $new_sponsor;
        }

        // Use function to update user profile (including name, email, and setting requests)
        if (update_user_full_profile_and_requests($conn, $new_fullname, $new_email, $new_nationality, $team_request, $sponsor_request, $user_id)) {
            $_SESSION['fullname'] = $new_fullname;

            $success_message = "Profile updated successfully!";
            if ($team_request) {
                $success_message .= " Team request for <strong>$team_request</strong> submitted for admin approval.";
            }
            if ($sponsor_request) {
                $success_message .= " Sponsor request for <strong>$sponsor_request</strong> submitted for admin approval.";
            }
            $message = "<div class='text-green-500 font-bold'>$success_message</div>";

        } else {
            $message = "<div class='text-red-500 font-bold'>Database Error: Unable to update profile. (Check if email is already in use)</div>";
        }
    }
}

$user = get_user_by_id($conn, $user_id);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$current_team = $user['team'] ?? "Not Selected";
$current_sponsor = $user['sponsor'] ?? "Not Assigned";
$team_request_status = $user['team_request'] ?? "";
$sponsor_request_status = $user['sponsor_request'] ?? "";
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
        
        <form method="POST" action="profile.php" class="space-y-4">
            
            <label for="fullname" class="block font-bold pt-4 text-gray-300">Full Name</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required 
                   class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">

            <label for="email" class="block font-bold pt-4 text-gray-300">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required 
                   class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
            
            <label class="block font-bold pt-4 text-gray-300">Age</label>
            <div class="display-value p-2 text-lg border-b border-gray-800 mb-4">
                <?php echo htmlspecialchars($user['age']); ?>
            </div>
            
            <label class="block font-bold pt-4 text-gray-300">Gender</label>
            <div class="display-value p-2 text-lg border-b border-gray-800 mb-4">
                <?php echo htmlspecialchars(ucfirst($user['gender'])); ?>
            </div>

            <label for="nationality" class="block font-bold pt-4 text-gray-300">Nationality</label>
            <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($user['nationality']); ?>" required 
                   class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">


            <label for="team" class="block font-bold pt-4 text-gray-300">Current Assigned Team: <span class="text-hotpink"><?php echo htmlspecialchars($current_team); ?></span></label>
            <?php if ($team_request_status): ?>
                <div class='text-yellow-500 font-bold'>Pending Team Request: <strong><?php echo htmlspecialchars($team_request_status); ?></strong></div>
            <?php endif; ?>
            <select id="team" name="team"
                    class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                <option value="">-- Choose New Team (Requires Admin Approval) --</option>
                <?php foreach ($f1_teams as $t): ?>
                    <option value="<?php echo $t; ?>">
                        <?php echo $t; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sponsor" class="block font-bold pt-4 text-gray-300">Current Assigned Sponsor: <span class="text-hotpink"><?php echo htmlspecialchars($current_sponsor); ?></span></label>
            <?php if ($sponsor_request_status): ?>
                <div class='text-yellow-500 font-bold'>Pending Sponsor Request: <strong><?php echo htmlspecialchars($sponsor_request_status); ?></strong></div>
            <?php endif; ?>
            <select id="sponsor" name="sponsor"
                    class="w-full p-3 mt-1 box-border border border-gray-600 bg-black text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                <option value="">-- Choose New Sponsor (Requires Admin Approval) --</option>
                <?php foreach ($f1_sponsors as $s): ?>
                    <option value="<?php echo $s; ?>">
                        <?php echo $s; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" name="update_profile" 
                    class="update-btn block w-full p-3 mt-6 bg-hotpink text-white border-none rounded-lg font-bold cursor-pointer transition duration-300 hover:bg-lightpink">
                Save Changes & Submit Requests
            </button>
        </form>
    </div>
</div>

<footer class="text-center p-5 text-sm text-gray-400 border-t border-gray-800 mt-auto">
    © 2025 F1 Academy. All rights reserved.
</footer>

</body>
</html>