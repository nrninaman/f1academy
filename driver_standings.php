<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$drivers = get_driver_standings_data($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Standings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.bg-hotpink { background-color: hotpink; }.text-hotpink { color: hotpink; }</style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-hotpink">Driver Standings</h1>
        <p class="text-gray-400">Current positions and points for all F1 Academy drivers.</p>
    </header>

    <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
        <table class="min-w-full table-auto text-left">
            <thead>
                <tr class="bg-gray-700 text-sm uppercase">
                    <th class="p-3">POS</th>
                    <th class="p-3">Driver</th>
                    <th class="p-3">Team</th>
                    <th class="p-3">Points</th>
                    <th class="p-3">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($drivers)): ?>
                    <?php foreach ($drivers as $driver): ?>
                        <tr class="hover:bg-gray-700 text-sm">
                            <td class="p-3 font-extrabold text-hotpink"><?php echo htmlspecialchars($driver['standing_position']); ?></td>
                            <td class="p-3 flex items-center gap-3">
                                <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="Driver" class="w-8 h-8 rounded-full object-cover">
                                <?php echo htmlspecialchars($driver['fullname']); ?>
                            </td>
                            <td class="p-3 text-gray-400"><?php echo htmlspecialchars($driver['team_name']); ?></td>
                            <td class="p-3 font-bold"><?php echo htmlspecialchars($driver['points']); ?></td>
                            <td class="p-3">
                                <a href="driver_details.php?id=<?php echo $driver['id']; ?>" class="text-hotpink hover:text-white text-xs font-bold">VIEW</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-gray-400">No drivers in the standings.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300">← Back to Dashboard</a>
    </div>
</body>
</html>