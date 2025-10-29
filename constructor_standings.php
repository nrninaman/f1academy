<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
// Use the function to calculate team totals
$teams = get_constructor_standings_data($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coach Ranking (Team Standings)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.bg-hotpink { background-color: hotpink; }.text-hotpink { color: hotpink; }</style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-hotpink">Coach Ranking (Team Standings)</h1>
        <p class="text-gray-400">Total points accumulated by each team's drivers this season.</p>
    </header>

    <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
        <table class="min-w-full table-auto text-left">
            <thead>
                <tr class="bg-gray-700 text-sm uppercase">
                    <th class="p-3">POS</th>
                    <th class="p-3">Team</th>
                    <th class="p-3">Total Points</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teams)): ?>
                    <?php foreach ($teams as $team): ?>
                        <tr class="hover:bg-gray-700 text-sm">
                            <td class="p-3 font-extrabold text-hotpink"><?php echo htmlspecialchars($team['standing_position']); ?></td>
                            <td class="p-3 flex items-center gap-3">
                                <?php if ($team['logo_path']): ?>
                                    <img src="<?php echo htmlspecialchars($team['logo_path']); ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?>" class="w-8 h-8 object-contain">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($team['team_name']); ?>
                            </td>
                            <td class="p-3 font-bold"><?php echo htmlspecialchars($team['total_points']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center py-4 text-gray-400">No teams in the standings.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300">← Back to Dashboard</a>
    </div>
</body>
</html>