<?php
session_start();
include("conn.php"); // Include conn.php to use the new DB functions

// Redirect if not logged in - Target updated to login.php
if (!isset($_SESSION['fullname'])) {
    header("Location: login.php");
    exit();
}

$all_drivers = get_driver_standings_data($conn);
$all_teams = get_all_teams($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>F1 Academy | Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Custom utility classes based on original design */
.bg-hotpink { background-color: hotpink; }
.text-hotpink { color: hotpink; }
.bg-lightpink:hover { background-color: lightpink; }

/* === INDIVIDUAL TEAM COLORS === */
.bg-mclaren { background-color: #ff8000; }
.bg-ferrari { background-color: #da1212; }
.bg-mercedes { background-color: #00c0b5; }
.bg-redbull { background-color: #002f6c; }
.bg-aston { background-color: #0A7968; }
.bg-alpine { background-color: #fd4bc7; }
.bg-williams { background-color: #00A3E0; }
.bg-kick { background-color: #52E252; color: black; }
.bg-rb { background-color: #1930A2; }

/* Custom styles for dropdown team cards (necessary for car image placement) */
.team-card img {
    height: 100px;
    object-fit: contain;
}

/* Show dropdown on hover */
.dropdown-content {
    display: none;
    position: absolute;
    top: 100%; 
    margin-top: -4px; 
    left: 50%;
    transform: translateX(-50%);
    padding: 20px;
    min-width: 800px;
    z-index: 200;
    background-color: #111; 
    border: 1px solid #333; 
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.6);
}

.dropdown:hover .dropdown-content {
    display: block;
}

.arrow {
    transition: transform 0.3s ease, color 0.3s ease;
}

.dropdown:hover .arrow {
    transform: rotate(180deg);
    color: hotpink;
}

</style>
</head>
<body class="m-0 font-sans bg-black text-white min-h-screen">

<nav class="bg-gray-900 flex justify-between items-center px-12 py-4 border-b-3 border-hotpink sticky top-0 z-50">
    <div class="nav-left">
        <img src="image/F1AcademyLogo.png" alt="F1 Logo" class="h-10">
    </div>

    <div class="nav-links flex gap-6 items-center">

        <div class="dropdown relative">
            <a href="#" class="text-white font-bold cursor-pointer transition duration-300 flex items-center gap-1 hover:text-hotpink">Schedule <span class="arrow text-xs">▼</span></a>
            <div class="dropdown-content bg-gray-900 border border-gray-700 rounded-xl shadow-lg text-center min-w-[300px]">
                <a href="schedule_calendar.php" class="block p-2 hover:bg-gray-800 rounded-lg">🗓️ 2025 Calendar (Monthly View)</a>
                <a href="race_weekends.php" class="block p-2 hover:bg-gray-800 rounded-lg">🏁 Race Weekends (Details)</a>
            </div>
        </div>

        <div class="dropdown relative">
            <a href="#" class="text-white font-bold cursor-pointer transition duration-300 flex items-center gap-1 hover:text-hotpink">Results <span class="arrow text-xs">▼</span></a>
            <div class="dropdown-content bg-gray-900 border border-gray-700 rounded-xl shadow-lg text-center min-w-[300px]">
                <a href="latest_results.php" class="block p-2 hover:bg-gray-800 rounded-lg">🏆 Latest Race Result</a>
                <a href="driver_standings.php" class="block p-2 hover:bg-gray-800 rounded-lg">🔢 Driver Standings</a>
                <a href="constructor_standings.php" class="block p-2 hover:bg-gray-800 rounded-lg">🏆 Coach Ranking (Team)</a>
            </div>
        </div>

        <div class="dropdown relative">
            <a href="#" class="text-white font-bold cursor-pointer transition duration-300 flex items-center gap-1 hover:text-hotpink">Drivers <span class="arrow text-xs">▼</span></a>
            <div class="dropdown-content bg-gray-900 border border-gray-700 rounded-xl shadow-lg text-center max-w-5xl">
                <p class="text-sm font-light text-gray-400 mb-2">Click a driver for details (Sponsors, Team)</p>
                <div class="team-grid grid grid-cols-5 gap-3">
                    <?php if (!empty($all_drivers)): ?>
                        <?php foreach (array_slice($all_drivers, 0, 10) as $driver): // Show top 10 drivers ?>
                            <div class="team-card p-3 rounded-xl font-bold cursor-pointer text-sm transition duration-300 ease-in-out hover:scale-105 hover:shadow-hotpink/50 bg-gray-800/50" onclick="location.href='driver_details.php?id=<?php echo $driver['id']; ?>'">
                                <p class="text-sm text-hotpink">#<?php echo htmlspecialchars($driver['standing_position']); ?></p>
                                <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="<?php echo htmlspecialchars($driver['fullname']); ?>" class="w-full h-24 object-contain mt-1 rounded-lg mx-auto">
                                <?php echo htmlspecialchars($driver['fullname']); ?>
                                <p class="text-xs font-light text-gray-400"><?php echo htmlspecialchars($driver['team_name']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-span-5 py-4 text-gray-400">No driver data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="dropdown relative">
            <a href="#" class="text-white font-bold cursor-pointer transition duration-300 flex items-center gap-1 hover:text-hotpink">Teams <span class="arrow text-xs">▼</span></a>
            <div class="dropdown-content bg-gray-900 border border-gray-700 rounded-xl shadow-lg text-center max-w-5xl">
                <p class="text-sm font-light text-gray-400 mb-2">Click a team for details (Drivers, Sponsors)</p>
                <div class="team-grid grid grid-cols-5 gap-3">
                    <?php if (!empty($all_teams)): ?>
                        <?php 
                            $team_colors = [
                                'McLaren' => 'bg-mclaren', 'Ferrari' => 'bg-ferrari', 'Mercedes' => 'bg-mercedes', 
                                'Red Bull Racing' => 'bg-redbull', 'Aston Martin' => 'bg-aston', 'Alpine' => 'bg-alpine', 
                                'Williams' => 'bg-williams', 'Kick Sauber' => 'bg-kick text-black', 'RB Cash App' => 'bg-rb'
                            ];
                        ?>
                        <?php foreach (array_slice($all_teams, 0, 10) as $team): ?>
                            <div class="team-card <?php echo $team_colors[$team['name']] ?? 'bg-gray-700'; ?> p-3 rounded-xl font-bold cursor-pointer transition duration-300 ease-in-out hover:scale-105 hover:shadow-hotpink/50" onclick="location.href='team_details.php?name=<?php echo urlencode($team['name']); ?>'">
                                <?php echo htmlspecialchars($team['name']); ?>
                                <img src="<?php echo htmlspecialchars($team['car_image_path']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?> Car" class="w-full max-h-32 object-contain mt-5">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-span-5 py-4 text-gray-400">No team data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <a href="profile.php" class="text-white font-bold transition duration-300 hover:text-hotpink">Profile</a>
    </div>

    <div class="nav-right">
        <a href="logout.php" class="bg-hotpink text-white py-2 px-4 no-underline rounded-md font-bold transition duration-300 hover:bg-lightpink">Logout</a>
    </div>
</nav>

<div class="hero relative bg-cover bg-center h-[85vh] flex items-center justify-center text-center" style="background-image: url('image/discover-unlocked-sep-25.jpg');">
    <div class="hero-overlay absolute inset-0 bg-black/60"></div>
    <div class="hero-content relative z-10">
        <h1 class="text-6xl uppercase mb-3 font-extrabold text-white">Welcome, <?php echo $_SESSION['fullname']; ?>!</h1>
        <p class="text-xl text-gray-300">F1 Academy Unlocked – Experience the thrill of racing.</p>
    </div>
</div>

<footer class="bg-gray-900 text-center p-4 border-t-3 border-hotpink text-gray-400 mt-8">
    © 2025 F1 Academy. All rights reserved.
</footer>

</body>
</html>