<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$latest_results_data = get_latest_race_result($conn);
$race = $latest_results_data['race'];
$results = $latest_results_data['results'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latest Race Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.bg-hotpink { background-color: hotpink; }.text-hotpink { color: hotpink; }</style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-hotpink">Latest Race Result</h1>
        <?php if ($race): ?>
            <p class="text-2xl font-semibold mt-2"><?php echo htmlspecialchars($race['name']); ?></p> 
            <p class="text-lg text-gray-400 mt-1"><?php echo htmlspecialchars($race['details']); ?></p>
        <?php else: ?>
            <p class="text-2xl font-semibold mt-2 text-gray-400">No completed races found.</p>
        <?php endif; ?>
    </header>

    <?php if ($results): ?>
        <div class="max-w-xl mx-auto bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th class="p-3">POS</th>
                        <th class="p-3">Driver</th>
                        <th class="p-3">Team</th>
                        <th class="p-3">Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr class="hover:bg-gray-700 text-sm">
                            <td class="p-3 font-extrabold text-hotpink"><?php echo htmlspecialchars($result['position']); ?></td>
                            <td class="p-3 flex items-center gap-3">
                                <img src="<?php echo htmlspecialchars($result['image_path']); ?>" alt="Driver" class="w-8 h-8 rounded-full object-cover">
                                <strong><?php echo htmlspecialchars($result['fullname']); ?></strong>
                            </td>
                            <td class="p-3 text-gray-400"><?php echo htmlspecialchars($result['team_name']); ?></td>
                            <td class="p-3 font-bold"><?php echo htmlspecialchars($result['points']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300">← Back to Dashboard</a>
    </div>
</body>
</html>