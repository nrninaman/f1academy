<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$team_name = $_GET['name'] ?? null;
$team = null;
$drivers = [];

if ($team_name) {
    $team = get_team_by_name($conn, $team_name);
    // Fetch drivers for this team (Manual query since no function for this specific fetch exists yet)
    $stmt = $conn->prepare("SELECT id, fullname, image_path, standing_position FROM drivers WHERE team_name = ? ORDER BY standing_position ASC");
    $stmt->bind_param("s", $team_name);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }
    $stmt->close();
}

$team_colors = [
    'McLaren' => '#ff8000', 'Ferrari' => '#da1212', 'Mercedes' => '#00c0b5', 
    'Red Bull Racing' => '#002f6c', 'Aston Martin' => '#0A7968', 'Alpine' => '#fd4bc7', 
    'Williams' => '#00A3E0', 'Kick Sauber' => '#52E252', 'RB Cash App' => '#1930A2'
];

$main_color = $team_colors[$team_name] ?? '#9ca3af';
$text_color = ($team_name == 'Kick Sauber') ? 'text-black' : 'text-white';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($team_name); ?> Team Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .main-color { background-color: <?php echo $main_color; ?>; }
        .text-main-color { color: <?php echo $main_color; ?>; }
        .border-main-color { border-color: <?php echo $main_color; ?>; }
        .bg-hotpink { background-color: hotpink; }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-main-color"><?php echo htmlspecialchars($team_name); ?></h1>
        <p class="text-gray-400">Team and car specifications.</p>
    </header>

    <?php if ($team): ?>
        <div class="max-w-4xl mx-auto bg-gray-800 p-8 rounded-xl shadow-lg">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 items-center border-b border-gray-700 pb-8">
                <div>
                    <img src="<?php echo htmlspecialchars($team['car_image_path']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?> Car" class="w-full object-cover">
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-4">Team Info</h2>
                    <p class="text-lg"><strong class="text-gray-300">Base Country:</strong> <span class="text-main-color"><?php echo htmlspecialchars($team['base_country']); ?></span></p>
                    <p class="text-lg"><strong class="text-gray-300">Engine Supplier:</strong> <span class="text-main-color"><?php echo htmlspecialchars($team['engine_supplier']); ?></span></p>
                    <img src="<?php echo htmlspecialchars($team['logo_path']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?> Logo" class="w-24 h-auto mt-4 object-contain">
                </div>
            </div>

            <h3 class="text-2xl font-bold text-main-color mb-4">Current Drivers</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <?php if (!empty($drivers)): ?>
                    <?php foreach ($drivers as $driver): ?>
                        <div class="bg-gray-700 p-4 rounded-lg text-center">
                            <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="<?php echo htmlspecialchars($driver['fullname']); ?>" class="w-24 h-24 object-cover rounded-full mx-auto mb-2 border-2 border-main-color">
                            <p class="font-semibold"><?php echo htmlspecialchars($driver['fullname']); ?></p>
                            <p class="text-sm text-gray-400">#<?php echo htmlspecialchars($driver['standing_position']); ?> in Standings</p>
                            <a href="driver_details.php?id=<?php echo $driver['id']; ?>" class="text-xs font-bold text-hotpink hover:text-white mt-1 block">View Profile</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-3 text-gray-400">No drivers currently assigned to this team.</p>
                <?php endif; ?>
            </div>

        </div>
    <?php else: ?>
        <p class="text-center text-red-400 text-xl">Team not found.</p>
    <?php endif; ?>

    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300">← Back to Dashboard</a>
    </div>
</body>
</html>