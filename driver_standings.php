<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Use the function to get all driver standings data
// ASSUMPTION: $drivers is correctly sorted by 'points' DESCENDING
$drivers = get_driver_standings_data($conn);

// Separate the top 3 from the rest
$top_drivers = array_slice($drivers, 0, 3);
$remaining_drivers = array_slice($drivers, 3);

// Reorder the top_drivers array for the visual podium: P2, P1, P3
$podium_drivers = [];
if (isset($drivers[1])) { $podium_drivers[] = $drivers[1]; } // Data for P2 (Silver Box)
if (isset($drivers[0])) { $podium_drivers[] = $drivers[0]; } // Data for P1 (Gold Center Box)
if (isset($drivers[2])) { $podium_drivers[] = $drivers[2]; } // Data for P3 (Bronze Box)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Standings - F1 Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* General Styling */
        .bg-hotpink { background-color: #FF69B4; }
        .text-hotpink { color: #FF69B4; }
        .glow-shadow { box-shadow: 0 0 15px rgba(255, 105, 180, 0.6); } /* Hotpink glow */
        
        /* Podium Gradients */
        .gradient-gold { background: linear-gradient(145deg, #FFD700 20%, #FFA500 80%); } 
        .gradient-silver { background: linear-gradient(145deg, #C0C0C0 20%, #A9A9A9 80%); } 
        .gradient-bronze { background: linear-gradient(145deg, #CD7F32 20%, #8B4513 80%); } 
    </style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-6 md:p-10">
    
    <div class="max-w-5xl mx-auto">
        <header class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-hotpink tracking-wider"> Driver Standings</h1>
            <p class="text-gray-400 mt-2 text-lg">Current positions and points for all F1 Academy drivers.</p>
        </header>

        <section class="mb-12 grid grid-cols-3 gap-4 md:gap-6 items-end">
            
            <?php foreach ($podium_drivers as $driver): ?>
                <?php
                    // Use the driver's actual standing position
                    $current_rank = (int)$driver['standing_position']; 
                    
                    $bg_class = '';
                    $text_color = 'text-gray-900';
                    $size_class = 'h-64'; // Default height
                    
                    if ($current_rank === 1) {
                        $bg_class = 'gradient-gold glow-shadow';
                        $icon = '🥇';
                        $size_class = 'h-72'; // P1 is taller
                    } elseif ($current_rank === 2) {
                        $bg_class = 'gradient-silver';
                        $icon = '🥈';
                    } else { // Rank 3
                        $bg_class = 'gradient-bronze';
                        $icon = '🥉';
                        $text_color = 'text-gray-100'; // For better contrast on bronze
                    }
                ?>
                <div class="p-4 md:p-6 rounded-xl text-center flex flex-col justify-center transform transition duration-500 hover:scale-[1.05] <?php echo $bg_class; ?> <?php echo $size_class; ?>">
                    <div class="text-4xl md:text-5xl mb-1 <?php echo $text_color; ?> font-black leading-none"><?php echo $icon; ?></div>
                    <h3 class="text-3xl md:text-5xl font-black mb-1 <?php echo $text_color; ?>"><?php echo htmlspecialchars($driver['points']); ?></h3>
                    <p class="text-xs md:text-sm uppercase font-semibold <?php echo $text_color; ?>">Total Points</p>
                    
                    <div class="h-px w-2/3 mx-auto my-2 <?php echo ($current_rank === 1) ? 'bg-gray-900' : 'bg-white/50'; ?>"></div>
                    
                    <?php if ($driver['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="<?php echo htmlspecialchars($driver['fullname']); ?>" class="w-14 h-14 object-cover rounded-full mx-auto mt-2 mb-2 border-2 <?php echo ($current_rank === 1) ? 'border-gray-900' : 'border-white/50'; ?>">
                    <?php endif; ?>
                    
                    <h4 class="text-md md:text-xl font-bold <?php echo $text_color; ?> mt-1">
                        <strong><?php echo htmlspecialchars($driver['fullname']); ?></strong>
                    </h4>
                    <p class="text-xs <?php echo $text_color; ?> opacity-80"><?php echo htmlspecialchars($driver['team_name']); ?></p>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="bg-gray-800 rounded-xl overflow-hidden shadow-2xl">
            <h3 class="text-xl font-bold p-4 bg-gray-700 border-b border-gray-600 text-hotpink">Full Grid</h3>
            <table class="min-w-full table-auto text-left divide-y divide-gray-700">
                <thead>
                    <tr class="text-xs uppercase text-gray-400">
                        <th class="p-3 w-16">Pos</th>
                        <th class="p-3">Driver</th>
                        <th class="p-3">Team</th>
                        <th class="p-3 text-right">Points</th>
                        <th class="p-3">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php if (!empty($remaining_drivers)): ?>
                        <?php foreach ($remaining_drivers as $driver): ?>
                            <tr class="hover:bg-gray-700 transition duration-150 text-sm">
                                <td class="p-3 font-semibold text-gray-300"><?php echo htmlspecialchars($driver['standing_position']); ?></td>
                                <td class="p-3 flex items-center gap-3 py-4">
                                    <?php if ($driver['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="<?php echo htmlspecialchars($driver['fullname']); ?>" class="w-10 h-10 object-cover rounded-full">
                                    <?php endif; ?>
                                    <span class="font-medium text-white"><strong><?php echo htmlspecialchars($driver['fullname']); ?></strong></span>
                                </td>
                                <td class="p-3 text-gray-400"><strong><?php echo htmlspecialchars($driver['team_name']); ?></strong></td>
                                <td class="p-3 text-right font-extrabold text-lg text-hotpink"><?php echo htmlspecialchars($driver['points']); ?></td>
                                <td class="p-3">
                                    <a href="driver_details.php?id=<?php echo $driver['id']; ?>" class="text-hotpink hover:text-white text-xs font-bold transition duration-200">VIEW</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">No further drivers in the standings.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        
        <div class="text-center mt-10">
            <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300 text-lg font-medium">← Back to Dashboard</a>
        </div>
        
        <?php if (empty($drivers)): ?>
            <div class="text-center mt-10 p-6 bg-gray-800 rounded-xl shadow-lg">
                <p class="text-2xl text-red-400">🚨 Standings Data Missing 🚨</p>
                <p class="text-gray-400 mt-2">Check your database connection or the <code>get_driver_standings_data()</code> function.</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>