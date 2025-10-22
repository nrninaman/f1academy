<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_team'])) {
    $team_id = $_POST['team_id'];
    if (delete_team_by_id($conn, $team_id)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Team ID $team_id deleted successfully.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error deleting Team ID $team_id.</div>";
    }
}

// Fetch all teams
$teams = get_all_teams($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Teams List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .admin-nav a { transition: color 0.3s; }
        .admin-nav a:hover { color: hotpink; }
        th, td { padding: 12px; border-bottom: 1px solid #374151; }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans flex">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-gray-800 h-screen fixed p-6">
        <h1 class="text-3xl font-extrabold mb-8 text-hotpink">Admin Panel</h1>
        <nav class="admin-nav space-y-4">
            <a href="admin_dashboard.php" class="block text-lg font-bold text-white hover:text-hotpink">📊 Dashboard</a>
            <a href="admin_users.php" class="block text-lg font-bold text-white hover:text-hotpink">👤 Users List</a>
            <a href="admin_teams.php" class="block text-lg font-bold text-hotpink">🏎️ Teams List</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-white hover:text-hotpink">💰 Sponsors List</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6">🚪 Logout</a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">F1 Teams (<?php echo count($teams); ?>)</h2>
            <p class="text-gray-400">Manage tracked F1 teams.</p>
        </header>

        <?php echo $message; ?>

        <!-- Teams Table -->
        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Base Country</th>
                        <th>Engine Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teams)): ?>
                        <?php foreach ($teams as $team): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($team['id']); ?></td>
                                <td><?php echo htmlspecialchars($team['name']); ?></td>
                                <td><?php echo htmlspecialchars($team['base_country']); ?></td>
                                <td><?php echo htmlspecialchars($team['engine_supplier']); ?></td>
                                <td class="whitespace-nowrap">
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this team?');">
                                        <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                        <button type="submit" name="delete_team" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-gray-400">No teams found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
