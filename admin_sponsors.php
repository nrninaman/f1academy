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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_sponsor'])) {
    $sponsor_id = $_POST['sponsor_id'];
    if (delete_sponsor_by_id($conn, $sponsor_id)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Sponsor ID $sponsor_id deleted successfully.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error deleting Sponsor ID $sponsor_id.</div>";
    }
}

// Fetch all sponsors
$sponsors = get_all_sponsors($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Sponsors List</title>
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
            <a href="admin_teams.php" class="block text-lg font-bold text-white hover:text-hotpink">🏎️ Teams List</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-hotpink">💰 Sponsors List</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6">🚪 Logout</a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">F1 Academy Sponsors (<?php echo count($sponsors); ?>)</h2>
            <p class="text-gray-400">Manage tracked sponsors.</p>
        </header>

        <?php echo $message; ?>

        <!-- Sponsors Table -->
        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Sector</th>
                        <th>Contract Value (USD)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sponsors)): ?>
                        <?php foreach ($sponsors as $sponsor): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($sponsor['id']); ?></td>
                                <td><?php echo htmlspecialchars($sponsor['name']); ?></td>
                                <td><?php echo htmlspecialchars($sponsor['sector']); ?></td>
                                <td>$<?php echo number_format($sponsor['contract_value']); ?></td>
                                <td class="whitespace-nowrap">
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this sponsor?');">
                                        <input type="hidden" name="sponsor_id" value="<?php echo $sponsor['id']; ?>">
                                        <button type="submit" name="delete_sponsor" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-gray-400">No sponsors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
