<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$races = get_all_races($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2025 Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.bg-hotpink { background-color: hotpink; }.text-hotpink { color: hotpink; }</style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-hotpink">2025 F1 Academy Calendar</h1>
        <p class="text-gray-400">All planned race events for the season.</p>
    </header>

    <div class="max-w-4xl mx-auto space-y-4">
        <?php if (!empty($races)): ?>
            <?php foreach ($races as $race): ?>
                <div class="bg-gray-800 p-5 rounded-xl flex justify-between items-center hover:bg-gray-700 transition duration-300">
                    <div>
                        <p class="text-sm font-semibold text-gray-400">Round <?php echo htmlspecialchars($race['round_number']); ?></p>
                        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($race['name']); ?></h2>
                    </div>
                    <div class="text-right">
                        <p class="text-lg"><?php echo date('F d, Y', strtotime($race['date'])); ?></p>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full <?php echo $race['is_completed'] ? 'bg-green-500' : 'bg-blue-500'; ?>">
                            <?php echo $race['is_completed'] ? 'Completed' : 'Upcoming'; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center py-4 text-gray-400">No race events found.</p>
        <?php endif; ?>
    </div>
    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300">← Back to Dashboard</a>
    </div>
</body>
</html>