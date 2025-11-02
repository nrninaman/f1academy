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
    <title>Race Weekend Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.bg-hotpink { background-color: hotpink; }.text-hotpink { color: hotpink; }</style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-hotpink">Race Weekend Details</h1>
        <p class="text-gray-400">In-depth information for each Grand Prix weekend.</p>
    </header>

    <div class="max-w-4xl mx-auto space-y-8">
        <?php if (!empty($races)): ?>
            <?php foreach ($races as $race): ?>
                <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-l-4 <?php echo $race['is_completed'] ? 'border-green-500' : 'border-blue-500'; ?>">
                    <h2 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($race['name']); ?></h2> 
                    <p class="text-gray-400 mb-4">Round <?php echo htmlspecialchars($race['round_number']); ?> | Date: <?php echo date('F d, Y', strtotime($race['date'])); ?></p>
                    <p class="text-lg"><strong class="text-gray-300">Details:</strong> <?php echo htmlspecialchars($race['details'] ?? 'No specific details provided for the weekend format.'); ?></p>
                    <p class="text-sm mt-3 text-gray-500">Weekend Schedule: Friday Practice, Saturday Qualifying/Sprint, Sunday Race.</p>
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